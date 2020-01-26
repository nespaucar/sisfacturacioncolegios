<?php
use App\Escuela;
use App\Especialidad;
?>
<script>
function cargarselect(entidad){
	var select = $('#escuela_id').val();
	route = 'alumno/cargarselect/'+select+'?entidad= '+entidad+'&t=no';
	console.log(route);

	$.ajax({
		url:route,
		headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
		type: 'GET',
		success: function(res){
			$('#especialidad_id').removeClass('input-sm');
        	$('#select' + entidad).html(res);
        }
	});
}
</script>

<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($alumno, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}

	
		<div class="panel-body">
			<div class="form-group col-xs-6">
				{!! Form::label('codigo', 'Codigo:', array('class' => '')) !!}
				{!! Form::text('codigo', null, array('class' => 'form-control input-xs', 'id' => 'codigo', 'placeholder' => 'Ingrese codigo', 'maxlength' => '9')) !!}
			</div>
			<div class="form-group col-xs-6" style="margin-left: 10px;">
				{!! Form::label('nombres', 'Nombres:', array('class' => '')) !!}
				{!! Form::text('nombres', null, array('class' => 'form-control input-xs', 'id' => 'nombres', 'placeholder' => 'Ingrese nombre')) !!}
			</div>
			<div class="form-group col-xs-6">
				{!! Form::label('apellidopaterno', 'Apellido Paterno:', array('class' => '')) !!}
				{!! Form::text('apellidopaterno', null, array('class' => 'form-control input-xs', 'id' => 'apellidopaterno', 'placeholder' => 'Ingrese apellido paterno')) !!}
			</div>

			<div class="form-group col-xs-6" style="margin-left: 10px;">
				{!! Form::label('apellidomaterno', 'Apellido Materno:', array('class' => '')) !!}
				{!! Form::text('apellidomaterno', null, array('class' => 'form-control input-xs', 'id' => 'apellidomaterno', 'placeholder' => 'Ingrese apellido materno')) !!}
			</div>

			<div class="form-group col-xs-6">
				{!! Form::label('dni', 'DNI:', array('class' => '')) !!}
				{!! Form::text('dni', null, array('class' => 'form-control input-xs input-number', 'id' => 'dni', 'placeholder' => 'Ingrese dni','maxlength' => '8')) !!}
			</div>

			<div class="form-group col-xs-6" style="margin-left: 10px;">
				{!! Form::label('fechanacimiento', 'Fecha de Nacimiento:', array('class' => '')) !!}
				{!! Form::date('fechanacimiento', null, array('class' => 'form-control input-xs', 'id' => 'fechanacimiento', 'placeholder' => 'fecha nacimiento')) !!}
			</div>
			<?php
				if($alumno != null){
					echo "<input type='hidden' id='fechaNac' value='".Date::parse($alumno->fechanacimiento )->format('d/m/Y')."'>";

				}else{
				echo "<input type='hidden' id='fechaNac' value=''>";
					
				}
			?>

			<div class="form-group col-xs-6">
				{!! Form::label('direccion', 'Direccion:', array('class' => '')) !!}
				{!! Form::text('direccion', null, array('class' => 'form-control input-xs', 'id' => 'direccion', 'placeholder' => 'Ingrese direccion')) !!}
			</div>

			<div class="form-group col-xs-6" style="margin-left: 10px;">
				{!! Form::label('telefono', 'Telefono:', array('class' => '')) !!}
				{!! Form::text('telefono', null, array('class' => 'form-control input-xs input-number', 'id' => 'telefono', 'placeholder' => 'Ingrese telefono','maxlength' => '11')) !!}
			</div>

			<div class="form-group col-xs-6">
				{!! Form::label('email', 'E-Mail:', array('class' => '')) !!}
				{!! Form::text('email', null, array('class' => 'form-control input-xs', 'id' => 'email', 'placeholder' => 'email@ejemplo.com')) !!}
			</div>

			<div class="form-group col-xs-6" style="margin-left: 10px;">
				{!! Form::label('escuela_id', 'Escuela:', array('class' => '')) !!}
				{!! Form::select('escuela_id', $cboEscuela, null, array('class' => 'form-control input-xs', 'id' => 'escuela_id', 'onchange' => 'cargarselect("especialidad")')) !!}
			</div>

			<div class="form-group col-xs-6">
				{!! Form::label('especialidad_id', 'Especialidad:', array('class' => '')) !!}
				<div id="selectespecialidad">
					{!! Form::select('especialidad_id', $cboEspecialidad, null, array('class' => 'form-control input-xs', 'id' => 'especialidad_id')) !!}
				</div>
			</div>

			<div class="form-group col-xs-6" style="margin-left: 10px;">
				{!! Form::label('situacion', 'Situacion:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
				{!! Form::select('situacion', $cboSituacion, null, array('class' => 'form-control input-xs', 'id' => 'situacion')) !!}
			</div>

		</div>
		<div class="col-12">
			<div class="form-group text-right">
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

	$('.input-number').on('input', function () { 
    	this.value = this.value.replace(/[^0-9]/g,'');
	});

	if($('#fechaNac').val() !== ""){
		// DD/MM/YYYY
		var valoresFecha = $('#fechaNac').val().split('/');
		//yyy/MM/DD
		var fecha = valoresFecha[2] + "-" + valoresFecha[1] + "-" + valoresFecha[0];
		$('#fechanacimiento').val(fecha);
	}


</script>
