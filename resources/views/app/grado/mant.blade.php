<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($grado, $formData) !!}	
{!! Form::hidden('listar', "SI", array('id' => 'listar')) !!}
<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="form-group">
			{!! Form::label('descripcion', 'Descripción:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
			<div class="col-lg-9 col-md-9 col-sm-9">
				{!! Form::text('descripcion', null, array('class' => 'form-control input-xs', 'id' => 'descripcion', 'placeholder' => 'Ingrese descripción', 'rows' => '3', 'maxlength' => '100')) !!}
			</div>
		</div>
		@if(Auth::user()->usertype_id!==1)
			<div class="form-group">
				{!! Form::label('local', 'Local:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
				<div class="col-lg-9 col-md-9 col-sm-9">
					{!! Form::text('local', $local, array('class' => 'form-control input-xs', 'id' => 'local', 'disabled' => true)) !!}
				</div>
			</div>
		@else
			<div class="form-group">
				{!! Form::label('local_id', 'Local:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
				<div class="col-lg-9 col-md-9 col-sm-9">
					{!! Form::select('local_id', $cboLocales, $local_id, array('class' => 'form-control input-xs', 'id' => 'local_id', 'onchange' => 'cargarNiveles();')) !!}
				</div>
			</div>
		@endif
		<div class="form-group">
			{!! Form::label('nivel_id', 'Nivel:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
			<div class="col-lg-9 col-md-9 col-sm-9">
				<select name="nivel_id" id="nivel_id" class="form-control input-xs"></select>
			</div>
		</div>		
	</div>
</div>
		
<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this);')) !!}
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('550');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[name="descripcion"]').focus();
		@if(Auth::user()->usertype_id!==1)
			$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[name="local_id"]').val('{{ $local_id }}');
		@endif
		cargarNiveles();
	});
	function cargarNiveles() {
		$.ajax({
			url: "grado/cargarNiveles?par=" + $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[name="local_id"]').val() + "&_token=" + $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[name="_token"]').val(),
			method: "POST",
			success: function(e) {
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[name="nivel_id"]').html(e);
				@if($grado!==NULL)
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[name="nivel_id"]').val('{{ $grado->nivel->id }}');
				@endif
			},
		});
	}
</script>