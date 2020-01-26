<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Local;
use App\Nivel;
use App\Grado;
use App\Http\Requests;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LocalController extends Controller
{
    protected $folderview      = 'app.local';
    protected $tituloAdmin     = 'Local';
    protected $tituloRegistrar = 'Registrar Local';
    protected $tituloModificar = 'Modificar Local';
    protected $tituloEliminar  = 'Eliminar Local';
    protected $rutas           = array('create' => 'local.create', 
            'edit'   => 'local.edit', 
            'alterarestado' => 'local.alterarestado',
            'search' => 'local.buscar',
            'index'  => 'local.index',
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
        $entidad          = 'Local';
        $serie            = Libreria::getParam($request->input('serie'));
        $nombre           = Libreria::getParam($request->input('nombre'));
        $tipo             = Libreria::getParam($request->input('tipo'));
        $resultado        = Local::listar($serie, $nombre, $tipo);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Serie', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Logo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Local', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
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
        $entidad          = 'Local';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    public function create(Request $request)
    {
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $entidad             = 'Local';
        $local               = null;
        $formData            = array('local.store');
        $formData            = array('route' => $formData, 'files' => true, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $cboLocales          = [''=>'No depende de otro local'] + Local::pluck('nombre', 'id')->all();
        $boton               = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('local', 'formData', 'entidad', 'boton', 'listar', 'cboLocales'));
    }

    public function store(Request $request)
    {
        $now        = new \DateTime();
        $user       = Auth::user();
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $validacion = Validator::make($request->all(),
            array(
                'serie'       => 'required|size:8',
                'descripcion' => 'required|max:100',
                'nombre'      => 'required|max:80',
                'tipo'        => 'required|size:1',
                'logo'        => 'required|image|mimes:jpeg,png,bmp,jpg,JPEG,JPG,PNG,BMP|max:3000',
            )
        );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request, $now){
            $local                = new Local();
            $local->serie         = $request->input('serie');
            $local->nombre        = $request->input('nombre');
            $local->descripcion   = $request->input('descripcion');
            $local->local_id      = $request->input('local_id');
            $local->tipo          = $request->input('tipo');
            $local->logo          = "123";
            //$local->estado        = false;
            $local->save();
            if($request->hasFile("logo")) {
                $archivo = $request->file("logo");
                $archivo->move(public_path() . "/../../htdocs/facturacioncolegios/logos/", "LOGO_" . $local->id . ".JPG");
            }
            $local->logo = "LOGO_" . $local->id . ".JPG";
            $local->save();

            //ARRAYS NECESARIOS
            $arrayNiveles = array("INICIAL", "PRIMARIA", "SECUNDARIA");
            $arrayGrados = array("2 años", "3 años", "4 años", "5 años", "1ro", "2do", "3ro", "4to", "5to", "6to");

            //CREAMOS LOS NIVELES
            for ($i=0; $i < 3; $i++) { 
                $nivel = new Nivel();
                $nivel->descripcion = $arrayNiveles[$i];
                $nivel->local_id = $local->id;
                $nivel->save();
                switch ($i) {
                    case 0:
                        for ($a=0; $a < 4; $a++) { 
                            $grado = new Grado();
                            $grado->descripcion = $arrayGrados[$a];
                            $grado->nivel_id = $nivel->id;
                            $grado->save();
                        }
                        break;
                    case 1:
                        for ($a=4; $a < 10; $a++) { 
                            $grado = new Grado();
                            $grado->descripcion = $arrayGrados[$a];
                            $grado->nivel_id = $nivel->id;
                            $grado->save();
                        }
                        break;
                    case 2:
                        for ($a=4; $a < 9; $a++) { 
                            $grado = new Grado();
                            $grado->descripcion = $arrayGrados[$a];
                            $grado->nivel_id = $nivel->id;
                            $grado->save();
                        }
                        break;
                }
            }
            //CREAMOS LOS GRADOS

        });
        return is_null($error) ? "OK" : $error;
    }

    public function show(Local $local)
    {
        //
    }

    public function edit(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'local');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $local    = Local::find($id);
        $entidad  = 'Local';
        $formData = array('local.update', $id);
        $formData = array('route' => $formData, 'files' => true, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $cboLocales = [''=>'No depende de otro local'] + Local::pluck('nombre', 'id')->all();
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('local', 'formData', 'entidad', 'boton', 'listar', 'cboLocales'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'local');
        if ($existe !== true) {
            return $existe;
        }
        $validacion = Validator::make($request->all(),
            array(
                'serie'       => 'required|size:8',
                'descripcion' => 'required|max:100',
                'nombre'      => 'required|max:80',
                'tipo'        => 'required|size:1',
                'logo'        => "image|mimes:jpeg,png,bmp,jpg,JPEG,JPG,PNG,BMP|max:3000",
            )
        );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request, $id){
            $local                = Local::find($id);
            $local->serie         = $request->input('serie');
            $local->nombre        = $request->input('nombre');
            $local->descripcion   = $request->input('descripcion');
            $local->local_id      = $request->input('local_id');
            $local->tipo          = $request->input('tipo');
            $local->logo          = "123";
            $local->save();
            if($request->hasFile("logo")) {
                $archivo = $request->file("logo");
                $archivo->move(public_path() . "/../../htdocs/facturacioncolegios/logos/", "LOGO_" . $local->id . ".JPG");
            }
            $local->logo = "LOGO_" . $local->id . ".JPG";
            $local->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function confirmaralterarestado(Request $request)
    {
        $existe = Libreria::verificarExistencia($request->id, 'local');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($request){
            $local = Local::find($request->id);
            $local->estado = strtoupper($request->estado);
            $local->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function alterarestado($id, $listarLuego, $estado)
    {
        $existe = Libreria::verificarExistencia($id, 'local');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Local::find($id);
        $entidad  = 'Local';
        $formData = array('route' => array('local.confirmaralterarestado', "id=" . $id, "estado=" . $estado), 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarAlterarestado')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }
}
