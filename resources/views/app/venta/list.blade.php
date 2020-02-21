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
		<tr @if($value['estado'] == "A") style="color: red;" title="ANULADO" @endif>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ $contador }}</td>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ date("d-m-Y", strtotime($value->fecha)) }}</td>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ $value->tipodocumento_id==1?"BOLETA":"FACTURA" }}</td>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ str_pad($value->serie,5,'0',STR_PAD_LEFT)."-".str_pad($value->numero,8,'0',STR_PAD_LEFT) }}</td>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ $value->persona!==NULL?($value->persona->apellidopaterno . " " . $value->persona->apellidomaterno." ".$value->persona->nombres):"-" }}</td>
			<td style="padding:5px;margin:5px;"><font style="font-size:13px;">{{ number_format($value->total,2,'.','') }}</font></td>
			<td style="padding:5px;margin:5px;"><font style="font-size:13px; font-weight: bold; @if($value['estado'] == "P") color: green; @else color: red; @endif">{{ $value['estado'] == "P"?"PAGADO":"ANULADO" }}</font></td>
			<td style="padding:5px;margin:5px;"><font style="font-size:13px;">-</font></td>
			<td style="padding:5px;margin:5px;"><font style="font-size:13px;">-</font></td>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ $value->responsable!==NULL?($value->responsable->apellidopaterno . " " . $value->responsable->apellidomaterno." ".$value->persona->nombres):"-" }}</td>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ $value->conceptopago!==NULL?$value->conceptopago->nombre:"-" }}</td>
			@if($value->estado !== "A" && $value->tipomovimiento_id !== 5) {{-- LA APERTURA Y CIERRE NO SE PUEDEN ANULAR --}}
				<td class="text-center" style="padding:5px;margin:5px;">{!! Form::button('<div class="glyphicon glyphicon-print"></div>', array('onclick' => '#', 'class' => 'btn btn-xs btn-info')) !!}</td>
				<td class="text-center" style="padding:5px;margin:5px;">{!! Form::button('<div class="glyphicon glyphicon-remove"></div>', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-xs btn-danger')) !!}</td>
			@else
				<td>-</td>
				<td>-</td>
			@endif
			
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif