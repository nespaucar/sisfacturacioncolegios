<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($apoderado, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	{!! Form::hidden('apoderado_id', ($apoderado!==NULL?$apoderado->id:""), array('id' => 'apoderado_id')) !!}
	{!! Form::hidden('alumno_id', $alumno_id, array('id' => 'alumno_id')) !!}
		<div class="panel-body">
			<div class="form-group">
				<div class="col-lg-12 col-md-12 col-sm-12">
					<div class="form-group">
						{!! Form::label('dni', 'DNI (*)', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label labelr')) !!}
						<div class="col-lg-4 col-md-4 col-sm-4">
							{!! Form::text('dni', null, array('class' => 'form-control input-xs', 'id' => 'dni', 'placeholder' => 'Ingrese dni', 'maxlength' => '8', 'onkeyup' => 'consultarDatosxDNI();')) !!}
						</div>
					</div>
					<div class="form-group">
						{!! Form::label('nombres', 'Nombres (*)', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label labelr')) !!}
						<div class="col-lg-8 col-md-8 col-sm-8">
							{!! Form::text('nombres', null, array('class' => 'form-control input-xs', 'id' => 'nombres', 'placeholder' => 'Ingrese nombre')) !!}
						</div>
					</div>
					<div class="form-group">
						{!! Form::label('apellidopaterno', 'Apellido Paterno (*)', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label labelr')) !!}
						<div class="col-lg-8 col-md-8 col-sm-8">
							{!! Form::text('apellidopaterno', null, array('class' => 'form-control input-xs', 'id' => 'apellidopaterno', 'placeholder' => 'Ingrese apellido paterno')) !!}
						</div>
					</div>
					<div class="form-group">
						{!! Form::label('apellidomaterno', 'Apellido Materno (*)', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label labelr')) !!}
						<div class="col-lg-8 col-md-8 col-sm-8">
							{!! Form::text('apellidomaterno', null, array('class' => 'form-control input-xs', 'id' => 'apellidomaterno', 'placeholder' => 'Ingrese apellido materno')) !!}
						</div>
					</div>
					<div class="form-group">
						{!! Form::label('direccion', 'Dirección (*)', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label labelr')) !!}
						<div class="col-lg-8 col-md-8 col-sm-8">
							{!! Form::text('direccion', null, array('class' => 'form-control input-xs', 'id' => 'direccion', 'placeholder' => 'Ingrese dirección')) !!}
						</div>
					</div>
					<div class="form-group">
						{!! Form::label('telefono', 'Teléfono (*)', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label labelr')) !!}
						<div class="col-lg-4 col-md-4 col-sm-4">
							{!! Form::text('telefono', null, array('class' => 'form-control input-xs input-number', 'id' => 'telefono', 'placeholder' => 'Ingrese teléfono','maxlength' => '9')) !!}
						</div>
					</div>
					<div class="form-group">
						{!! Form::label('email', 'E-Mail (*)', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label labelr')) !!}
						<div class="col-lg-8 col-md-8 col-sm-8">
							{!! Form::text('email', $correo, array('class' => 'form-control input-xs', 'id' => 'email', 'placeholder' => 'email@ejemplo.com')) !!}
						</div>
					</div>
					<div class="form-group">
						{!! Form::label('fechanacimiento', 'Fecha de Nacimiento', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
						<div class="col-lg-8 col-md-8 col-sm-8">
							{!! Form::date('fechanacimiento', null, array('class' => 'form-control input-xs', 'id' => 'fechanacimiento', 'placeholder' => 'Ingrese fecha de nacimiento')) !!}
						</div>
					</div>
				</div>
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
		@if($apoderado !== NULL)
			$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="dni"]').prop("readonly", true);
		@endif
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="dni"]').focus();
	});

	function consultarDatosxDNI() {
		//alert($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="dni"]').val().length);
		if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="dni"]').val().length == 8) {
			var dni = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="dni"]').val();
			var url = 'ReniecPHP/consulta_reniec.php';
			$.ajax({
				type:'POST',
				url:url,
				data:'dni='+dni,
				beforeSend: function() {
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="dni"]').val("Cargando...");
				},
				success: function(datos_dni){
					var datos = eval(datos_dni);
					//$('#mostrar_dni').text(datos[0]);
					//$('#paterno').text(datos[1]);
					//$('#materno').text(datos[2]);
					//$('#nombres').text(datos[3]);
					//alert(datos[3]);
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="dni"]').val(dni);
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="apellidopaterno"]').val(datos[2]);
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="apellidomaterno"]').val(datos[3]);
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="nombres"]').val(datos[1]);
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="direccion"]').focus();
				}
			});
		}
	}
</script>
