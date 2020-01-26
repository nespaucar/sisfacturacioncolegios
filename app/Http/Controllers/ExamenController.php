<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\AlumnoAlternativa;
use App\Alumno;
use App\Examen;
use App\Pregunta;
use App\Alternativa;
use App\Curso;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
            'cargarselect' => 'examen.cargarselect',
            'alternativacorrecta' => 'examen.alternativacorrecta',
            'resultados' => 'examen.resultados',
        );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function buscar(Request $request)
    {
        $user             = Auth::user();
        $profesor_id      = $user->persona_id;
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Examen';
        $nombre           = Libreria::getParam($request->input('nombre'));
        $curso_id         = Libreria::getParam($request->input('curso_id'));
        $filtro = '';
        if(!is_null($curso_id)) {
            $filtro = 1;
        }
        $resultado        = Examen::listar($nombre, $curso_id, $profesor_id, $filtro);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripción', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Curso', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Preguntas', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Resultados', 'numero' => '1');
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
        $user             = Auth::user();
        $profesor_id      = $user->persona_id;
        $entidad          = 'Examen';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $cboCurso         = Curso::select('id', 'descripcion')
                                ->where('profesor_id', $profesor_id)
                                ->where('estado', true)
                                ->get();
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboCurso'));
    }

    public function create(Request $request)
    {
        $user             = Auth::user();
        $profesor_id      = $user->persona_id;
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Examen';
        $cboCurso = Curso::select('id', 'descripcion')
                    ->where('profesor_id', $profesor_id)
                    ->where('estado', true)
                    ->get();
        $examen   = null;
        $formData = array('examen.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton         = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('examen', 'formData', 'entidad', 'boton', 'cboCurso', 'listar'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $validacion = Validator::make($request->all(),
            array(
                'descripcion' => 'required|max:80',
                'curso_id'    => 'required'
                )
            );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $examen                  = new Examen();
            $examen->descripcion     = $request->input('descripcion');
            $examen->curso_id        = $request->input('curso_id');
            $examen->estado          = false;
            $examen->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {
        $user             = Auth::user();
        $profesor_id      = $user->persona_id;
        $existe = Libreria::verificarExistencia($id, 'examen');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $examen   = Examen::find($id);
        $entidad  = 'Examen';
        $cboCurso = Curso::select('id', 'descripcion')
                    ->where('profesor_id', $profesor_id)
                    ->where('estado', true)
                    ->get();
        $formData = array('examen.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('examen', 'formData', 'entidad', 'boton', 'cboCurso', 'listar'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'examen');
        if ($existe !== true) {
            return $existe;
        }
        $validacion = Validator::make($request->all(),
            array(
                'descripcion' => 'required|max:80',
                'curso_id'    => 'required'
                )
            );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $examen              = Examen::find($id);
            $examen->descripcion = $request->input('descripcion');
            $examen->curso_id    = $request->input('curso_id');
            $examen->save();
        });
        return is_null($error) ? "OK" : $error;
    }

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
        $mensaje = '<p class="text-inverse">¿Esta seguro de eliminar el examen "'.$modelo->descripcion.'"?</p>';
        $entidad  = 'Examen';
        $formData = array('route.store');
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
        $cabecera[]       = array('valor' => 'Alternativas', 'numero' => '1');
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

    public function nuevapregunta($examen_id, Request $request)
    {
        $pregunta            = new Pregunta();
        $pregunta->nombre    = $request->get('pregunta');
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
        $cabecera[]       = array('valor' => 'Alternativas', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Eliminar', 'numero' => '1');
        
        $titulo_modificar      = $this->tituloModificar;
        $titulo_eliminar       = $this->tituloEliminar;
        $ruta                  = $this->rutas;
        $inicio                = 0;

        if(count($lista) == 0) {
            return '<h4 class="text-warning">No se encontraron resultados.</h4>';
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
                        <td>'. $value->nombre . "</td>";                    
                    $tabla .= '<td><a href="#carousel-ejemplo" class="btn btn-default btn-xs" data-slide="next" onclick=\'gestionpa(3, "alternativa", "", ' . $value->id . ');\'><div class="glyphicon glyphicon-list"></div> Alt.</a></td>';                    
                    $tabla .= '<td><button onclick=\'gestionpa(2, "pregunta", ' . $value->id . ',' . $examen_id . ');\' class="btn btn-xs btn-danger" type="button"><div class="glyphicon glyphicon-remove"></div> Eliminar</button></td>
                    </tr>';
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
                        <button class="correcto btn btn-success input-sm waves-effect waves-light m-l-10 btn-md hidden" onclick="#" type="button"><i class="glyphicon glyphicon-check"></i> Bien!</button>
                    </div>
                </div>                  
            </form><br>';
                

        if(count($lista) == 0) {
            return $tabla . '<h4 class="text-warning">No hay alternativas para esta pregunta.</h4>';
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

                    if($value->correcta == 1){
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

    public function alternativacorrecta(Request $request) 
    {
        $alternativa_id = $request->get('alternativa_id');
        $pregunta_id = $request->get('pregunta_id');

        Alternativa::where('pregunta_id', $pregunta_id)->update(['correcta' => false]);

        $alternativa           = Alternativa::find($alternativa_id);
        $alternativa->correcta = true;
        $alternativa->save();
    }

    public function eliminarexamens(Request $request) {
        Alternativa::truncate();
        Pregunta::truncate();
        Examen::truncate();
    }

    public function resultados($idexamen) 
    {
        $user        = Auth::user();
        $profesor_id = $user->persona_id;
        $curso       = Examen::select('curso_id')
                        ->where('id', $idexamen)
                        ->get();
        $examen = Examen::listar('', $curso[0]->curso_id, $profesor_id, 1)->get();

        if(count($examen) > 0) {
            $resultados = AlumnoAlternativa::select(DB::raw('CONCAT(persona.nombres, " ", apellidopaterno, " ", apellidomaterno) AS alumno'), DB::raw('COUNT(correcta) AS resultado'))
            ->join('persona', 'alumno_alternativa.alumno_id', 'persona.id')
            ->join('alternativa', 'alumno_alternativa.alternativa_id', 'alternativa.id')
            ->join('pregunta', 'alternativa.pregunta_id', 'pregunta.id')
            ->join('examen', 'pregunta.examen_id', 'examen.id')
            ->where('correcta', 1)
            ->where('examen.id', $examen[0]->id)
            ->groupBy('persona.nombres', 'persona.apellidopaterno', 'persona.apellidomaterno')
            ->get();

            $totalpreguntas = Examen::select(DB::raw('COUNT(pregunta.id) AS totalpreguntas'))
            ->join('pregunta', 'pregunta.examen_id', 'examen.id')
            ->where('examen.id', $examen[0]->id)
            ->where('pregunta.deleted_at', null)
            ->get();

            return view($this->folderview.'.resultados')->with(compact('resultados', 'totalpreguntas'));

        } else {
            return view($this->folderview.'.resultados');
        }  
        
    }
}
