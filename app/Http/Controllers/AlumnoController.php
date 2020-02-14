<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use Hash;
use App\Persona;
use App\Local;
use App\AlumnoApoderado;
use App\Nivel;
use App\Usuario;
use App\Grado;
use App\Http\Requests;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AlumnoController extends Controller
{
    protected $folderview      = 'app.alumno';
    protected $tituloAdmin     = 'Alumno';
    protected $tituloRegistrar = 'Registrar Alumno';
    protected $tituloModificar = 'Modificar Alumno';
    protected $tituloEliminar  = 'Eliminar Alumno';
    protected $rutas           = array('create' => 'alumno.create', 
            'edit'   => 'alumno.edit', 
            'alterarestado' => 'alumno.alterarestado',
            'createapoderado' => 'alumno.createapoderado',
            'search' => 'alumno.buscar',
            'delete' => 'alumno.eliminar',
            'index'  => 'alumno.index',
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
        $entidad          = 'Alumno';
        $nombre           = Libreria::getParam($request->input('nombres'));
        $dni              = Libreria::getParam($request->input('dni'));
        $local_id         = $user->persona->local_id;
        $resultado        = Persona::listarpersonas($nombre, $dni, 2, $local_id); //ALUMNOS
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DNI', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Alumno', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Correo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Apoderado', 'numero' => '1');
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
        $entidad          = 'Alumno';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    public function create(Request $request)
    {
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $entidad             = 'Alumno';
        $alumno              = null;
        $formData            = array('alumno.store');
        $formData            = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $correo              = "";
        $boton               = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('alumno', 'formData', 'entidad', 'boton', 'listar', 'correo'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $validacion = Validator::make($request->all(),
            array(
                'dni'             => 'required|digits:8|unique:persona,dni,NULL,id,deleted_at,NULL',
                'nombres'  		  => 'required|max:100',
                'apellidopaterno' => 'required|max:100',
                'apellidomaterno' => 'required|max:100',
                'direccion'       => 'max:100',
                'telefono'        => 'max:9',
                'email'           => 'email|unique:usuario,email,NULL,id,deleted_at,NULL',
            )
        );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $user                    = Auth::user();
            $local_id                = $user->persona->local_id;
            $alumno                  = new Persona();
            $alumno->dni             = $request->input('dni');
            $alumno->nombres         = $request->input('nombres');
            $alumno->apellidopaterno = $request->input('apellidopaterno');
            $alumno->apellidomaterno = $request->input('apellidomaterno');
            $alumno->direccion       = $request->input('direccion');
            $alumno->fechanacimiento = $request->input('fechanacimiento')==""?NULL:$request->input('fechanacimiento');
            $alumno->telefono        = $request->input('telefono');
            $alumno->local_id        = $local_id;
            $alumno->save();
            //CREAMOS USUARIO
            $usuario               = new Usuario();
            $usuario->login        = $request->input('dni');
            $usuario->password     = Hash::make("@bFtA#ab8G85D"); //CONTRASEÑA ESTÁNDAR
            $usuario->usertype_id  = 2; //TIPO DE USUARIO DE ALUMNO
            $usuario->avatar       = "alumno.jpg";
            $usuario->email        = $request->input('email');
            $usuario->state        = "H";
            $usuario->persona_id   = $alumno->id;
            $usuario->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function createapoderado(Request $request)
    {
        $listar          = Libreria::getParam($request->input('listar'), 'NO');
        $entidad         = 'Alumno';
        $alumno_id       = $request->id;
        $correo          = "";
        $alumnoapoderado = AlumnoApoderado::where("alumno_id", "=", $request->id)->first();
        if($alumnoapoderado!==NULL) {
            $apoderado   = Persona::find($alumnoapoderado->apoderado_id);
            $usuario     = Usuario::where("persona_id", "=", $apoderado->id)->first();
            $correo      = $usuario!==NULL?$usuario->email:"";
        } else {
            $apoderado   = NULL;
        }        
        $formData            = array('alumno.storeapoderado');
        $formData            = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Registrar'; 
        return view($this->folderview.'.mantapoderado')->with(compact('apoderado', 'formData', 'entidad', 'boton', 'listar', 'alumno_id', 'correo'));
    }

    public function storeapoderado(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        if($request->apoderado_id!==NULL&&$request->apoderado_id!=="") {
            $usuario = Usuario::where("persona_id", "=", $request->apoderado_id)->first();
            $validacion = Validator::make($request->all(),
                array(
                    'dni'             => 'required|digits:8|unique:persona,dni,'.$request->apoderado_id.',id,deleted_at,NULL',
                    'nombres'         => 'required|max:100',
                    'apellidopaterno' => 'required|max:100',
                    'apellidomaterno' => 'required|max:100',
                    'direccion'       => 'required|max:100',
                    'telefono'        => 'required|max:9',
                    'email'           => 'required|email|unique:usuario,email,'.$usuario->id.',id,deleted_at,NULL',
                )
            );
        } else {
            $validacion = Validator::make($request->all(),
                array(
                    'dni'             => 'required|digits:8|unique:persona,dni,NULL,id,deleted_at,NULL',
                    'nombres'         => 'required|max:100',
                    'apellidopaterno' => 'required|max:100',
                    'apellidomaterno' => 'required|max:100',
                    'direccion'       => 'required|max:100',
                    'telefono'        => 'required|max:9',
                    'email'           => 'required|email|unique:usuario,email,NULL,id,deleted_at,NULL',
                )
            );
        }            
            
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){ 
            $apoderado                  = new Persona();           
            if($request->apoderado_id!==NULL&&$request->apoderado_id!=="") {
                $apoderado = Persona::find($request->apoderado_id);
            }
            $user                       = Auth::user();
            $local_id                   = $user->persona->local_id;
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
            $usuario                  = new Usuario();
            if($request->apoderado_id!==NULL&&$request->apoderado_id!=="") {
                $usuario = Usuario::where("persona_id", "=", $request->apoderado_id)->first();
            }
            $usuario->login        = $request->input('dni');
            $usuario->password     = Hash::make("@bFtA#ab8G85D"); //CONTRASEÑA ESTÁNDAR
            $usuario->usertype_id  = 5; //TIPO DE USUARIO DE APODERADO
            $usuario->avatar       = "admin.jpg";
            $usuario->email        = $request->input('email');
            $usuario->state        = "H";
            $usuario->persona_id   = $apoderado->id;
            $usuario->save();
            //COMPROBAMOS SI EXISTE DETALLE ALUMNOAPODERADO CON ESE ALUMNO Y ESE APODERADO
            $detalle = AlumnoApoderado::where("alumno_id", "=", $request->alumno_id)
                        ->where("apoderado_id", "=", $apoderado->id)
                        ->first();
            //SI NO EXISTE CREAMOS EL DETALLEAPODERADO, DE LO CONTRARIO NO HACEMOS NADA PORQUE YA ESTÁ CREADO
            if($detalle==NULL) {
                $detallenuevo = new AlumnoApoderado();
                $detallenuevo->alumno_id = $request->alumno_id;
                $detallenuevo->apoderado_id = $apoderado->id;
                $detallenuevo->save();
                //ELIMINAMOS TODOS LOS DETALLES ANTERIORES MENOS EL QUE ACABAMOS DE CREAR
                $detallesanteriores = AlumnoApoderado::where("alumno_id", "=", $request->alumno_id)
                                        ->where("apoderado_id", "!=", $apoderado->id)
                                        ->get();
                if(count($detallesanteriores)) {
                    foreach ($detallesanteriores as $k) {
                        $k = AlumnoApoderado::find($k->id);
                        $k->delete();
                    }
                }
            }
        });
        return is_null($error) ? "OK" : $error;
    }

    public function show(Alumno $alumno)
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
        $alumno   = Persona::find($id);
        $correo   = Usuario::where("persona_id", "=", $alumno->id)->first()->email;
        $entidad  = 'Alumno';
        $formData = array('alumno.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('alumno', 'formData', 'entidad', 'boton', 'listar', 'correo'));
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
                'direccion'       => 'max:100',
                'telefono'        => 'max:9',
                'email'           => 'required|email|unique:usuario,email,'.$usuario->id.',id,deleted_at,NULL',
            )
        );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request, $id){
            $user                    = Auth::user();
            $local_id                = $user->persona->local_id;
            $alumno                  = Persona::find($id);
            $alumno->dni             = $request->input('dni');
            $alumno->nombres         = $request->input('nombres');
            $alumno->apellidopaterno = $request->input('apellidopaterno');
            $alumno->apellidomaterno = $request->input('apellidomaterno');
            $alumno->direccion       = $request->input('direccion');
            $alumno->fechanacimiento = $request->input('fechanacimiento')==""?NULL:$request->input('fechanacimiento');
            $alumno->telefono        = $request->input('telefono');
            $alumno->local_id        = $local_id;
            $alumno->save();
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
            $alumno = Persona::find($id);
            $alumno->delete();
            $usuario = Usuario::where("persona_id", "=", $alumno->id)->first();
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
        $entidad  = 'Alumno';
        $formData = array('route' => array('alumno.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
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
            $alumno = Persona::find($request->id);
            $usuario = Usuario::where("persona_id", "=", $alumno->id)->first();
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
        $entidad  = 'Alumno';
        $formData = array('route' => array('alumno.confirmaralterarestado', "id=" . $request->id, "estado=" . $request->estado), 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarAlterarestado')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function comprobarapoderado(Request $request) {
        $alumnoapoderado = AlumnoApoderado::where("alumno_id", "=", $request->id)->first();
        $retorno = "N";
        if($alumnoapoderado!==NULL) {
            $retorno = "S";
        }
        echo $retorno;
    }
}
