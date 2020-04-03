<?php

use Illuminate\Support\Facades\Auth;

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout');
Route::get('logout', 'Auth\LoginController@logout');

//other routes

Route::group(['middleware' => 'guest'], function() {    
    //Password reset routes
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
    Route::get('password','Auth\ResetPasswordController@showPasswordReset');
    Route::get('registro','Auth\RegisterController@showRegistrationForm');
    Route::post('registro', 'Auth\RegisterController@register');
});

Route::get('mensualidad/envioBoletas', 'MensualidadController@envioBoletas')->name('mensualidad.envioBoletas');

Route::group(['middleware' => 'auth'], function () {

    Route::get('/', function(){
        return View::make('principal.home');
    });

    /* CAMBIAR CONTRASEÑA*/
    Route::resource('updatepassword', 'UpdatePasswordController', array('except' => array('show')));

    /*ACTUALIZAR DATOS*/
    Route::post('actualizardatosavatar','ActualizarDatosController@avatar');
    Route::resource('actualizardatos', 'ActualizarDatosController');

    /*USUARIO*/
    Route::post('usuario/buscar', 'UsuarioController@buscar')->name('usuario.buscar');
    Route::get('usuario/eliminar/{id}/{listarluego}', 'UsuarioController@eliminar')->name('usuario.eliminar');
    Route::resource('usuario', 'UsuarioController', array('except' => array('show')));

    /*CURSOS*/
    Route::post('curso/buscar', 'CursoController@buscar')->name('curso.buscar');
    Route::get('curso/eliminar/{id}/{listarluego}', 'CursoController@eliminar')->name('curso.eliminar');
    Route::resource('curso', 'CursoController', array('except' => array('show')));
    Route::get('curso/matriculados/{id}', 'CursoController@matriculados')->name('curso.matriculados');
    Route::get('curso/activarcurso/{id}/{estado}', 'CursoController@activarcurso')->name('curso.activarcurso');

    /*CURSOS DISPONIBLES - ALUMNO*/
    Route::resource('cursodisponible', 'CursoDisponibleController', array('except' => array('show')));
    Route::post('cursodisponible/buscar', 'CursoDisponibleController@buscar')->name('cursodisponible.buscar');
    Route::get('cursodisponible/confirmarMatricularme/{id}/{listarluego}', 'CursoDisponibleController@confirmarMatricularme')->name('cursodisponible.confirmarMatricularme');
    Route::get('cursodisponible/matricularme/{id}', 'CursoDisponibleController@matricularme')->name('cursodisponible.matricularme');

    /*CURSOS MATRICULADOS - ALUMNO*/
    Route::resource('cursomatriculado', 'CursoMatriculadoController', array('except' => array('show')));
    Route::post('cursomatriculado/buscar', 'CursoMatriculadoController@buscar')->name('cursomatriculado.buscar');
    Route::get('cursomatriculado/confirmarDesmatricularme/{id}/{listarluego}', 'CursoMatriculadoController@confirmarDesmatricularme')->name('cursomatriculado.confirmarDesmatricularme');
    Route::get('cursomatriculado/desmatricularme/{id}', 'CursoMatriculadoController@desmatricularme')->name('cursomatriculado.desmatricularme');

    /*CATEGORIA OPCION MENU*/
    Route::post('categoriaopcionmenu/buscar', 'CategoriaopcionmenuController@buscar')->name('categoriaopcionmenu.buscar');
    Route::get('categoriaopcionmenu/eliminar/{id}/{listarluego}', 'CategoriaopcionmenuController@eliminar')->name('categoriaopcionmenu.eliminar');
    Route::resource('categoriaopcionmenu', 'CategoriaopcionmenuController', array('except' => array('show')));

    /*OPCION MENU*/
    Route::post('opcionmenu/buscar', 'OpcionmenuController@buscar')->name('opcionmenu.buscar');
    Route::get('opcionmenu/eliminar/{id}/{listarluego}', 'OpcionmenuController@eliminar')->name('opcionmenu.eliminar');
    Route::resource('opcionmenu', 'OpcionmenuController', array('except' => array('show')));

    /*TIPO DE USUARIO*/
    Route::post('tipousuario/buscar', 'TipousuarioController@buscar')->name('tipousuario.buscar');
    Route::get('tipousuario/obtenerpermisos/{listar}/{id}', 'TipousuarioController@obtenerpermisos')->name('tipousuario.obtenerpermisos');
    Route::post('tipousuario/guardarpermisos/{id}', 'TipousuarioController@guardarpermisos')->name('tipousuario.guardarpermisos');
    Route::get('tipousuario/eliminar/{id}/{listarluego}', 'TipousuarioController@eliminar')->name('tipousuario.eliminar');
    Route::resource('tipousuario', 'TipousuarioController', array('except' => array('show')));

    /* PROFESOR EXAMEN */
    Route::post('examen/buscar', 'ExamenController@buscar')->name('examen.buscar');
    Route::get('examen/eliminar/{id}/{listarluego}', 'ExamenController@eliminar')->name('examen.eliminar');
    Route::resource('examen', 'ExamenController', array('except' => array('show')));

    Route::get('examen/resultados/{id}', 'ExamenController@resultados')->name('examen.resultados');

    /* PREGUNTAS */
    Route::get('examen/listarpreguntas/{examen_id}', 'ExamenController@listarpreguntas')->name('examen.listarpreguntas');
    Route::get('examen/nuevapregunta/{examen_id}', 'ExamenController@nuevapregunta')->name('examen.nuevapregunta');
    Route::get('examen/eliminarpregunta/{id}/{examen_id}', 'ExamenController@eliminarpregunta')->name('examen.eliminarpregunta');

    /* ALTERNATIVAS */
    Route::get('examen/listaralternativas/{pregunta_id}', 'ExamenController@retornarTablaAlternativas')->name('examen.listaralternativas');
    Route::get('examen/nuevaalternativa/{pregunta_id}', 'ExamenController@nuevaalternativa')->name('examen.nuevaalternativa');
    Route::get('examen/eliminaralternativa/{id}/{pregunta_id}', 'ExamenController@eliminaralternativa')->name('examen.eliminaralternativa');

    /* RESPUESTAS */
    Route::get('examen/alternativacorrecta', 'ExamenController@alternativacorrecta')->name('examen.alternativacorrecta');

    /* ALUMNO-EXAMENES */
    Route::post('alumnoexamen/buscar', 'AlumnoExamenController@buscar')->name('alumnoexamen.buscar');
    Route::resource('alumnoexamen', 'AlumnoExamenController', array('except' => array('show')));
    
    /* LLENAR EXAMEN */ 
    Route::get('alumnoexamen/llenarexamen', 'AlumnoExamenController@llenarexamen')->name('alumnoexamen.llenarexamen');
    Route::post('alumnoexamen/guardarexamen', 'AlumnoExamenController@guardarexamen')->name('alumnoexamen.guardarexamen');
    
    /* VER RESPUESTAS DE EXAMEN */
    Route::get('alumnoexamen/respuestasexamen', 'AlumnoExamenController@respuestasexamen')->name('alumnoexamen.respuestasexamen');

    /* REPORTES */   
    Route::get('/generarcurriculum', 'PdfController@generarcurriculum')->name('generarcurriculum');   

    ////////////////////////////PROGRAMACIÓN FACTURACIÓN COLEGIOS --------------------------------

    /*LOCAL*/
    Route::post('local/buscar', 'LocalController@buscar')->name('local.buscar');
    Route::post('local/confirmaralterarestado', 'LocalController@confirmaralterarestado')->name('local.confirmaralterarestado');
    Route::get('local/alterarestado/{id}/{listarluego}/{estado}', 'LocalController@alterarestado')->name('local.alterarestado');
    Route::resource('local', 'LocalController', array('except' => array('show')));

    Route::get('setearlocal', 'UsuarioController@escogerlocal')->name('usuario.escogerlocal');
    Route::post('guardarlocal', 'UsuarioController@guardarlocal')->name('usuario.guardarlocal');

    /*NIVEL*/
    Route::post('nivel/buscar', 'NivelController@buscar')->name('nivel.buscar');
    Route::get('nivel/eliminar/{id}/{listarluego}', 'NivelController@eliminar')->name('nivel.eliminar');
    Route::resource('nivel', 'NivelController', array('except' => array('show')));

    /*GRADO*/
    Route::post('grado/buscar', 'GradoController@buscar')->name('grado.buscar');
    Route::post('grado/anadirSeccion', 'GradoController@anadirSeccion')->name('grado.anadirSeccion');
    Route::get('grado/secciones', 'GradoController@secciones')->name('grado.secciones');
    Route::post('grado/eliminarSeccion', 'GradoController@eliminarSeccion')->name('grado.eliminarSeccion');
    Route::post('grado/editarSeccion', 'GradoController@editarSeccion')->name('grado.editarSeccion');
    Route::post('grado/cargarNiveles', 'GradoController@cargarNiveles')->name('grado.cargarNiveles');
    Route::get('grado/eliminar/{id}/{listarluego}', 'GradoController@eliminar')->name('grado.eliminar');
    Route::get('grado/grados', 'GradoController@grados')->name('grado.grados');
    Route::get('grado/seccionesM', 'GradoController@seccionesM')->name('grado.seccionesM');
    Route::resource('grado', 'GradoController', array('except' => array('show')));

    /*TIPO DOCUMENTO*/
    Route::post('tipodocumento/buscar', 'TipodocumentoController@buscar')->name('tipodocumento.buscar');
    Route::get('tipodocumento/eliminar/{id}/{listarluego}', 'TipodocumentoController@eliminar')->name('tipodocumento.eliminar');
    Route::resource('tipodocumento', 'TipodocumentoController', array('except' => array('show')));

    /*TIPO MOVIMIENTO*/
    Route::post('tipomovimiento/buscar', 'TipomovimientoController@buscar')->name('tipomovimiento.buscar');
    Route::get('tipomovimiento/eliminar/{id}/{listarluego}', 'TipomovimientoController@eliminar')->name('tipomovimiento.eliminar');
    Route::resource('tipomovimiento', 'TipomovimientoController', array('except' => array('show')));

    /*CONCEPTO DE PAGO*/
    Route::post('conceptopago/buscar', 'ConceptopagoController@buscar')->name('conceptopago.buscar');
    Route::get('conceptopago/eliminar/{id}/{listarluego}', 'ConceptopagoController@eliminar')->name('conceptopago.eliminar');
    Route::resource('conceptopago', 'ConceptopagoController', array('except' => array('show')));

    /*AÑO ESCOLAR*/
    Route::post('anoescolar/buscar', 'AnoescolarController@buscar')->name('anoescolar.buscar');
    Route::get('anoescolar/eliminar/{id}/{listarluego}', 'AnoescolarController@eliminar')->name('anoescolar.eliminar');
    Route::post('anoescolar/confirmarcierre', 'AnoescolarController@confirmarcierre')->name('anoescolar.confirmarcierre');
    Route::get('anoescolar/cierre', 'AnoescolarController@cierre')->name('anoescolar.cierre');
    Route::resource('anoescolar', 'AnoescolarController', array('except' => array('show')));

    /*CONFIGURACIÓN DE PAGO*/
    Route::post('configuracionpago/buscar', 'ConfiguracionpagoController@buscar')->name('configuracionpago.buscar');
    Route::get('configuracionpago/eliminar/{id}/{listarluego}', 'ConfiguracionpagoController@eliminar')->name('configuracionpago.eliminar');
    Route::resource('configuracionpago', 'ConfiguracionpagoController', array('except' => array('show')));
    Route::get('configuracionpago/alumnoautocompleting/{searching}', 'ConfiguracionpagoController@alumnoautocompleting')->name('configuracionpago.alumnoautocompleting');
    Route::get('configuracionpago/nivelautocompleting/{searching}', 'ConfiguracionpagoController@nivelautocompleting')->name('configuracionpago.nivelautocompleting');
    Route::get('configuracionpago/gradoautocompleting/{searching}', 'ConfiguracionpagoController@gradoautocompleting')->name('configuracionpago.gradoautocompleting');
    Route::get('configuracionpago/seccionautocompleting/{searching}', 'ConfiguracionpagoController@seccionautocompleting')->name('configuracionpago.seccionautocompleting');

    Route::get('configuracionpago/personasautocompleting/{searching}', 'PersonaController@personasautocompleting')->name('configuracionpago.personasautocompleting');

    /*ALUMNO*/
    Route::post('alumno/buscar', 'AlumnoController@buscar')->name('alumno.buscar');
    Route::get('alumno/eliminar/{id}/{listarluego}', 'AlumnoController@eliminar')->name('alumno.eliminar');
    Route::get('alumno/alterarestado', 'AlumnoController@alterarestado')->name('alumno.alterarestado');
    Route::post('alumno/confirmaralterarestado', 'AlumnoController@confirmaralterarestado')->name('alumno.confirmaralterarestado');
    Route::get('alumno/createapoderado', 'AlumnoController@createapoderado')->name('alumno.createapoderado');
    Route::post('alumno/storeapoderado', 'AlumnoController@storeapoderado')->name('alumno.storeapoderado');
    Route::get('alumno/comprobarapoderado', 'AlumnoController@comprobarapoderado')->name('alumno.comprobarapoderado');
    Route::resource('alumno', 'AlumnoController', array('except' => array('show')));

    /*APODERADO*/
    Route::post('apoderado/buscar', 'ApoderadoController@buscar')->name('apoderado.buscar');
    Route::get('apoderado/eliminar/{id}/{listarluego}', 'ApoderadoController@eliminar')->name('apoderado.eliminar');
    Route::get('apoderado/alterarestado', 'ApoderadoController@alterarestado')->name('apoderado.alterarestado');
    Route::post('apoderado/confirmaralterarestado', 'ApoderadoController@confirmaralterarestado')->name('apoderado.confirmaralterarestado');
    Route::get('apoderado/estudiantes', 'ApoderadoController@estudiantes')->name('apoderado.estudiantes');
    Route::post('apoderado/agregarestudiantes', 'ApoderadoController@agregarestudiantes')->name('apoderado.agregarestudiantes');
    Route::resource('apoderado', 'ApoderadoController', array('except' => array('show')));

    /*ALUMNOSECCION*/
    Route::post('alumnoseccion/buscar', 'AlumnoSeccionController@buscar')->name('alumnoseccion.buscar');
    Route::resource('alumnoseccion', 'AlumnoSeccionController', array('except' => array('show')));
    Route::get('alumnoseccion/matriculados', 'AlumnoSeccionController@matriculados')->name('alumnoseccion.matriculados');
    Route::post('alumnoseccion/matricularalumno', 'AlumnoSeccionController@matricularalumno')->name('alumnoseccion.matricularalumno');
    Route::post('alumnoseccion/llenarTablaMatriculados', 'AlumnoSeccionController@llenarTablaMatriculados')->name('alumnoseccion.llenarTablaMatriculados');
    Route::get('alumnoseccion/comprobarSiAlumnoEstaMatriculado', 'AlumnoSeccionController@comprobarSiAlumnoEstaMatriculado')->name('alumnoseccion.comprobarSiAlumnoEstaMatriculado');
    Route::get('alumnoseccion/eliminar/{id}/{listarluego}/{adicional}', 'AlumnoSeccionController@eliminar')->name('alumnoseccion.eliminar');

    //NÚMERO SIGUIENTE MOVIMIENTO
    Route::get('alumnoseccion/numeroSigue', 'AlumnoSeccionController@numeroSigue')->name('alumnoseccion.numeroSigue');

    /*MENSUALIDAD*/
    Route::post('mensualidad/buscar', 'MensualidadController@buscar')->name('mensualidad.buscar');
    Route::resource('mensualidad', 'MensualidadController', array('except' => array('show')));
    Route::get('mensualidad/conceptopago', 'MensualidadController@conceptopago')->name('mensualidad.conceptopago');
    Route::post('mensualidad/realizarPago', 'MensualidadController@realizarPago')->name('mensualidad.realizarPago');
    Route::post('mensualidad/llenarTablaPagos', 'MensualidadController@llenarTablaPagos')->name('mensualidad.llenarTablaPagos');
    Route::get('mensualidad/eliminar/{id}/{listarluego}/{adicional}', 'MensualidadController@eliminar')->name('mensualidad.eliminar');
    Route::get('mensualidad/resumen', 'MensualidadController@resumen')->name('mensualidad.resumen');
    Route::post('mensualidad/confirmarchecktodo', 'MensualidadController@confirmarchecktodo')->name('mensualidad.confirmarchecktodo');
    Route::get('mensualidad/checktodo', 'MensualidadController@checktodo')->name('mensualidad.checktodo');
    Route::get('mensualidad/eliminar/{id}/{listarluego}', 'MensualidadController@eliminar')->name('mensualidad.eliminar');
    Route::get('mensualidad/destroy/{id}', 'MensualidadController@destroy')->name('mensualidad.destroy');

    /*VENTA*/
    Route::post('venta/buscar', 'VentaController@buscar')->name('venta.buscar');
    Route::get('venta/eliminar/{id}/{listarluego}', 'VentaController@eliminar')->name('venta.eliminar');
    Route::post('venta/confirmarcierre', 'VentaController@confirmarcierre')->name('venta.confirmarcierre');
    Route::get('venta/cierre', 'VentaController@cierre')->name('venta.cierre');
    Route::resource('venta', 'VentaController', array('except' => array('show')));
});

Route::get('storage/{archivo}', function ($archivo) {
     $public_path = storage_path();
     $url = $public_path.'/app/'.$archivo;print_r($url);
     //verificamos si el archivo existe y lo retornamos
     if (Storage::exists($archivo))
     {
       return response()->download($url);
     }
     //si no se encuentra lanzamos un error 404.
     abort(404);

});
