<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use Hash;
use File;
use App\Local;
use App\Nivel;
use App\Grado;
use App\Usuario;
use App\Persona;
use App\Conceptopago;
use App\Montoconceptopago;
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
        $local_id         = $user->persona->local_id;
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Local';
        $serie            = Libreria::getParam($request->input('serie'));
        $nombre           = Libreria::getParam($request->input('nombre'));
        $tipo             = Libreria::getParam($request->input('tipo'));
        $resultado        = Local::listar($serie, $nombre, $tipo, $local_id);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Serie Bol', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Serie Fact', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Serie Not', 'numero' => '1');
        $cabecera[]       = array('valor' => 'RUC', 'numero' => '1');
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
        $boton               = 'Registrar'; 
        $infoNiveles         = "";
        return view($this->folderview.'.mant')->with(compact('local', 'formData', 'entidad', 'boton', 'listar', 'infoNiveles'));
    }

    public function store(Request $request)
    {
        $now        = new \DateTime();
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $validacion = Validator::make($request->all(),
            array(
                'serie'       => 'required|numeric',
                'serie2'      => 'required|numeric',
                'serie3'      => 'required|numeric',
                'ruc'         => 'required|digits:11|unique:local,ruc,NULL,id,deleted_at,NULL',
                'nombre'      => 'required|max:80',
                'nombreusuario' => 'required|max:80',
                'dniusuario'  => 'required|digits:8',
                'razonsocial' => 'required|max:120',
                'descripcion' => 'max:100',
                'tipo'        => 'required|size:1',
                'logo'        => 'required|image|mimes:jpeg,png,bmp,jpg,JPEG,JPG,PNG,BMP|max:3000',
            )
        );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request, $now){
            $user                 = Auth::user();
            $local_id             = $user->persona->local_id;
            $local                = new Local();
            $local->serie         = $request->input('serie');
            $local->serie2        = $request->input('serie2');
            $local->serie3        = $request->input('serie3');
            $local->nombre        = $request->input('nombre');
            $local->ruc           = $request->input('ruc');
            $local->razonsocial   = $request->input('razonsocial');
            $local->descripcion   = $request->input('descripcion');
            $local->local_id      = $local_id;
            //SI SOY SUPERUSUARIO ID 1, ENTONCES PUEDO ELEGIR SI EL LOCAL ES NUEVO O DEPENDIENTE DEL LOCAL_ID DE SU USUARIO
            if($user->usertype_id == 1) {
                if($request->nuevo == "N") {
                    $local->local_id = NULL;
                }
            }
            $local->tipo          = $request->input('tipo');
            $local->logo          = "123";
            //$local->estado        = false;
            $local->save();

            //CREAMOS A LA PERSONA DEL USUARIO
            $administrador = new Persona();
            if($request->nombreu!=="") {
                $administrador->nombres = $request->nombreu;
                $administrador->apellidopaterno = $request->apellidopaternou;
                $administrador->apellidomaterno = $request->apellidomaternou;
            } else {
                $administrador->nombres = $request->nombreusuario;
                $administrador->apellidopaterno = "";
                $administrador->apellidomaterno = "";
            }
            $administrador->local_id = $local->id;
            //$administrador->tipo = "A";
            $administrador->dni = $request->dniusuario;
            $administrador->save();

            //CREAMOS AL USUARIO
            $adminuser              = new Usuario();
            $adminuser->login       = $local->ruc;
            $adminuser->password    = Hash::make($local->ruc);
            $adminuser->avatar      = "admin.jpg";
            $adminuser->email       = "@";
            $adminuser->state       = "H";
            $adminuser->persona_id  = $administrador->id;
            $adminuser->usertype_id = 4;//ADMINISTRADOR
            $adminuser->save();               

            if($request->hasFile("logo")) {
                if(file_exists(public_path() . "/../../html/colegios/logos/LOGO_" . $local->id . ".JPG")) {
                    File::delete(public_path() . "/../../html/colegios/logos/LOGO_" . $local->id . ".JPG");
                }
                $archivo = $request->file("logo");
                $archivo->move(public_path() . "/../../htdocs/facturacioncolegios/logos/", "LOGO_" . $local->id . ".JPG");
            }
            $local->logo = "LOGO_" . $local->id . ".JPG";
            $local->save();

            //ARRAYS NECESARIOS
            $arrayNiveles = array("INICIAL", "PRIMARIA", "SECUNDARIA", "GENERAL");
            $arrayGrados = array("2 años", "3 años", "4 años", "5 años", "1ro", "2do", "3ro", "4to", "5to", "6to");

            //CREAMOS LOS NIVELES
            for ($i=0; $i < 4; $i++) { 
                if($request->input("niv_" . $i) == "1") {
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
            }
            //CREAMOS LOS MONTOS CONCEPTOS DE PAGO
            //MENSUALIDAD
            $cpago1                    = Conceptopago::find(7);
            $detalle1                  = new Montoconceptopago();
            $detalle1->conceptopago_id = $cpago1->id;
            $detalle1->local_id        = $local->id;
            $detalle1->monto           = 0;
            $detalle1->save();
            //MATRÍCULA
            $cpago2                    = Conceptopago::find(6);
            $detalle2                  = new Montoconceptopago();
            $detalle2->conceptopago_id = $cpago2->id;
            $detalle2->local_id        = $local->id;
            $detalle2->monto           = 0;
            $detalle2->save();
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
        $boton    = 'Modificar';
        //OBTENGO NIVELES
        $infoNiveles = "";
        $niveles      = Nivel::where("local_id", "=", $id)->get();
        foreach ($niveles as $ni) {
            $infoNiveles .= '<div class="form-group">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-   <font style="font-size: 17px;">' . $ni->descripcion . '</font>
                </div>';
        }
        return view($this->folderview.'.mant')->with(compact('local', 'formData', 'entidad', 'boton', 'listar', 'infoNiveles'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'local');
        if ($existe !== true) {
            return $existe;
        }
        $validacion = Validator::make($request->all(),
            array(
                'serie'       => 'required|numeric',
                'serie2'      => 'required|numeric',
                'serie3'      => 'required|numeric',
                'ruc'         => 'required|digits:11|unique:local,ruc,'.$id.',id,deleted_at,NULL',
                'nombre'      => 'required|max:80',
                'razonsocial' => 'required|max:120',
                'descripcion' => 'max:100',
                'tipo'        => 'required|size:1',
                'logo'        => "image|mimes:jpeg,png,bmp,jpg,JPEG,JPG,PNG,BMP|max:3000",
            )
        );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request, $id){
            $user                 = Auth::user();
            $local_id             = $user->persona->local_id;
            $local                = Local::find($id);
            $local->serie         = $request->input('serie');
            $local->serie2        = $request->input('serie2');
            $local->serie3        = $request->input('serie3');
            $local->nombre        = $request->input('nombre');
            $local->ruc           = $request->input('ruc');
            $local->razonsocial   = $request->input('razonsocial');
            $local->descripcion   = $request->input('descripcion');
            $local->local_id      = $local_id;
            $local->tipo          = $request->input('tipo');
            $local->logo          = "123";
            $local->save();
            if($request->hasFile("logo")) {
                if(file_exists(public_path() . "/../../html/colegios/logos/LOGO_" . $local->id . ".JPG")) {
                    File::delete(public_path() . "/../../html/colegios/logos/LOGO_" . $local->id . ".JPG");
                }
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
