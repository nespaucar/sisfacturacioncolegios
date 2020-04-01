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
use App\Montoconceptopago;
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
            'checktodo' => 'mensualidad.checktodo',
            'confirmarchecktodo' => 'mensualidad.confirmarchecktodo'
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
        $nivel_id          = Libreria::getParam($request->input('nivel_id'));
        $grado_id          = Libreria::getParam($request->input('grado_id'));
        $seccion_id        = Libreria::getParam($request->input('seccion_id'));
        //$anoescolar        = Libreria::getParam($request->input('anoescolar'));
        $anoescolar        = date("Y");
        $cicloacademico    = Cicloacademico::where(DB::raw("YEAR(created_at)"), "=", $anoescolar)
                            ->where("local_id", "=", $local_id)
                            ->first();
        $cicloacademico_id = ($cicloacademico==NULL?0:$cicloacademico->id);
        $resultado         = AlumnoSeccion::listar($nivel_id, $grado_id, $seccion_id, $cicloacademico_id, $local_id);
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
        $cabecera[]        = array('valor' => 'Habilitar todos los pagos', 'numero' => '1');

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
        $cboNiveles       = [''=>'--TODOS--'];
        $user             = Auth::user();
        $local_id         = $user->persona->local_id;

        $niveles          = Nivel::where("nivel.local_id", "=", $local_id)
                            ->select("nivel.id", "nivel.descripcion")
                            ->get();

        foreach ($niveles as $s) {
            $cboNiveles[$s->id] = $s->descripcion;
        }

        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'ruta', 'cboNiveles'));
    }

    public function conceptopago(Request $request) {
        $user     = Auth::user();
        $local_id = $user->persona->local_id;
        $entidad            = 'Mensualidad';
        $alumno_seccion_id  = $request->id;
        $mes                = $request->mes;
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
                        $monto_mensualidad = Montoconceptopago::where("conceptopago_id", "=", $cpago->id)
                            ->where("local_id", "=", $local_id)
                            ->first()->monto; //BUSCO EL CONCEPTO DE PAGO PARA LA MENSUALIDAD
                    }
                }
            }
        }
        if($request->listar==1) {
            $cpago             = Conceptopago::find(6);
            if($configuracionpago1!==NULL) {
                $monto_mensualidad = $configuracionpago1->montom."";
            } else {
                if($configuracionpago2!==NULL) {
                    $monto_mensualidad = $configuracionpago2->montom."";
                } else {
                    if($configuracionpago3!==NULL) {
                        $monto_mensualidad = $configuracionpago3->montom."";
                    } else {
                        if($configuracionpago4!==NULL) {
                            $monto_mensualidad = $configuracionpago4->montom."";
                        } else {
                            $monto_mensualidad = Montoconceptopago::where("conceptopago_id", "=", $cpago->id)
                                ->where("local_id", "=", $local_id)
                                ->first()->monto; //BUSCO EL CONCEPTO DE PAGO PARA LA MENSUALIDAD
                        }
                    }
                }
            }
        }
        $monto_mensualidad  = number_format($monto_mensualidad, 2, '.', '');
        $listar             = 'NO';
        $title              = $this->tituloAdmin;
        $ruta               = $this->rutas;
        $formData           = array('route' => array('mensualidad.realizarPago', $alumno_seccion_id), 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        return view($this->folderview.'.conceptopago')->with(compact('entidad', 'title', 'ruta', 'formData', 'listar', 'alumnoseccion', 'monto_mensualidad', 'cpago', 'mes'));
    }

    public function realizarPago(Request $request) {
        $error = DB::transaction(function() use($request){
            $user              = Auth::user();
            $local_id          = $user->persona->local_id;
            $seccion_id        = $request->seccion_id;
            $conceptopago_id   = $request->conceptopago_id;
            $alumno_seccion_id = $request->alumnoseccion_id;
            $cicloacademico_id = $request->cicloacademico_id;
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
            $mes               = $request->mes;
            $venta             = $request->venta;
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
                    ->where("mes", "=", $mes)
                    ->first();

            //SACO EL CONCEPTO DE PAGO PARA EL DETALLE
            if($conceptopago_id == 6) {
                $cp = "MATRÍCULA";
            } else if($conceptopago_id == 7) {
                $cp = "MENSUALIDAD";
            }

            if($cuota == NULL) {
                $cuota                    = new Cuota();
                $cuota->monto             = $total;
                $cuota->cicloacademico_id = $cicloacademico->id;
                $cuota->observacion       = $cp." DE ALUMNO";
                $cuota->estado            = "P";
                $cuota->alumno_seccion_id = $mensualidad->id;
                $cuota->mes               = $mes;
                $cuota->save();
            }

            ####SI EL ALUMNO HA COMPETADO EL PAGO TOTAL
            if((float)$cuenta == 0) {
                //CAMBIO ESTADO DE CUOTA
                $cuota->estado = "C"; //COMPLETA
                $cuota->save();           
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
                $movimiento->conceptopago_id   = $conceptopago_id; //PAGO POR EL CONCEPTO O MATRICULA O MENSUALIDAD
                $movimiento->totalvisa         = $visa;
                $movimiento->totalmaster       = $master;
                $movimiento->total             = $total2;
                $movimiento->igv               = 0;
                $movimiento->tipodocumento_id  = NULL;
                $movimiento->totalpagado       = $total2;
                $movimiento->estado            = "P"; //PAGADO
                $movimiento->local_id          = $local_id;
                $movimiento->cuota_id          = $cuota->id;
                $movimiento->cicloacademico_id = $cicloacademico->id;
                $movimiento->alumno_cuota_id   = $alumnocuota->id;
                $movimiento->save();                
                //CREO EL DOCUMENTO DE VENTA
                //SOLO SI EL TOTAL ES MAYOR A CERO

                if($total > 0) {
                    //SOLO SI SE ACEPTA QUE SE GENERE DOCUMENTO DE VENTA
                    if($venta == "S") {
                        $movimientoventa                    = new Movimiento();
                        $movimientoventa->fecha             = date("Y-m-d");
                        $movimientoventa->numero            = Movimiento::numerosigue(8, ($tipodocumento=="B"?1:2), $local_id); //NÚMERO DE DOC VENTA QUE SIGUE
                        $movimientoventa->persona_id        = $alumno->id;
                        $movimientoventa->serie             = ($tipodocumento=="B"?$user->persona->local->serie:$user->persona->local->serie2);
                        $movimientoventa->responsable_id    = $user->persona->id;
                        $movimientoventa->tipomovimiento_id = 8; //VENTA
                        $movimientoventa->conceptopago_id   = $conceptopago_id; //PAGO POR CONCEPTO O MATRICULA O MENSUALIDAD
                        $movimientoventa->total             = $total;
                        $movimientoventa->igv               = 0;
                        $movimientoventa->tipodocumento_id  = ($tipodocumento=="B"?1:2);
                        $movimientoventa->comentario        = "PAGO COMPLETO POR ".$cp;
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

                        $movimiento->comentario             = "PAGO COMPLETO POR " . $cp . " - ".$tipodocumento.$request->serie."-".$movimientoventa->numero;
                        $movimiento->save();
                    } else {
                        $movimiento->comentario             = "PAGO COMPLETO POR " . $cp . " - SIN GENERACIÓN DE DOCUMENTO DE VENTA";
                        $movimiento->save();
                    }
                } else {
                    $movimiento->comentario             = "PAGO COMPLETO POR " . $cp . " - SIN GENERACIÓN DE DOCUMENTO DE VENTA, MONTO 0.00 SOLES";
                    $movimiento->save();
                }
            ####SI EL ALUMNO NO HA COMPLETADO EL PAGO TOTAL
            } else {
                //CREO EL PRIMER DETALLE DE CUOTA, NO IMPORTA SI ES CERO, AMARRAMOS AMARRAMOS AL ALUMNO A LA CUOTA
                $cuota->estado = "P"; //PENDIENTE
                $cuota->save();
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
                    $movimiento->conceptopago_id   = $conceptopago_id; //PAGO POR CONCEPTO O MATRICULA O MENSUALIDAD
                    $movimiento->totalvisa         = $visa;
                    $movimiento->totalmaster       = $master;
                    $movimiento->total             = $total2;
                    $movimiento->igv               = 0;
                    $movimiento->tipodocumento_id  = NULL;
                    $movimiento->comentario        = "PAGO PARCIAL POR ".$cp." DE ALUMNO";
                    $movimiento->totalpagado       = $total2;
                    $movimiento->estado            = "P"; //PAGADO
                    $movimiento->cuota_id          = $cuota->id;
                    $movimiento->local_id          = $local_id;
                    $movimiento->alumno_cuota_id   = $alumnocuota->id;
                    $movimiento->cicloacademico_id = $cicloacademico->id;
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
        $existe = Libreria::verificarExistencia($id, 'alumno_cuota');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            //BUSCO LA MATRÍCULA
            $alumno_cuota    = AlumnoCuota::find($id);
            $ingreso         = Movimiento::where("alumno_cuota_id", "=", $id)->first();
            $ingreso->estado = "A"; //ANULAMOS INGRESO A CAJA
            $ingreso->save();
            $alumno_cuota->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego, $adicional)
    {
        $existe = Libreria::verificarExistencia($id, 'alumno_cuota');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = AlumnoSeccion::find($id);
        $entidad  = 'Mensualidad2';
        $formData = array('route' => array('mensualidad.destroy', $id), 'method' => 'GET', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar', 'adicional'));
    }

    function llenarTablaPagos(Request $request) {
        $tabla              = "";
        $documentoventa     = "<font style='color:red;'>DOCUMENTO DE VENTA NO GENERADO</font>";
        $seccion_id         = $request->seccion_id;
        $alumno_id          = $request->persona_id;
        $cicloacademico_id  = $request->cicloacademico_id;
        $conceptopago_id    = $request->conceptopago_id;
        $alumno_seccion_id  = $request->alumnoseccion_id;
        $mes                = $request->mes;
        $user               = Auth::user();
        $local_id           = $user->persona->local_id;

        $cuota = Cuota::where("alumno_seccion_id", "=", $alumno_seccion_id)
                ->where("cicloacademico_id", "=", $cicloacademico_id)
                ->where("mes", "=", $mes)
                ->first();

        $montopagado = 0.00;

        if($cuota!==NULL) {
            $cuotas = AlumnoCuota::where("alumno_cuota.cuota_id", "=", $cuota->id)
                ->where("alumno_cuota.alumno_id", "=", $alumno_id)
                ->get();

            if(count($cuotas) > 0) {
                $contador = 1;
                foreach ($cuotas as $cta) {
                    if($cuota->estado == "C") {
                        $tabla .= '<tr id="tabPagos'.$cta->id.'">
                            <td style="padding:5px;margin:5px;font-size: 13px;" class="text-center">'.$contador.'</td>
                            <td style="padding:5px;margin:5px;font-size: 13px;" class="text-center">'.date("d-m-Y", strtotime($cta->created_at)).'</td>
                            <td style="padding:5px;margin:5px;font-size: 13px;" class="text-center">'.number_format($cta->monto,2,'.','').'</td>
                            <td style="padding:5px;margin:5px;font-size: 13px;" class="text-center">-</td>
                        </tr>';
                    } else if($cuota->estado == "P") {
                        $tabla .= '<tr id="tabPagos'.$cta->id.'">
                            <td style="padding:5px;margin:5px;font-size: 13px;" class="text-center">'.$contador.'</td>
                            <td style="padding:5px;margin:5px;font-size: 13px;" class="text-center">'.date("d-m-Y", strtotime($cta->created_at)).'</td>
                            <td style="padding:5px;margin:5px;font-size: 13px;" class="text-center">'.number_format($cta->monto,2,'.','').'</td>
                            <td style="padding:5px;margin:5px;font-size: 13px;" class="text-center">
                                <button onclick="modal(\'mensualidad/eliminar/'.$cta->id.'/SI/TABLACUOTAS\', \'Eliminar Detalle de Cuota\', this);" class="btn btn-xs btn-danger" type="button"><div class="glyphicon glyphicon-remove"></div> Eliminar</button>
                            </td>
                        </tr>';
                    }                        
                    $contador++;
                    $montopagado += $cta->monto;
                }
            }

            //BUSCO EL DOCUMENTO DE VENTA
            $movimientoventa = Movimiento::where("persona_id", "=", $cuota->alumnoseccion->alumno_id)
                        ->where("tipomovimiento_id", "=", 8) //VENTA
                        ->where("conceptopago_id", "=", $conceptopago_id) //PAGO POR MATRÍCULA O MENSUALIDAD
                        ->where("estado", "=", "P") //PAGADO
                        ->where("cuota_id", "=", $cuota->id)
                        ->where("local_id", "=", $local_id)
                        ->where("cicloacademico_id", "=", $cicloacademico_id)
                        ->first();

            if($movimientoventa!==NULL) {
                $documentoventa = '<font style="color:green;">DOCUMENTO DE VENTA GENERADO</font>
                    <br>
                    <br>
                    <table id="datatable" class="table table-xs table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="padding:5px;margin:5px; font-size: 13px;" class="text-center" width="22%"><u>Fecha</u></th>
                                <th style="padding:5px;margin:5px; font-size: 13px;" class="text-center" width="22%"><u>Número</u></th>
                                <th style="padding:5px;margin:5px; font-size: 13px;" class="text-center" width="22%"><u>Total</u></th>
                                <th style="padding:5px;margin:5px; font-size: 13px;" class="text-center" width="22%"><u>IGV</u></th>
                                <th style="padding:5px;margin:5px; font-size: 13px;" class="text-center" width="12%"><u>Imp.</u></th>
                            </tr>
                        </thead>
                        <tbody id="tablaPagos"
                            <tr>
                                <th style="padding:5px;margin:5px; font-size: 13px;" class="text-center">'.date("d-m-Y", strtotime($movimientoventa->fecha)).'</th>
                                <th style="padding:5px;margin:5px; font-size: 13px;" class="text-center">'.($movimientoventa->tipodocumento_id==1?"B":"F").$movimientoventa->serie."-".$movimientoventa->numero.'</th>
                                <th style="padding:5px;margin:5px; font-size: 13px;" class="text-center">'.$movimientoventa->total.'</th>
                                <th style="padding:5px;margin:5px; font-size: 13px;" class="text-center">'.$movimientoventa->igv.'</th>
                                <th style="padding:0px;margin:0px;" class="text-center">
                                    <button onclick="#" class="btn btn-xs btn-info" type="button">
                                        <div class="fa fa-print"></div>
                                    </button>
                                </th>
                            </tr>
                        </tbody>
                </table>';
            }
        }

        $jsonArray = json_encode(
            array(
                "tabla" => $tabla,
                "montopagado" => $montopagado,
                "documentoventa" => $documentoventa,
            )
        );

        return $jsonArray;
    }

    function envioBoletas1(Request $request) {
        $esdiafinal = $this->_data_last_month_day();
        $error = DB::transaction(function() use($request, $esdiafinal){
            //SOLO CREO LAS BOLETAS Y SI ES FIN DE MES
            $user              = Auth::user();
            $local_id          = $user->persona->local_id;
            $anoescolar        = date("Y");
            $mensactual        = date("m");
            $cicloacademico    = Cicloacademico::where(DB::raw("YEAR(created_at)"), "=", $anoescolar)
                                ->where("local_id", "=", $local_id)
                                ->first();
            if($esdiafinal) {
                $matriculas_de_ciclo = AlumnoSeccion::where('cicloacademico_id', '=', $cicloacademico->id)
                ->join("cicloacademico", "cicloacademico.id", "=", "alumno_seccion.cicloacademico_id")
                ->where("cicloacademico.local_id", "=", $local_id)
                ->orderBy('alumno_seccion.id', 'DESC')
                ->select("alumno_seccion.*")
                ->get();

                if(count($matriculas_de_ciclo) > 0) {
                    foreach ($matriculas_de_ciclo as $mdc) {

                        $cuotaant = Cuota::where("alumno_seccion_id", "=", $mdc->id)
                            ->where("cicloacademico_id", "=", $cicloacademico->id)
                            ->where("mes", "=", $mensactual)
                            ->first();

                        $crearcuota = false;
                        $crearboleta = false;

                        if($cuotaant==NULL) { // SI NO EXISTE LA CUOTA LA CREAMOS
                            $crearcuota = true;
                        } else {
                            if($cuotaant->estado == "P") { //SI AUN ESTÁ PENDIENTE CREO LA BOLETA
                                $crearboleta = true;
                            }
                        }

                        if($crearboleta || $crearcuota) { //EXISTE LA CUOTA CON ESTADO PENDIENTE POR ESO CREAMOS LA BOLETA
                            //BUSCO LA CONFIGURACIÓN DE PAGO DE MENSUALIDAD QUE TIENE ESTE ALUMNO
                            $monto_mensualidad  = 0.00;
                            $configuracionpago1 = Configuracionpago::where("alumno_id", "=", $mdc->alumno_id)->first();
                            if($configuracionpago1!==NULL) {
                                $monto_mensualidad = $configuracionpago1->monto;
                            } else {
                                $configuracionpago2 = Configuracionpago::where("seccion_id", "=", $mdc->seccion_id)->first();
                                if($configuracionpago2!==NULL) {
                                    $monto_mensualidad = $configuracionpago2->monto;
                                } else {
                                    $configuracionpago3 = Configuracionpago::where("grado_id", "=", $mdc->seccion->grado_id)->first();
                                    if($configuracionpago3!==NULL) {
                                        $monto_mensualidad = $configuracionpago3->monto;
                                    } else {
                                        $configuracionpago4 = Configuracionpago::where("nivel_id", "=", $mdc->seccion->grado->nivel_id)->first();
                                        if($configuracionpago4!==NULL) {
                                            $monto_mensualidad = $configuracionpago4->monto;
                                        } else {
                                            $cpago = Conceptopago::find(7); //MENSUALIDAD
                                            $monto_mensualidad = Montoconceptopago::where("conceptopago_id", "=", $cpago->id)
                                                ->where("local_id", "=", $local_id)
                                                ->first()->monto; //BUSCO EL CONCEPTO DE PAGO PARA LA MENSUALIDAD
                                        }
                                    }
                                }
                            }

                            #######################
                            $seccion_id        = $mdc->seccion_id;
                            $persona_id        = $mdc->alumno_id;
                            $efectivo          = 0.00;
                            $visa              = 0.00;
                            $master            = 0.00;
                            $total             = $monto_mensualidad;
                            $cuenta            = 0.00;
                            $tipodocumento     = 1; //SOLO BOLETAS
                            $ruc               = NULL;
                            $razon             = NULL;
                            $direccion         = NULL;

                            ####SI EL ALUMNO NO HA COMPLETADO EL PAGO TOTAL
                            //CREO LA CUOTA
                            if($crearcuota) {
                                $cuota = new Cuota();
                                $cuota->monto             = $total;
                                $cuota->estado            = "P"; //PENDIENTE
                                $cuota->cicloacademico_id = $cicloacademico->id;
                                $cuota->observacion       = "MENSUALIDAD DE ALUMNO";
                                $cuota->alumno_seccion_id = $mdc->id;
                                $cuota->mes               = $mensactual;
                                $cuota->save();
                            } else {
                                $cuota = $cuotaant;
                            }
                            
                            //CREO EL DETALLE DE CUOTA
                            $alumnocuota            = new AlumnoCuota();
                            $alumnocuota->monto     = 0.00;
                            $alumnocuota->alumno_id = $persona_id;
                            $alumnocuota->cuota_id  = $cuota->id;
                            $alumnocuota->save();
                            //CREO EL MOVIMIENTO DE INGRESO A CAJA
                            $movimiento                    = new Movimiento();
                            $movimiento->fecha             = date("Y-m-d");
                            $movimiento->numero            = Movimiento::numerosigue(1, null, $local_id); //NÚMERO DE MOVIMIENTO EN CAJA
                            $movimiento->persona_id        = $persona_id;
                            $movimiento->responsable_id    = $user->persona->id;
                            $movimiento->tipomovimiento_id = 1; //CAJA
                            $movimiento->totalefectivo     = $efectivo;
                            $movimiento->conceptopago_id   = 7; //PAGO POR MENSUALIDAD
                            $movimiento->totalvisa         = $visa;
                            $movimiento->totalmaster       = $master;
                            $movimiento->total             = 0.00;
                            $movimiento->igv               = 0;
                            $movimiento->tipodocumento_id  = NULL;
                            $movimiento->totalpagado       = 0.00;
                            $movimiento->estado            = "P"; //PAGADO
                            $movimiento->local_id          = $local_id;
                            $movimiento->cuota_id          = $cuota->id;
                            $movimiento->cicloacademico_id = $cicloacademico->id;
                            $movimiento->alumno_cuota_id   = $alumnocuota->id;
                            $movimiento->save();                
                            //CREO EL DOCUMENTO DE VENTA
                            if($monto_mensualidad > 0) {
                                $movimientoventa                    = new Movimiento();
                                $movimientoventa->fecha             = date("Y-m-d");
                                $movimientoventa->numero            = Movimiento::numerosigue(8, 1, $local_id); //NÚMERO DE BOLETA QUE SIGUE
                                $movimientoventa->persona_id        = $persona_id;
                                $movimientoventa->serie             = $user->persona->local->serie;
                                $movimientoventa->responsable_id    = $user->persona->id;
                                $movimientoventa->tipomovimiento_id = 8; //VENTA
                                $movimientoventa->conceptopago_id   = 7; //PAGO POR MENSUALIDAD
                                $movimientoventa->total             = $total;
                                $movimientoventa->igv               = 0;
                                $movimientoventa->tipodocumento_id  = 1; //BOLETO
                                $movimientoventa->comentario        = "GENERACIÓN AUTOMÁTICA DE BOLETA DE VENTA SIN PAGO";
                                $movimientoventa->totalpagado       = 0.00;
                                $movimientoventa->estado            = "D"; //DEUDA
                                $movimientoventa->ruc               = $ruc;
                                $movimientoventa->razon             = $razon;
                                $movimientoventa->direccion         = $direccion;
                                $movimientoventa->cuota_id          = $cuota->id;
                                $movimientoventa->movimiento_id     = $movimiento->id;
                                $movimientoventa->local_id          = $local_id;
                                $movimientoventa->cicloacademico_id = $cicloacademico->id;
                                $movimientoventa->alumno_cuota_id   = $alumnocuota->id;
                                $movimientoventa->save();

                                $movimiento->comentario             = "GENERACIÓN AUTOMÁTICA DE BOLETA DE VENTA SIN PAGO - ".$tipodocumento.$request->serie."-".$movimientoventa->numero;
                                $movimiento->save();
                            } else {
                                $movimiento->comentario             = "GENERACIÓN AUTOMÁTICA DE PAGO, SIN GENERACIÓN DE DOCUMENTO DE VENTA POR MONTO 0.00 SOLES";
                                $movimiento->save();
                            }
                        }                        
                        #######################
                    }
                }
            }                
        });
        return is_null($error) ? "OK" : $error;
    }

    function _data_last_month_day() { 
        $esdiafinal = false;
        $month = date('m');
        $year = date('Y');
        $todayday = date('d');
        $finalday = date("d", mktime(0,0,0, $month+1, 0, $year));

        if($todayday == $finalday) {
            $esdiafinal = true;
        } 
        return $esdiafinal;
    }

    public function resumen(Request $request) {
        $user     = Auth::user();
        $local_id = $user->persona->local_id;
        $entidad            = 'Mensualidad';
        $alumno_seccion_id  = $request->id;
        $mes                = $request->mes;
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
                        $monto_mensualidad = Montoconceptopago::where("conceptopago_id", "=", $cpago->id)
                            ->where("local_id", "=", $local_id)
                            ->first()->monto; //BUSCO EL CONCEPTO DE PAGO PARA LA MENSUALIDAD
                    }
                }
            }
        }
        if($request->listar==1) {
            $cpago             = Conceptopago::find(6);
            if($configuracionpago1!==NULL) {
                $monto_mensualidad = $configuracionpago1->montom."";
            } else {
                if($configuracionpago2!==NULL) {
                    $monto_mensualidad = $configuracionpago2->montom."";
                } else {
                    if($configuracionpago3!==NULL) {
                        $monto_mensualidad = $configuracionpago3->montom."";
                    } else {
                        if($configuracionpago4!==NULL) {
                            $monto_mensualidad = $configuracionpago4->montom."";
                        } else {
                            $monto_mensualidad = Montoconceptopago::where("conceptopago_id", "=", $cpago->id)
                                ->where("local_id", "=", $local_id)
                                ->first()->monto; //BUSCO EL CONCEPTO DE PAGO PARA LA MENSUALIDAD
                        }
                    }
                }
            }
        }
        $listar             = 'NO';
        $title              = $this->tituloAdmin;
        $ruta               = $this->rutas;
        $formData           = array('route' => array('mensualidad.realizarPago', $alumno_seccion_id), 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        return view($this->folderview.'.resumen')->with(compact('entidad', 'title', 'ruta', 'formData', 'listar', 'alumnoseccion', 'monto_mensualidad', 'cpago', 'mes'));
    }

    public function checktodo(Request $request) {
        $id = $request->id;
        $existe = Libreria::verificarExistencia($id, 'alumno_seccion');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "SI";
        $modelo   = AlumnoSeccion::find($id);
        $entidad  = 'Mensualidad';
        $formData = array('route' => array('mensualidad.confirmarchecktodo', "id=".$id), 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarAlterarestado')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function confirmarchecktodo(Request $request) {
        $id = $request->id;
        $existe = Libreria::verificarExistencia($id, 'alumno_seccion');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            //BUSCO LA MATRÍCULA
            $mensualidad = AlumnoSeccion::find($id);
            $mensualidad->checktodo = 1;
            $mensualidad->save();
        });
        return is_null($error) ? "OK" : $error;
    }
}
