<?php
	use Illuminate\Support\Facades\Auth;
	$user          = Auth::user();
	$persona       = $user->persona;
	$nombrepersona = $persona->apellidopaterno.' '.$persona->apellidomaterno.' '.$persona->nombres;
?>
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">Setear Local</h4>
        </div>
    </div>
</div>
<div class="card-box table-responsive">
	{!! Form::open(['route' => $ruta["guardarlocal"], 'method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'form-horizontal', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">		
		<div class="form-group">
			{!! Form::label('nombrepersona', 'Usuario', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
			<div class="col-lg-10 col-md-10 col-sm-10">
				{{$nombrepersona}}
			</div>				
		</div>
		<div class="form-group">
			{!! Form::label('local_id', 'Local', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
			<div class="col-lg-10 col-md-10 col-sm-10">
				{!! Form::select('local_id', $cboLocales, $local_id, array('class' => 'form-control input-xs', 'id' => 'local_id')) !!}
			</div>				
		</div>
		<div class="form-group">
			<div class="col-lg-12 col-md-12 col-sm-12 text-right">
				{!! Form::button('<i class="glyphicon glyphicon-floppy-disk"></i> Guardar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-sm', 'id' => 'btnGuardarLocal', 'onclick' => 'guardarLocal();')) !!}
			</div>
		</div>
	</div>
	{!! Form::close() !!}
</div>

<script>
	function guardarLocal() {
		$.ajax({
			method: "POST",
			url: "{{ url('guardarlocal') }}",
			data: {
				"local_id" : $("#local_id").val(),
				"_token": "{{ csrf_token() }}",
			},
			"beforeSend": function() {
				$('#btnGuardarLocal').button('loading');
			}
		}).done(function(info) {
			$("#nombreGeneralLocal").html(info);
			$('#btnGuardarLocal').button('reset');
			$.Notification.autoHideNotify('success', 'top right', "¡ÉXITO!", 'Cambio de Local exitoso');      
		}).error(function () {
			$.Notification.autoHideNotify('error', 'top right', "¡ERROR!", 'No pudiste cambiar de local');    
		});
	}
</script>