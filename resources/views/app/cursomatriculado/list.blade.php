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
			<td>{{ $value->curso->profesor->nombres . ' ' . $value->curso->profesor->apellidopaterno }}</td>
			<td>{{ $value->fecha_matricula }}</td>
			<td>
			{!! Form::button('<div class="glyphicon glyphicon-list"></div> Desmatricularme', array('onclick' => 'modal (\''.URL::route($ruta["confirmarDesmatricularme"], array($value->id, 'SI')).'\', \''.$titulo_desmatricularme.'\', this);', 'class' => 'btn btn-xs btn-warning')) !!}
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