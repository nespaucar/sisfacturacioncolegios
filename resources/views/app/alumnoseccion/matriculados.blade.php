<?php 
	use App\AlumnoSeccion;
?>
@if($cicloacademico==NULL)
<h4 class="text-danger text-center">Aun no se ha realizado la apertura del año escolar {{$anoescolar}}.</h4>
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
	<div class="panel-body">
		<div class="form-group">
			<div class="col-lg-12 col-md-12 col-sm-12 text-center">
				{!! Form::button('<i class="fa fa-plus fa-lg"></i> Nuevo', array('class' => 'btn btn-info btn-sm', 'id' => 'btnMatricularAlumno', "onclick" => "focoAlumno();", "href" => "#matricularAlumno", "data-toggle" => "collapse")) !!}
			</div>
		</div>
		<div class="form-group collapse" id="matricularAlumno">
			<div class="img img-thumbnail" style="border-style: ridge; padding: 10px; box-shadow: 2px 2px 10px #666;">
		        <div class="col-lg-6 col-md-6 col-sm-6">
		            <div class="form-group text-center">
		                {!! Form::label('numero', 'N° Movimiento', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label input-sm')) !!}
		                <div class="col-lg-3 col-md-3 col-sm-3">
		                    {!! Form::text('numero', "000001", array('class' => 'form-control input-sm', 'id' => 'numero', 'readonly' => 'true')) !!}
		                </div>
		            </div>
		            <div class="form-group">
		        		{!! Form::label('alumno', "Alumno", array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label input-sm')) !!}
		        		<div class="col-lg-10 col-md-10 col-sm-10">
		                {!! Form::hidden('person_id', null, array('id' => 'person_id')) !!}
		        		{!! Form::text('alumno', "", array('class' => 'form-control input-sm', 'id' => 'alumno', 'placeholder' => 'Ingrese Paciente')) !!}
		        		</div>
		        	</div>
		            <div class="form-group">
		            	<div class="col-lg-12 col-md-12 col-sm-12 text-center">
		            		{!! Form::label('formapago', "FORMA DE PAGO", array('class' => 'control-label input-sm caja')) !!}
		            	</div>
		            </div>
		            <div class="form-group">
		                <div class="col-lg-7 col-md-7 col-sm-7 img img-thumbnail" style="border-style: ridge; padding: 10px; box-shadow: 1px 1px 3px #666;">            
		                    <div class="input-group">
		                    	<div class="col-lg-4 col-md-4 col-sm-4">
		                        	<span class="input-group-addon input-sm">EFECTIVO</span>
		                        </div>
		                        <div class="col-lg-8 col-md-8 col-sm-8">
		                        	<input onkeyup="calcularTotalPago();" name="efectivo" id="efectivo" type="text" class="form-control input-sm">
		                        </div>
		                    </div>
		                    <div class="input-group">
								<div class="col-lg-4 col-md-4 col-sm-4">
		                        	<span class="input-group-addon input-sm">VISA</span>
		                        </div>
		                        <div class="col-lg-8 col-md-8 col-sm-8">
			                        <input onkeyup="calcularTotalPago();" name="visa" id="visa" type="text" class="form-control input-sm">
			                        <span style="display:none;" class="input-group-addon input-sm">N°</span>
			                        <input style="display:none;" name="numvisa" id="numvisa" type="text" class="form-control input-sm">
			                    </div>
		                    </div>
		                    <div class="input-group">
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
		                <div class="col-lg-5 col-md-5 col-sm-5">            
		                    <div class="input-group">
		                        <span class="input-group-addon input-sm">TOTAL</span>
		                        <input name="total" id="total" type="text" class="form-control input-sm" readonly="" value="100.00">
		                    </div>
		                    <div class="input-group">
		                        <span class="input-group-addon input-sm">A PAGAR</span>
		                        <input name="total2" id="total2" type="text" class="form-control input-sm" readonly="" value="0.00">
		                    </div>
		                    <div class="input-group">
		                        <span class="input-group-addon input-sm">A CUENTA</span>
		                        <input name="cuenta" id="cuenta" type="text" class="form-control input-sm" readonly="" value="0.00">
		                    </div>
		                    <div class="input-group">
		                    	<b class="text-right" id="mensajeMontos" style="padding-top: 20px; font-size: 12px; color:red"></b>
		                    </div>
		                </div>   
		            </div>
		        </div>
		        <div class="col-lg-6 col-md-6 col-sm-6 img img-thumbnail" style="border-style: ridge; padding: 10px; box-shadow: 1px 1px 3px #666;">
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
		        			{!! Form::text('ruc', null, array('class' => 'form-control input-sm', 'id' => 'ruc')) !!}
		        		</div>
		            </div>
		            <div class="form-group datofactura hide">
		                {!! Form::label('razon', 'Razón', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label input-sm')) !!}
		        		<div class="col-lg-10 col-md-10 col-sm-10">
		        			{!! Form::text('razon', null, array('class' => 'form-control input-sm', 'id' => 'razon')) !!}
		        		</div>
		            </div>
		            <div class="form-group datofactura hide">
		                {!! Form::label('direccion', "Dirección", array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label input-sm')) !!}
		        		<div class="col-lg-10 col-md-10 col-sm-10">
		        			{!! Form::text('direccion', null, array('class' => 'form-control input-sm', 'id' => 'direccion')) !!}
		        		</div>
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
	});

	function matricularAlumno() {

	}

	function focoAlumno() {
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="alumno"]').focus();
	}

	function generarNumero() {
		var tipodocumento = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="tipodocumento"]').val();
		switch (tipodocumento) {
			case "B":
				$(".datofactura").addClass("hide");
				break;
			case "F":
				$(".datofactura").removeClass("hide");
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
	    if(parseFloat($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total"]').val()) == parseFloat($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total2"]').val())) {
	        $("#mensajeMontos").html('Los Montos coinciden').css('color', 'green');
	        $('#genComp').css('display', '');
	        return true;
	    } else if(parseFloat($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total"]').val()) > parseFloat($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total2"]').val())) {
	        $("#mensajeMontos").html('Monto a pagar menor.').css('color', 'orange');  
	        $('#genComp').css('display', 'none');       
	        return true;
	    } else if(parseFloat($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total"]').val()) < parseFloat($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total2"]').val())) {
	        $("#mensajeMontos").html('Monto a pagar mayor.').css('color', 'red'); 
	        $('#genComp').css('display', 'none');       
	        return false;
	    }
	}
</script>
@endif
