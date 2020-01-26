@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
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
			<td>{{ $value->curso->descripcion }}</td>
			<td>@if($value->estado == 1) ACTIVO @else ACTIVADO @endif</td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-list"></div> ' . count($value->preguntas) . ' Preguntas', array('onclick' => 'modal(\''.URL::route($ruta["listarpreguntas"], $value->id).'\', \'Preguntas para: '.$value->descripcion.'\', this);', 'class' => 'btn btn-default btn-xs')) !!}</td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-eye-open"></div> Resultados', array('onclick' => 'modal(\''.URL::route($ruta["resultados"], $value->id).'\', \'Resultados Resultados de Examen: ' . $value->descripcion . '\', this);', 'class' => 'btn btn-default btn-xs')) !!}</td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Editar', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-xs btn-warning')) !!}</td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-remove"></div> Eliminar', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-xs btn-danger')) !!}</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
{!! $paginacion or '' !!}
@endif
