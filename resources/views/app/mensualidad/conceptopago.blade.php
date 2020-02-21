@if($alumnoseccion->cicloacademico==NULL)
<h4 class="text-danger text-center">Aún no se ha realizado la apertura de este año escolar.</h4>
<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
@else
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($alumnoseccion, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	{!! Form::hidden('persona_id', $alumnoseccion!==NULL?$alumnoseccion->alumno_id:"", array('id' => 'persona_id')) !!}
	{!! Form::hidden('alumnoseccion_id', $alumnoseccion!==NULL?$alumnoseccion->id:"", array('id' => 'alumnoseccion_id')) !!}
	{!! Form::hidden('cicloacademico_id', $alumnoseccion!==NULL?$alumnoseccion->cicloacademico_id:"", array('id' => 'cicloacademico_id')) !!}
	{!! Form::hidden('conceptopago_id', $cpago==NULL?"":$cpago->id, array('id' => 'conceptopago_id')) !!}
	{!! Form::hidden('seccion_id', $alumnoseccion!==NULL?$alumnoseccion->seccion_id:"", array('id' => 'seccion_id')) !!}
	{!! Form::hidden('mes', $mes, array('id' => 'mes')) !!}
	<div class="panel-body">
		<div class="form-group text-center">
            {!! Form::label('alumno', 'Alumno', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label input-sm')) !!}
            <div class="col-lg-5 col-md-5 col-sm-5">
                {!! Form::text('alumno', ($alumnoseccion->alumno->apellidopaterno." ".$alumnoseccion->alumno->apellidomaterno." ".$alumnoseccion->alumno->nombres), array('class' => 'form-control input-sm', 'id' => 'alumno', 'readonly' => 'true')) !!}
            </div>
            {!! Form::label('matriculado', 'Matriculado', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label input-sm')) !!}
            <div class="col-lg-5 col-md-5 col-sm-5">
                {!! Form::text('matriculado', ($alumnoseccion->seccion->grado->descripcion." grado ".$alumnoseccion->seccion->descripcion." del nivel ".$alumnoseccion->seccion->grado->nivel->descripcion), array('class' => 'form-control input-sm', 'id' => 'matriculado', 'readonly' => 'true')) !!}
            </div>
        </div>
		<div class="form-group" id="realizarPago">
			<div class="img img-thumbnail" style="border-style: ridge; padding: 10px; box-shadow: 2px 2px 10px #666;">
		        <div id="divDatosAlumno" class="col-lg-6 col-md-6 col-sm-6">
		            <div class="form-group text-center">
		                {!! Form::label('numero', 'N° Mov.', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label input-sm')) !!}
		                <div class="col-lg-2 col-md-2 col-sm-2">
		                    {!! Form::text('numero', "", array('class' => 'form-control input-sm', 'id' => 'numero', 'readonly' => 'true')) !!}
		                </div>
		                {!! Form::label('pago', 'Pago', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label input-sm')) !!}
		                <div class="col-lg-6 col-md-6 col-sm-6">
		                    {!! Form::text('pago', $cpago==NULL?"":$cpago->nombre, array('class' => 'form-control input-sm', 'id' => 'pago', 'readonly' => 'true')) !!}
		                </div>
		            </div>
	        		<div class="form-group">
		            	<div class="col-lg-12 col-md-12 col-sm-12 text-center">
		            		{!! Form::label('formapago', "FORMA DE PAGO", array('class' => 'control-label input-sm caja')) !!}
		            	</div>
		            </div>
		            <div class="form-group">
		                <div class="col-lg-6 col-md-6 col-sm-6 img img-thumbnail" style="border-style: ridge; padding: 10px; box-shadow: 1px 1px 3px #666;">            
		                    <div class="form-group">
		                    	<div class="col-lg-4 col-md-4 col-sm-4">
		                        	<span class="input-group-addon input-sm">EFECTIVO</span>
		                        </div>
		                        <div class="col-lg-8 col-md-8 col-sm-8">
		                        	<input onkeyup="calcularTotalPago();" name="efectivo" id="efectivo" type="text" class="form-control input-sm">
		                        </div>
		                    </div>
		                    <div class="form-group">
								<div class="col-lg-4 col-md-4 col-sm-4">
		                        	<span class="input-group-addon input-sm">VISA</span>
		                        </div>
		                        <div class="col-lg-8 col-md-8 col-sm-8">
			                        <input onkeyup="calcularTotalPago();" name="visa" id="visa" type="text" class="form-control input-sm">
			                        <span style="display:none;" class="input-group-addon input-sm">N°</span>
			                        <input style="display:none;" name="numvisa" id="numvisa" type="text" class="form-control input-sm">
			                    </div>
		                    </div>
		                    <div class="form-group">
								<div class="col-lg-4 col-md-4 col-sm-4">
			                        <span class="input-group-addon input-sm">MASTER</span>
			                    </div>
			                    <div class="col-lg-8 col-md-8 col-sm-8">
			                        <input onkeyup="calcularTotalPago();" name="master" id="master" type="text" class="form-control input-sm">
			                        <span style="display:none;" class="input-group-addon input-sm">N°</span>
			                        <input style="display:none;" name="nummaster" id="nummaster" type="text" class="form-control input-sm">
			                    </div>
		                    </div>  
		                </div>  
		                <div class="col-lg-6 col-md-6 col-sm-6">            
		                    <div class="form-group">
		                    	<div class="col-lg-4 col-md-4 col-sm-4">
		                        	<span class="input-group-addon input-sm">TOTAL</span>
		                        </div>
		                        <div class="col-lg-8 col-md-8 col-sm-8">
		                        	<input name="total" id="total" type="text" class="form-control input-sm" readonly="">
		                        </div>
		                    </div>
		                    <div class="form-group">
		                    	<div class="col-lg-4 col-md-4 col-sm-4">
		                        	<span class="input-group-addon input-sm">A PAGAR</span>
		                        </div>
		                        <div class="col-lg-8 col-md-8 col-sm-8">
		                        	<input name="total2" id="total2" type="text" class="form-control input-sm" readonly="">
		                        </div>
		                    </div>
		                    <div class="form-group">
		                    	<div class="col-lg-4 col-md-4 col-sm-4">
		                        	<span class="input-group-addon input-sm">A CUENTA</span>
		                        </div>
		                        <div class="col-lg-8 col-md-8 col-sm-8">
		                        	<input name="cuenta" id="cuenta" type="text" class="form-control input-sm" readonly="">
		                        </div>
		                    </div>
		                    <div class="form-group">
		                    	<div class="col-lg-8 col-md-8 col-sm-8">
		                    		<b id="mensajeMontos" style="padding-top: 20px; font-size: 12px; color:red"></b>
		                    	</div>
		                    </div>
		                </div>   
		            </div>	
		            <div id="divDocVenta">	
		            	<div class="form-group" style="background-color: yellow">
				        	{!! Form::label('venta', 'Generar Documento de Venta?', array('class' => 'col-lg-9 col-md-9 col-sm-9 control-label input-sm')) !!}
						  	<div class="col-lg-3 col-md-3 col-sm-3">
			        			{!! Form::select('venta', array("S"=>"SI", "N"=>"NO"), null, array('class' => 'form-control input-sm', 'id' => 'venta', 'onchange' => 'generarDocumentoVenta(this.value);')) !!}
			        		</div>
						</div>
						<div id="parteDocumentoVenta">
							<hr>
							<hr>
				            <div class="form-group">
				            	<div class="col-lg-12 col-md-12 col-sm-12 text-center">
				            		{!! Form::label('formapago', htmlentities('DOCUMENTO DE VENTA'), array('class' => 'control-label input-sm caja')) !!}
				            	</div>
				            </div>
				            <div class="form-group">
				        		<div class="col-lg-5 col-md-5 col-sm-5">
				        			{!! Form::select('tipodocumento', array("B"=>"BOLETA", "F"=>"FACTURA"), null, array('class' => 'form-control input-sm', 'id' => 'tipodocumento', 'onchange' => 'generarNumeroVenta()')) !!}
				        		</div>
				                {!! Form::label('numeroventa', 'N°', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label input-sm')) !!}
				        		<div class="col-lg-3 col-md-3 col-sm-3">
				        			{!! Form::text('serieventa', "001", array('class' => 'form-control input-sm', 'id' => 'serieventa', "readonly")) !!}
				        		</div>
				                <div class="col-lg-3 col-md-3 col-sm-3">
				        			{!! Form::text('numeroventa', "", array('class' => 'form-control input-sm', 'id' => 'numeroventa', 'readonly' => 'true')) !!}
				        		</div>
				        	</div>
				            <div class="form-group datofactura hide">
				                {!! Form::label('ruc', 'RUC', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label input-sm')) !!}
				        		<div class="col-lg-5 col-md-5 col-sm-5">
				        			{!! Form::text('ruc', null, array('class' => 'form-control input-sm', 'id' => 'ruc', 'maxlength' => '11', 'onkeyup' => 'buscarEmpresa(this.value);')) !!}
				        		</div>
				            </div>
				            <div class="form-group datofactura hide">
				                {!! Form::label('razon', 'Razón', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label input-sm')) !!}
				        		<div class="col-lg-10 col-md-10 col-sm-10">
				        			{!! Form::text('razon', null, array('class' => 'form-control input-sm', 'id' => 'razon', 'readonly' => true, 'maxlength' => '80')) !!}
				        		</div>
				            </div>
				            <div class="form-group datofactura hide">
				                {!! Form::label('direccion', "Dirección", array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label input-sm')) !!}
				        		<div class="col-lg-10 col-md-10 col-sm-10">
				        			{!! Form::text('direccion', null, array('class' => 'form-control input-sm', 'id' => 'direccion', 'maxlength' => '80')) !!}
				        		</div>
				        	</div> 
				        </div>          
			        </div> 
			        <div id="btnRegistrarPago" class="form-group">
						<div class="col-lg-12 col-md-12 col-sm-12 text-right">
							{!! Form::button('<i class="fa fa-check fa-lg"></i> Registrar Pago', array('class' => 'btn btn-success btn-sm', 'id' => 'btnMatricular'.$entidad, 'onclick' => 'realizarPago(this);')) !!}
						</div>
					</div>
		        </div>
		        <div class="col-lg-6 col-md-6 col-sm-6">
		        	<table id="datatable" class="table table-xs table-striped table-bordered">
						<thead>
							<tr>
								<th style="padding:5px;margin:5px; font-size: 13px;" class="text-center" width="10%"><u>#</u></th>
								<th style="padding:5px;margin:5px; font-size: 13px;" class="text-center" width="40%"><u>Fecha</u></th>
								<th style="padding:5px;margin:5px; font-size: 13px;" class="text-center" width="40%"><u>Monto</u></th>
								<th style="padding:5px;margin:5px; font-size: 13px;" class="text-center" width="10%"><u>X</u></th>
							</tr>
						</thead>
						<tbody id="tablaPagos">
						</tbody>
						<tfood>
							<tr>
								<th style="padding:5px;margin:5px; font-size: 13px;" colspan="2" class="text-right" width="10%">Total Pagado</th>
								<th style="padding:5px;margin:5px; font-size: 13px;" class="text-center" width="10%" id="montopagado">0.00</th>
							</tr>
						</tfood>
				    </table>
				    <hr>
				    <div id="infoDocumentoVenta"></div>
		        </div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-lg-12 col-md-12 col-sm-12 text-right">
				{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
			</div>
		</div>
	</div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('1200');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="cuenta"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="efectivo"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="visa"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="master"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total2"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
		//SETEO EL VALOR DE LA MENSUALIDAD
		@if($monto_mensualidad !== NULL)
			$("#total").val("{{ $monto_mensualidad }}");
			$("#cuenta").val("{{ $monto_mensualidad }}");
		@endif
		$("#divDocVenta").hide();
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="direccion"]').val('-');
		llenarTablaPagos('{{ $entidad }}');
		nuevoPago();
	});

	function realizarPago(btn) {
		if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="persona_id"]').val()=="") {
			$.Notification.autoHideNotify('error', 'top right', "¡CUIDADO!",'Debes seleccionar a un alumno.');
		} else {
			if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total2"]').val()=="0.00") {
				$.Notification.autoHideNotify('error', 'top right', "¡CUIDADO!",'Debes digitar un monto mayor a 0.00');
			} else {
				if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="venta"]').val()=="S") {
					if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="tipodocumento"]').val()=="F") {
						if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="ruc"]').val()!==""&&
							$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="razon"]').val()!==""&&
							$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="direccion"]').val()!=="") {
							confirmarPago('{{ $entidad }}', btn);
						} else {
							$.Notification.autoHideNotify('error', 'top right', "CUIDADO!",'Debes ingresar datos de la empresa.');
						}
					}
				} else {
					confirmarPago('{{ $entidad }}', btn);
				}
			}			
		}
	}

	function nuevoPago() {
		mostrarDivDocVenta(false);
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="efectivo"]').val("");
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="visa"]').val("");
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="master"]').val("");
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="ruc"]').val("");
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="razon"]').val("");
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="direccion"]').val("");
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="tipodocumento"]').val("B");
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total2"]').val("0.00");
		numeroSigue(1, '', 'numero');
		generarNumeroVenta();
		setTimeout(function() {
		    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="alumno"]').focus();
		    $("#mensajeMontos").html('');
		}, 500);
	}

	function buscarEmpresa(ruc){  
		if(ruc.length==11) {
			$.ajax({
	            type: 'GET',
	            url: "SunatPHP/demo.php?ruc="+ruc,
	            beforeSend(){
	                $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="ruc"]').val('Comprobando...');
	            },
	            success: function (data, textStatus, jqXHR) {
	                if(data.RazonSocial == null) {
	                    $.Notification.autoHideNotify('error', 'top right', "¡CUIDADO!",'El RUC ingresado no existe... Digite uno válido.');
	                    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="ruc"]').val('').focus();
	                    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="razon"]').val('');
	                    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="direccion"]').val('');
	                } else {
	                    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="ruc"]').val(ruc);
	                    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="razon"]').val(data.RazonSocial);
	                    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="direccion"]').val('-');
	                }
	            }
	        });
		} else {
			$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="razon"]').val('');
			$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="direccion"]').val('-');
		}
    }

	function generarNumeroVenta() {
		var tipodocumento = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="tipodocumento"]').val();
		var tipodocumento_id = 0;
		switch (tipodocumento) {
			case "B":
				$(".datofactura").addClass("hide");
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="serieventa"]').val("001");
				tipodocumento_id = 1;
				break;
			case "F":
				$(".datofactura").removeClass("hide");
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="ruc"]').focus();
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="serieventa"]').val("002");
				tipodocumento_id = 2;
				break;
		}
		numeroSigue(8, tipodocumento_id, 'numeroventa');
	}

	function calcularTotalPago() {
	    var efectivo    = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="efectivo"]').val();
	    var visa        = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="visa"]').val();
	    var master      = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="master"]').val();
	    var totalreal   = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total"]').val();
	    var totalpagado = $("#montopagado").html();
	    var total     = 0.00;
	    var cuenta    = 0.00;
	    if(efectivo == '') {
	        efectivo  = 0.00;
	    } 
	    if(visa == '') {
	        visa      = 0.00;
	    }
	    if(master == '') {
	        master    = 0.00;
	    }
	    if(totalreal == '') {
	        totalreal = 0.00;
	    }
	    if(totalpagado == '') {
	        totalpagado = 0.00;
	    }
	    total = parseFloat(efectivo) + parseFloat(visa) + parseFloat(master);
	    cuenta = parseFloat(totalreal) - parseFloat(efectivo) - parseFloat(visa) - parseFloat(master) - parseFloat(totalpagado);
	    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total2"]').val(total.toFixed(2));
	    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="cuenta"]').val(cuenta.toFixed(2));
	    coincidenciasMontos();
	}

	function coincidenciasMontos() {
		mostrarDivDocVenta(false);
		$("#btnRegistrarPago").show();
	    if(parseFloat($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="cuenta"]').val()) == 0.00) {
	        $("#mensajeMontos").html('Los Montos coinciden').css('color', 'green');
	        $('#genComp').css('display', '');
	        mostrarDivDocVenta(true);
	    } else if(parseFloat($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="cuenta"]').val()) > 0.00) {
	        $("#mensajeMontos").html('Monto a pagar menor.').css('color', 'orange');  
	        $('#genComp').css('display', 'none');
	    } else if(parseFloat($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="cuenta"]').val()) < 0.00) {
	        $("#mensajeMontos").html('Monto a pagar mayor.').css('color', 'red'); 
	        $('#genComp').css('display', 'none');
	        $("#btnRegistrarPago").hide();
	    }
	}

	function mostrarDivDocVenta(mostrar) {
		$("#divDocVenta").hide();
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="venta"]').val("N");
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="venta"]').prop("disabled", true);
		if(mostrar) {
			$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="venta"]').val("S");
			$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="venta"]').prop("disabled", false);
			$("#parteDocumentoVenta").show();
			$("#divDocVenta").show();
			if(parseFloat($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total"]').val()) == 0) {
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="venta"]').val("N");
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="venta"]').prop("disabled", true);
				$("#parteDocumentoVenta").hide();
			}
		}
	}

	function confirmarPago(entidad, idboton) {
		var idformulario = IDFORMMANTENIMIENTO + entidad;
		var form = $(idformulario);
		var data = form.serialize();
		var listar       = 'SI';
		var btn = $(idboton);
		btn.button('loading');
		var accion = $(idformulario).attr('action');
		var metodo = $(idformulario).attr('method');
		$.ajax({
			url : accion,
			type: metodo,
			data: data,
		}).done(function(msg) {
			respuesta = msg;
		}).fail(function(xhr, textStatus, errorThrown) {
			respuesta = 'ERROR';
		}).always(function() {
			btn.button('reset');
			if(respuesta === 'ERROR'){
			}else{
				if (respuesta === 'OK') {
					if (listar === 'SI') {
						buscar('Mensualidad');
					}
					nuevoPago();
					$.Notification.autoHideNotify('success', 'top right', "¡ÉXITO!", 'Pago registrado correctamente');  
					llenarTablaPagos(entidad);   
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="efectivo"]').focus(); 
				} else {
					mostrarErrores(respuesta, idformulario, entidad);
				}
			}
		});
	}

	function numeroSigue(tipomovimiento_id, tipodocumento_id, input_id) {
		$.ajax({
			url : "alumnoseccion/numeroSigue?tipomovimiento_id="+tipomovimiento_id+"&tipodocumento_id="+tipodocumento_id,
			type: "GET",
			dataType: "JSON",
		}).done(function(msg) {
			$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="' + input_id + '"]').val(msg.numero);
			$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="serieventa"]').val(msg.serie);
		});
	}

	function llenarTablaPagos(entidad) {
		var idformulario = IDFORMMANTENIMIENTO + entidad;
		var form = $(idformulario);
		var data = form.serialize();
		var accion = "mensualidad/llenarTablaPagos";
		var metodo = $(idformulario).attr('method');
		$.ajax({
			url : accion,
			type: metodo,
			data: data,
			dataType: "JSON",
		}).done(function(msg) {
			respuesta = msg;
		}).fail(function(xhr, textStatus, errorThrown) {
			respuesta = 'ERROR';
		}).always(function() {
			$("#tablaPagos").html("Cargando, por favor espere...")
			if(respuesta === 'ERROR'){
			}else{
				$("#tablaPagos").html(respuesta.tabla);
				$("#montopagado").html(respuesta.montopagado.toFixed(2));
				$("#infoDocumentoVenta").html(respuesta.documentoventa);
				var total = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total"]').val();
				if(total - $("#montopagado").html() == 0) {
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total2"]').val("0.00");
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="cuenta"]').val("0.00");
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="efectivo"]').val("").prop("readonly", true);
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="visa"]').val("").prop("readonly", true);
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="master"]').val("").prop("readonly", true);
					$("#btnRegistrarPago").remove();
				} else {
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total2"]').val("0.00");
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="cuenta"]').val((total - $("#montopagado").html()).toFixed(2));
				}
			}
		});
	}

	function generarDocumentoVenta(val) {
		$("#parteDocumentoVenta").hide();
		if(val === "S") {
			$("#parteDocumentoVenta").show();
		}
	}
</script>
@endif
