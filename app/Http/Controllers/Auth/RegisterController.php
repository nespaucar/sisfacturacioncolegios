<?php

namespace App\Http\Controllers\Auth;

use App\Usuario;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Persona;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectPath = 'registro';
    protected $redirectTo = 'registro';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm(){
        return view('auth.registrarUsuario');
    }

    //Handles registration request for seller
    public function register(Request $request)
    {

       //Validates data
        $this->validator($request->all())->validate();

        //Create seller
        $error = $this->create($request->all());

        return view('auth.registrarUsuario')->with(compact('error'));
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nombres' => 'required|string|max:100',
            'apellidopaterno' => 'required|string|max:100',
            'apellidomaterno' => 'required|string|max:100',
            'dni' => 'required|string|digits:8|unique:persona',
            'direccion' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:usuario',
            'password' => 'required|min:6|max:18|confirmed',
        ]);
    }

    protected function create(array $request)
    {
        //Creamos Persona
        
        $error = DB::transaction(function() use($request){
            $persona = new Persona();
            $persona->nombres = $request['nombres'];
            $persona->apellidopaterno = $request['apellidopaterno'];
            $persona->apellidomaterno = $request['apellidomaterno'];
            $persona->dni = $request['dni'];
            $persona->direccion = $request['direccion'];
            $persona->save();
            
            if($request['tipo'] == 1) {
                $avatar = 'admin.png';
            } else if($request['tipo'] == 2) {
                $avatar = 'profe.png';
            } else {
                $avatar = 'alumno.jpg';
            }
            $persona = DB::table('persona')->where('dni', '=', $request['dni'])->first();
            $usuario = new Usuario();
            $usuario->login = $request['dni'];
            $usuario->email = $request['email'];
            $usuario->usertype_id = $request['tipo'];            
            $usuario->persona_id = $persona->id;
            $usuario->state = 'H';
            $usuario->avatar = $avatar;
            $usuario->password = bcrypt($request['password']);
            $usuario->save();
        });

        return is_null($error) ? "OK" : "NO";
    }

    //Get the guard to authenticate Usuario
    protected function guard()
    {
        return Auth::guard('web_usuario');
    }
}
