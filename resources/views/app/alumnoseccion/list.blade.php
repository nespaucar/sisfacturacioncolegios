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
		?>
		@foreach ($lista as $key => $value)
		<tr>
			<td>{{ $contador }}</td>
			<td>{{ $value->grado->nivel->descripcion or '-' }}</td>
			<td>{{ $value->grado->descripcion or '-' }}</td>
			<td class="text-center">{{ $value->descripcion }}</td>
			<td class="text-center">{{ $anoescolar }}</td>
			<td class="text-center" style="padding:5px;margin:5px;font-size:13px;" class="text-center">{!! Form::button('<div class="fa fa-eye"></div> Matriculados', array('onclick' => 'modal (\''.URL::route('alumnoseccion.matriculados', array("id=".$value->id, 'listar=SI', 'anoescolar='.$anoescolar)).'\', \'Alumnos matriculados en ' . ($value->grado!==NULL?$value->grado->descripcion:'-') . ' grado '.($value->descripcion) . ' del nivel ' . ($value->grado!==NULL?($value->grado->nivel!==NULL?$value->grado->nivel->descripcion:'-'):'-') . '\', this);', 'class' => 'btn btn-xs btn-primary')) !!}
				&nbsp;&nbsp; {{count($value->alumnos)}}
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