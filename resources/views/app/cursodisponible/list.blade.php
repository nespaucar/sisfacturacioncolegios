@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron cursos activados.</h3>
@else
{!! $paginacion or '' !!}
<table id="example1" class="table table-bordered table-striped table-condensed table-hover">

	<thead>
		<tr>
			@foreach($cabecera as $key => $value)
				<th @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<?php
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
		<tr>
			<td>{{ $contador }}</td>
			<td>{{ $value->descripcion }}</td>
			<td>{{ $value->apertura }}</td>
			<td>{{ $value->profesor->nombres . ' ' . $value->profesor->apellidopaterno }}</td>
			<td>@if($value->estado == 1) ACTIVADO @else DESACTIVADO @endif</td>
			<td>
			{!! Form::button('<div class="glyphicon glyphicon-list"></div> Matricularme', array('onclick' => 'modal (\''.URL::route($ruta["confirmarMatricularme"], array($value->id, 'SI')).'\', \''.$titulo_matricularme.'\', this);', 'class' => 'btn btn-xs btn-success')) !!}
			</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
{!! $paginacion or '' !!}
@endif