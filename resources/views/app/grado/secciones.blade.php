<style>
	.boton {
		display: none;
	}
	.celdita:hover {
		cursor: pointer;
	}
	.celdita:hover > div > i {
		display: block;
	}
</style>
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($grado, $formData) !!}
<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="form-group">
			{!! Form::label('descripcion', 'Descripción:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('descripcion', "", array('onkeypress' => 'enterAnadirSeccion(' . $id . ')', 'class' => 'form-control input-xs', 'id' => 'descripcion', 'placeholder' => 'Ingrese descripción', 'maxlength' => '100')) !!}
			</div>
			<div class="col-lg-2 col-md-2 col-sm-2 text-center">
				{!! Form::button('<i class="fa fa-plus fa-lg"></i> Añadir', array('class' => 'btn btn-primary btn-sm', 'id' => 'btnAnadir', 'onclick' => 'anadirSeccion(\'' . $id . '\')')) !!}
			</div>
		</div>
		<div class="form-group">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<table class="table table-bordered table-striped table-condensed table-hover">
					<thead>
						<tr>
							<th width="90%">Descripción</th>
							<th width="10%">Elim.</th>
						</tr>
					</thead>
					<tbody id="tablaSecciones">
						@if($grado->secciones!==NULL)
							@if(count($grado->secciones)>0)
								@foreach($grado->secciones as $seccion)
									<tr id="rowSeccion{{$seccion->id}}">
				                        <td class="celdita text-center" onclick="editarSeccion({{$seccion->id}})">
				                        	<div class="mostrarDescripcion" id="mostrarDescripcion{{$seccion->id}}">
				                        		<font id="fontDescripcion{{$seccion->id}}">{{$seccion->descripcion}}</font><i class="boton glyphicon glyphicon-pencil"></i>
				                        	</div>
				                        	<div class="hide editarDescripcion" id="editarDescripcion{{$seccion->id}}">
				                        		{!! Form::text('descripcion2'.$seccion->id, $seccion->descripcion, array('class' => 'form-control input-xs text-center', 'id' => 'descripcion2'.$seccion->id, 'placeholder' => 'Ingrese descripción', 'onkeypress' => 'detectarScapeEnter('.$seccion->id.');', 'maxlength' => '100', 'onblur' => 'reseteoTabla()')) !!}
				                        	</div>
				                        </td>
				                        <td class="text-center">
				                            <a class="btn btn-xs btn-danger" onclick="eliminarSeccion({{$seccion->id}})" href="#">
				                            	<i class="fa fa-minus fa-xs"></i>
				                            </a>
				                        </td>
				                    </tr>
								@endforeach
							@endif
						@endif
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
		
<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('550');
		
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[name="descripcion"]').focus();
	});

	function anadirSeccion(id) {
		if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[name="descripcion"]').val()=="") {
			$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[name="descripcion"]').focus();
		} else {
			$.ajax({
				url: "grado/anadirSeccion?par=" + id + "&_token=" + $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[name="_token"]').val() + "&descripcion=" + $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[name="descripcion"]').val(),
				method: "POST",
				success: function(e) {
					if(e==="KO") {
						$.Notification.autoHideNotify('error', 'top right', "CUIDADO!",'Ya existe una seccion con este nombre.');
						$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[name="descripcion"]').val("");
						$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[name="descripcion"]').focus();
					} else {
						$("#tablaSecciones").append(e);
						$.Notification.autoHideNotify('success', 'top right', "CORRECTO!",'Seccion insertada correctamente.');
						$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[name="descripcion"]').val("");
						$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[name="descripcion"]').focus();
						buscar('{{ $entidad }}');
					}
				},
			});
		}			
	}

	function eliminarSeccion(id) {
		$.ajax({
			url: "grado/eliminarSeccion?par=" + id + "&_token=" + $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[name="_token"]').val(),
			method: "POST",
			success: function(e) {
				if(e==="OK") {
					$("#rowSeccion"+id).remove();
					$.Notification.autoHideNotify('success', 'top right', "CORRECTO!",'Seccion eliminada correctamente.');
					buscar('{{ $entidad }}');
				} else if(e === "OK") {
					$.Notification.autoHideNotify('error', 'top right', "CUIDADO!",'No puedes eliminar esta seccion porque hay alumnos matriculados aquí.');
				}
			},
		});
	}

	function editarSeccion(id) {
		reseteoTabla();
		$("#editarDescripcion"+id).removeClass("hide").addClass("show");
		$("#descripcion2"+id).focus();
		$("#mostrarDescripcion"+id).addClass("hide").removeClass("show");
	}

	function editarSeccion2(id) {
		var desc = $("#descripcion2"+id).val();
		if(desc == "") {
			$("#descripcion2"+id).val($("#fontDescripcion"+id).html());
			$.Notification.autoHideNotify('error', 'top right', "CUIDADO!",'La descripción no puede quedar vacía.');
			reseteoTabla();
		} else {
			$.ajax({
				url: "grado/editarSeccion?par=" + id + "&_token=" + $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[name="_token"]').val() + '&desc=' + desc,
				method: "POST",
				success: function(e) {
					if(e==="OK") {
						$.Notification.autoHideNotify('success', 'top right', "CORRECTO!",'Seccion editada correctamente.');
						$("#fontDescripcion"+id).html(desc);
						reseteoTabla();
					} else if(e==="KO") {
						$("#descripcion2"+id).val($("#fontDescripcion"+id).html());
						$.Notification.autoHideNotify('error', 'top right', "CUIDADO!",'No puede existir secciones con el mismo nombre.');
						reseteoTabla();
					}
				},
			});
		}
	}

	function reseteoTabla() {
		$(".editarDescripcion").removeClass("show").addClass("hide");
		$(".mostrarDescripcion").removeClass("hide").addClass("show");
	}

	function detectarScapeEnter(id) {
		var codigo = event.which || event.keyCode;
		if(codigo == 13){
      		editarSeccion2(id);
   		} else if(codigo == 27) {
			reseteoTabla();
		}
	}

	function enterAnadirSeccion(id) {
		var codigo = event.which || event.keyCode;
		if(codigo == 13){
      		anadirSeccion(id);
   		}		
	}
</script>