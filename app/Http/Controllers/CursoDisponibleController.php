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

class CursoDisponibleController extends Controller
{
    protected $folderview         = 'app.cursodisponible';
    protected $tituloAdmin        = 'Cursos Disponibles';
    protected $tituloMatricularme = 'Matricularme en este Curso';
    protected $rutas              = array(
            'search' => 'cursodisponible.buscar',
            'index'  => 'cursodisponible.index',
            'confirmarMatricularme'  => 'cursodisponible.confirmarMatricularme',
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
        $resultado   = Curso::listar($descripcion, null, true, $alumno_id, '!=');
        $lista       = $resultado->get();
        $cabecera    = array();
        $cabecera[]  = array('valor' => '#', 'numero' => '1');
        $cabecera[]  = array('valor' => 'Descripcion', 'numero' => '1');
        $cabecera[]  = array('valor' => 'Apertura', 'numero' => '1');
        $cabecera[]  = array('valor' => 'Profesor', 'numero' => '1');
        $cabecera[]  = array('valor' => 'Estado', 'numero' => '1');
        $cabecera[]  = array('valor' => 'Matricularme', 'numero' => '1');

        $ruta        = $this->rutas;
        $title       = 'Cursos Disponibles';
        $titulo_matricularme = $this->tituloMatricularme;

        if (count($lista) > 0) {
            $clsLibreria     = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            $lista           = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'ruta', 'title', 'titulo_matricularme'));
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

    public function confirmarMatricularme($id, $listarLuego)
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
        $mensaje = '<p class="text-inverse">¿Estás seguro que quieres matricularte en el curso "'.$modelo->descripcion.'"?</p>';
        $entidad  = 'Curso';
        $formData = array('route' => array('cursodisponible.matricularme', $id), 'method' => 'GET', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Matricularme';
        return view($this->folderview.'.confirmarMatricularme')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    public function matricularme($curso_id)
    {
        $existe = Libreria::verificarExistencia($curso_id, 'curso');
        if ($existe !== true) {
            return $existe;
        }
        $now = new \DateTime();
        $user       = Auth::user();
        $alumno_id  = $user->persona_id;
        $error = DB::transaction(function() use($curso_id, $alumno_id, $now){
            $alumnocurso                  = new AlumnoCurso();
            $alumnocurso->curso_id        = $curso_id;
            $alumnocurso->alumno_id       = $alumno_id;
            $alumnocurso->fecha_matricula = $now->format('Y-m-d H:i:s');
            $alumnocurso->save();
        });
        return is_null($error) ? "OK" : $error;
    }
}