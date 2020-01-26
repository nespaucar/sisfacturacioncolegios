<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Hash;
use Validator;
use App\Alumno;
use App\Usuario;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;

class AlumnoController extends Controller
{

    protected $folderview      = 'app.alumno';
    protected $tituloAdmin     = 'Alumno';
    protected $tituloRegistrar = 'Registrar alumno';
    protected $tituloModificar = 'Modificar alumno';
    protected $tituloEliminar  = 'Eliminar alumno';
    protected $tituloPassword  = 'Restablecer contraseña de alumno';
    protected $rutas           = array('create' => 'alumno.create', 
            'edit'   => 'alumno.edit', 
            'delete' => 'alumno.eliminar',
            'search' => 'alumno.buscar',
            'index'  => 'alumno.index',
            'password' => 'alumno.password',
            'restablecer' => 'alumno.restablecerPassword',
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
        $pagina             = $request->input('page');
        $filas              = $request->input('filas');
        $entidad            = 'Alumno';
        $codigo             = Libreria::getParam($request->input('codigo'));
        $nombre             = Libreria::getParam($request->input('nombre'));
        $escuela_id         = Libreria::getParam($request->input('escuela1_id'));
        $facultad_id        = Libreria::getParam($request->input('facultad_id'));
        $resultado          = Alumno::listar($codigo, $nombre, $escuela_id, $facultad_id);
        $lista              = $resultado->get();
        $cabecera           = array();
        $cabecera[]         = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DNI', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Código', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Alumno', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Escuela', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Especialidad', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Situación', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '3');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_password  = $this->tituloPassword;
        $ruta             = $this->rutas;
        $cboSituacion     = array('ES'=>'Estudiante','EG' => 'Egresado', 'GR' => 'Graduado');
        if (count($lista) > 0) {
            $clsLibreria     = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            $lista           = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_password' , 'titulo_eliminar', 'ruta', 'cboSituacion'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad', 'cboSituacion'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entidad          = 'Alumno';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $cboFacultad      = [''=>'Todas'] + Facultad::pluck('nombre', 'id')->all();
        $cboEscuela       = [''=>'Todas'];
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboFacultad', 'cboEscuela'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $entidad        = 'Alumno';
        $alumno        = null;
        $cboEscuela = array('' => 'Seleccione') + Escuela::pluck('nombre', 'id')->all();
        $cboEspecialidad = array('' => 'Seleccione');
        $cboSituacion         = [''=>'Seleccione']+ array('ES'=>'Estudiante','EG' => 'Egresado', 'GR' => 'Graduado');
        $formData       = array('alumno.store');
        $formData       = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('alumno', 'formData', 'entidad', 'boton', 'listar', 'cboEscuela','cboEspecialidad','cboSituacion'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas = array(
            'codigo'       => 'required|max:60|unique:alumno,codigo,NULL,id,deleted_at,NULL',
            'nombres' => 'required|max:100',
            'apellidopaterno' => 'required|max:100',
            'apellidomaterno' => 'required|max:100',
            'dni' => 'required|max:8',
            'fechanacimiento' => 'required',
            'direccion' => 'required|max:100',
            'telefono' => 'required|max:12',
            'escuela_id' => 'required|integer|exists:escuela,id,deleted_at,NULL',
            'situacion' => 'required'
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $alumno               = new Alumno();
            $alumno->codigo        = $request->input('codigo');
            $alumno->nombres     = $request->input('nombres');
            $alumno->apellidopaterno     = $request->input('apellidopaterno');
            $alumno->apellidomaterno     = $request->input('apellidomaterno');
            $alumno->dni     = $request->input('dni');
            $alumno->fechanacimiento     = $request->input('fechanacimiento');
            $alumno->direccion     = $request->input('direccion');
            $alumno->telefono     = $request->input('telefono');
            $alumno->email     = $request->input('email');
            $alumno->escuela_id  = $request->input('escuela_id');
            $alumno->especialidad_id    = $request->input('especialidad_id');
            $alumno->situacion    = $request->input('situacion');
            $alumno->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'alumno');
        if ($existe !== true) {
            return $existe;
        }
        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $alumno        = Alumno::find($id);
        $entidad        = 'Alumno';
        $cboEscuela = array('' => 'Seleccione') + Escuela::pluck('nombre', 'id')->all();
        //$cboEspecialidad = array('' => 'Seleccione') + Especialidad::pluck('nombre', 'id')->all();
        $cboEspecialidad = array('' => 'Seleccione') + Especialidad::where('escuela_id', '=', $alumno->escuela_id)->pluck('nombre', 'id')->all();
        $cboSituacion         = [''=>'Seleccione']+ array('ES'=>'Estudiante','EG' => 'Egresado', 'GR' => 'Graduado');
        $formData       = array('alumno.update', $id);
        $formData       = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('alumno', 'formData', 'entidad', 'boton', 'listar', 'cboEscuela','cboEspecialidad','cboSituacion'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'alumno');
        if ($existe !== true) {
            return $existe;
        }
        $reglas = array(
            'codigo'       => 'required|max:60|unique:alumno,codigo,'.$id.',id,deleted_at,NULL',
            'nombres' => 'required|max:100',
            'apellidopaterno' => 'required|max:100',
            'apellidomaterno' => 'required|max:100',
            'dni' => 'required|max:8',
            'fechanacimiento' => 'required|max:100',
            'direccion' => 'required|max:50',
            'telefono' => 'required|max:12',
            'escuela_id' => 'required|integer|exists:escuela,id,deleted_at,NULL',
            'situacion' => 'required'
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $alumno                 = Alumno::find($id);
            $alumno->codigo        = $request->input('codigo');
            $alumno->nombres     = $request->input('nombres');
            $alumno->apellidopaterno     = $request->input('apellidopaterno');
            $alumno->apellidomaterno     = $request->input('apellidomaterno');
            $alumno->dni     = $request->input('dni');
            $alumno->fechanacimiento     = $request->input('fechanacimiento');
            $alumno->direccion     = $request->input('direccion');
            $alumno->telefono     = $request->input('telefono');
            $alumno->email     = $request->input('email');
            $alumno->escuela_id  = $request->input('escuela_id');
            $alumno->especialidad_id    = $request->input('especialidad_id');
            $alumno->situacion    = $request->input('situacion');
            $alumno->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'alumno');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $alumno = Alumno::find($id);
            $alumno->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    /**
     * Función para confirmar la eliminación de un registrlo
     * @param  integer $id          id del registro a intentar eliminar
     * @param  string $listarLuego consultar si luego de eliminar se listará
     * @return html              se retorna html, con la ventana de confirmar eliminar
     */
    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'alumno');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Alumno::find($id);
        $entidad  = 'Alumno';
        $formData = array('route' => array('alumno.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function restablecerPassword($id)
    {
        $existe = Libreria::verificarExistencia($id, 'usuario');
        if ($existe !== true) {
            return $existe;
        }

        $error = DB::transaction(function() use($id){
            $usuario = Usuario::find($id);
            $usuario->password = bcrypt($usuario->login);
            $usuario->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function password($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'alumno');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Alumno::find($id);

        $usuario  = Usuario::where('alumno_id', "=", $id)->first();
        $id = $usuario->id;

        $entidad  = 'Alumno';
        $formData = array('route' => array('alumno.restablecer', $id), 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Restablecer Contraseña';
        return view('app.confirmarPassword')->with(compact('modelo','formData', 'entidad', 'boton', 'listar'));
    }
    
    //cargar datos de especialidad segun el id de escuela
    public function cargarselect($idselect, Request $request)
    {
        $entidad = $request->get('entidad');
        $t = '';
        $tt = '';

        if($request->get('t') == ''){
            $t = '_';
            $tt = '2';
        }
        $retorno = '<select class="form-control input-sm" id="' . $t . $entidad . '_id" name="' . $t . $entidad . '_id"';
        $cbo = Especialidad::select('id', 'nombre')
            ->where('escuela_id', '=', $idselect)
            ->get();    

        $retorno .= '><option value="" selected="selected">Seleccione</option>';

        foreach ($cbo as $row) {
            $retorno .= '<option value="' . $row['id'] .  '">' . $row['nombre'] . '</option>';
        }
        $retorno .= '</select></div>';

        echo $retorno;
    }
    
    public function cambiarsituacion(Request $request) {
        $idalumno          = $request->get('idalumno');
        $situacion         = $request->get('situacion');
        $alumno            = Alumno::find($idalumno);
        $alumno->situacion = $situacion;
        $alumno->save();
    }
    
    public function cargaralumnos(Request $request) {
        $nomalumno  = $request->get('nomalumno');
        $alumnos    = Alumno::listar(null, $nomalumno, null, null)->get();
        $options = '';
        if(count($alumnos) == 0){
            $options = '<option value="">Alumno no presente</option>';
        } else {
            foreach ($alumnos as $alumno) {
                $options .= '<option value="' . $alumno->id . '">' . $alumno->nombres . ' ' . $alumno->apellidopaterno . ' ' . $alumno->apellidomaterno . '</option>';
            }
        }            
        echo $options;        
    }
}
