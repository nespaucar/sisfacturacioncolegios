<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\AlumnoSeccion;
use App\Nivel;
use App\Cicloacademico;
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
            'confirmarmatricularalumno' => 'alumnoseccion.confirmarmatricularalumno',
            'search' => 'alumnoseccion.buscar',
            'index'  => 'alumnoseccion.index',
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
        $entidad          = 'Matricula';
        $seccion_id       = Libreria::getParam($request->input('seccion_id'));
        $anoescolar       = Libreria::getParam($request->input('anoescolar'));
        $resultado        = Seccion::listar($seccion_id);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nivel', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Grado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Sección', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Año escolar', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Matriculados', 'numero' => '1');

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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta', 'anoescolar'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad', 'anoescolar'));
    }

    public function index()
    {
        $entidad          = 'Matricula';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $cboSecciones     = [''=>'--TODAS--'];

        $secciones        = Seccion::all();

        foreach ($secciones as $s) {
            $cboSecciones[$s->id] = ($s->grado!==NULL?$s->grado->descripcion:'-') . ' grado '.($s->descripcion) . ' del nivel ' . ($s->grado!==NULL?($s->grado->nivel!==NULL?$s->grado->nivel->descripcion:'-'):'-');
        }

        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboSecciones'));
    }

    public function matriculados(Request $request) {
        $entidad          = 'Curso';
        $listar           = 'SI';
        $title            = $this->tituloAdmin;
        $ruta             = $this->rutas;
        $anoescolar       = $request->anoescolar;
        $seccion_id       = $request->id;
        $cicloacademico   = Cicloacademico::where(DB::raw("YEAR(created_at)"), "=", $anoescolar)->first();
        $formData = array('route' => array('alumnoseccion.matricularalumno', $seccion_id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        return view($this->folderview.'.matriculados')->with(compact('entidad', 'title', 'ruta', 'anoescolar', 'seccion_id', 'cicloacademico', 'formData', 'listar'));
    }

    public function matricularalumno(Request $request) {
        $entidad          = 'Curso';
        $listar           = 'SI';
        $title            = $this->tituloAdmin;
        $ruta             = $this->rutas;
        $anoescolar       = $request->anoescolar;
        $seccion_id       = $request->id;
        $cicloacademico   = Cicloacademico::where(DB::raw("YEAR(created_at)"), "=", $anoescolar)->first();
        $formData = array('route' => array('alumnoseccion.confirmarmatricularalumno', $seccion_id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        return view($this->folderview.'.matricularalumno')->with(compact('entidad', 'title', 'ruta', 'anoescolar', 'seccion_id', 'cicloacademico', 'formData', 'listar'));
    }

    public function confirmarmatricularalumno(Request $request) {
        $error = DB::transaction(function() use($request){
            $seccion_id        = $request->seccion_id;
            $listar            = $request->listar;
            $cicloacademico_id = $request->cicloacademico_id;
        });
        return is_null($error) ? "OK" : $error;
    }
}
