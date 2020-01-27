<?php 

use App\Persona;
use App\Nivel;
use App\Grado;
use App\Seccion;

//SI ES PARA EDITAR INICIALIZAMOS EL TIPO Y LA ENTIDAD ELEGIDA, ARMAMOS SU DESCRIPCIÓN REAL
$tipo = 1;
$descripcion1 = "";
$entidad0_id1 = "";
$descripcion2 = "";
$entidad0_id2 = "";
$descripcion3 = "";
$entidad0_id3 = "";
$descripcion4 = "";
$entidad0_id4 = "";
$monto = "";
if($configuracionpago!==NULL) {
	if($configuracionpago->alumno_id!==NULL) {
		$entidad0 = Persona::find($configuracionpago->alumno_id);
		$descripcion1 = $entidad0->apellidopaterno.' '.$entidad0->apellidomaterno.' '.$entidad0->nombres;
		$entidad0_id1 = $configuracionpago->alumno_id;
	} else if($configuracionpago->nivel_id!==NULL) {
		$entidad0 = Nivel::find($configuracionpago->nivel_id);
		$descripcion2 = $entidad0->descripcion;
		$entidad0_id2 = $configuracionpago->nivel_id;
		$tipo = 2;
	} else if($configuracionpago->grado_id!==NULL) {
		$entidad0 = Grado::find($configuracionpago->grado_id);
		$descripcion3 = $entidad0->descripcion.($entidad0->nivel!==NULL?(" - ".$entidad0->nivel->descripcion):"");
		$entidad0_id3 = $configuracionpago->grado_id;
		$tipo = 3;
	} else if($configuracionpago->seccion_id!==NULL) {
		$entidad0 = Seccion::find($configuracionpago->seccion_id);
		$descripcion4 = '"'.$entidad0->descripcion.'"'.(
                                $entidad0->grado!==NULL?
                                (
                                    " - ".$entidad0->grado->descripcion .
                                    (
                                        $entidad0->grado->nivel!==NULL?
                                        (" - ".$entidad0->grado->nivel->descripcion):
                                    "")
                                ):
                            "");
		$entidad0_id4 = $configuracionpago->seccion_id;
		$tipo = 4;
	}
	$monto = $configuracionpago->monto;
}

?>
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($configuracionpago, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	<div class="form-group">
		{!! Form::label('tipo', 'Aplicado a:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-9 col-md-9 col-sm-9">
			{!! Form::select('tipo', array("1" => "UN ALUMNO", "2" => "UN NIVEL", "3" => "UN GRADO", "4" => "UNA SECCIÓN"), $tipo, array('class' => 'form-control input-xs', 'id' => 'tipo', 'onchange' => 'cambiarTabla(this.value)')) !!}
		</div>
	</div>
	<div class="form-group" id="divAlumno">
		{!! Form::label('alumno', 'Alumno:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-9 col-md-9 col-sm-9">
			{!! Form::text('alumno', $descripcion1, array('class' => 'form-control input-xs', 'id' => 'alumno')) !!}
		</div>
		{!! Form::hidden('alumno_id', $entidad0_id1, array('id' => 'alumno_id')) !!}
	</div>
	<div class="form-group" id="divNivel">
		{!! Form::label('nivel', 'Nivel:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-9 col-md-9 col-sm-9">
			{!! Form::text('nivel', $descripcion2, array('class' => 'form-control input-xs', 'id' => 'nivel')) !!}
		</div>
		{!! Form::hidden('nivel_id', $entidad0_id2, array('id' => 'nivel_id')) !!}
	</div>
	<div class="form-group" id="divGrado">
		{!! Form::label('grado', 'Grado:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-9 col-md-9 col-sm-9">
			{!! Form::text('grado', $descripcion3, array('class' => 'form-control input-xs', 'id' => 'grado')) !!}
		</div>
		{!! Form::hidden('grado_id', $entidad0_id3, array('id' => 'grado_id')) !!}
	</div>
	<div class="form-group" id="divSeccion">
		{!! Form::label('seccion', 'Sección:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-9 col-md-9 col-sm-9">
			{!! Form::text('seccion', $descripcion4, array('class' => 'form-control input-xs', 'id' => 'seccion')) !!}
		</div>
		{!! Form::hidden('seccion_id', $entidad0_id4, array('id' => 'seccion_id')) !!}
	</div>
	<div class="form-group">
		{!! Form::label('monto', 'Monto mensual:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('monto', $monto, array('class' => 'form-control input-xs', 'id' => 'monto')) !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-12 col-md-12 col-sm-12 text-right">
			{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardarConfiguracionpago(\''.$entidad.'\', this)')) !!}
			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
		</div>
	</div>
	
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('600');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		cambiarTabla({{$tipo}});
		tipearEntidad("alumno");
		tipearEntidad("nivel");
		tipearEntidad("grado");
		tipearEntidad("seccion");
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="alumno"]').focus();
		$(".twitter-typeahead").prop("style", ""); //PARA QUITAR ESTILO A TYPEAHEAD
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="monto"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
	});

	function cambiarTabla(val) {
		var val = parseInt(val);
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="alumno"]').typeahead('val', '');
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="nivel"]').typeahead('val', '');
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="grado"]').typeahead('val', '');
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="seccion"]').typeahead('val', '');
		$("#divAlumno").hide();
		$("#divNivel").hide();
		$("#divGrado").hide();
		$("#divSeccion").hide();
		switch (val) {
			case 1:
				$("#divAlumno").show();
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="alumno"]').focus();
				break;
			case 2:
				$("#divNivel").show();
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="nivel"]').focus();
				break;
			case 3:
				$("#divGrado").show();
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="grado"]').focus();
				break;
			case 4:
				$("#divSeccion").show();
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="seccion"]').focus();
				break;
		}
	}

	function tipearEntidad(valor) {
		var entidad = new Bloodhound({
			datumTokenizer: function (d) {
				return Bloodhound.tokenizers.whitespace(d.value);
			},
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				cache: false,
				url: 'configuracionpago/' + valor + 'autocompleting/%QUERY',
				filter: function (entidad) {
					return $.map(entidad, function (movie) {
						return {
							value: movie.value,
							id: movie.id
						};
					});
				}
			}
		});
		entidad.initialize();
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="'+valor+'"]').typeahead(null,{
			displayKey: 'value',
			source: entidad.ttAdapter()
		}).on('typeahead:selected', function (object, datum) {
			$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="alumno_id"]').val("");
			$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="nivel_id"]').val("");
			$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="grado_id"]').val("");
			$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="seccion"]').val("");
			$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="'+valor+'_id"]').val(datum.id);
			$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="'+valor+'"]').val(datum.value);
			$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="monto"]').focus();
			entidad.clear();
		}).on("keyup", function(e) {
			e.preventDefault();
		    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="'+valor+'_id"]').val("");
		});
	}

	function guardarConfiguracionpago(entidad, btn) {
		var enviar = true;
		var mensaje = "";
		switch ($("#tipo").val()) {
			case "1":
				if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="alumno_id"]').val()=="") {
					enviar = false;
					mensaje = crearMensaje("Alumno");
				}
				break;
			case "2":
				if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="nivel_id"]').val()=="") {
					enviar = false;
					mensaje = crearMensaje("Nivel");
				}
				break;
			case "3":
				if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="grado_id"]').val()=="") {
					enviar = false;
					mensaje = crearMensaje("Grado");
				}
				break;
			case "4":
				if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="seccion_id"]').val()=="") {
					enviar = false;
					mensaje = crearMensaje("Seccion");
				}
				break;
		}

		if(enviar) {
			guardar(entidad, btn);
		} else {
			$("#divMensajeErrorConfiguracionpago").html(mensaje);
		}
	}

	function crearMensaje(entidad) {
		return '<div class="alert alert-danger">' +
				'<button type="button" class="close" data-dismiss="alert">×</button>' +
				'<strong>Por favor corrige los siguentes errores:</strong>' +
				'<ul>' +
					'<li>El campo ' + entidad + ' es obligatorio.</li>'
				'</ul>' +
			'</div>';
	}
</script>