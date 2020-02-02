<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Local;
use App\Nivel;
use App\Grado;
use App\Http\Requests;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NivelController extends Controller
{
    protected $folderview      = 'app.nivel';
    protected $tituloAdmin     = 'Nivel';
    protected $tituloRegistrar = 'Registrar Nivel';
    protected $tituloModificar = 'Modificar Nivel';
    protected $tituloEliminar  = 'Eliminar Nivel';
    protected $rutas           = array('create' => 'nivel.create', 
            'edit'   => 'nivel.edit', 
            'delete' => 'nivel.eliminar',
            'search' => 'nivel.buscar',
            'index'  => 'nivel.index',
        );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function buscar(Request $request)
    {
        $user             = Auth::user();
        $local_id         = $user->persona->local_id;
        $id               = $user->persona_id;
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Nivel';
        $descripcion      = Libreria::getParam($request->input('descripcion'));
        $resultado        = Nivel::listar($descripcion, $local_id);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Local', 'numero' => '1');
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
        $entidad          = 'Nivel';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    public function create(Request $request)
    {
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $entidad             = 'Nivel';
        $nivel               = null;
        $formData            = array('nivel.store');
        $formData            = array('route' => $formData, 'files' => true, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('nivel', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function store(Request $request)
    {
        $validacion = Validator::make($request->all(),
            array(
                'descripcion' => 'required|max:100',
                'local_id'    => 'required|numeric',
            )
        );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $local_id             = $user->persona->local_id;
            $nivel                = new Nivel();
            $nivel->descripcion   = $request->input('descripcion');
            $nivel->local_id      = $local_id;
            $nivel->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function show(Nivel $nivel)
    {
        //
    }

    public function edit(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'nivel');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $nivel    = Nivel::find($id);
        $entidad  = 'Nivel';
        $formData = array('nivel.update', $id);
        $formData = array('route' => $formData, 'files' => true, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('nivel', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'nivel');
        if ($existe !== true) {
            return $existe;
        }
        $validacion = Validator::make($request->all(),
            array(
                'descripcion' => 'required|max:100',
                'local_id'    => 'required|numeric',
            )
        );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request, $id){
            $user                 = Auth::user();
            $local_id             = $user->persona->local_id;
            $nivel                = Nivel::find($id);
            $nivel->descripcion   = $request->input('descripcion');
            $nivel->local_id      = $local_id;
            $nivel->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'nivel');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $nivel = Nivel::find($id);
            $nivel->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'nivel');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Nivel::find($id);
        $entidad  = 'Nivel';
        $formData = array('route' => array('nivel.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }
}
