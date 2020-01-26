<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Tipomovimiento;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TipomovimientoController extends Controller
{
    protected $folderview      = 'app.tipomovimiento';
    protected $tituloAdmin     = 'Tipo de Movimiento';
    protected $tituloRegistrar = 'Registrar Tipo de Movimiento';
    protected $tituloModificar = 'Modificar Tipo de Movimiento';
    protected $tituloEliminar  = 'Eliminar Tipo de Movimiento';
    protected $rutas           = array('create' => 'tipomovimiento.create', 
            'edit'   => 'tipomovimiento.edit', 
            'delete' => 'tipomovimiento.eliminar',
            'search' => 'tipomovimiento.buscar',
            'index'  => 'tipomovimiento.index',
        );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function buscar(Request $request)
    {
        $user             = Auth::user();
        $id               = $user->persona_id;
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Tipomovimiento';
        $nombre           = Libreria::getParam($request->input('nombre'));
        $resultado        = Tipomovimiento::listar($nombre);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '2');

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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function index()
    {
        $entidad          = 'Tipomovimiento';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    public function create(Request $request)
    {
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $entidad             = 'Tipomovimiento';
        $tipomovimiento       = null;
        $formData            = array('tipomovimiento.store');
        $formData            = array('route' => $formData, 'files' => true, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('tipomovimiento', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function store(Request $request)
    {
        $now        = new \DateTime();
        $user       = Auth::user();
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $validacion = Validator::make($request->all(),
            array(
                'nombre'      => 'required|max:100',
            )
        );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request, $now){
            $tipomovimiento              = new Tipomovimiento();
            $tipomovimiento->nombre      = $request->input('nombre');
            $tipomovimiento->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function show(Tipomovimiento $tipomovimiento)
    {
        //
    }

    public function edit(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'tipomovimiento');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $tipomovimiento    = Tipomovimiento::find($id);
        $entidad  = 'Tipomovimiento';
        $formData = array('tipomovimiento.update', $id);
        $formData = array('route' => $formData, 'files' => true, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('tipomovimiento', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'tipomovimiento');
        if ($existe !== true) {
            return $existe;
        }
        $validacion = Validator::make($request->all(),
            array(
                'nombre'      => 'required|max:100',
            )
        );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request, $id){
            $tipomovimiento                = Tipomovimiento::find($id);
            $tipomovimiento->nombre      = $request->input('nombre');
            $tipomovimiento->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'tipomovimiento');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $tipomovimiento = Tipomovimiento::find($id);
            $tipomovimiento->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'tipomovimiento');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Tipomovimiento::find($id);
        $entidad  = 'Tipomovimiento';
        $formData = array('route' => array('tipomovimiento.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }
}
