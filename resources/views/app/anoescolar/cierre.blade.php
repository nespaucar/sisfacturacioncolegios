<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($anoescolar, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	<div class="form-group">
		{!! Form::label('fecha', 'Fecha:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		<div class="col-lg-8 col-md-8 col-sm-8">
			{!! Form::date('fecha', date('Y-m-d'), array('class' => 'form-control', 'id' => 'fecha', 'readonly' => 'true')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('numero', 'Nro:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		<div class="col-lg-8 col-md-8 col-sm-8">
			{!! Form::text('numero', $numero, array('class' => 'form-control', 'id' => 'numero', 'readonly' => 'true')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('concepto', 'Concepto:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		<div class="col-lg-8 col-md-8 col-sm-8">
			{!! Form::text('concepto', 'CIERRE DE AÑO ESCOLAR', array('class' => 'form-control', 'id' => 'concepto', 'readonly' => 'true')) !!}
		</div>
	</div>
	@if($monto != null)
	<div class="form-group" style="display:none">
		{!! Form::label('monto', 'Monto:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		<div class="col-lg-8 col-md-8 col-sm-8">
			{!! Form::text('monto', $monto, array('class' => 'form-control', 'id' => 'monto', 'readonly' => 'true')) !!}
		</div>
	</div>
	@endif
    <div class="form-group">
		{!! Form::label('comentario', 'Comentario:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		<div class="col-lg-8 col-md-8 col-sm-8">
			{!! Form::textarea('comentario', null, array('class' => 'form-control', 'id' => 'comentario', 'cols' => 15 , 'rows','6')) !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-12 col-md-12 col-sm-12 text-right">
			{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
		</div>
	</div>
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('450');
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id = "comentario"]').focus();
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :button[id = "btnGuardar"]').on("click", function() {
		$("#btnCierre").prop("disabled", true);
		$("#btnApertura").removeAttr("disabled");
	});
}); 
</script>