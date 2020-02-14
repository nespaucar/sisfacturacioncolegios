<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Local;
use App\Nivel;
use App\Grado;
use App\Seccion;
use App\Http\Requests;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GradoController extends Controller
{
    protected $folderview      = 'app.grado';
    protected $tituloAdmin     = 'Grado';
    protected $tituloRegistrar = 'Registrar Grado';
    protected $tituloModificar = 'Modificar Grado';
    protected $tituloEliminar  = 'Eliminar Grado';
    protected $rutas           = array('create' => 'grado.create', 
            'edit'   => 'grado.edit', 
            'secciones'   => 'grado.secciones', 
            'delete' => 'grado.eliminar',
            'search' => 'grado.buscar',
            'index'  => 'grado.index',
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
        $entidad          = 'Grado';
        $descripcion      = Libreria::getParam($request->input('descripcion'));
        $resultado        = Grado::listar($descripcion, $local_id);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nivel', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Local', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '3');

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
        $entidad          = 'Grado';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    public function create(Request $request)
    {
        $listar              = 'SI';
        $entidad             = 'Grado';
        $grado               = null;
        $formData            = array('grado.store');
        $formData            = array('route' => $formData, 'files' => true, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $local               = Auth::user()->persona->local->nombre;
        $local_id            = Auth::user()->persona->local->id;
        $boton               = 'Registrar';
        $primLocal           = Local::first();
        return view($this->folderview.'.mant')->with(compact('grado', 'formData', 'entidad', 'boton', 'listar', 'local', 'local_id'));
    }

    public function store(Request $request)
    {
        $validacion = Validator::make($request->all(),
            array(
                'descripcion' => 'required|max:100',
                'nivel_id'    => 'required|numeric',
            )
        );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $grado                = new Grado();
            $grado->descripcion   = $request->input('descripcion');
            $grado->nivel_id      = $request->input('nivel_id');
            $grado->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function show(Grado $grado)
    {
        //
    }

    public function edit(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'grado');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = 'SI';
        $grado    = Grado::find($id);
        $entidad  = 'Grado';
        $formData = array('grado.update', $id);
        $formData = array('route' => $formData, 'files' => true, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $cboNiveles = Nivel::pluck('descripcion', 'id')->all();
        $boton    = 'Modificar';
        $local = $grado!==NULL?($grado->nivel!==NULL?($grado->nivel->local!==NULL?$grado->nivel->local->nombre:"-"):"-"):"-";
        $local_id = $grado!==NULL?($grado->nivel!==NULL?($grado->nivel->local!==NULL?$grado->nivel->local->id:NULL):NULL):NULL;
        return view($this->folderview.'.mant')->with(compact('grado', 'formData', 'entidad', 'boton', 'listar', 'cboNiveles', 'local', 'local_id'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'grado');
        if ($existe !== true) {
            return $existe;
        }
        $validacion = Validator::make($request->all(),
            array(
                'descripcion' => 'required|max:100',
                'nivel_id'    => 'required|numeric',
            )
        );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request, $id){
            $grado                = Grado::find($id);
            $grado->descripcion   = $request->input('descripcion');
            $grado->nivel_id      = $request->input('nivel_id');
            $grado->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'grado');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $grado = Grado::find($id);
            $grado->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'grado');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Grado::find($id);
        $entidad  = 'Grado';
        $formData = array('route' => array('grado.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function cargarNiveles(Request $request) {
        $retorno = "";
        $niveles = Nivel::where("local_id", "=", Auth::user()->persona->local_id)->get();
        if(count($niveles) > 0) {
            foreach ($niveles as $niv) {
                $retorno .= '<option value="' . $niv->id . '">' . $niv->descripcion . '</option>';
            }
        }

        echo $retorno;
    }

    public function grados(Request $request) {
        $user     = Auth::user();
        $local_id = $user->persona->local_id;
        $nivel_id = $request->nivel_id;
        $retorno  = "<option value=''>--TODOS--</option>";
        if($nivel_id!=="") {
            $grados = Grado::where("nivel_id", "=", $nivel_id)
                ->get();
            if(count($grados)>0) {
                foreach ($grados as $grado) {
                    $retorno  .= "<option value='".$grado->id."'>".$grado->descripcion."</option>";
                }
            }
        }
        echo $retorno;
    }

    public function seccionesM(Request $request) {
        $user     = Auth::user();
        $local_id = $user->persona->local_id;
        $grado_id = $request->grado_id;
        $retorno  = "<option value=''>--TODOS--</option>";
        if($grado_id!=="") {
            $secciones = Seccion::where("grado_id", "=", $grado_id)
                ->get();
            if(count($secciones)>0) {
                foreach ($secciones as $seccion) {
                    $retorno  .= "<option value='".$seccion->id."'>".$seccion->descripcion."</option>";
                }
            }
        }
        echo $retorno;
    }

    public function secciones(Request $request)
    {
        $entidad             = 'Grado';
        $id                  = $request->id;
        $grado               = Grado::find($id);
        $formData            = array('grado.secciones');
        $formData            = array('route' => $formData, 'files' => true, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off', 'onsubmit' => 'return false');
        $local               = Auth::user()->persona->local->nombre;
        $local_id            = Auth::user()->persona->local->id;
        $boton               = 'Registrar';
        $primLocal           = Local::first();
        return view($this->folderview.'.secciones')->with(compact('id', 'grado', 'formData', 'entidad', 'boton', 'listar', 'local', 'local_id'));
    }

    public function anadirSeccion(Request $request) {
        $retorno = "";
        $seccion = new Seccion();
        $seccion->descripcion = $request->descripcion;
        $seccion->grado_id = $request->par;
        $seccion->save();

        $retorno = '<tr id="rowSeccion'.$seccion->id.'">
                        <td class="text-center">'.$seccion->descripcion.'</td>
                        <td class="text-center">
                            <a class="btn btn-xs btn-danger" onclick="eliminarSeccion('.$seccion->id.')" href="#"><i class="fa fa-minus fa-xs"></i></a>
                        </td>
                    </tr>';

        echo $retorno;
    }

    public function eliminarSeccion(Request $request) {
        $retorno = "";
        $seccion = Seccion::find($request->par);
        $seccion->delete();

        echo "OK";
    }
}
