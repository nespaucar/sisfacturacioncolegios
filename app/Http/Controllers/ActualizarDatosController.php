<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Hash;
use Validator;
use App\Persona;
use App\Usuario;
use App\Librerias\Libreria;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManagerStatic as Image;

class ActualizarDatosController extends Controller
{

    protected $folderview      = 'app.actualizardatos';
    protected $tituloAdmin     = 'Actualizar Datos';
    protected $rutas           = array(
            'update'   => 'actualizardatos.update',
            'index'  => 'actualizardatos.index',
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title            = $this->tituloAdmin;
        $ruta             = $this->rutas;

        $user             = Auth::user();

        if($user->persona_id !== null){

            $entidad          = 'Persona';
            $persona        = Persona::find($user->persona_id);
            $existe = Libreria::verificarExistencia($user->persona_id, 'persona');
            if ($existe !== true) {
                return $existe;
            }
            $listar         = Libreria::getParam($request->input('listar'), 'NO');
            $formData       = array('actualizardatos.update', $user->persona_id);
            $formData       = array('route' => $formData, 'method' => 'PUT','class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
            $boton          = 'Modificar';

            return view($this->folderview.'.user')->with(compact('persona', 'title', 'ruta', 'formData', 'entidad', 'boton', 'listar'));
        }

    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {

        $user             = Auth::user();

        if($user->persona_id !== null){

            $existe = Libreria::verificarExistencia($id, 'persona');
            if ($existe !== true) {
                return $existe;
            }
            $reglas = array(
                'dni' => 'required|digits:8',
                'dni' => [
                    'required',
                    Rule::unique('persona')->ignore($user->persona_id),
                ],
                'direccion' => 'required|max:50',
                'email' => 'required|max:100',
                'email' => [
                    'required',
                    Rule::unique('usuario')->ignore($user->id),
                ],
                'image' => 'required|image|mimes:jpg,png,jpeg,bmp|max:1024',
                );
            $validacion = Validator::make($request->all(),$reglas);
            if ($validacion->fails()) {
                return $validacion->messages()->toJson();
            } 
            $error = DB::transaction(function() use($request, $id){
                $user             = Auth::user();
                $persona                 = Persona::find($id);
                $persona->dni     = $request->input('dni');
                $persona->direccion     = $request->input('direccion');
                $usuario                 = Usuario::find($user->id);
                $usuario->email     = $request->input('email');
                $persona->save();
                $usuario->save();
            });

            $path = '';
            
            if ($request->hasFile('image')){
                if ($request->file('image')->isValid()){

                    $user = Auth::user();
                    $usuario = Usuario::find($user->id);

                    if(\File::exists('avatar/'.$usuario->avatar) && $usuario->avatar !== 'default_avatar.png'){
                        \File::delete('avatar/'.$usuario->avatar);
                    }
                    
                    $filename = Auth::id().'_'.time().'.'.$request->image->getClientOriginalExtension();
                    $path = public_path('avatar/'.$filename);

                    $file = $request->file('image');
                    Image::make($file)->fit(144, 144);//->save($path);
                    $file->move('avatar', $filename);
                    
                    $usuario->avatar = $filename;
                    $usuario->save();
                }
            }

            return is_null($error) ? "OK" : $error;

        }

    }

    public function avatar(Request $request){
        $user = Auth::user();
        $usuario = Usuario::find($user->id);
        return $usuario->avatar;
    }

}
