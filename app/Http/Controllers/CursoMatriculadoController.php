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

class CursoMatriculadoController extends Controller
{
    protected $folderview            = 'app.cursomatriculado';
    protected $tituloAdmin           = 'Cursos Matriculados';
    protected $tituloDesmatricularme = 'Desmatricularme en este Curso';
    protected $rutas                 = array(
            'search' => 'cursomatriculado.buscar',
            'index'  => 'cursomatriculado.index',
            'confirmarDesmatricularme'  => 'cursomatriculado.confirmarDesmatricularme',
        );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function buscar(Request $request)
    {
        $user        = Auth::user();
        $alumno_id   = $user->persona_id;
        $pagina      = $request->input('page');
        $filas       = $request->input('filas');
        $entidad     = 'Curso';
        $descripcion = Libreria::getParam($request->input('nombre'));

        $resultado = AlumnoCurso::select('alumno_curso.id', 'descripcion', 'profesor_id', 'fecha_matricula', 'curso_id')
        ->join('curso', 'alumno_curso.curso_id', '=', 'curso.id')
        ->where('alumno_id', $alumno_id)
        ->where('estado', 1)
        ->where('descripcion', 'LIKE', '%'.$descripcion.'%')
        ->orderBy('descripcion', 'desc');

        $lista       = $resultado->get();
        $cabecera    = array();
        $cabecera[]  = array('valor' => '#', 'numero' => '1');
        $cabecera[]  = array('valor' => 'Descripcion', 'numero' => '1');
        $cabecera[]  = array('valor' => 'Profesor', 'numero' => '1');
        $cabecera[]  = array('valor' => 'Fecha de Matrícula', 'numero' => '1');
        $cabecera[]  = array('valor' => 'Desmatricularme', 'numero' => '1');

        $ruta        = $this->rutas;
        $title       = 'Cursos Matriculados';
        $titulo_desmatricularme = $this->tituloDesmatricularme;

        if (count($lista) > 0) {
            $clsLibreria     = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            $lista           = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'ruta', 'title', 'titulo_desmatricularme'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function index()
    {
        $entidad             = 'Curso';
        $title               = $this->tituloAdmin;
        $ruta                = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'ruta'));
    }

    public function show($id)
    {
        //
    }

    public function confirmarDesmatricularme($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'alumno_curso');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = AlumnoCurso::find($id);
        $mensaje = '<p class="text-inverse">¿Estás seguro que quieres desmatricularte en el curso "'.$modelo->curso->descripcion.'"?</p>';
        $entidad  = 'Curso';
        $formData = array('route' => array('cursomatriculado.desmatricularme', $id), 'method' => 'GET', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Desmatricularme';
        return view($this->folderview.'.confirmarDesmatricularme')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function desmatricularme($alumno_curso_id)
    {
        $existe = Libreria::verificarExistencia($alumno_curso_id, 'alumno_curso');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($alumno_curso_id){
            $alumno_curso = AlumnoCurso::find($alumno_curso_id);
            $alumno_curso->delete();
        });
        return is_null($error) ? "OK" : $error;
    }
}