<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\AlumnoCurso;
use App\Usuario;
use App\Usertype;
use App\Curso;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CursoController extends Controller
{
    protected $folderview      = 'app.curso';
    protected $tituloAdmin     = 'Curso';
    protected $tituloRegistrar = 'Registrar Curso';
    protected $tituloModificar = 'Modificar Curso';
    protected $tituloMatriculados = 'Matriculados en el Curso';
    protected $tituloEliminar  = 'Eliminar Curso';
    protected $rutas           = array('create' => 'curso.create', 
            'edit'   => 'curso.edit', 
            'delete' => 'curso.eliminar',
            'search' => 'curso.buscar',
            'index'  => 'curso.index',
            'matriculados' => 'curso.matriculados',
            'activarcurso' => 'curso.activarcurso',
            'cursosdisponibles' => 'curso.cursosdisponibles',
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
        $entidad          = 'Curso';
        $descripcion      = Libreria::getParam($request->input('nombre'));
        $resultado        = Curso::listar($descripcion, $id, null, null, null);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripcion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Apertura', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Profesor', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Alumnos', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '2');

        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_matriculados  = $this->tituloMatriculados;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'titulo_matriculados', 'ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function index()
    {
        $entidad          = 'Curso';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    public function create(Request $request)
    {
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $entidad             = 'Curso';
        $curso               = null;
        $formData            = array('curso.store');
        $formData            = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('curso', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function store(Request $request)
    {
        $now        = new \DateTime();
        $user       = Auth::user();
        $id         = $user->persona_id;
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $validacion = Validator::make($request->all(),
            array(
                'descripcion'            => 'required|max:80',
                )
            );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request, $now, $id){
            $curso                     = new Curso();
            $curso->descripcion        = $request->input('descripcion');
            $curso->apertura           = $now->format('Y-m-d H:i:s');
            $curso->profesor_id        = $id;
            $curso->estado             = false;
            $curso->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'curso');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $curso    = Curso::find($id);
        $entidad  = 'Curso';
        $formData = array('curso.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('curso', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'curso');
        if ($existe !== true) {
            return $existe;
        }
        $validacion = Validator::make($request->all(),
            array(
                'descripcion'            => 'required|max:60',
                )
            );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $curso                 = Curso::find($id);
            $curso->descripcion    = $request->input('descripcion');
            $curso->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'curso');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $curso = Curso::find($id);
            $curso->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'curso');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Curso::find($id);
        $mensaje = '<p class="text-inverse">¿Está seguro de eliminar el registro "'.$modelo->descripcion.'"?</p>';
        $entidad  = 'Curso';
        $formData = array('route' => array('curso.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function matriculados($curso_id)
    {
        $curso   = Curso::find($curso_id);
        return view($this->folderview.'.matriculados')->with(compact('curso'));
    }

    public function activarcurso($curso_id, $estado)
    {
        $curso   = Curso::find($curso_id);
        $curso->estado = $estado;
        $curso->save();
        if($estado == 1) {
            echo '<button data-route="curso/activarcurso/' . $curso_id . '/0" class="btnActivarCurso btn btn-xs btn-success" type="button"><div class="glyphicon glyphicon-ok"></div> Activado</button>';
        } else {
            echo '<button data-route="curso/activarcurso/' . $curso_id . '/1" class="btnActivarCurso btn btn-xs btn-danger" type="button"><div class="glyphicon glyphicon-remove"></div> Desactivado</button>';
        }
    }
}