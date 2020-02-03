<?php 
	use App\AlumnoSeccion;
	use App\Cuota;
?>
@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion or '' !!}
<table id="example1" class="table table-bordered table-striped table-condensed table-hover">

	<thead>
		<tr>
			@foreach($cabecera as $key => $value)
				<th style="padding:5px;margin:5px;font-size:13px;" @if($value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<?php
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
		<tr>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ $contador }}</td>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ $value->seccion->grado->nivel->descripcion or '-' }}</td>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ $value->seccion->grado->descripcion or '-' }}</td>
			<td style="padding:5px;margin:5px;font-size:13px;" class="text-center">{{ $value->seccion->descripcion or '-' }}</td>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ $value->alumno==NULL?"-":($value->alumno->apellidopaterno." ".$value->alumno->apellidomaterno." ".$value->alumno->nombres) }}</td>
			<?php 
				$meses = array("Matrícula", "Mensualidad Enero", "Mensualidad Febrero", "Mensualidad Marzo", "Mensualidad Abril", "Mensualidad Mayo", "Mensualidad Junio", "Mensualidad Julio", "Mensualidad Agosto", "Mensualidad Setiembre", "Mensualidad Octubre", "Mensualidad Noviembre", "Mensualidad Diciembre");
				$mesactual = date("m");
				$disabled = false;
				if((int)$anoescolar > (int)date("Y")) {
					$disabled = true;
				}
			?>
			@for($i = 0; $i < 13; $i++)
				{{-- COMPRUEBO SI EXISTE Y ESTÁ PENDIENTE, TAMBIÉN SI ES DE ESTE AÑO PENDIENTE --}}
				<?php
					if(!$disabled && ((int)$anoescolar == (int)date("Y"))) {
						//DESHABILITO LOS MESES MAYORES A ESTE MES
						if($i>$mesactual) {
							$disabled = true;
						}
					}
				?>
				<td style="padding:5px;margin:5px;font-size:13px;" class="text-center">
				@if($disabled)
					{!! Form::button('<div class="fa fa-remove"></div>', array('onclick' => 'modal (\''.URL::route('mensualidad.conceptopago', array("id=".$value->id, 'listar=SIS', 'mes='.$i)).'\', \'Historial de Pagos de '.$meses[$i].'\', this);', 'class' => 'btn btn-xs btn-danger', 'disabled' => true)) !!}
				@else
					<?php
						$logo = "check";//CONFORME
						$color = "success";//VERDE
						//SI NO EXISTE LA CUOTA BOTON ROJO MAS LOGO EQUIS
						$cuota = Cuota::where("cicloacademico_id", "=", $cicloacademico_id)
							->where("alumno_seccion_id", "=", $value->id)
							->where("mes", "=", $i)
							->first();
						//SI EXISTE Y ESTÁ PENDIENTE AUN CAMBIAMOS LOS VALORES DEL LOGO Y COLOR
						if($cuota!==NULL) {
							if($cuota->estado!=="C") {
								$logo = "remove";//NO CONFORME
								$color = "danger";//ROJO
							}
						}
						//SI NO EXISTE CAMBIAMOS EL VALOR DEL LOGO Y LA CUOTA
						else {
							$logo = "remove";//NO CONFORME
							$color = "danger";//ROJO
						}
					?>
					{!! Form::button('<div class="fa fa-'.$logo.'"></div>', array('onclick' => 'modal (\''.URL::route('mensualidad.conceptopago', array("id=".$value->id, 'listar=SIS', 'mes='.$i)).'\', \'Historial de Pagos de '.$meses[$i].'\', this);', 'class' => 'btn btn-xs btn-'.$color)) !!}
				@endif				
				</td>
			@endfor
			<td style="padding:5px;margin:5px;font-size:13px;" class="text-center">{!! Form::button('<div class="fa fa-eye"></div>', array('onclick' => 'modal (\''.URL::route('mensualidad.conceptopago', array("id=".$value->id, 'listar=SI')).'\', \'Historial de Pagos\', this);', 'class' => 'btn btn-xs btn-primary')) !!}
			</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
{!! $paginacion or '' !!}
<script>
	$(document).ready(function () {
		
	});
</script>
@endif