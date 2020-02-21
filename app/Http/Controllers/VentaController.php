<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Movimiento;
use App\Cicloacademico;
use App\Alumnocuota;
use App\Cuota;
use App\Http\Requests;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    protected $folderview      = 'app.venta';
    protected $tituloAdmin     = 'Documento de Venta';
    protected $tituloEliminar  = 'Eliminar Documento de Venta';
    protected $rutas           = array('create' => 'venta.create', 
            'cierre' => 'venta.cierre', 
            'delete' => 'venta.eliminar',
            'search' => 'venta.buscar',
            'index'  => 'venta.index',
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
        $numero            = $request->input('numero');
        $serie             = $request->input('serie');
        $tipodocumento_id  = $request->input('tipodocumento_id');
        $estado            = $request->input('estado');
        $fecha             = date("Y-m-d", strtotime($request->input('fecha')));
        $fecha2            = date("Y-m-d", strtotime($request->input('fecha2')));
        $entidad           = 'Anoescolar';
        $resultado         = Movimiento::listardocumentoventa($fecha, $fecha2, $numero, $serie, 8, $tipodocumento_id, $estado, $local_id);
        $lista             = $resultado->get();
        $cabecera          = array();
        $cabecera[] = array('valor' => '#', 'numero' => '1');
        $cabecera[] = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[] = array('valor' => 'Documento', 'numero' => '1');
        $cabecera[] = array('valor' => 'Nro', 'numero' => '1');
        $cabecera[] = array('valor' => 'Estudiante', 'numero' => '1');
        $cabecera[] = array('valor' => 'Total', 'numero' => '1');
        $cabecera[] = array('valor' => 'Situación', 'numero' => '1');
        $cabecera[] = array('valor' => 'Estado Sunat', 'numero' => '1');
        $cabecera[] = array('valor' => 'Msg. Sunat', 'numero' => '1');
        $cabecera[] = array('valor' => 'Usuario', 'numero' => '1');
        $cabecera[] = array('valor' => 'Observación', 'numero' => '1');
        $cabecera[] = array('valor' => 'Operaciones', 'numero' => '3');

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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_eliminar', 'ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function index()
    {
        $entidad          = 'Anoescolar';
        $title            = $this->tituloAdmin;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'ruta'));
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'movimiento');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            //ANULO BOLETA DE VENTA
            $venta = Movimiento::find($id);
            $venta->estado = "A";
            $venta->save();
            //ANULO INGRESO A CAJA
            $movimientocaja = Movimiento::find($venta->movimiento_id);
            $movimientocaja->estado = "A";
            $movimientocaja->save();
            //ELIMINO ALUMNO CUOTA
            $alumnocuota = Alumnocuota::find($venta->alumno_cuota_id);
            //PERO ANTES CAMBIO ESTADO A CUOTA PENDIENTE
            $cuota = Cuota::find($alumnocuota->cuota_id);
            $cuota->estado = "P";
            $cuota->save();
            //FINALMENTE ELIMINO EL ALUMNO CUOTA
            $alumnocuota->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'movimiento');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "SI";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Movimiento::find($id);
        $entidad  = 'Anoescolar';
        $formData = array('route' => array('venta.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }
}
