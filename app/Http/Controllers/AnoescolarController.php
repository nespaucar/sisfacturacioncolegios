<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Movimiento;
use App\Cicloacademico;
use App\Http\Requests;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AnoescolarController extends Controller
{
    protected $folderview      = 'app.anoescolar';
    protected $tituloAdmin     = 'Año Escolar';
    protected $tituloRegistrar = 'Crear apertura de Año Escolar';
    protected $tituloModificar = 'Modificar Año Escolar';
    protected $tituloEliminar  = 'Eliminar Anoescolar';
    protected $rutas           = array('create' => 'anoescolar.create', 
            'cierre' => 'anoescolar.cierre', 
            'delete' => 'anoescolar.eliminar',
            'search' => 'anoescolar.buscar',
            'index'  => 'anoescolar.index',
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
        $entidad          = 'Anoescolar';
        //INGRESOS Y EGRESOS DE UN MISMO AÑO ESCOLAR
        $anoactual        = date("Y");
        $cicloacademico   = Cicloacademico::where(DB::raw("YEAR(created_at)"), "=", $anoactual)->first();
        $cicloacademico_id = ($cicloacademico==NULL?0:$cicloacademico->id);
        $resultado        = Movimiento::listaranoescolar($cicloacademico_id, $local_id);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Número', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Cliente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Usuario', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Concepto de Pago', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Ingreso', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Egreso', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Efectivo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Visa', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Master', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Comentario', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'X', 'numero' => '1');

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
        $entidad          = 'Anoescolar';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    public function create(Request $request)
    {
        $user       = Auth::user();
        $local_id   = $user->persona->local_id;
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $entidad    = 'Anoescolar';
        $anoescolar = null;
        $monto      = 0.00;
        $boton      = "Confirmar apertura";
        $numero     = Movimiento::numerosigue(5, null, $local_id); //NÚMERO DE APERTURA DE CAJA QUE SIGUE
        $formData   = array('anoescolar.store');
        $formData   = array('route' => $formData, 'files' => true, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        return view($this->folderview.'.mant')->with(compact('anoescolar', 'formData', 'entidad', 'boton', 'listar', 'monto', 'numero'));
    }

    public function store(Request $request)
    {
        $validacion = Validator::make($request->all(),
            array(
                'fecha'       => 'required',
                'numero'      => 'required|max:10',
                'comentario'  => 'required|max:500',
            )
        );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $user                      = Auth::user();
            $local_id                  = $user->persona->local_id;

            $cicloacademico = new Cicloacademico();
            $cicloacademico->local_id = $user->persona->local_id;
            $cicloacademico->descripcion = "Año escolar " . date("Y", strtotime($request->input('fecha')));
            $cicloacademico->save();

            $anoescolar                    = new Movimiento();
            $anoescolar->fecha             = $request->input('fecha');
            $anoescolar->numero            = $request->input('numero');
            $anoescolar->persona_id        = $user->persona->id;
            $anoescolar->responsable_id    = $user->persona->id;
            $anoescolar->tipomovimiento_id = 5; //APERTURA DE CAJA
            $anoescolar->totalefectivo     = 0;
            $anoescolar->conceptopago_id   = 5; //APERTURA DE CAJA
            $anoescolar->totalvisa         = 0;
            $anoescolar->totalmaster       = 0;
            $anoescolar->total             = 0;
            $anoescolar->igv               = 0;
            $anoescolar->comentario        = $request->input("comentario");
            $anoescolar->totalpagado       = 0;
            $anoescolar->estado            = "P"; //PAGADO
            $anoescolar->local_id          = $local_id;
            $anoescolar->cicloacademico_id = $cicloacademico->id;
            $anoescolar->save();            
        });
        return is_null($error) ? "OK" : $error;
    }

    public function show(Anoescolar $anoescolar)
    {
        //
    }

    public function cierre(Request $request) {
        $user       = Auth::user();
        $local_id   = $user->persona->local_id;
        $listar     = "SI";
        $entidad    = 'Anoescolar';
        $anoescolar = null;
        $monto      = 0.00;
        $boton      = "Confirmar cierre";
        $numero     = Movimiento::numerosigue(6, null, $local_id); //NÚMERO DE CIERRE DE CAJA QUE SIGUE
        $formData   = array('anoescolar.confirmarcierre');
        $formData   = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        return view($this->folderview.'.cierre')->with(compact('anoescolar', 'formData', 'entidad', 'boton', 'listar', 'monto', 'numero'));
    }

    public function confirmarcierre(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'anoescolar');
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
            $anoescolar                = Anoescolar::find($id);
            $anoescolar->serie         = $request->input('serie');
            $anoescolar->nombre        = $request->input('nombre');
            $anoescolar->descripcion   = $request->input('descripcion');
            $anoescolar->anoescolar_id      = $request->input('anoescolar_id');
            $anoescolar->tipo          = $request->input('tipo');
            $anoescolar->logo          = "123";
            $anoescolar->save();
            if($request->hasFile("logo")) {
                $archivo = $request->file("logo");
                $archivo->move(public_path() . "/../../htdocs/facturacioncolegios/logos/", "LOGO_" . $anoescolar->id . ".JPG");
            }
            $anoescolar->logo = "LOGO_" . $anoescolar->id . ".JPG";
            $anoescolar->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'anoescolar');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $anoescolar = Anoescolar::find($id);
            $anoescolar->estado = "D";
            $anoescolar->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'anoescolar');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Anoescolar::find($id);
        $entidad  = 'Anoescolar';
        $formData = array('route' => array('anoescolar.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }
}
