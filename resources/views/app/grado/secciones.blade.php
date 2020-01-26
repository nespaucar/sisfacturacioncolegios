<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($grado, $formData) !!}
<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="form-group">
			{!! Form::label('descripcion', 'Descripción:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('descripcion', "", array('class' => 'form-control input-xs', 'id' => 'descripcion', 'placeholder' => 'Ingrese descripción', 'rows' => '3', 'maxlength' => '100')) !!}
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
				                        <td class="text-center">{{$seccion->descripcion}}</td>
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
					$("#tablaSecciones").append(e);
					$("#rowSeccion"+id).css('display', 'none').fadeIn(1000);
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[name="descripcion"]').val("");
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[name="descripcion"]').focus();
					buscar('{{ $entidad }}');
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
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[name="descripcion"]').focus();
					buscar('{{ $entidad }}');
				}
			},
		});
	}
</script>