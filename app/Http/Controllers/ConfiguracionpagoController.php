<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Configuracionpago;
use App\Persona;
use App\Nivel;
use App\Grado;
use App\Seccion;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ConfiguracionpagoController extends Controller
{
    protected $folderview      = 'app.configuracionpago';
    protected $tituloAdmin     = 'Configuracion de Pago';
    protected $tituloRegistrar = 'Registrar Configuracion de Pago';
    protected $tituloModificar = 'Modificar Configuracion de Pago';
    protected $tituloEliminar  = 'Eliminar Configuracion de Pago';
    protected $rutas           = array('create' => 'configuracionpago.create', 
            'edit'   => 'configuracionpago.edit', 
            'delete' => 'configuracionpago.eliminar',
            'search' => 'configuracionpago.buscar',
            'index'  => 'configuracionpago.index',
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
        $entidad          = 'Configuracionpago';
        $tabla            = Libreria::getParam($request->input('tabla'));
        switch ($tabla) {
            case "1":
                // ALUMNO
                $resultado = Configuracionpago::listar($tabla, NULL, NULL, NULL);
                break;            
            case "2":
                // NIVEL
                $resultado = Configuracionpago::listar(NULL, $tabla, NULL, NULL);
                break;
            case "3":
                // GRADO
                $resultado = Configuracionpago::listar(NULL, NULL, $tabla, NULL);
                break;
            case "4":
                // SECCION
                $resultado = Configuracionpago::listar(NULL, NULL, NULL, $tabla);
                break;
            default:
                // TODOS
                $resultado = Configuracionpago::listar(NULL, NULL, NULL, NULL);
                break;
        }
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');        
        $cabecera[]       = array('valor' => 'Aplicado a', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Frecuencia', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Monto', 'numero' => '1');
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
        $entidad          = 'Configuracionpago';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    public function create(Request $request)
    {
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $entidad             = 'Configuracionpago';
        $configuracionpago       = null;
        $formData            = array('configuracionpago.store');
        $formData            = array('route' => $formData, 'files' => true, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('configuracionpago', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function store(Request $request)
    {
        $now        = new \DateTime();
        $user       = Auth::user();
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $validacion = Validator::make($request->all(),
            array(
                'tipo'         => 'required|max:1',
                'alumno'  => 'max:100',
                'nivel'  => 'max:100',
                'grado'  => 'max:100',
                'seccion'  => 'max:100',
            )
        );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request, $now){
            $configuracionpago         = new Configuracionpago();
            $configuracionpago->tipo   = $request->input('tipo');
            $configuracionpago->nombre = $request->input('nombre');
            $configuracionpago->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function show(Configuracionpago $configuracionpago)
    {
        //
    }

    public function edit(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'configuracionpago');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $configuracionpago    = Configuracionpago::find($id);
        $entidad  = 'Configuracionpago';
        $formData = array('configuracionpago.update', $id);
        $formData = array('route' => $formData, 'files' => true, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('configuracionpago', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'configuracionpago');
        if ($existe !== true) {
            return $existe;
        }
        $validacion = Validator::make($request->all(),
            array(
                'tipo' => 'required|max:1',
                'nombre'      => 'required|max:100',
            )
        );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request, $id){
            $configuracionpago                = Configuracionpago::find($id);
            $configuracionpago->tipo = $request->input('tipo');
            $configuracionpago->nombre      = $request->input('nombre');
            $configuracionpago->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'configuracionpago');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $configuracionpago = Configuracionpago::find($id);
            $configuracionpago->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'configuracionpago');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Configuracionpago::find($id);
        $entidad  = 'Configuracionpago';
        $formData = array('route' => array('configuracionpago.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function alumnoautocompleting($searching) {
        $resultado = Persona::where(DB::raw('CONCAT(apellidopaterno," ",apellidomaterno," ",nombres)'), 'LIKE', '%'.strtoupper($searching).'%')
                ->orderBy('apellidopaterno', 'ASC')
                ->join("usuario", "usuario.persona_id", "=", "persona.id")
                ->join("usertype", "usertype.id", "=", "usuario.usertype_id")
                ->where("usertype.id", "=", 2) //SOLO ALUMNOS
                ->select('persona.*');
        $list      = $resultado->get();
        $data = array();
        foreach ($list as $key => $value) {
            $data[] = array(
                'label' => $value->apellidopaterno.' '.$value->apellidomaterno.' '.$value->nombres,
                'id'    => $value->id,
                'value' => $value->apellidopaterno.' '.$value->apellidomaterno.' '.$value->nombres,
            );
        }
        return json_encode($data);
    }

    public function nivelautocompleting($searching) {
        $resultado = Nivel::where("descripcion", 'LIKE', '%'.strtoupper($searching).'%')->orderBy('descripcion', 'ASC');
        $list      = $resultado->get();
        $data = array();
        foreach ($list as $key => $value) {
            $data[] = array(
                'label' => $value->descripcion,
                'id'    => $value->id,
                'value' => $value->descripcion,
            );
        }
        return json_encode($data);
    }

    public function gradoautocompleting($searching) {
        $resultado = Grado::where("descripcion", 'LIKE', '%'.strtoupper($searching).'%')->orderBy('descripcion', 'ASC');
        $list      = $resultado->get();
        $data = array();
        foreach ($list as $key => $value) {
            $data[] = array(
                            'label' => $value->descripcion.($value->nivel!==NULL?(" - ".$value->nivel->descripcion):""),
                            'id'    => $value->id,
                            'value' => $value->descripcion.($value->nivel!==NULL?(" - ".$value->nivel->descripcion):""),
                        );
        }
        return json_encode($data);
    }

    public function seccionautocompleting($searching) {
        $resultado = Seccion::where("descripcion", 'LIKE', '%'.strtoupper($searching).'%')->orderBy('descripcion', 'ASC');
        $list      = $resultado->get();
        $data = array();
        foreach ($list as $key => $value) {
            $data[] = array(
                            'label' => '"'.$value->descripcion.'" '.(
                                $value->grado!==NULL?
                                (
                                    " - ".$value->grado->descripcion .
                                    (
                                        $value->grado->nivel!==NULL?
                                        (" - ".$value->grado->nivel->descripcion):
                                    "")
                                ):
                            ""),
                            'id'    => $value->id,
                            'value' => '"'.$value->descripcion.'" '.(
                                $value->grado!==NULL?
                                (
                                    " - ".$value->grado->descripcion .
                                    (
                                        $value->grado->nivel!==NULL?
                                        (" - ".$value->grado->nivel->descripcion):
                                    "")
                                ):
                            ""),
                        );
        }
        return json_encode($data);
    }
}
