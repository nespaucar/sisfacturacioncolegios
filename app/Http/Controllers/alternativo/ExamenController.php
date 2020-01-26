<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Alumno;
use App\Examen;
use App\Tipoexamen;
use App\Pregunta;
use App\Alternativa;
use App\Direccion;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ExamenController extends Controller
{
    protected $folderview      = 'app.examen';
    protected $tituloAdmin     = 'Examen';
    protected $tituloRegistrar = 'Registrar examen';
    protected $tituloModificar = 'Modificar examen';
    protected $tituloEliminar  = 'Eliminar examen';
    protected $rutas           = array('create' => 'examen.create', 
            'edit'   => 'examen.edit', 
            'delete' => 'examen.eliminar',
            'search' => 'examen.buscar',
            'index'  => 'examen.index',
            'listarpreguntas' => 'examen.listarpreguntas',
            'nuevapregunta' => 'examen.nuevapregunta',
            'eliminarpregunta' => 'examen.eliminarpregunta',
            'listaralternativas' => 'examen.listaralternativas',
            'nuevaalternativa' => 'examen.nuevaalternativa',
            'eliminaralternativa' => 'examen.eliminaralternativa',
            'listardirecciones' => 'examen.listardirecciones',
            'nuevadireccion' => 'examen.nuevadireccion',
            'eliminardireccion' => 'examen.eliminardireccion',
            'cargarselect' => 'examen.cargarselect',
            'alternativacorrecta' => 'examen.alternativacorrecta',
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
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Examen';
        $nombre           = Libreria::getParam($request->input('nombre'));
        $tipoexamen_id  = Libreria::getParam($request->input('tipoexamen_id'));
        $facultad_id      = Libreria::getParam($request->input('_facultad_id'));
        $escuela_id       = Libreria::getParam($request->input('_escuela_id'));
        $especialidad_id  = Libreria::getParam($request->input('_especialidad_id'));
        $resultado        = Examen::listar($nombre, $tipoexamen_id, $facultad_id, $escuela_id, $especialidad_id);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Objetivo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Preguntas', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Direcciones', 'numero' => '1');
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entidad          = 'Examen';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $cboTipoExamen  = [''=>'Todos'] + Tipoexamen::pluck('nombre', 'id')->all();
        $cboFacultad     = [''=>'Seleccione'] + Facultad::pluck('nombre', 'id')->all();
        $cboEscuela      = [''=>'Seleccione'];
        $cboEspecialidad = [''=>'Seleccione'];
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboTipoExamen', 'cboFacultad', 'cboEscuela', 'cboEspecialidad'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $entidad             = 'Examen';
        $cboTipoExamen     = [''=>'Seleccione una categoría'] + Tipoexamen::pluck('nombre', 'id')->all();
        $examen            = null;
        $formData            = array('examen.store');
        $formData            = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('examen', 'formData', 'entidad', 'boton', 'cboTipoExamen', 'listar'));
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
        $validacion = Validator::make($request->all(),
            array(
                'nombre'            => 'required|max:60',
                'objetivo'          => 'required|max:200',
                'tipoexamen_id'   => 'required'
                )
            );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $examen                  = new Examen();
            $examen->nombre          = $request->input('nombre');
            $examen->objetivo        = $request->input('objetivo');
            $examen->tipoexamen_id = $request->input('tipoexamen_id');
            $examen->save();
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
    public function edit(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'examen');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $examen = Examen::find($id);
        $entidad  = 'Examen';
        $cboTipoExamen     = [''=>'Seleccione una categoría'] + Tipoexamen::pluck('nombre', 'id')->all();
        $formData = array('examen.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('examen', 'formData', 'entidad', 'boton', 'cboTipoExamen', 'listar'));
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
        $existe = Libreria::verificarExistencia($id, 'examen');
        if ($existe !== true) {
            return $existe;
        }
        $validacion = Validator::make($request->all(),
            array(
                'nombre'            => 'required|max:60',
                'objetivo'          => 'required|max:200',
                'tipoexamen_id'   => 'required'
                )
            );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $examen                 = Examen::find($id);
            $examen->nombre           = $request->input('nombre');
            $examen->objetivo       = $request->input('objetivo');
            $examen->tipoexamen_id = $request->input('tipoexamen_id');
            $examen->save();
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
        $existe = Libreria::verificarExistencia($id, 'examen');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $examen = Examen::find($id);
            $examen->delete();
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
        $existe = Libreria::verificarExistencia($id, 'examen');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Examen::find($id);
        $mensaje = '<p class="text-inverse">¿Esta seguro de eliminar el registro "'.$modelo->nombre.'"?</p>';
        $entidad  = 'Examen';
        $formData = array('route' => array('examen.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }

    //----------PREGUNTAS

    public function listarpreguntas($examen_id, Request $request)
    {
        $resultado        = Pregunta::listar($examen_id);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Eliminar', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Alternativas', 'numero' => '1');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $ruta             = $this->rutas;
        $inicio           = 0;
        if (count($lista) > 0) {
            return view($this->folderview.'.preguntas')->with(compact('lista', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta', 'inicio', 'examen_id'));
        }
        return view($this->folderview.'.preguntas')->with(compact('lista', 'entidad', 'examen_id', 'ruta'));
    }

    public function nuevapregunta($examen_id, Request $request)
    {
        $pregunta              = new Pregunta();
        $pregunta->nombre      = $request->get('pregunta');
        $pregunta->tipo        = $request->get('tipopregunta');
        $pregunta->examen_id = $examen_id;
        $pregunta->save();

        echo $this->retornarTablaPreguntas($examen_id);
    }

    public function eliminarpregunta($id, $examen_id)
    {
        $existe = Libreria::verificarExistencia($id, 'pregunta');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $pregunta = Pregunta::find($id);
            $pregunta->delete();
        });
        echo $this->retornarTablaPreguntas($examen_id);
    }

    public function retornarTablaPreguntas($examen_id)
    {
        $resultado        = Pregunta::listar($examen_id);
        $lista            = $resultado->get();

        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Eliminar', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Alternativas', 'numero' => '1');
        
        $titulo_modificar      = $this->tituloModificar;
        $titulo_eliminar       = $this->tituloEliminar;
        $ruta                  = $this->rutas;
        $inicio                = 0;

        if(count($lista) == 0) {
            return '<h3 class="text-warning">No se encontraron resultados.</h3>';
        } else {
            $tabla = '<table id="example1" class="table table-bordered table-striped table-condensed table-hover">
                <thead>
                    <tr>';
                    foreach($cabecera as $key => $value) {
                        $tabla .= '<th ';
                        if((int)$value['numero'] > 1) {
                            $tabla .= 'colspan="'. $value['numero'] . '"';
                        }
                        $tabla .= '>' . $value['valor'] . '</th>';
                    }
                $tabla .= '</tr>
                </thead>
                <tbody>';
                    $contador = $inicio + 1;
                    foreach ($lista as $key => $value) {
                    $tabla .= '<tr>
                        <td>'. $contador . '</td>
                        <td>'. $value->nombre . "</td>
                        <td>";
                    $tabla .= '<button onclick=\'gestionpa(2, "pregunta", ' . $value->id . ',' . $examen_id . ');\' class="btn btn-xs btn-danger" type="button"><div class="glyphicon glyphicon-remove"></div> Eliminar</button>';
                    $tabla .= '</td>
                        <td>';

                        if($value->tipo == 1) {
                            $tabla .= '<a href="#carousel-ejemplo" style="btn btn-default btn-xs" data-slide="next" onclick=\'gestionpa(3, "alternativa", "", ' . $value->id . ');\'><div class="glyphicon glyphicon-list"></div> Alternativas</a>';
                        } else {
                            $tabla .= '<a href="javascript:void(0)" style="btn btn-default btn-xs">Libre</a>';
                        }
                    
                    $tabla .= "</td>
                    </tr>";
                    $contador = $contador + 1;
                    }
                $tabla .= '</tbody>
                <tfoot>
                    <tr>';
                    foreach($cabecera as $key => $value) {
                        $tabla .= '<th ';
                        if((int)$value['numero'] > 1) {
                            $tabla .= 'colspan="'. $value['numero'] . '"';
                        }
                        $tabla .= '>' . $value['valor'] . '</th>';
                    }
                    $tabla .= '</tr>
                </tfoot>
            </table>';
            return $tabla;
        }
    }

    //----------ALTERNATIVAS


    public function listaralternativas($pregunta_id, Request $request)
    {
        $resultado        = Alternativa::listar($pregunta_id);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Respuesta', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Eliminar', 'numero' => '1');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $ruta             = $this->rutas;
        $inicio           = 0;
        if (count($lista) > 0) {
            return view($this->folderview.'.preguntas')->with(compact('lista', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta', 'inicio', 'examen_id'));
        }
        return view($this->folderview.'.preguntas')->with(compact('lista', 'entidad', 'examen_id', 'ruta'));
    }

    public function nuevaalternativa($pregunta_id, Request $request)
    {
        $alternativa              = new Alternativa();
        $alternativa->nombre      = $request->get('alternativa');
        $alternativa->correcta    = false;
        $alternativa->pregunta_id = $pregunta_id;
        $alternativa->save();

        echo $this->retornarTablaAlternativas($pregunta_id);
    }

    public function eliminarAlternativa($id, $pregunta_id)
    {
        $existe = Libreria::verificarExistencia($id, 'alternativa');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $alternativa = Alternativa::find($id);
            $alternativa->delete();
        });
        echo $this->retornarTablaAlternativas($pregunta_id);
    }

    public function retornarTablaAlternativas($pregunta_id)
    {
        $resultado        = Alternativa::listar($pregunta_id);
        $lista            = $resultado->get();

        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Respuesta', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Eliminar', 'numero' => '1');
        
        $titulo_modificar      = $this->tituloModificar;
        $titulo_eliminar       = $this->tituloEliminar;
        $ruta                  = $this->rutas;
        $inicio                = 0;

        $pregunta = Pregunta::find($pregunta_id);

        $tabla = '<b>Altenativas para la pregunta: </b>' . $pregunta->nombre . '<br><br>';

        $tabla .= '
            <form method="GET" action="#" accept-charset="UTF-8" onsubmit="return false;" class="form-horizontal" id="formnuevaalternativa">
                <div class="form-group">
                    <label for="alternativa" class="col-lg-2 col-md-2 col-sm-2 control-label input-sm">Alternativa:</label>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <input class="form-control input-sm" id="alternativa" name="alternativa" type="text" value="">
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1">
                        <button class="btn btn-info input-sm waves-effect waves-light m-l-10 btn-md btnAnadir" onclick=\'gestionpa(1, "alternativa", "", ' . $pregunta_id . ');\' type="button"><i class="glyphicon glyphicon-plus"></i></button>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2">
                        <button class="correcto btn btn-success input-sm waves-effect waves-light m-l-10 btn-md hidden" onclick="#" type="button"><i class="glyphicon glyphicon-check"></i> ¡Correcto!</button>
                    </div>
                </div>                  
            </form><br>';
                

        if(count($lista) == 0) {
            return $tabla . '<h3 class="text-warning">No hay alternativas para esta pregunta.</h3>';
        } else {
            $tabla .= '<table id="example1" class="table table-bordered table-striped table-condensed table-hover">
                <thead>
                    <tr>';
                    foreach($cabecera as $key => $value) {
                        $tabla .= '<th ';
                        if((int)$value['numero'] > 1) {
                            $tabla .= 'colspan="'. $value['numero'] . '"';
                        }
                        $tabla .= '>' . $value['valor'] . '</th>';
                    }
                $tabla .= '</tr>
                </thead>
                <tbody>';
                    $contador = $inicio + 1;
                    foreach ($lista as $key => $value) {
                    $tabla .= '<tr>
                        <td>'. $contador . '</td>
                        <td>'. $value->nombre . "</td>
                        <td>";

                    $icon = 'remove';
                    $color = 'danger';

                    if($value->correcto == 1){
                        $icon = 'ok';
                        $color = 'success';
                    }

                    $tabla .= '<center><button id="respuesta' . $value->id . '" onclick=\'correcto(' . $value->id . ', ' . $pregunta_id . ');\' class="respuesta btn btn-xs btn-' . $color . '" type="button"><div class="glyphicon glyphicon-' . $icon . '"></div></button>';
                    $tabla .= '</td></center><td>';
                    $tabla .= '<button onclick=\'gestionpa(2, "alternativa", ' . $value->id . ', ' . $pregunta_id . ');\' class="btn btn-xs btn-danger" type="button"><div class="glyphicon glyphicon-remove"></div> Eliminar</button>';
                    $tabla .= '</td></tr>';
                    $contador = $contador + 1;
                    }
                $tabla .= '</tbody>
                <tfoot>
                    <tr>';
                    foreach($cabecera as $key => $value) {
                        $tabla .= '<th ';
                        if((int)$value['numero'] > 1) {
                            $tabla .= 'colspan="'. $value['numero'] . '"';
                        }
                        $tabla .= '>' . $value['valor'] . '</th>';
                    }
                    $tabla .= '</tr>
                </tfoot>
            </table>';
            return $tabla;
        }
    }

    //----------DIRECCIONES

    public function listardirecciones($examen_id, Request $request)
    {
        $resultado        = Direccion::listar($examen_id);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Eliminar', 'numero' => '1');

        $cboFacultad      = array('' => 'Seleccione') + Facultad::pluck('nombre', 'id')->all();
        $cboEscuela      = array('' => 'Seleccione');
        $cboEspecialidad      = array('' => 'Seleccione');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $ruta             = $this->rutas;
        $inicio           = 0;
        if (count($lista) > 0) {
            return view($this->folderview.'.direcciones')->with(compact('lista', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta', 'inicio', 'examen_id', 'cboFacultad', 'cboEscuela', 'cboEspecialidad'));
        }
        return view($this->folderview.'.direcciones')->with(compact('lista', 'entidad', 'examen_id', 'ruta', 'cboFacultad', 'cboEscuela', 'cboEspecialidad'));
    }

    public function nuevadireccion($examen_id, Request $request)
    {
        $direccion                   = new Direccion();
        $direccion->examen_id      = $examen_id;
        $direccion->facultad_id      = $request->get('facultad_id');
        $direccion->escuela_id       = $request->get('escuela_id');
        $direccion->especialidad_id  = $request->get('especialidad_id');
        $direccion->save();

        echo $this->retornarTablaDirecciones($examen_id);
    }

    public function eliminardireccion($id, $examen_id)
    {
        $existe = Libreria::verificarExistencia($id, 'direccion');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $direccion = Direccion::find($id);
            $direccion->delete();
        });
        echo $this->retornarTablaDirecciones($examen_id);
    }

    public function retornarTablaDirecciones($examen_id)
    {
        $resultado        = Direccion::listar($examen_id);
        $lista            = $resultado->get();

        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Eliminar', 'numero' => '1');
        
        $titulo_modificar      = $this->tituloModificar;
        $titulo_eliminar       = $this->tituloEliminar;
        $ruta                  = $this->rutas;
        $inicio                = 0;

        if(count($lista) == 0) {
            return '<h3 class="text-warning">No se encontraron resultados.</h3>';
        } else {
            $tabla = '<table id="example1" class="table table-bordered table-striped table-condensed table-hover">
                <thead>
                    <tr>';
                    foreach($cabecera as $key => $value) {
                        $tabla .= '<th ';
                        if((int)$value['numero'] > 1) {
                            $tabla .= 'colspan="'. $value['numero'] . '"';
                        }
                        $tabla .= '>' . $value['valor'] . '</th>';
                    }
                $tabla .= '</tr>
                </thead>
                <tbody>';
                    $contador = $inicio + 1;
                    foreach ($lista as $key => $value) {
                    //Formo la ruta de la direccion
                    $facultadx = Facultad::find($value->facultad_id);
                    $escuelax = Escuela::find($value->escuela_id);
                    $especialidadx = Especialidad::find($value->especialidad_id);

                    $rutadireccion = '';
                    if($facultadx != null) {
                        $rutadireccion .= $facultadx->nombre;
                    } if($escuelax != null) {
                        $rutadireccion .= ' -> ' . $escuelax->nombre;
                    } if($especialidadx != null) {
                        $rutadireccion .= ' -> ' . $especialidadx->nombre;
                    }

                    $tabla .= '<tr>
                        <td>'. $contador . '</td>
                        <td>'. $rutadireccion .  "</td>
                        <td>";
                    $tabla .= '<button onclick=\'gestionpa(' . $examen_id . ',' . $value->id . ', 2);\' class="btn btn-xs btn-danger" type="button"><div class="glyphicon glyphicon-remove"></div> Eliminar</button></td></tr>';
                    $contador = $contador + 1;
                    }
                $tabla .= '</tbody>
                <tfoot>
                    <tr>';
                    foreach($cabecera as $key => $value) {
                        $tabla .= '<th ';
                        if((int)$value['numero'] > 1) {
                            $tabla .= 'colspan="'. $value['numero'] . '"';
                        }
                        $tabla .= '>' . $value['valor'] . '</th>';
                    }
                    $tabla .= '</tr>
                </tfoot>
            </table>';
            return $tabla;
        }
    }

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
        if($entidad == 'escuela'){
            $cbo = Escuela::select('id', 'nombre')
            ->where('facultad_id', '=', $idselect)
            ->get();
            $retorno .= ' onchange=\'cargarselect' . $tt . '("especialidad")\'';
        } else {
            $cbo = Especialidad::select('id', 'nombre')
            ->where('escuela_id', '=', $idselect)
            ->get();
        }      

        $retorno .= '><option value="" selected="selected">Seleccione</option>';

        foreach ($cbo as $row) {
            $retorno .= '<option value="' . $row['id'] .  '">' . $row['nombre'] . '</option>';
        }
        $retorno .= '</select></div>';

        echo $retorno;
    }

    public function alternativacorrecta(Request $request) 
    {
        $alternativa_id = $request->get('alternativa_id');
        $pregunta_id = $request->get('pregunta_id');

        Alternativa::where('pregunta_id', $pregunta_id)->update(['correcto' => false]);

        $alternativa           = Alternativa::find($alternativa_id);
        $alternativa->correcto = true;
        $alternativa->save();
    }

    public function eliminarexamen(Request $request) {
        Alternativa::truncate();
        Pregunta::truncate();
        Examen::truncate();
    }
}
