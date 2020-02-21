<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($modelo, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
{!! $mensaje or '<blockquote><p class="text-danger">¿Esta seguro de eliminar el registro?</p></blockquote>' !!}
<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="glyphicon glyphicon-remove"></i> '.$boton, array('class' => 'btn btn-danger btn-sm', 'id' => 'btnGuardar', 'onclick' => 'confirmacionTotal(this);')) !!}
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal((contadorModal - 1));')) !!}
	</div>
</div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		configurarAnchoModal('350');
	}); 
	function confirmacionTotal(btn) {
		guardar('{{$entidad}}', btn);
		@if(isset($adicional))
			@if($adicional == "MATRICULA")
				setTimeout(function() {
				    llenarTablaMatriculados('Curso');
				}, 500);
			@elseif($adicional == "TABLACUOTAS")
				setTimeout(function() {
				    llenarTablaPagos('Mensualidad');
				}, 500);
			@endif
		@endif
	}
</script>