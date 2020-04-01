<?php 
	use Illuminate\Support\Facades\Auth;
	use App\Configuracionpago;
	use App\Conceptopago;
	use App\Movimiento;
	use App\Cuota;
	use App\AlumnoCuota;
	use App\Montoconceptopago;
?>
@if($alumnoseccion->cicloacademico==NULL)
<h4 class="text-danger text-center">Aún no se ha realizado la apertura de este año escolar.</h4>
<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
@else
<?php 
	
	$meses = array("MATRICULA", "ABRIL", "AGOSTO", "DICIEMBRE", "ENERO", "MAYO", "SETIEMBRE", "FEBRERO", "JUNIO", "OCTUBRE", "MARZO", "JULIO", "NOVIEMBRE");
	$numeros = array(0, 4, 8, 12, 1, 5, 9, 2, 6, 10, 3, 7, 11);

?>
<style>
	.panel-success, .panel-primary {
		border-style: ridge;
	}
</style>
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
            {!! Form::label('alumno', 'Alumno', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label input-xs')) !!}
            <div class="col-lg-5 col-md-5 col-sm-5">
                {!! Form::text('alumno', ($alumnoseccion->alumno->apellidopaterno." ".$alumnoseccion->alumno->apellidomaterno." ".$alumnoseccion->alumno->nombres), array('class' => 'form-control input-xs', 'id' => 'alumno', 'readonly' => 'true')) !!}
            </div>
            {!! Form::label('matriculado', 'Matriculado', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label input-xs')) !!}
            <div class="col-lg-5 col-md-5 col-sm-5">
                {!! Form::text('matriculado', ($alumnoseccion->seccion->grado->descripcion." grado ".$alumnoseccion->seccion->descripcion." del nivel ".$alumnoseccion->seccion->grado->nivel->descripcion), array('class' => 'form-control input-xs', 'id' => 'matriculado', 'readonly' => 'true')) !!}
            </div>
        </div>
		<div class="form-group">
			<?php 
				$inicio = 0;
				$fin = 4;
				$colorcabecera = "success";
			?>
			@for($aa = 0; $aa < 4; $aa++)
	        <div class="col-lg-3 col-md-3 col-sm-3">
	        	<div class="panel-group">
	        		@for($i = $inicio; $i < $fin; $i++)
	        		<?php 
						if($i == 1) {
							$colorcabecera = "primary";
						}

						//LÓGICA PARA SACAR LOS DATOS DEL ÍTEM
						$user     = Auth::user();
				        $local_id = $user->persona->local_id;
				        $mes                = $numeros[$i];
				        //Buscamos la configuración de pago de mensualidad para el alumno
				        $monto_mensualidad  = 0.00;
				        $monto_pagado  = 0.00;
				        $documento_venta  = "NO GENERADA";
				        $situacion_final  = "CON DEUDA";
				        $situacion_color = "red";
				        $configuracionpago1 = Configuracionpago::where("alumno_id", "=", $alumnoseccion->alumno_id)->first();
				        $configuracionpago2 = Configuracionpago::where("seccion_id", "=", $alumnoseccion->seccion_id)->first();
				        $configuracionpago3 = Configuracionpago::where("grado_id", "=", $alumnoseccion->seccion->grado_id)->first();
				        $configuracionpago4 = Configuracionpago::where("nivel_id", "=", $alumnoseccion->seccion->grado->nivel_id)->first();
				        if($i==0) {
				            $cpago             = Conceptopago::find(6);
				            if($configuracionpago1!==NULL) {
				                $monto_mensualidad = $configuracionpago1->montom."";
				            } else {
				                if($configuracionpago2!==NULL) {
				                    $monto_mensualidad = $configuracionpago2->montom."";
				                } else {
				                    if($configuracionpago3!==NULL) {
				                        $monto_mensualidad = $configuracionpago3->montom."";
				                    } else {
				                        if($configuracionpago4!==NULL) {
				                            $monto_mensualidad = $configuracionpago4->montom."";
				                        } else {
				                            $monto_mensualidad = Montoconceptopago::where("conceptopago_id", "=", $cpago->id)
				                                ->where("local_id", "=", $local_id)
				                                ->first()->monto; //BUSCO EL CONCEPTO DE PAGO PARA LA MENSUALIDAD
				                        }
				                    }
				                }
				            }
				        } else {				        	
					        $cpago = Conceptopago::find(7);
					        if($configuracionpago1!==NULL) {
					            $monto_mensualidad = $configuracionpago1->monto;
					        } else {
					            if($configuracionpago2!==NULL) {
					                $monto_mensualidad = $configuracionpago2->monto;
					            } else {
					                if($configuracionpago3!==NULL) {
					                    $monto_mensualidad = $configuracionpago3->monto;
					                } else {
					                    if($configuracionpago4!==NULL) {
					                        $monto_mensualidad = $configuracionpago4->monto;
					                    } else {
					                        $monto_mensualidad = Montoconceptopago::where("conceptopago_id", "=", $cpago->id)
					                            ->where("local_id", "=", $local_id)
					                            ->first()->monto; //BUSCO EL CONCEPTO DE PAGO PARA LA MENSUALIDAD
					                    }
					                }
					            }
					        }
				        }
				        //Busco la cuota para ver la cantidad que ha pagado y falta pagar
				        $cuota = Cuota::where("cicloacademico_id", "=", $alumnoseccion->cicloacademico_id)
				        	->where("alumno_seccion_id", "=", $alumnoseccion->id)
				        	->where("mes", "=", $mes)
				        	->where("estado", "!=", "A")
				        	->first();
				        if($cuota!==NULL) {
				        	//Busco detalles de cuota
				        	$alumnocuotas = AlumnoCuota::where("alumno_id", "=", $alumnoseccion->alumno_id)
				        		->where("cuota_id", "=", $cuota->id)
				        		->get();
				        	if(count($alumnocuotas) > 0) {
				        		foreach ($alumnocuotas as $alcuo) {
				        			$monto_pagado += $alcuo->monto;
				        		}
				        	}

				        	//BUSCO BOLETA DE VENTA SOLO SI YA ESTÁ PAGADA LA CUOTA
				        	$movimientoventa = Movimiento::where("estado", "!=", "A")
			        			->where("persona_id", "=", $alumnoseccion->alumno_id)
			        			->where("tipomovimiento_id", "=", 8)
			        			->whereIn("tipodocumento_id", [1,2])
			        			->where("cuota_id", "=", $cuota->id)
			        			->where("local_id", "=", $local_id)
			        			->where("cicloacademico_id", "=", $alumnoseccion->cicloacademico_id)
			        			->first();
			        		if($movimientoventa !== NULL) {
			        			$documento_venta = "GENERADA ".($movimientoventa->tipodocumento_id==1?"B":"F").($movimientoventa->tipodocumento_id==1?$user->persona->local->serie:$user->persona->local->serie2)."-".$movimientoventa->numero;
			        			$situacion_final  = "PAGADO";
			        		}
				        	if($cuota->estado=="C") {
				        		$situacion_final  = "CANCELADO";
				        		$situacion_color = "green";
				        	} 
				        }
					?>
				    <div class="panel panel-{{$colorcabecera}}">
				      	<div class="panel-heading text-center" style="color: white;">{{$meses[$i]}}</div>
				      	<div class="panel-body">
				      		<div class="form-group">
				      			<div class="col-lg-6 col-md-6 col-sm-6">
									<h5>A PAGAR</h5>
									<h5>PAGADO</h5>
									<h5>FALTANTE</h5>
									<h5>BOLETA</h5>
									<h5>SITUACION FINAL</h5>
								</div>
								<div class="col-lg-6 col-md-6 col-sm-6">
									<h5>{{number_format($monto_mensualidad,2,'.','')}}</h5>
									<h5>{{number_format($monto_pagado,2,'.','')}}</h5>
									<h5>{{number_format(($monto_mensualidad-$monto_pagado),2,'.','')}}</h5>
									<h5>{{$documento_venta}}</h5>
									<h5 style="color:{{$situacion_color}};">{{$situacion_final}}</h5>
								</div>
							</div>
				      	</div>
				    </div>
				    @endfor
				    <?php 
				    	$inicio = $fin;
				    	$fin += 3;
				    ?>
				 </div>
	        </div>
	        @endfor
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
		configurarAnchoModal('1450');
	});
</script>
@endif
