<?php 
	use App\AlumnoSeccion;
?>
@if($cicloacademico==NULL)
<h4 class="text-danger text-center">Aún no se ha realizado la apertura del año escolar. {{$anoescolar}}.</h4>
<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
@else
<?php 
	$alumnosecciones  = AlumnoSeccion::where("cicloacademico_id", "=", $cicloacademico->id)
        ->where("seccion_id", "=", $seccion_id)
        ->get();
?>
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($cicloacademico, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	{!! Form::hidden('seccion_id', $seccion_id, array('id' => 'seccion_id')) !!}
	{!! Form::hidden('anoescolar', $anoescolar, array('id' => 'anoescolar')) !!}
	{!! Form::hidden('cicloacademico_id', $cicloacademico!==NULL?$cicloacademico->id:"", array('id' => 'cicloacademico_id')) !!}
	<div class="panel-body">
		<div class="form-group">
			<div class="col-lg-12 col-md-12 col-sm-12 text-center">
				{!! Form::button('<i class="fa fa-plus fa-lg"></i> Nuevo', array('class' => 'btn btn-info btn-sm', "onmouseup" => "nuevoAlumno();", "href" => "#matricularAlumno", "data-toggle" => "collapse")) !!}
			</div>
		</div>
		<div class="form-group collapse" id="matricularAlumno">
			<div class="img img-thumbnail" style="border-style: ridge; padding: 10px; box-shadow: 2px 2px 10px #666;">
		        <div id="divDatosAlumno" class="col-lg-12 col-md-12 col-sm-12">
		            <div class="form-group text-center">
		                {!! Form::label('numero', 'N° Movimiento', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label input-sm')) !!}
		                <div class="col-lg-3 col-md-3 col-sm-3">
		                    {!! Form::text('numero', "000001", array('class' => 'form-control input-sm', 'id' => 'numero', 'readonly' => 'true')) !!}
		                </div>
		            </div>
		            <div class="form-group">
		        		{!! Form::label('alumno', "Alumno", array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label input-sm')) !!}
		        		<div class="col-lg-10 col-md-10 col-sm-10">
		                {!! Form::hidden('persona_id', null, array('id' => 'persona_id')) !!}
		        		{!! Form::text('alumno', "", array('class' => 'form-control input-sm', 'id' => 'alumno', 'placeholder' => 'Ingrese Paciente')) !!}
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
		                        	<input name="total" id="total" type="text" class="form-control input-sm" readonly="" value="0.00">
		                        </div>
		                    </div>
		                    <div class="form-group">
		                    	<div class="col-lg-4 col-md-4 col-sm-4">
		                        	<span class="input-group-addon input-sm">A PAGAR</span>
		                        </div>
		                        <div class="col-lg-8 col-md-8 col-sm-8">
		                        	<input name="total2" id="total2" type="text" class="form-control input-sm" readonly="" value="0.00">
		                        </div>
		                    </div>
		                    <div class="form-group">
		                    	<div class="col-lg-4 col-md-4 col-sm-4">
		                        	<span class="input-group-addon input-sm">A CUENTA</span>
		                        </div>
		                        <div class="col-lg-8 col-md-8 col-sm-8">
		                        	<input name="cuenta" id="cuenta" type="text" class="form-control input-sm" readonly="" value="0.00">
		                        </div>
		                    </div>
		                    <div class="form-group">
		                    	<div class="col-lg-8 col-md-8 col-sm-8">
		                    		<b id="mensajeMontos" style="padding-top: 20px; font-size: 12px; color:red"></b>
		                    	</div>
		                    </div>
		                </div>   
		            </div>		            
		        </div>
		        <div id="divDocVenta" class="col-lg-6 col-md-6 col-sm-6 img img-thumbnail" style="border-style: ridge; padding: 10px; box-shadow: 1px 1px 3px #666;">
		        	<div class="form-group">
		            	<div class="col-lg-12 col-md-12 col-sm-12 text-center">
		            		{!! Form::label('formapago', htmlentities('DOCUMENTO DE VENTA'), array('class' => 'control-label input-sm caja')) !!}
		            	</div>
		            </div>
		            <div class="form-group">
		        		<div class="col-lg-5 col-md-5 col-sm-5">
		        			{!! Form::select('tipodocumento', array("B"=>"BOLETA", "F"=>"FACTURA"), null, array('class' => 'form-control input-sm', 'id' => 'tipodocumento', 'onchange' => 'generarNumero()')) !!}
		        		</div>
		                {!! Form::label('numeroventa', 'N°', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label input-sm')) !!}
		        		<div class="col-lg-3 col-md-3 col-sm-3">
		        			{!! Form::text('serieventa', "002", array('class' => 'form-control input-sm', 'id' => 'serieventa', "readonly")) !!}
		        		</div>
		                <div class="col-lg-3 col-md-3 col-sm-3">
		        			{!! Form::text('numeroventa', "000002", array('class' => 'form-control input-sm', 'id' => 'numeroventa', 'readonly' => 'true')) !!}
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
		        <div class="form-group">
					<div class="col-lg-12 col-md-12 col-sm-12 text-right">
						{!! Form::button('<i class="fa fa-check fa-lg"></i> Matricular a Alumno', array('class' => 'btn btn-success btn-sm', 'id' => 'btnMatricular'.$entidad, 'onclick' => 'matricularAlumno(this);')) !!}
					</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<table id="datatable" class="table table-xs table-striped table-bordered">
					<thead>
						<tr>
							<th style="padding:5px;margin:5px;" class="text-center" width="10%"><u>#</u></th>
							<th style="padding:5px;margin:5px;" class="text-center" width="20%"><u>DNI</u></th>
							<th style="padding:5px;margin:5px;" class="text-center" width="60%"><u>Estudiante</u></th>
							<th style="padding:5px;margin:5px;" class="text-center" width="10%"><u>X</u></th>
						</tr>
					</thead>
					<tbody id="tablaAlumnos">
						@if(count($alumnosecciones)>0)
							<?php $contador = 1; ?>
							@foreach($alumnosecciones as $apal)
								<tr id="tabAlumnos{{$apal->alumno->id}}">
									<td style="padding:5px;margin:5px;" class="text-center">*</td>
									<td style="padding:5px;margin:5px;" class="text-center">{{$apal->alumno->dni}}</td>
									<td style="padding:5px;margin:5px;" class="text-center">{{$apal->alumno->apellidopaterno." ".$apal->alumno->apellidomaterno." ".$apal->alumno->nombres}}</td>
									<td style="padding:5px;margin:5px;" class="text-center">
										<a href="javascript:0" onclick="removeDetalle('{{$apal->alumno->id}}');" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-remove"></i></a>
										<input type="hidden" class="idDetallitos" value="{{$apal->alumno->id}}">
									</td>
								</tr>
								<?php $contador++; ?>
							@endforeach
						@endif
					</tbody>
			    </table>
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
		configurarAnchoModal('950');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="cuenta"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="efectivo"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="visa"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="master"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total2"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
		var entidad = new Bloodhound({
			datumTokenizer: function (d) {
				return Bloodhound.tokenizers.whitespace(d.value);
			},
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				cache: false,
				url: 'configuracionpago/alumnoautocompleting/%QUERY',
				filter: function (entidad) {
					return $.map(entidad, function (movie) {
						return {
							value: movie.value,
							id: movie.id,
							dni: movie.dni
						};
					});
				}
			}
		});
		entidad.initialize();
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="alumno"]').typeahead(null,{
			displayKey: 'value',
			source: entidad.ttAdapter()
		}).on('typeahead:selected', function (object, datum) {
			$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="persona_id"]').val(datum.id);
			$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="alumno"]').val(datum.dni+" - "+datum.value);
			$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="efectivo"]').focus();
			entidad.initialize();
		}).on("keyup", function(e) {
			e.preventDefault();
		    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="persona_id"]').val("");
		});

		$(".twitter-typeahead").prop("style", ""); //PARA QUITAR ESTILO A TYPEAHEAD

		//SETEO EL VALOR DE LA MATRÍCULA
		@if($cmatricula !== NULL)
			$("#total").val("{{ $cmatricula->monto }}");
		@endif

		$("#divDocVenta").hide();
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="direccion"]').val('-');
	});

	function matricularAlumno(btn) {
		if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="persona_id"]').val()=="") {
			$.Notification.autoHideNotify('error', 'top right', "¡CUIDADO!",'Debes seleccionar a un alumno.');
            $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="alumno"]').val('').focus();
		} else {
			confirmarMatriculaDeAlumno('{{ $entidad }}', btn);
		}
	}

	function nuevoAlumno() {
		mostrarDivDocVenta(false);
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="alumno"]').typeahead('val', '');
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="alumno"]').val("");
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="persona_id"]').val("");
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="efectivo"]').val("");
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="visa"]').val("");
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="master"]').val("");
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="ruc"]').val("");
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="razon"]').val("");
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="direccion"]').val("");
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="tipodocumento"]').val("B");
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total2"]').val("0.00");
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="cuenta"]').val("0.00");
		generarNumero();
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

	function generarNumero() {
		var tipodocumento = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="tipodocumento"]').val();
		switch (tipodocumento) {
			case "B":
				$(".datofactura").addClass("hide");
				break;
			case "F":
				$(".datofactura").removeClass("hide");
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="ruc"]').focus();
				break;
		}
	}

	function calcularTotalPago() {
	    var efectivo = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="efectivo"]').val();
	    var visa = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="visa"]').val();
	    var master = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="master"]').val();
	    var totalreal = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total"]').val();
	    var total = 0.00;
	    var cuenta = 0.00;
	    if(efectivo == '') {
	        efectivo = 0.00;
	    } 
	    if(visa == '') {
	        visa = 0.00;
	    }
	    if(master == '') {
	        master = 0.00;
	    }
	    if(totalreal == '') {
	        totalreal = 0.00;
	    }
	    total = parseFloat(efectivo) + parseFloat(visa) + parseFloat(master);
	    cuenta = parseFloat(totalreal) - parseFloat(efectivo) - parseFloat(visa) - parseFloat(master);
	    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total2"]').val(total.toFixed(2));
	    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="cuenta"]').val(cuenta.toFixed(2));
	    coincidenciasMontos();
	}

	function coincidenciasMontos() {
		mostrarDivDocVenta(false);
	    if(parseFloat($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total"]').val()) == parseFloat($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total2"]').val())) {
	        $("#mensajeMontos").html('Los Montos coinciden').css('color', 'green');
	        $('#genComp').css('display', '');
	        mostrarDivDocVenta(true);
	    } else if(parseFloat($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total"]').val()) > parseFloat($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total2"]').val())) {
	        $("#mensajeMontos").html('Monto a pagar menor.').css('color', 'orange');  
	        $('#genComp').css('display', 'none');
	    } else if(parseFloat($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total"]').val()) < parseFloat($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total2"]').val())) {
	        $("#mensajeMontos").html('Monto a pagar mayor.').css('color', 'red'); 
	        $('#genComp').css('display', 'none');
	    }
	}

	function mostrarDivDocVenta(mostrar) {
		$("#divDocVenta").hide();
		$("#divDatosAlumno").addClass("col-lg-12").addClass("col-md-12").addClass("col-sm-12");
		$("#divDatosAlumno").removeClass("col-lg-6").removeClass("col-md-6").removeClass("col-sm-6");
		if(mostrar) {
			$("#divDatosAlumno").removeClass("col-lg-12").removeClass("col-md-12").removeClass("col-sm-12");
			$("#divDatosAlumno").addClass("col-lg-6").addClass("col-md-6").addClass("col-sm-6");
			$("#divDocVenta").show();
		}
	}

	function confirmarMatriculaDeAlumno(entidad, idboton) {
		var idformulario = IDFORMMANTENIMIENTO + entidad;
		alert(idformulario);
		var form = $(idformulario);
		var data = form.serialize();
		alert(data);
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
					cerrarModal();
					if (listar === 'SI') {
						if(typeof entidad2 != 'undefined' && entidad2 !== ''){
							entidad = entidad2;
						}
						buscarCompaginado('', 'Accion realizada correctamente', entidad, 'OK');
					}  
					$("#matricularAlumno").collapse("hide");
					$.Notification.autoHideNotify('success', 'top right', "¡ÉXITO!", 'Alumno registrado correctamente');      
				} else {
					mostrarErrores(respuesta, idformulario, entidad);
				}
			}
		});
	}
</script>
@endif
