<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($local, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="form-group">
	<div class="col-lg-6 col-md-6 col-sm-6">
		<div class="form-group">
			{!! Form::label('local_id', 'Local:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
			<div class="col-lg-9 col-md-9 col-sm-9">
				{!! Form::select('local_id', $cboLocales, null, array('class' => 'form-control input-xs', 'id' => 'local_id')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('serie', 'Serie:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
			<div class="col-lg-9 col-md-9 col-sm-9">
				{!! Form::text('serie', null, array('class' => 'form-control input-xs', 'id' => 'serie', 'placeholder' => 'Ingrese serie', 'maxlength' => '8')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('nombre', 'Nombre:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
			<div class="col-lg-9 col-md-9 col-sm-9">
				{!! Form::text('nombre', null, array('class' => 'form-control input-xs', 'id' => 'nombre', 'placeholder' => 'Ingrese nombre', 'maxlength' => '80')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('descripcion', 'Descripción:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
			<div class="col-lg-9 col-md-9 col-sm-9">
				{!! Form::textarea('descripcion', null, array('class' => 'form-control input-xs', 'id' => 'descripcion', 'placeholder' => 'Ingrese descripción', 'rows' => '3', 'maxlength' => '100')) !!}
			</div>
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6">
		<div class="form-group">
			{!! Form::label('tipo', 'Tipo:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
			<div class="col-lg-9 col-md-9 col-sm-9">
				{!! Form::select("tipo", array("P" => "Particular", "N" => "Nacional"), null, array("class" => "form-control input-xs", "id" => "tipo")) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('logo', 'Logo:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
			<div class="col-lg-9 col-md-9 col-sm-9">
				{!! Form::file("logo", array("class" => "form-control input-xs", "id" => "logo")) !!}
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-9 col-sm-offset-2">
				<div id="imagen_local" class="center-block"></div>
			</div>
		</div>
	</div>
</div>
		
<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardarLocal(\''.$entidad.'\', this)')) !!}
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('900');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		@if($local!==null)
			$("#imagen_local").html("<img height='200px' width='200px' class='img img-responsive center-block' src='{{ asset("logos/" . $local->logo) }}' />");
		@endif
	}); 

	function filePreview(input) {
		if(input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function(e) {
				$("#imagen_local").html("<img height='200px' width='200px'  class='img img-responsive center-block' src='" + e.target.result + "' />")
			}

			reader.readAsDataURL(input.files[0]);
		}
	}

	$("#logo").change(function() {
		filePreview(this);
	});

	function guardarLocal(entidad, idboton) {
		var idformulario = IDFORMMANTENIMIENTO + entidad;
		var form = $(idformulario)[0];
		var data = new FormData(form);
		var respuesta    = '';
		var listar       = 'NO';
		if ($(idformulario + ' :input[id = "listar"]').length) {
			var listar = $(idformulario + ' :input[id = "listar"]').val();
		}
		var btn = $(idboton);
		btn.button('loading');
		var accion     = $(idformulario).attr('action');
		var metodo     = $(idformulario).attr('method');
		var enctype     = $(idformulario).attr('enctype');

		var respuesta  = $.ajax({
			url : accion,
			type: metodo,
			enctype: enctype,
			data: data,
			processData: false,
	        contentType: false,
	        cache: false,
	        timeout: 600000,		
		});

		respuesta.done(function(msg) {
			respuesta = msg;
		}).fail(function(xhr, textStatus, errorThrown) {
			respuesta = 'ERROR';
		}).always(function() {
			btn.button('reset');
			if(respuesta === 'ERROR'){
			}else{
				if (respuesta === 'OK') {
					cerrarModal();
					if (listar === 'SI') {
						if(typeof entidad2 != 'undefined' && entidad2 !== ''){
							entidad = entidad2;
						}
						buscarCompaginado('', 'Accion realizada correctamente', entidad, 'OK');
					}        
				} else {
					mostrarErrores(respuesta, idformulario, entidad);
				}
			}
		});
	}
</script>