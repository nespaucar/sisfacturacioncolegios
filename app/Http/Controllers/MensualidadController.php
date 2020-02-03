<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\AlumnoSeccion;
use App\AlumnoCuota;
use App\Movimiento;
use App\Nivel;
use App\Cicloacademico;
use App\Conceptopago;
use App\Configuracionpago;
use App\Persona;
use App\Cuota;
use App\Seccion;
use App\Grado;
use App\Http\Requests;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MensualidadController extends Controller
{
    protected $folderview      = 'app.mensualidad';
    protected $tituloAdmin     = 'Mensualidad';
    protected $rutas           = array('create' => 'mensualidad.create', 
            'edit'   => 'mensualidad.edit', 
            'conceptopago' => 'mensualidad.conceptopago',
            'realizarPago' => 'mensualidad.realizarPago',
            'search' => 'mensualidad.buscar',
            'index'  => 'mensualidad.index',
        );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function buscar(Request $request)
    {
        $user              = Auth::user();
        $id                = $user->persona_id;
        $local_id          = $user->persona->local_id;
        $pagina            = $request->input('page');
        $filas             = $request->input('filas');
        $entidad           = 'Mensualidad';
        $seccion_id        = Libreria::getParam($request->input('seccion_id'));
        $anoescolar        = Libreria::getParam($request->input('anoescolar'));
        $cicloacademico    = Cicloacademico::where(DB::raw("YEAR(created_at)"), "=", $anoescolar)->first();
        $cicloacademico_id = ($cicloacademico==NULL?0:$cicloacademico->id);
        $resultado         = AlumnoSeccion::listar($seccion_id, $cicloacademico_id, $local_id);
        $lista             = $resultado->get();
        $cabecera          = array();
        $cabecera[]        = array('valor' => '#', 'numero' => '1');
        $cabecera[]        = array('valor' => 'Nivel', 'numero' => '1');
        $cabecera[]        = array('valor' => 'Grado', 'numero' => '1');
        $cabecera[]        = array('valor' => 'Sección', 'numero' => '1');
        $cabecera[]        = array('valor' => 'Alumno', 'numero' => '1');
        $cabecera[]        = array('valor' => 'Matrícula', 'numero' => '1');
        $cabecera[]        = array('valor' => 'Ene', 'numero' => '1');
        $cabecera[]        = array('valor' => 'Feb', 'numero' => '1');
        $cabecera[]        = array('valor' => 'Mar', 'numero' => '1');
        $cabecera[]        = array('valor' => 'Abr', 'numero' => '1');
        $cabecera[]        = array('valor' => 'May', 'numero' => '1');
        $cabecera[]        = array('valor' => 'Jun', 'numero' => '1');
        $cabecera[]        = array('valor' => 'Jul', 'numero' => '1');
        $cabecera[]        = array('valor' => 'Ago', 'numero' => '1');
        $cabecera[]        = array('valor' => 'Set', 'numero' => '1');
        $cabecera[]        = array('valor' => 'Oct', 'numero' => '1');
        $cabecera[]        = array('valor' => 'Nov', 'numero' => '1');
        $cabecera[]        = array('valor' => 'Dic', 'numero' => '1');
        $cabecera[]        = array('valor' => 'Resumen', 'numero' => '1');

        $ruta             = $this->rutas;
        if (count($lista) > 0) {
            $clsLibreria     = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            $lista           = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'ruta', 'anoescolar', 'cicloacademico_id'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad', 'anoescolar', 'cicloacademico_id'));
    }

    public function index()
    {
        $entidad          = 'Mensualidad';
        $title            = $this->tituloAdmin;
        $ruta             = $this->rutas;
        $cboSecciones     = [''=>'--TODAS--'];
        $user             = Auth::user();
        $local_id         = $user->persona->local_id;

        $secciones        = Seccion::join("grado", "grado.id", "=", "seccion.grado_id")
                            ->join("nivel", "nivel.id", "=", "grado.nivel_id")
                            ->where("nivel.local_id", "=", $local_id)
                            ->select("seccion.id", "seccion.descripcion", "seccion.grado_id", "grado.nivel_id")
                            ->get();

        foreach ($secciones as $s) {
            $cboSecciones[$s->id] = ($s->grado!==NULL?$s->grado->descripcion:'-') . ' grado '.($s->descripcion) . ' del nivel ' . ($s->grado!==NULL?($s->grado->nivel!==NULL?$s->grado->nivel->descripcion:'-'):'-');
        }

        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'ruta', 'cboSecciones'));
    }

    public function conceptopago(Request $request) {
        $entidad            = 'Mensualidad';
        $alumno_seccion_id  = $request->id;
        $alumnoseccion      = AlumnoSeccion::find($alumno_seccion_id);
        //Buscamos la configuración de pago de mensualidad para el alumno
        $monto_mensualidad  = "0.00";
        $configuracionpago1 = Configuracionpago::where("alumno_id", "=", $alumnoseccion->alumno_id)->first();
        $configuracionpago2 = Configuracionpago::where("seccion_id", "=", $alumnoseccion->seccion_id)->first();
        $configuracionpago3 = Configuracionpago::where("grado_id", "=", $alumnoseccion->seccion->grado_id)->first();
        $configuracionpago4 = Configuracionpago::where("nivel_id", "=", $alumnoseccion->seccion->grado->nivel_id)->first();
        $cpago = Conceptopago::find(7);
        if($configuracionpago1!==NULL) {
            $monto_mensualidad = $configuracionpago1->monto."";
        } else {
            if($configuracionpago2!==NULL) {
                $monto_mensualidad = $configuracionpago2->monto."";
            } else {
                if($configuracionpago3!==NULL) {
                    $monto_mensualidad = $configuracionpago3->monto."";
                } else {
                    if($configuracionpago4!==NULL) {
                        $monto_mensualidad = $configuracionpago4->monto."";
                    } else {
                        $monto_mensualidad = $cpago->monto.""; //BUSCO EL CONCEPTO DE PAGO PARA LA MENSUALIDAD
                    }
                }
            }
        }
        if($request->listar=="SIS") {
            $cpago             = Conceptopago::find(6);
            $monto_mensualidad = $cpago->monto.""; //BUSCO EL CONCEPTO DE PAGO PARA LA MATRÍCULA
        }
        $listar             = 'NO';
        $title              = $this->tituloAdmin;
        $ruta               = $this->rutas;
        $formData           = array('route' => array('mensualidad.realizarPago', $alumno_seccion_id), 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        return view($this->folderview.'.conceptopago')->with(compact('entidad', 'title', 'ruta', 'formData', 'listar', 'alumnoseccion', 'monto_mensualidad', 'cpago'));
    }

    public function realizarPago(Request $request) {
        $error = DB::transaction(function() use($request){
            $user              = Auth::user();
            $local_id          = $user->persona->local_id;
            $seccion_id        = $request->seccion_id;
            $conceptopago_id   = $request->conceptopago_id;
            $alumno_seccion_id = $request->alumnoseccion_id;
            $cicloacademico_id = $request->cicloacademico_id;
            $conceptopago_id   = $request->conceptopago_id;
            $listar            = $request->listar;
            $cicloacademico    = Cicloacademico::find($cicloacademico_id);
            $persona_id        = $request->persona_id;
            $efectivo          = ($request->efectivo==""?0.00:$request->efectivo);
            $visa              = ($request->visa==""?0.00:$request->visa);
            $master            = ($request->master==""?0.00:$request->master);
            $total             = $request->total;
            $total2            = $request->total2;
            $cuenta            = $request->cuenta;
            $tipodocumento     = $request->tipodocumento;
            $ruc               = $request->ruc;
            $razon             = $request->razon;
            $direccion         = $request->direccion;
            $user              = Auth::user();

            //BUSCO LA MATRÍCULA DEL ALUMNO
            $seccion = Seccion::find($seccion_id);
            $alumno  = Persona::find($persona_id);

            $mensualidad  = AlumnoSeccion::where("alumno_id", "=", $persona_id)
                    ->where("cicloacademico_id", "=", $cicloacademico_id)
                    ->where("seccion_id", "=", $seccion_id)
                    ->first();

            //BUSCO LA CUOTA DEL ALUMNO
            $cuota        = Cuota::where("alumno_seccion_id", "=", $alumno_seccion_id)
                    ->where("cicloacademico_id", "=", $cicloacademico_id)
                    ->first();

            ####SI EL ALUMNO HA COMPETADO EL PAGO TOTAL
            if((float)$cuenta == 0) {                
                //CREO EL PRIMERO Y ÚNICO DETALLE DE CUOTA
                $alumnocuota            = new AlumnoCuota();
                $alumnocuota->monto     = $total2;
                $alumnocuota->alumno_id = $alumno->id;
                $alumnocuota->cuota_id  = $cuota->id;
                $alumnocuota->save();
                //CREO EL MOVIMIENTO DE INGRESO A CAJA
                $movimiento                    = new Movimiento();
                $movimiento->fecha             = date("Y-m-d");
                $movimiento->numero            = Movimiento::numerosigue(1, null, $local_id); //NÚMERO DE MOVIMIENTO EN CAJA
                $movimiento->persona_id        = $alumno->id;
                $movimiento->responsable_id    = $user->persona->id;
                $movimiento->tipomovimiento_id = 1; //CAJA
                $movimiento->totalefectivo     = $efectivo;
                $movimiento->conceptopago_id   = 6; //PAGO POR MATRÍCULA
                $movimiento->totalvisa         = $visa;
                $movimiento->totalmaster       = $master;
                $movimiento->total             = $total;
                $movimiento->igv               = 0;
                $movimiento->tipodocumento_id  = NULL;
                $movimiento->comentario        = "PAGO COMPLETO POR MATRÍCULA";
                $movimiento->totalpagado       = $total;
                $movimiento->estado            = "P"; //PAGADO
                $movimiento->local_id          = $local_id;
                $movimiento->cuota_id          = $cuota->id;
                $movimiento->save();                
                //CREO EL DOCUMENTO DE VENTA
                $movimientoventa                    = new Movimiento();
                $movimientoventa->fecha             = date("Y-m-d");
                $movimientoventa->numero            = Movimiento::numerosigue(8, ($tipodocumento=="B"?1:2), $local_id); //NÚMERO DE DOC VENTA QUE SIGUE
                $movimientoventa->persona_id        = $alumno->id;
                $movimientoventa->serie             = $request->serie;
                $movimientoventa->responsable_id    = $user->persona->id;
                $movimientoventa->tipomovimiento_id = 8; //VENTA
                $movimientoventa->conceptopago_id   = 6; //PAGO POR MATRÍCULA
                $movimientoventa->total             = $total;
                $movimientoventa->igv               = 0;
                $movimientoventa->tipodocumento_id  = ($tipodocumento=="B"?1:2);
                $movimientoventa->comentario        = "PAGO COMPLETO POR MATRÍCULA";
                $movimientoventa->totalpagado       = $total;
                $movimientoventa->estado            = "P"; //PAGADO
                $movimientoventa->ruc               = $ruc;
                $movimientoventa->razon             = $razon;
                $movimientoventa->direccion         = $direccion;
                $movimientoventa->cuota_id          = $cuota->id;
                $movimientoventa->movimiento_id     = $movimiento->id;
                $movimientoventa->local_id          = $local_id;
                $movimientoventa->save();

            ####SI EL ALUMNO NO HA COMPLETADO EL PAGO TOTAL
            } else {
                //CREO EL PRIMER DETALLE DE CUOTA, NO IMPORTA SI ES CERO, AMARRAMOS AMARRAMOS AL ALUMNO A LA CUOTA
                $alumnocuota            = new AlumnoCuota();
                $alumnocuota->monto     = $total2;
                $alumnocuota->alumno_id = $alumno->id;
                $alumnocuota->cuota_id  = $cuota->id;
                $alumnocuota->save();
                //SI EL PAGO ES MAYOR A 0 SOLES
                if((float)$total2 > 0.00) {
                    //CREO EL MOVIMIENTO DE INGRESO A CAJA
                    $movimiento                    = new Movimiento();
                    $movimiento->fecha             = date("Y-m-d");
                    $movimiento->numero            = Movimiento::numerosigue(1, null, $local_id); //NÚMERO DE MOVIMIENTO EN CAJA
                    $movimiento->persona_id        = $alumno->id;
                    $movimiento->responsable_id    = $user->persona->id;
                    $movimiento->tipomovimiento_id = 1; //CAJA
                    $movimiento->totalefectivo     = $efectivo;
                    $movimiento->conceptopago_id   = 6; //PAGO POR MATRÍCULA
                    $movimiento->totalvisa         = $visa;
                    $movimiento->totalmaster       = $master;
                    $movimiento->total             = $total2;
                    $movimiento->igv               = 0;
                    $movimiento->tipodocumento_id  = ($tipodocumento=="B"?1:2);
                    $movimiento->comentario        = "PAGO COMPLETO POR MATRÍCULA";
                    $movimiento->totalpagado       = $total2;
                    $movimiento->estado            = "P"; //PAGADO
                    $movimiento->cuota_id          = $cuota->id;
                    $movimiento->local_id          = $local_id;
                    $movimiento->save();
                }
            }
        });
        return is_null($error) ? "OK" : $error;
    }

    function numeroSigue(Request $request) {
        $user              = Auth::user();
        $local_id          = $user->persona->local_id;
        $tipomovimiento_id = $request->tipomovimiento_id==""?NULL:$request->tipomovimiento_id;
        $tipodocumento_id  = $request->tipodocumento_id==""?NULL:$request->tipodocumento_id;

        return Movimiento::numeroSigue($tipomovimiento_id, $tipodocumento_id, $local_id);
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'alumno_seccion');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            //BUSCO LA MATRÍCULA
            $mensualidad = AlumnoSeccion::find($id);
            //BUSCO LA CUOTA
            $cuota         = Cuota::where("alumno_seccion_id", "=", $mensualidad->id)->first();
            //BUSCO LOS DETALLES DE LA CUOTA
            $alumnocuotas  = AlumnoCuota::where("cuota_id", "=", $cuota->id)->get();
            //BUSCO EL MOVIMIENTO DE INGRESO A CAJA Y LA ANULO
            $movimiento    = Movimiento::where("tipomovimiento_id", "=", 1)
                            ->where("conceptopago_id", "=", 6)
                            ->whereNull("tipodocumento_id")
                            ->where("cuota_id", "=", $cuota->id)
                            ->first();
            if($movimiento!==NULL) {
                $movimiento->estado = "A"; //ANULADO
                $movimiento->save();
            }           
            //BUSCO EL DOCUMENTO DE VENTA Y LA ANULO
            $movimientoventa    = Movimiento::where("tipomovimiento_id", "=", 8)
                                ->where("conceptopago_id", "=", 6)
                                ->whereIn("tipodocumento_id", [1, 2]) //BOLETA O FACTURA
                                ->where("cuota_id", "=", $cuota->id)
                                ->first();
            if($movimientoventa!==NULL) {
                $movimientoventa->estado = "A"; //ANULADO
                $movimientoventa->save();
            }                
            //ELIMINO PRIMERO LOS DETALLES DE LA CUOTA SI EXISTIEREN
            if(count($alumnocuotas)>0) {
                foreach ($alumnocuotas as $ac) {
                    $ac->delete();
                }
            }
            //ELIMINO LA CUOTA DEL ALUMNO
            $cuota->delete();
            //ELIMINO LA MATRÍCULA
            $mensualidad->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego, $adicional)
    {
        $existe = Libreria::verificarExistencia($id, 'alumno_seccion');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = AlumnoSeccion::find($id);
        $entidad  = 'Mensualidad';
        $formData = array('route' => array('mensualidad.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar', 'adicional'));
    }

    function llenarTablaPagos(Request $request) {
        $retorno            = "";
        $seccion_id         = $request->seccion_id;
        $alumno_id          = $request->persona_id;
        $cicloacademico_id  = $request->cicloacademico_id;
        $alumno_seccion_id  = $request->alumnoseccion_id;

        $cuota = Cuota::where("alumno_seccion_id", "=", $alumno_seccion_id)
                ->where("cicloacademico_id", "=", $cicloacademico_id)
                ->first();

        $cuotas = AlumnoCuota::where("alumno_cuota.cuota_id", "=", $cuota->id)
                ->where("alumno_cuota.alumno_id", "=", $alumno_id)
                ->get();

        $montopagado = 0.00;

        if(count($cuotas) > 0) {
            $contador = 1;
            foreach ($cuotas as $cta) {
                $retorno .= '<tr id="tabPagos'.$cta->id.'">
                    <td style="padding:5px;margin:5px;font-size: 13px;" class="text-center">'.$contador.'</td>
                    <td style="padding:5px;margin:5px;font-size: 13px;" class="text-center">'.date("d-m-Y", strtotime($cta->created_at)).'</td>
                    <td style="padding:5px;margin:5px;font-size: 13px;" class="text-center">'.number_format($cta->monto,2,'.','').'</td>
                    <td style="padding:5px;margin:5px;font-size: 13px;" class="text-center">
                        <button onclick="modal(\'http://localhost/facturacioncolegios/alumnoseccion/eliminar/'.$cta->id.'/SI/MATRICULA\', \'Eliminar Matrícula\', this);" class="btn btn-xs btn-danger" type="button"><div class="glyphicon glyphicon-remove"></div> Eliminar</button>
                    </td>
                </tr>';
                $contador++;
                $montopagado += $cta->monto;
            }
        }

        $jsonArray = json_encode(
            array(
                "tabla" => $retorno,
                "montopagado" => $montopagado,
            )
        );

        return $jsonArray;
    }
}
