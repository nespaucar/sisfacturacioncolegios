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
			<td>{{ $value->nivel!==NULL?$value->nivel->descripcion:"-" }}</td>
			<td>{{ $value->nivel->local==NULL?"-":$value->nivel->local->nombre }}</td>
			<td class="text-center">{!! Form::button('<div class="glyphicon glyphicon-list"></div> Secciones', array('onclick' => 'modal (\''.URL::route($ruta["secciones"], array("id"=>$value->id)).'\', \'Secciones de grado ' . $value->descripcion . ' del nivel ' . ($value->nivel!==NULL?$value->nivel->descripcion:"-") . ' del colegio ' . ($value->nivel->local==NULL?"-":$value->nivel->local->nombre) . '\', this);', 'class' => 'btn btn-xs btn-success')) !!} &nbsp;&nbsp;&nbsp; {{ count($value->secciones) }}</td>
			<td class="text-center">{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Editar', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-xs btn-warning')) !!}</td>
			<td class="text-center">{!! Form::button('<div class="glyphicon glyphicon-remove"></div> Eliminar', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-xs btn-danger')) !!}</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
{!! $paginacion or '' !!}
@endif