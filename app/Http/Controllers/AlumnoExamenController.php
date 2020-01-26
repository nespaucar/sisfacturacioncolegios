<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Alumno;
use App\Examen;
use App\Curso;
use App\AlumnoExamen;
use App\AlumnoAlternativa;
use App\Pregunta;
use App\Alternativa;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

class AlumnoExamenController extends Controller
{
	protected $folderview      = 'app.alumnoexamen';
    protected $tituloAdmin     = 'Mis Exámenes';
    protected $rutas           = array(
            'search' => 'alumnoexamen.buscar',
            'index'  => 'alumnoexamen.index',
            'llenarexamen'  => 'alumnoexamen.llenarexamen',
            'guardarexamen'  => 'alumnoexamen.guardarexamen',
            'respuestasexamen' => 'alumnoexamen.respuestasexamen',
        );

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar el resultado de búsquedas
     * 
     * @return Response 
     */
    public function buscar(Request $request)
    {
    	$pagina     = $request->input('page');
        $filas      = $request->input('filas');
        $entidad    = 'AlumnoExamen';
        $user       = Auth::user();
        $alumno_id  = $user->persona_id; 
        $nombre     = Libreria::getParam($request->input('nombre'));
        $curso_id   = Libreria::getParam($request->input('curso_id'));
        $filtro     = '';
        if(!is_null($curso_id)) {
            $filtro = 1;
        }
        $resultado  = Examen::listar2($nombre, $curso_id, $alumno_id, $filtro);
        $lista      = $resultado->get();
        $cabecera   = array();
        $cabecera[] = array('valor' => '#', 'numero' => '1');
        $cabecera[] = array('valor' => 'Examen', 'numero' => '1');
        $cabecera[] = array('valor' => 'Curso', 'numero' => '1');
        $cabecera[] = array('valor' => 'Profesor', 'numero' => '1');
        $cabecera[] = array('valor' => 'Preguntas', 'numero' => '1');
        $cabecera[] = array('valor' => 'Estado', 'numero' => '1');

        $ruta       = $this->rutas;
        if (count($lista) > 0) {
            $clsLibreria     = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            $lista           = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'ruta', 'alumno_id'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function index()
    {
        $user      = Auth::user();
        $alumno_id = $user->persona_id;
        $entidad   = 'AlumnoExamen';
        $title     = $this->tituloAdmin;
        $ruta      = $this->rutas;
        $cboCurso  = Curso::select('curso.id', 'descripcion')
                        ->distinct()
                        ->join('alumno_curso', 'alumno_curso.curso_id', '=', 'curso.id')
                        ->where('alumno_id', $alumno_id)
                        ->where('curso.estado', true)
                        ->where('alumno_curso.deleted_at', null)
                        ->get();
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'ruta', 'cboCurso'));
    }

    public function llenarexamen(Request $request) 
    {
        $user      = Auth::user();
        $alumno_id = $user->persona_id;
        $acceso     = false;
        $examen_id = Libreria::getParam($request->get('examen_id'));
        $examen    = Examen::find($examen_id);
        if(count($examen) == 1) {
            $curso = Curso::join('alumno_curso', 'alumno_curso.curso_id', '=', 'curso.id')
            ->where('alumno_id', $alumno_id)
            ->where('curso.id', $examen->curso->id)
            ->where('curso.estado', true)
            ->where('alumno_curso.deleted_at', null)
            ->get();
            if(count($curso) == 1) {
                $acceso = true;
            }
        }
        return view($this->folderview.'.llenarexamen')->with(compact('acceso', 'examen'));
    }

    public function guardarexamen(Request $request) 
    {
        $now           = new \DateTime();        
        $user          = Auth::user();
        $alumno_id     = $user->persona_id;
        $examen_id     = $request->get('examen_id');
        $cantpreguntas = $request->get('cantpreguntas');

        $alumnoexamen                = new AlumnoExamen();
        $alumnoexamen->examen_id     = $examen_id;
        $alumnoexamen->alumno_id     = $alumno_id;
        $alumnoexamen->fecha_entrega = $now->format('Y-m-d H:i:s');
        $alumnoexamen->save();

        for ($i = 1; $i <= $cantpreguntas; $i++) { 
            $alternativa_id            = $request->get('alternativa' . $i);
            $respuesta                 = new AlumnoAlternativa();
            $respuesta->alumno_id      = $alumno_id;
            $respuesta->alternativa_id = $alternativa_id;
            $respuesta->save();
        }
    }

    public function respuestasexamen(Request $request) {
        $examen_id = $request->get('examen_id');
        $user        = Auth::user();
        $alumno_id   = $user->persona_id;

        $preguntas   = Pregunta::where('examen_id', $examen_id)->get();

        return view($this->folderview.'.respuestasexamen')->with(compact('preguntas'));
    }
}
