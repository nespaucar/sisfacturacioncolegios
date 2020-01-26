<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($alumnoencuesta, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="form-group">
	{!! Form::label('tipoencuesta_id', 'Tipo:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
	<div class="col-lg-9 col-md-9 col-sm-9">
		{!! Form::select('tipoencuesta_id', $cboTipoEncuesta, null, array('class' => 'form-control input-xs', 'id' => 'tipoencuesta_id')) !!}
	</div>
</div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('450');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
	}); 
</script>