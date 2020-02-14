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
use App\Persona;
use App\Cuota;
use App\Seccion;
use App\Grado;
use App\Http\Requests;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AlumnoSeccionController extends Controller
{
    protected $folderview      = 'app.alumnoseccion';
    protected $tituloAdmin     = 'Matrícula';
    protected $tituloRegistrar = 'Registrar Matrícula';
    protected $tituloModificar = 'Modificar Matrícula';
    protected $tituloEliminar  = 'Eliminar Matrícula';
    protected $rutas           = array('create' => 'alumnoseccion.create', 
            'edit'   => 'alumnoseccion.edit', 
            'matriculados' => 'alumnoseccion.matriculados',
            'matricularalumno' => 'alumnoseccion.matricularalumno',
            'search' => 'alumnoseccion.buscar',
            'index'  => 'alumnoseccion.index',
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
        $entidad           = 'Matricula';
        $nivel_id          = Libreria::getParam($request->input('nivel_id'));
        $grado_id          = Libreria::getParam($request->input('grado_id'));
        //$anoescolar        = Libreria::getParam($request->input('anoescolar'));
        $anoescolar        = date("Y");
        $cicloacademico    = Cicloacademico::where(DB::raw("YEAR(created_at)"), "=", $anoescolar)
                        ->where("local_id", "=", $local_id)
                        ->first();
        $cicloacademico_id = ($cicloacademico==NULL?0:$cicloacademico->id);
        $resultado         = Seccion::listar($nivel_id, $grado_id, $local_id);
        $lista             = $resultado->get();
        $cabecera          = array();
        $cabecera[]        = array('valor' => '#', 'numero' => '1');
        $cabecera[]        = array('valor' => 'Nivel', 'numero' => '1');
        $cabecera[]        = array('valor' => 'Grado', 'numero' => '1');
        $cabecera[]        = array('valor' => 'Sección', 'numero' => '1');
        $cabecera[]        = array('valor' => 'Año escolar', 'numero' => '1');
        $cabecera[]        = array('valor' => 'Matriculados', 'numero' => '1');

        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta', 'anoescolar', 'cicloacademico_id'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad', 'anoescolar', 'cicloacademico_id'));
    }

    public function index()
    {
        $entidad          = 'Matricula';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $cboNiveles       = [''=>'--TODOS--'];
        $user             = Auth::user();
        $local_id         = $user->persona->local_id;

        $niveles          = Nivel::where("nivel.local_id", "=", $local_id)
                            ->select("nivel.id", "nivel.descripcion")
                            ->get();

        foreach ($niveles as $s) {
            $cboNiveles[$s->id] = $s->descripcion;
        }

        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboNiveles'));
    }

    public function matriculados(Request $request) {
        $user             = Auth::user();
        $local_id         = $user->persona->local_id;
        $entidad          = 'Curso';
        $listar           = 'SI';
        $title            = $this->tituloAdmin;
        $ruta             = $this->rutas;
        $anoescolar       = $request->anoescolar;
        $seccion_id       = $request->id;
        $cmatricula       = Conceptopago::find(6); //BUSCO EL CONCEPTO DE PAGO PARA LA MATRÍCULA
        $cicloacademico   = Cicloacademico::where(DB::raw("YEAR(created_at)"), "=", $anoescolar)
                    ->where("local_id", "=", $local_id)
                    ->first();
        $formData = array('route' => array('alumnoseccion.matricularalumno', $seccion_id), 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        return view($this->folderview.'.matriculados')->with(compact('entidad', 'title', 'ruta', 'anoescolar', 'seccion_id', 'cicloacademico', 'formData', 'listar', 'cmatricula'));
    }

    public function matricularalumno(Request $request) {
        $error = DB::transaction(function() use($request){
            $user              = Auth::user();
            $local_id          = $user->persona->local_id;
            $seccion_id        = $request->seccion_id;
            $listar            = $request->listar;
            $anoescolar        = $request->anoescolar;
            $cicloacademico    = Cicloacademico::where(DB::raw("YEAR(created_at)"), "=", $anoescolar)
                        ->where("local_id", "=", $local_id)
                        ->first();
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

            //EMPIEZO A MATRICULAR AL ALUMNO
            $seccion = Seccion::find($seccion_id);
            $alumno  = Persona::find($persona_id);

            $alumnoseccion  = new AlumnoSeccion();
            $alumnoseccion->alumno_id = $persona_id;
            $alumnoseccion->cicloacademico_id = $cicloacademico->id;
            $alumnoseccion->seccion_id = $seccion_id;
            $alumnoseccion->observacion = "MATRÍCULA DE ALUMNO";
            $alumnoseccion->save();

            ####SI EL ALUMNO HA COMPETADO EL PAGO TOTAL
            if((float)$cuenta == 0) {            
                //CREO LA CUOTA
                $cuota                    = new Cuota();
                $cuota->monto             = $total2;
                $cuota->estado            = "C"; //CANCELADA
                $cuota->cicloacademico_id = $cicloacademico->id;
                $cuota->observacion       = "MATRÍCULA DE ALUMNO";
                $cuota->alumno_seccion_id = $alumnoseccion->id;
                $cuota->mes               = 0;
                $cuota->save();
                //CREO EL PRIMERO Y ÚNICO DETALLE DE CUOTA
                $alumnocuota            = new AlumnoCuota();
                $alumnocuota->monto     = $total;
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
                $movimiento->totalpagado       = $total;
                $movimiento->estado            = "P"; //PAGADO
                $movimiento->local_id          = $local_id;
                $movimiento->cuota_id          = $cuota->id;
                $movimiento->cicloacademico_id = $cicloacademico->id;
                $movimiento->alumno_cuota_id   = $alumnocuota->id;
                $movimiento->save();                
                //CREO EL DOCUMENTO DE VENTA
                $movimientoventa                    = new Movimiento();
                $movimientoventa->fecha             = date("Y-m-d");
                $movimientoventa->numero            = Movimiento::numerosigue(8, ($tipodocumento=="B"?1:2), $local_id); //NÚMERO DE DOC VENTA QUE SIGUE
                $movimientoventa->persona_id        = $alumno->id;
                $movimientoventa->serie             = ($tipodocumento=="B"?$user->persona->local->serie:$user->persona->local->serie2);
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
                $movimientoventa->cicloacademico_id = $cicloacademico->id;
                $movimientoventa->alumno_cuota_id   = $alumnocuota->id;
                $movimientoventa->save();

                $movimiento->comentario             = "PAGO COMPLETO POR MATRÍCULA - ".$tipodocumento.$request->serie."-".$movimientoventa->numero;
                $movimiento->save();

            ####SI EL ALUMNO NO HA COMPLETADO EL PAGO TOTAL
            } else {
                //CREO LA CUOTA
                $cuota                    = new Cuota();
                $cuota->monto             = $total2;
                $cuota->estado            = "P"; //PENDIENTE
                $cuota->cicloacademico_id = $cicloacademico->id;
                $cuota->observacion       = "MATRÍCULA DE ALUMNO";
                $cuota->alumno_seccion_id = $alumnoseccion->id;
                $cuota->mes               = 0;
                $cuota->save();
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
                    $movimiento->cicloacademico_id = $cicloacademico->id;
                    $movimiento->alumno_cuota_id   = $alumnocuota->id;
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

        //FACTURA
        $serie             = str_pad($user->persona->local->serie2,5,'0',STR_PAD_LEFT);
        if($tipodocumento_id == "1") {
            //BOLETA
            $serie         = str_pad($user->persona->local->serie,5,'0',STR_PAD_LEFT);
        }

        $arrayjson = array(
            'numero' => Movimiento::numeroSigue($tipomovimiento_id, $tipodocumento_id, $local_id), 
            'serie' => $serie, 
        );

        return json_encode($arrayjson);
    }

    function comprobarSiAlumnoEstaMatriculado(Request $request) {
        $user              = Auth::user();
        $local_id          = $user->persona->local_id;
        $alumno_id         = $request->alumno_id;
        $anoescolar        = $request->anoescolar;
        $cicloacademico    = Cicloacademico::where(DB::raw("YEAR(created_at)"), "=", $anoescolar)
                            ->where("local_id", "=", $local_id)
                            ->first();
        $matricula         = AlumnoSeccion::where("alumno_id", "=", $alumno_id)
                                        ->where("cicloacademico_id", "=", $cicloacademico->id)->first();
        return ($matricula==NULL?"N":"S");
    }

    public function llenarTablaMatriculados(Request $request) {
        $user              = Auth::user();
        $local_id          = $user->persona->local_id;
        $retorno           = "";
        $seccion_id        = $request->seccion_id;
        $anoescolar        = $request->anoescolar;
        $cicloacademico    = Cicloacademico::where(DB::raw("YEAR(created_at)"), "=", $anoescolar)
                            ->where("local_id", "=", $local_id)
                            ->first();

        $matriculados = AlumnoSeccion::where("seccion_id", "=", $seccion_id)
                ->where("cicloacademico_id", "=", $cicloacademico->id)
                ->get();

        if(count($matriculados) > 0) {
            $contador = 1;
            foreach ($matriculados as $matri) {
                $retorno .= '<tr id="tabAlumnos'.$matri->id.'">
                    <td style="padding:5px;margin:5px;" class="text-center">'.$contador.'</td>
                    <td style="padding:5px;margin:5px;" class="text-center">'.$matri->alumno->dni.'</td>
                    <td style="padding:5px;margin:5px;" class="text-center">'.$matri->alumno->apellidopaterno.' '.$matri->alumno->apellidomaterno.' '.$matri->alumno->nombres.'</td>
                    <td style="padding:5px;margin:5px;" class="text-center">
                        <button onclick="modal(\'http://localhost/facturacioncolegios/alumnoseccion/eliminar/'.$matri->id.'/SI/MATRICULA\', \'Eliminar Matrícula\', this);" class="btn btn-xs btn-danger" type="button"><div class="glyphicon glyphicon-remove"></div> Eliminar</button>
                    </td>
                </tr>';
                $contador++;
            }
        }

        return $retorno;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'alumno_seccion');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            //BUSCO LA MATRÍCULA
            $alumnoseccion = AlumnoSeccion::find($id);
            //BUSCO LA CUOTA
            $cuota         = Cuota::where("alumno_seccion_id", "=", $alumnoseccion->id)->first();
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
            $alumnoseccion->delete();
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
        $entidad  = 'Matricula';
        $formData = array('route' => array('alumnoseccion.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar', 'adicional'));
    }
}
