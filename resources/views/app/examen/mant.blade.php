<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($examen, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="form-group">
	{!! Form::label('curso_id', 'Curso:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
	<div class="col-lg-9 col-md-9 col-sm-9">
		<select class="form-control input-xs" name="curso_id" id="curso_id">
			<option value="">Seleccione un curso</option>
			@foreach($cboCurso as $curso)
				<option value="{{ $curso->id }}">{{ $curso->descripcion }}</option>
			@endforeach
		</select>
	</div>
</div>
<div class="form-group">
	{!! Form::label('descripcion', 'Descripción:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
	<div class="col-lg-9 col-md-9 col-sm-9">
		{!! Form::text('descripcion', null, array('class' => 'form-control input-xs', 'id' => 'descripcion', 'placeholder' => 'Ingrese descripcion')) !!}
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
		configurarAnchoModal('650');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
	}); 
</script>