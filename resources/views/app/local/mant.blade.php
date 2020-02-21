<?php 
	
	use Illuminate\Support\Facades\Auth;
	$user        = Auth::user();
    $usertype_id = $user->usertype_id;

?>
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($local, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="form-group">
	<div class="col-lg-6 col-md-6 col-sm-6">
		<div class="form-group text-center">
			{!! Form::label('ruc', 'INFORMACIÓN DE COLEGIO', array('class' => 'col-lg-12 col-md-12 col-sm-12 text-center', 'style' => 'color:green')) !!}
		</div>
		@if($usertype_id == 1)
		<div class="form-group">
			{!! Form::label('nuevo', 'Tipo Local (*)', array('class' => 'col-lg-6 col-md-6 col-sm-6 control-label labelr')) !!}
			<div class="col-lg-6 col-md-6 col-sm-6">
				{!! Form::select("nuevo", array("N" => "Nuevo", "D" => "Dependiente"), null, array("class" => "form-control input-xs", "id" => "nuevo")) !!}
			</div>
		</div>
		@endif
		<div class="form-group">
			{!! Form::label('ruc', 'RUC (*)', array('class' => 'col-lg-6 col-md-6 col-sm-6 control-label labelr')) !!}
			<div class="col-lg-6 col-md-6 col-sm-6">
				{!! Form::text('ruc', null, array('class' => 'form-control input-xs', 'id' => 'ruc', 'placeholder' => 'Ingrese ruc', 'maxlength' => '11', 'onkeyup' => 'consultarRUC(this.value)')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('serie', 'Serie Boletas (*)', array('class' => 'col-lg-6 col-md-6 col-sm-6 control-label labelr')) !!}
			<div class="col-lg-6 col-md-6 col-sm-6">
				{!! Form::text('serie', null, array('class' => 'form-control input-xs', 'id' => 'serie', 'placeholder' => 'Ingrese serie')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('serie2', 'Serie Facturas (*)', array('class' => 'col-lg-6 col-md-6 col-sm-6 control-label labelr')) !!}
			<div class="col-lg-6 col-md-6 col-sm-6">
				{!! Form::text('serie2', null, array('class' => 'form-control input-xs', 'id' => 'serie2', 'placeholder' => 'Ingrese serie de facturas')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('serie3', 'Serie Notas de Crédito (*)', array('class' => 'col-lg-6 col-md-6 col-sm-6 control-label labelr')) !!}
			<div class="col-lg-6 col-md-6 col-sm-6">
				{!! Form::text('serie3', null, array('class' => 'form-control input-xs', 'id' => 'serie3', 'placeholder' => 'Ingrese serie de notas de crédito')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('razonsocial', 'Razon Social', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label labelr')) !!}
			<div class="col-lg-9 col-md-9 col-sm-9">
				{!! Form::text('razonsocial', null, array('class' => 'form-control input-xs', 'id' => 'razonsocial', 'placeholder' => 'Ingrese razon social', 'maxlength' => '120')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('nombre', 'Nombre (*)', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label labelr')) !!}
			<div class="col-lg-9 col-md-9 col-sm-9">
				{!! Form::text('nombre', null, array('class' => 'form-control input-xs', 'id' => 'nombre', 'placeholder' => 'Ingrese nombre', 'maxlength' => '80')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('descripcion', 'Descripción', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
			<div class="col-lg-9 col-md-9 col-sm-9">
				{!! Form::textarea('descripcion', null, array('class' => 'form-control input-xs', 'id' => 'descripcion', 'placeholder' => 'Ingrese descripción', 'rows' => '3', 'maxlength' => '100')) !!}
			</div>
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6">
		<div class="form-group">
			{!! Form::label('tipo', 'Tipo (*)', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label labelr')) !!}
			<div class="col-lg-9 col-md-9 col-sm-9">
				{!! Form::select("tipo", array("P" => "Particular", "N" => "Nacional"), null, array("class" => "form-control input-xs", "id" => "tipo")) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('logo', 'Logo (*)', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label labelr')) !!}
			<div class="col-lg-9 col-md-9 col-sm-9">
				{!! Form::file("logo", array("class" => "form-control input-xs", "id" => "logo")) !!}
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-9 col-sm-offset-2">
				<div id="imagen_local" class="center-block"></div>
			</div>
		</div>
		<hr>
		<div class="form-group text-center">
			{!! Form::label('dniusuario', 'INFORMACIÓN DE USUARIO', array('class' => 'col-lg-12 col-md-12 col-sm-12 text-center', 'style' => 'color:green')) !!}
		</div>
		<div class="form-group">
			{!! Form::label('dniusuario', 'DNI de Usuario (*)', array('class' => 'col-lg-6 col-md-6 col-sm-6 control-label labelr')) !!}
			<div class="col-lg-6 col-md-6 col-sm-6">
				{!! Form::text('dniusuario', null, array('class' => 'form-control input-xs', 'id' => 'dniusuario', 'placeholder' => 'Ingrese dni de usuario', 'maxlength' => '8', 'onkeyup' => 'consultarDatosxDNI();')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('nombreusuario', 'Nombre de Usuario (*)', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label labelr')) !!}
			<div class="col-lg-9 col-md-9 col-sm-9">
				{!! Form::text('nombreusuario', null, array('class' => 'form-control input-xs', 'id' => 'nombreusuario', 'placeholder' => 'Ingrese nombre de usuario')) !!}
				{!! Form::hidden('nombreu', null, array('id' => 'nombreu')) !!}
				{!! Form::hidden('apellidopaternou', null, array('id' => 'apellidopaternou')) !!}
				{!! Form::hidden('apellidomaternou', null, array('id' => 'apellidomaternou')) !!}
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
		configurarAnchoModal('1200');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		@if($local!==null)
			$("#imagen_local").html("<img height='200px' width='200px' class='img img-responsive center-block' src='{{ asset("logos/" . $local->logo) }}' />");
		@endif
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="ruc"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="serie"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="serie2"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="serie3"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="ruc"]').focus();
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

	function consultarRUC(ruc){  
		if(ruc.length===11) {
			$.ajax({
		        type: 'GET',
		        url: "SunatPHP/demo.php",
		        data: "ruc="+ruc,
		        beforeSend(){
		            $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="razonsocial"]').val('Comprobando...');
		        },
		        success: function (data, textStatus, jqXHR) {
		            if(data.RazonSocial == null) {
		        		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="ruc"]').val('').focus();
		                $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="razonsocial"]').val('');
		                $.Notification.autoHideNotify('error', 'top right', "ERROR!", 'El RUC ingresado no existe... Digite uno válido.');  
		            } else {
		                $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="ruc"]').val(ruc);
		                $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="razonsocial"]').val(data.RazonSocial);
		                $.Notification.autoHideNotify('success', 'top right', "EXITO!", 'RUC válido.');  
		            }
		        }
		    });
		}
	}

	function consultarDatosxDNI() {
		//alert($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="dni"]').val().length);
		if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="dniusuario"]').val().length == 8) {
			var dni = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="dniusuario"]').val();
			var url = 'ReniecPHP/consulta_reniec.php';
			$.ajax({
				type:'POST',
				url:url,
				data:'dni='+dni,
				beforeSend: function() {
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="dniusuario"]').val("Cargando...");
				},
				success: function(datos_dni){
					var datos = eval(datos_dni);
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="dniusuario"]').val(dni);
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="nombreusuario"]').val("");
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="apellidopaternou"]').val("");
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="apellidomaternou"]').val("");
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="nombreu"]').val("");
					if(datos[2]!==null&&datos[3]!==null&&datos[1]!==null) {
						$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="nombreusuario"]').val(datos[2]+" "+datos[3]+" "+datos[1]);
						$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="apellidopaternou"]').val(datos[2]);
						$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="apellidomaternou"]').val(datos[3]);
						$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="nombreu"]').val(datos[1]);
					}				
				}
			});
		}
	}
</script>