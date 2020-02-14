<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use Hash;
use App\Persona;
use App\AlumnoApoderado;
use App\Nivel;
use App\Usuario;
use App\Local;
use App\Grado;
use App\Http\Requests;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApoderadoController extends Controller
{
    protected $folderview      = 'app.apoderado';
    protected $tituloAdmin     = 'Apoderado';
    protected $tituloRegistrar = 'Registrar Apoderado';
    protected $tituloModificar = 'Modificar Apoderado';
    protected $tituloEliminar  = 'Eliminar Apoderado';
    protected $rutas           = array('create' => 'apoderado.create', 
            'edit'   => 'apoderado.edit',
            'alterarestado' => 'apoderado.alterarestado',
            'search' => 'apoderado.buscar',
            'delete' => 'apoderado.eliminar',
            'index'  => 'apoderado.index',
            'agregarestudiantes' => 'apoderado.agregarestudiantes',
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
        $entidad          = 'Apoderado';
        $user             = Auth::user();
        $local_id         = $user->persona->local_id;
        $nombre           = Libreria::getParam($request->input('nombres'));
        $dni              = Libreria::getParam($request->input('dni'));
        $resultado        = Persona::listarpersonas($nombre, $dni, 5, $local_id); //APODERADOS
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DNI', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Correo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Teléfono', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '4');

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
        $entidad          = 'Apoderado';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    public function create(Request $request)
    {
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $entidad             = 'Apoderado';
        $apoderado           = null;
        $formData            = array('apoderado.store');
        $formData            = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $correo              = "";
        $boton               = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('apoderado', 'formData', 'entidad', 'boton', 'listar', 'correo'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $validacion = Validator::make($request->all(),
            array(
                'dni'             => 'required|size:8|unique:persona,dni,NULL,id,deleted_at,NULL',
                'nombres'  		  => 'required|max:100',
                'apellidopaterno' => 'required|max:100',
                'apellidomaterno' => 'required|max:100',
                'direccion'       => 'required|max:100',
                'telefono'        => 'required|max:9',
                'email'           => 'required|email|unique:usuario,email,NULL,id,deleted_at,NULL',
            )
        );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $user                       = Auth::user();
            $local_id                   = $user->persona->local_id;
            $apoderado                  = new Persona();
            $apoderado->dni             = $request->input('dni');
            $apoderado->nombres         = $request->input('nombres');
            $apoderado->apellidopaterno = $request->input('apellidopaterno');
            $apoderado->apellidomaterno = $request->input('apellidomaterno');
            $apoderado->direccion       = $request->input('direccion');
            $apoderado->fechanacimiento = $request->input('fechanacimiento')==""?NULL:$request->input('fechanacimiento');
            $apoderado->telefono        = $request->input('telefono');
            $apoderado->local_id        = $local_id;
            $apoderado->save();
            //CREAMOS USUARIO
            $usuario               = new Usuario();
            $usuario->login        = $request->input('dni');
            $usuario->password     = Hash::make("@bFtA#ab8G85D"); //CONTRASEÑA ESTÁNDAR
            $usuario->usertype_id  = 5; //TIPO DE USUARIO DE APODERADO
            $usuario->avatar       = "apoderado.jpg";
            $usuario->email        = $request->input('email');
            $usuario->state        = "H";
            $usuario->persona_id   = $apoderado->id;
            $usuario->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function estudiantes(Request $request)
    {
        $listar       = Libreria::getParam($request->input('listar'), 'NO');
        $apoderado    = Persona::find($request->id);
        $entidad      = 'Apoderado';
        $apoderado_id = $request->id;
        $detalles     = AlumnoApoderado::where("apoderado_id", "=", $request->id)->get();
        $formData     = "apoderado.agregarestudiantes";
        $formData     = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete'=> 'off');        
        return view($this->folderview.'.estudiantes')->with(compact('detalles', 'formData', 'entidad', 'listar', 'apoderado_id', 'apoderado'));
    }

    public function show(Apoderado $apoderado)
    {
        //
    }

    public function edit(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'persona');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $apoderado   = Persona::find($id);
        $correo   = Usuario::where("persona_id", "=", $apoderado->id)->first()->email;
        $entidad  = 'Apoderado';
        $formData = array('apoderado.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('apoderado', 'formData', 'entidad', 'boton', 'listar', 'correo'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'persona');
        if ($existe !== true) {
            return $existe;
        }
        $usuario = Usuario::where("persona_id", "=", $id)->first();
        $validacion = Validator::make($request->all(),
            array(
                'dni'             => 'required|size:8|unique:persona,dni,'.$id.',id,deleted_at,NULL',
                'nombres'  		  => 'required|max:100',
                'apellidopaterno' => 'required|max:100',
                'apellidomaterno' => 'required|max:100',
                'direccion'       => 'required|max:100',
                'fechanacimiento' => 'required|date',
                'telefono'        => 'required|max:9',
                'email'           => 'required|email|unique:usuario,email,'.$usuario->id.',id,deleted_at,NULL',
            )
        );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request, $id){
            $user                       = Auth::user();
            $local_id                   = $user->persona->local_id;
            $apoderado                  = Persona::find($id);
            $apoderado->dni             = $request->input('dni');
            $apoderado->nombres         = $request->input('nombres');
            $apoderado->apellidopaterno = $request->input('apellidopaterno');
            $apoderado->apellidomaterno = $request->input('apellidomaterno');
            $apoderado->direccion       = $request->input('direccion');
            $apoderado->fechanacimiento = $request->input('fechanacimiento')==""?NULL:$request->input('fechanacimiento');
            $apoderado->telefono        = $request->input('telefono');
            $apoderado->local_id        = $local_id;
            $apoderado->save();
            //CREAMOS USUARIO
            $usuario               = Usuario::where("persona_id", "=", $id)->first();
            $usuario->login        = $request->input('dni');
            $usuario->email        = $request->input('email');
            $usuario->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'persona');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $apoderado = Persona::find($id);
            $apoderado->delete();
            $usuario = Usuario::where("persona_id", "=", $apoderado->id)->first();
            $usuario->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'persona');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Persona::find($id);
        $entidad  = 'Apoderado';
        $formData = array('route' => array('apoderado.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function confirmaralterarestado(Request $request)
    {
        $existe = Libreria::verificarExistencia($request->id, 'persona');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($request){
            $apoderado = Persona::find($request->id);
            $usuario = Usuario::where("persona_id", "=", $apoderado->id)->first();
            $usuario->state = strtoupper($request->estado);
            $usuario->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function alterarestado(Request $request)
    {
        $existe = Libreria::verificarExistencia($request->id, 'persona');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($request->listarLuego))) {
            $listar = $request->listarLuego;
        }
        $modelo   = Persona::find($request->id);
        $entidad  = 'Apoderado';
        $formData = array('route' => array('apoderado.confirmaralterarestado', "id=" . $request->id, "estado=" . $request->estado), 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarAlterarestado')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function agregarestudiantes(Request $request)
    {
        $error = DB::transaction(function() use($request){
            $cadenaids = $request->cadenaidalumnos;
            $explodecadenaids = explode(";", $cadenaids);
            if(count($explodecadenaids)>0) {
                foreach ($explodecadenaids as $ecid) {
                    //CREO EL DETALLE SOLO SI NO EXISTE
                    if($ecid!=="") {
                        $malumnoapoderado = AlumnoApoderado::where("alumno_id", "=", $ecid)
                            ->where("apoderado_id", "=", $request->apoderado_id)
                            ->first();
                        if($malumnoapoderado==NULL) {
                            $alumnoapoderado = new AlumnoApoderado();
                            $alumnoapoderado->alumno_id = $ecid;
                            $alumnoapoderado->apoderado_id = $request->apoderado_id;
                            $alumnoapoderado->save();
                        }
                    }
                }
            }
            //ELIMINO DETALLES QUE SOBRAN
            $sdetalles = AlumnoApoderado::where("apoderado_id", "=", $request->apoderado_id);
            if(count($explodecadenaids)>0) {
                foreach ($explodecadenaids as $ecid) {
                    if($ecid!=="") {
                        $sdetalles = $sdetalles->where("alumno_id", "!=", $ecid);
                    }
                }
            }
            $sdetalles = $sdetalles->get();
            if(count($sdetalles)>0) {
                foreach ($sdetalles as $sd) {
                    $sd->delete();
                }
            }
        });
        return is_null($error) ? "OK" : $error;
    }
}
