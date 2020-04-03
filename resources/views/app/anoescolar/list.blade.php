@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else

{!! $paginacion or '' !!}
<table id="example1" class="table table-bordered table-striped table-condensed table-hover">

	<thead>
		<tr>
			@foreach($cabecera as $key => $value)
				<th @if($value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<?php
		$contador = $inicio + 1;
		$totalefectivo = 0;
		$totalmaster = 0;
		$totalvisa = 0;
		$totalegresos = 0;
		?>
		@foreach ($lista as $key => $value)
		<?php 

		if($value->estado == "P") {
			if($value->conceptopago!==NULL) {
				switch ($value->conceptopago->tipo) {
					case 'I':
						$totalefectivo+=$value->totalefectivo;
						$totalmaster+=$value->totalmaster;
						$totalvisa+=$value->totalvisa;
						break;
					case 'E':
						$totalegresos+=$value->total;
						break;
				}
			}				
		}

		?>
		<tr @if($value['estado'] == "A") style="color: red;" title="ANULADO" @endif>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ $contador }}</td>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ str_pad($value->numero,8,'0',STR_PAD_LEFT) }}</td>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ date("d-m-Y", strtotime($value->fecha)) }}</td>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ $value->persona!==NULL?($value->persona->apellidopaterno . " " . $value->persona->apellidomaterno." ".$value->persona->nombres):"-" }}</td>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ $value->responsable!==NULL?($value->responsable->apellidopaterno . " " . $value->responsable->apellidomaterno." ".$value->responsable->nombres):"-" }}</td>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ $value->conceptopago!==NULL?$value->conceptopago->nombre:"-" }}</td>
			<td style="padding:5px;margin:5px;"><font style="font-size:13px;color:green;font-weight: bold;">{{ $value->conceptopago!==NULL?($value->conceptopago->tipo=="I"?$value->total:""):"" }}</font></td>
			<td style="padding:5px;margin:5px;"><font style="font-size:13px;color:green;font-weight: bold;">{{ $value->conceptopago!==NULL?($value->conceptopago->tipo=="I"?"":$value->total):"" }}</font></td>
			<td style="padding:5px;margin:5px;"><font style="font-size:13px;">{{ (float)$value->totalefectivo==0?"":$value->totalefectivo }}</font></td>
			<td style="padding:5px;margin:5px;"><font style="font-size:13px;">{{ (float)$value->totalvisa==0?"":$value->totalvisa }}</font></td>
			<td style="padding:5px;margin:5px;"><font style="font-size:13px;">{{ (float)$value->totalmaster==0?"":$value->totalmaster }}</font></td>
			<td style="padding:5px;margin:5px;"><font style="font-size:13px;">{{ $value->comentario }}</font></td>
			<td style="padding:5px;margin:5px;"><font style="font-size:13px;">{{ $value->estado=="P"?"PAGADO":"ANULADO" }}</font></td>
			@if($value->estado !== "A" && $value->tipomovimiento_id !== 5) {{-- LA APERTURA Y CIERRE NO SE PUEDEN ANULAR --}}
				{{--<td class="text-center" style="padding:5px;margin:5px;">{!! Form::button('<div class="glyphicon glyphicon-remove"></div>', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-xs btn-danger')) !!}</td>--}}
			@else
				{{--<td>-</td>--}}
			@endif
			
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>

<br>

<div class="col-md-offset-4 col-md-4">
	<table id="datatable" class="table table-xs table-striped table-bordered">
		<tr>
			<th colspan="2" class="text-center">RESUMEN DE CAJA</th>
		</tr>
		<tr>
			<th style="padding:5px;margin:5px;" class="text-center" width="70%"><u>CONCEPTO</u></th>
			<th style="padding:5px;margin:5px;" class="text-center" width="30%"><u>MONTO</u></th>
		</tr>
		<tr>
			<th style="padding:5px;margin:5px;" class="text-center" width="70%">INGRESOS</th>
			<th style="padding:5px;margin:5px;" class="text-center" width="30%">{{ number_format($totalefectivo + $totalmaster + $totalvisa,2,'.','') }}</th>
		</tr>
		<tr>
			<td style="padding:5px;margin:5px;" class="text-center" width="70%">Efectivo</td>
			<td style="padding:5px;margin:5px;" class="text-center" width="30%">{{ number_format($totalefectivo,2,'.','') }}</td>
		</tr>
		<tr>
			<td style="padding:5px;margin:5px;" class="text-center" width="70%">Visa</td>
			<td style="padding:5px;margin:5px;" class="text-center" width="30%">{{ number_format($totalvisa,2,'.','') }}</td>
		</tr>
		<tr>
			<td style="padding:5px;margin:5px;" class="text-center" width="70%">Master</td>
			<td style="padding:5px;margin:5px;" class="text-center" width="30%">{{ number_format($totalmaster,2,'.','') }}</td>
		</tr>
		<tr>
			<th style="padding:5px;margin:5px;" class="text-center" width="70%">EGRESOS</th>
			<th style="padding:5px;margin:5px;" class="text-center" width="30%">{{ number_format($totalegresos,2,'.','') }}</th>
		</tr>
		<tr>
			<th style="padding:5px;margin:5px;" class="text-center" width="70%">SALDO</th>
			<th style="padding:5px;margin:5px;" class="text-center" width="30%">{{ number_format($totalefectivo + $totalmaster + $totalvisa - $totalegresos,2,'.','') }}</th>
		</tr>
    </table>
</div>  
@endif