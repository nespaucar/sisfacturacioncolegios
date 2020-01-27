<?php 
	$cadenaidalumnos = "";

	if(count($apoderado->alumnos)>0) {
		foreach($apoderado->alumnos as $apal) {
			$cadenaidalumnos .= $apal->alumno_id . ";";
		}
	}

?>
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($apoderado, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	{!! Form::hidden('apoderado_id', $apoderado_id, array('id' => 'apoderado_id')) !!}
	{!! Form::hidden('cadenaidalumnos', $cadenaidalumnos, array('id' => 'cadenaidalumnos')) !!}
	<div class="panel-body">
		<div class="form-group">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<div class="form-group">
					{!! Form::label('estudiante', 'Estudiante', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label label-xs')) !!}
					<div class="col-lg-9 col-md-9 col-sm-9">
						{!! Form::text('estudiante', null, array('class' => 'form-control input-xs', 'id' => 'estudiante', 'placeholder' => 'Ingrese estudiante', 'maxlength' => '100')) !!}
					</div>
					{!! Form::hidden('estudiante_id', "", array('id' => 'estudiante_id')) !!}
					{!! Form::hidden('estudiante_dni', "", array('id' => 'estudiante_dni')) !!}
					<div class="col-lg-1 col-md-1 col-sm-1">
						{!! Form::button('<i class="fa fa-plus fa-lg"></i>', array('class' => 'btn btn-info btn-sm', 'id' => 'btnAgregar', 'onclick' => 'agregar();')) !!}
					</div>
				</div>
				<table id="datatable" class="table table-xs table-striped table-bordered">
					<thead>
						<tr>
							<th style="padding:5px;margin:5px;" class="text-center" width="10%"><u>#</u></th>
							<th style="padding:5px;margin:5px;" class="text-center" width="20%"><u>DNI</u></th>
							<th style="padding:5px;margin:5px;" class="text-center" width="60%"><u>Estudiante</u></th>
							<th style="padding:5px;margin:5px;" class="text-center" width="10%"><u>X</u></th>
						</tr>
					</thead>
					<tbody id="tablaEstudiantes">
						@if(count($apoderado->alumnos)>0)
							<?php $contador = 1; ?>
							@foreach($apoderado->alumnos as $apal)
								<tr id="tabEstudiantes{{$apal->alumno->id}}">
									<td style="padding:5px;margin:5px;" class="text-center">*</td>
									<td style="padding:5px;margin:5px;" class="text-center">{{$apal->alumno->dni}}</td>
									<td style="padding:5px;margin:5px;" class="text-center">{{$apal->alumno->apellidopaterno." ".$apal->alumno->apellidomaterno." ".$apal->alumno->nombres}}</td>
									<td style="padding:5px;margin:5px;" class="text-center">
										<a href="javascript:0" onclick="removeDetalle('{{$apal->alumno->id}}');" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-remove"></i></a>
										<input type="hidden" class="idDetallitos" value="{{$apal->alumno->id}}">
									</td>
								</tr>
								<?php $contador++; ?>
							@endforeach
						 @endif
					</tbody>
			    </table>
			</div>
		</div>
		<div class="form-group">
			<div class="col-lg-12 col-md-12 col-sm-12 text-right">
				{!! Form::button('<i class="fa fa-check fa-lg"></i> Guardar datos', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
				{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
			</div>
		</div>
	</div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('850');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		var entidad = new Bloodhound({
			datumTokenizer: function (d) {
				return Bloodhound.tokenizers.whitespace(d.value);
			},
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				cache: false,
				url: 'configuracionpago/alumnoautocompleting/%QUERY',
				filter: function (entidad) {
					return $.map(entidad, function (movie) {
						return {
							value: movie.value,
							id: movie.id,
							dni: movie.dni
						};
					});
				}
			}
		});
		entidad.initialize();
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="estudiante"]').typeahead(null,{
			displayKey: 'value',
			source: entidad.ttAdapter()
		}).on('typeahead:selected', function (object, datum) {
			$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="estudiante_id"]').val(datum.id);
			$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="estudiante"]').val(datum.value);
			$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="estudiante_dni"]').val(datum.dni);
			entidad.initialize();
		}).on("keyup", function(e) {
			e.preventDefault();
		    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="estudiante_id"]').val("");
		    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="estudiante_dni"]').val("");
		});

		$(".twitter-typeahead").prop("style", ""); //PARA QUITAR ESTILO A TYPEAHEAD
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="estudiante"]').focus();
	});

	function agregar() {
		if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="estudiante_id"]').val()==="") {
			$.Notification.autoHideNotify('error', 'top right', "¡CUIDADO!",'Debes seleccionar un estudiante');
		} else {
			var id_estudiante = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="estudiante_id"]').val();
			$.ajax({
				url: 'alumno/comprobarapoderado?id=' + id_estudiante,
				success:function(e) {
					if(e === "N") { //NO TIENE APODERADO
						if(estaEnCadenaIdEstudiante(id_estudiante)) {
							$.Notification.autoHideNotify('error', 'top right', "¡CUIDADO!",'El estudiante ya ha sido seleccionado');
						} else {
							var mestudiante_id = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="estudiante_id"]').val();
							var mestudiante_dni = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="estudiante_dni"]').val();
							var mestudiante = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="estudiante"]').val();
							$("#tablaEstudiantes").append('<tr id="tabEstudiantes' + mestudiante_id + '">' +
								'<td style="padding:5px;margin:5px;" class="text-center">*</td>' +
								'<td style="padding:5px;margin:5px;" class="text-center">' + mestudiante_dni + '</td>' +
								'<td style="padding:5px;margin:5px;" class="text-center">' + mestudiante + '</td>' +
								'<td style="padding:5px;margin:5px;" class="text-center">' +
									'<a href="javascript:0" onclick="removeDetalle(\'' + mestudiante_id + '\');" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-remove"></i></a>' +
									'<input type="hidden" class="idDetallitos" value="' + mestudiante_id + '">' +
								'</td>' +
							'</tr>');
							tejerCadenaIdEstudiantes();
						}
					} else { //YA TIENE APODERADO
						$.Notification.autoHideNotify('error', 'top right', "¡CUIDADO!",'Este estudiante ya tiene apoderado');
					}					
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="estudiante_id"]').val("");
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="estudiante_dni"]').val("");
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="estudiante"]').val("");
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="estudiante"]').typeahead('val', '');
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="estudiante"]').focus();
				}
			});
		}
	}

	function estaEnCadenaIdEstudiante(id_estudiante) {
		var esta = false;
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[class="idDetallitos"]').each(function(index, elemento) {
            if($(this).val()===id_estudiante) {
            	esta = true;
            }
		});	
		return esta;
	}

	function removeDetalle(estudiante_id) {
		$("#tabEstudiantes"+estudiante_id).remove();
		tejerCadenaIdEstudiantes();
	}

	function tejerCadenaIdEstudiantes() {
		var retorno = "";
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[class="idDetallitos"]').each(function(index, elemento) {
            retorno += $(this).val() + ";";
		});
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="cadenaidalumnos"]').val(retorno);
	}
</script>
