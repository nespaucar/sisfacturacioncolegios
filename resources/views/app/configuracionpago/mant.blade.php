<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($configuracionpago, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	<div class="form-group">
		{!! Form::label('tipo', 'Aplicado a:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-9 col-md-9 col-sm-9">
			{!! Form::select('tipo', array("1" => "UN ALUMNO", "2" => "UN NIVEL", "3" => "UN GRADO", "4" => "UNA SECCIÓN"), null, array('class' => 'form-control input-xs', 'id' => 'tipo', 'onchange' => 'cambiarTabla(this.value)')) !!}
		</div>
	</div>
	<div class="form-group" id="divAlumno">
		{!! Form::label('alumno', 'Alumno:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-9 col-md-9 col-sm-9">
			{!! Form::text('alumno', null, array('class' => 'form-control input-xs', 'id' => 'alumno')) !!}
		</div>
		{!! Form::hidden('alumno_id', "", array('id' => 'alumno_id')) !!}
	</div>
	<div class="form-group" id="divNivel">
		{!! Form::label('nivel', 'Nivel:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-9 col-md-9 col-sm-9">
			{!! Form::text('nivel', null, array('class' => 'form-control input-xs', 'id' => 'nivel')) !!}
		</div>
		{!! Form::hidden('nivel_id', "", array('id' => 'nivel_id')) !!}
	</div>
	<div class="form-group" id="divGrado">
		{!! Form::label('grado', 'Grado:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-9 col-md-9 col-sm-9">
			{!! Form::text('grado', null, array('class' => 'form-control input-xs', 'id' => 'grado')) !!}
		</div>
		{!! Form::hidden('grado_id', "", array('id' => 'grado_id')) !!}
	</div>
	<div class="form-group" id="divSeccion">
		{!! Form::label('seccion', 'Sección:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-9 col-md-9 col-sm-9">
			{!! Form::text('seccion', null, array('class' => 'form-control input-xs', 'id' => 'seccion')) !!}
		</div>
		{!! Form::hidden('seccion_id', "", array('id' => 'seccion_id')) !!}
	</div>
	<div class="form-group">
		{!! Form::label('monto', 'Monto mensual:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('monto', null, array('class' => 'form-control input-xs', 'id' => 'monto')) !!}
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
		$("#divAlumno").show();
		$("#divNivel").hide();
		$("#divGrado").hide();
		$("#divSeccion").hide();
		tipearEntidad("alumno");
		tipearEntidad("nivel");
		tipearEntidad("grado");
		tipearEntidad("seccion");
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="alumno"]').focus();
		$(".twitter-typeahead").prop("style", ""); //PARA QUITAR ESTILO A TYPEAHEAD
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="monto"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
	});

	function cambiarTabla(val) {
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="alumno"]').typeahead('val', '');
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="nivel"]').typeahead('val', '');
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="grado"]').typeahead('val', '');
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="seccion"]').typeahead('val', '');
		$("#divAlumno").hide();
		$("#divNivel").hide();
		$("#divGrado").hide();
		$("#divSeccion").hide();
		switch (val) {
			case "1":
				$("#divAlumno").show();
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="alumno"]').focus();
				break;
			case "2":
				$("#divNivel").show();
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="nivel"]').focus();
				break;
			case "3":
				$("#divGrado").show();
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="grado"]').focus();
				break;
			case "4":
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