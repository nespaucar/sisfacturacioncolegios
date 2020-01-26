<?php 
	use App\AlumnoExamen;
?>
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
			<td>{{ $value->curso->profesor->nombres }} {{ $value->curso->profesor->apellidopaterno }}</td>
			<?php $alumnoexamen = AlumnoExamen::where('alumno_id', $alumno_id)->where('examen_id', $value->id)->get(); ?>
			@if(count($alumnoexamen) == 0)
				@if(count($value->preguntas) > 0)
					<td>{!! Form::button('<div class="glyphicon glyphicon-list"></div> ' . count($value->preguntas) . ' Preguntas', array('onclick' => 'cargarRuta(\'alumnoexamen/llenarexamen?examen_id=' . $value->id . '\', \'container\');', 'class' => 'btn btn-primary btn-xs')) !!}</td>
					<td>{!! Form::button('<div class="glyphicon glyphicon-remove"></div> Pendiente', array('onclick' => '#', 'class' => 'btn btn-xs btn-danger')) !!}</td>
				@else
					<td>{!! Form::button('<div class="glyphicon glyphicon-list"></div> 0 Preguntas', array('class' => 'btn btn-default btn-xs')) !!}</td>
					<td>{!! Form::button('<div class="glyphicon glyphicon-remove"></div> Pendiente', array('onclick' => '#', 'class' => 'btn btn-xs btn-danger')) !!}</td>
				@endif					
			@else
			<td>{!! Form::button('<div class="glyphicon glyphicon-list"></div> Ver mi Nota', array('class' => 'btn btn-info btn-xs', 'onclick' => 'modal (\'' . URL::route($ruta["respuestasexamen"], array('examen_id'=>$value->id)) . '\', \'Respuestas de examen: ' . $value->descripcion . '\', this);')) !!}</td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-ok"></div> Completo', array('onclick' => '#', 'class' => 'btn btn-xs btn-success')) !!}</td>
			@endif
			
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
{!! $paginacion or '' !!}
@endif