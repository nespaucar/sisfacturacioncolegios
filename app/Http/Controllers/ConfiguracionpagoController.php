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
        $local_id         = $user->persona->local_id;
        $id               = $user->persona_id;
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Configuracionpago';
        $tabla            = Libreria::getParam($request->input('tabla'));
        switch ($tabla) {
            case "1":
                // ALUMNO
                $resultado = Configuracionpago::listar($tabla, NULL, NULL, NULL, $local_id);
                break;            
            case "2":
                // NIVEL
                $resultado = Configuracionpago::listar(NULL, $tabla, NULL, NULL, $local_id);
                break;
            case "3":
                // GRADO
                $resultado = Configuracionpago::listar(NULL, NULL, $tabla, NULL, $local_id);
                break;
            case "4":
                // SECCION
                $resultado = Configuracionpago::listar(NULL, NULL, NULL, $tabla, $local_id);
                break;
            default:
                // TODOS
                $resultado = Configuracionpago::listar(NULL, NULL, NULL, NULL, $local_id);
                break;
        }
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');        
        $cabecera[]       = array('valor' => 'Aplicado a', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Frecuencia', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Monto matricula', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Monto mensual', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Unidad', 'numero' => '1');
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
        $configuracionpago   = null;
        $formData            = array('configuracionpago.store');
        $formData            = array('route' => $formData, 'files' => true, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('configuracionpago', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function store(Request $request)
    {
        $validacion = Validator::make($request->all(),
            array(
                'tipo'       => 'required|max:1',
                'alumno'     => 'nullable|max:100',
                'monto'      => 'required|numeric',
                'montom'     => 'required|numeric',
                'nivel'      => 'nullable|max:100',
                'grado'      => 'nullable|max:100',
                'seccion'    => 'nullable|max:100',
                'alumno_id'  => 'nullable|numeric',
                'nivel_id'   => 'nullable|numeric',
                'grado_id'   => 'nullable|numeric',
                'seccion_id' => 'nullable|numeric',
            )
        );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $configuracionpago         = new Configuracionpago();
            $configuracionpago->alumno_id = NULL;
            $configuracionpago->nivel_id = NULL;
            $configuracionpago->grado_id = NULL;
            $configuracionpago->seccion_id = NULL;
            $local_id = NULL;
            switch ($request->tipo) {
                case '1':
                    $configuracionpago->descripcion = $request->alumno;
                    $configuracionpago->alumno_id = $request->alumno_id;
                    $local_id = Persona::find($request->alumno_id)->local_id;
                    break;
                case '2':
                    $configuracionpago->descripcion = $request->nivel;
                    $configuracionpago->nivel_id = $request->nivel_id;
                    $local_id = Nivel::find($request->nivel_id)->local_id;
                    break;
                case '3':
                    $configuracionpago->descripcion = $request->grado;
                    $configuracionpago->grado_id = $request->grado_id;
                    $local_id = Grado::find($request->grado_id)->nivel->local_id;
                    break;
                case '4':
                    $configuracionpago->descripcion = $request->seccion;
                    $configuracionpago->seccion_id = $request->seccion_id;
                    $local_id = Seccion::find($request->seccion_id)->grado->nivel->local_id;
                    break;
            }
            $configuracionpago->frecuencia = "M";//MENSUAL
            $configuracionpago->unidad   = "S";//SOLES
            $configuracionpago->monto    = $request->monto;//SOLES
            $configuracionpago->montom   = $request->montom;//SOLES
            $configuracionpago->local_id = $local_id;
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
                'tipo'       => 'required|max:1',
                'monto'      => 'required|numeric',
                'montom'     => 'required|numeric',
                'alumno'     => 'nullable|max:100',
                'nivel'      => 'nullable|max:100',
                'grado'      => 'nullable|max:100',
                'seccion'    => 'nullable|max:100',
                'alumno_id'  => 'nullable|numeric',
                'nivel_id'   => 'nullable|numeric',
                'grado_id'   => 'nullable|numeric',
                'seccion_id' => 'nullable|numeric',
            )
        );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request, $id){
            $configuracionpago                = Configuracionpago::find($id);
            $configuracionpago->alumno_id = NULL;
            $configuracionpago->nivel_id = NULL;
            $configuracionpago->grado_id = NULL;
            $configuracionpago->seccion_id = NULL;
            $local_id = NULL;
            switch ($request->tipo) {
                case '1':
                    $configuracionpago->descripcion = $request->alumno;
                    $configuracionpago->alumno_id = $request->alumno_id;
                    $local_id = Persona::find($request->alumno_id)->local_id;
                    break;
                case '2':
                    $configuracionpago->descripcion = $request->nivel;
                    $configuracionpago->nivel_id = $request->nivel_id;
                    $local_id = Nivel::find($request->nivel_id)->local_id;
                    break;
                case '3':
                    $configuracionpago->descripcion = $request->grado;
                    $configuracionpago->grado_id = $request->grado_id;
                    $local_id = Grado::find($request->grado_id)->nivel->local_id;
                    break;
                case '4':
                    $configuracionpago->descripcion = $request->seccion;
                    $configuracionpago->seccion_id = $request->seccion_id;
                    $local_id = Seccion::find($request->seccion_id)->grado->nivel->local_id;
                    break;
            }
            $configuracionpago->frecuencia = "M";//MENSUAL
            $configuracionpago->unidad   = "S";//SOLES
            $configuracionpago->monto   = $request->monto;//SOLES
            $configuracionpago->montom   = $request->montom;//SOLES
            $configuracionpago->local_id = $local_id;
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
        $user      = Auth::user();
        $local_id  = $user->persona->local_id;
        $resultado = Persona::where(DB::raw('CONCAT(apellidopaterno," ",apellidomaterno," ",nombres)'), 'LIKE', '%'.strtoupper($searching).'%')
                ->orderBy('apellidopaterno', 'ASC')
                ->join("usuario", "usuario.persona_id", "=", "persona.id")
                ->join("usertype", "usertype.id", "=", "usuario.usertype_id")
                ->where("usertype.id", "=", 2) //SOLO ALUMNOS
                ->where("persona.local_id", "=", $local_id)
                ->select('persona.*');
        $list      = $resultado->get();
        $data = array();
        foreach ($list as $key => $value) {
            $data[] = array(
                'label' => $value->apellidopaterno.' '.$value->apellidomaterno.' '.$value->nombres,
                'id'    => $value->id,
                'value' => $value->apellidopaterno.' '.$value->apellidomaterno.' '.$value->nombres,
                'dni'   => $value->dni,
            );
        }
        return json_encode($data);
    }

    public function nivelautocompleting($searching) {
        $user      = Auth::user();
        $local_id  = $user->persona->local_id;
        $resultado = Nivel::where("descripcion", 'LIKE', '%'.strtoupper($searching).'%')
            ->orderBy('descripcion', 'ASC')
            ->where("nivel.local_id", "=", $local_id);
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
        $user      = Auth::user();
        $local_id  = $user->persona->local_id;
        $resultado = Grado::where("grado.descripcion", 'LIKE', '%'.strtoupper($searching).'%')
            ->join("nivel", "nivel.id", "=", "grado.nivel_id")
            ->orderBy('grado.descripcion', 'ASC')
            ->where("nivel.local_id", "=", $local_id)
            ->select("grado.descripcion", "grado.nivel_id", "grado.id");
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
        $user      = Auth::user();
        $local_id  = $user->persona->local_id;
        $resultado = Seccion::where("seccion.descripcion", 'LIKE', '%'.strtoupper($searching).'%')
            ->join("grado", "grado.id", "=", "seccion.grado_id")
            ->join("nivel", "nivel.id", "=", "grado.nivel_id")
            ->orderBy('seccion.descripcion', 'ASC')
            ->select("seccion.descripcion", "seccion.id", "seccion.grado_id", "grado.nivel_id")
            ->where("nivel.local_id", "=", $local_id);
        $list      = $resultado->get();
        $data = array();
        foreach ($list as $key => $value) {
            $data[] = array(
                'label' => '"'.$value->descripcion.'"'.(
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
