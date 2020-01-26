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
			<td>{{ $value->apertura }}</td>
			<td>{{ $value->profesor->nombres . ' ' . $value->profesor->apellidopaterno }}</td>
			<td id="btnActivarCurso{{ $value->id }}">@if($value->estado == 1) 
					{!! Form::button('<div class="glyphicon glyphicon-ok"></div> Activado', array('data-route' => URL::route($ruta["activarcurso"], array($value->id, 0)), 'class' => 'btnActivarCurso btn btn-xs btn-success')) !!}
				@else 
					{!! Form::button('<div class="glyphicon glyphicon-remove"></div> Desactivado', array('data-route' => URL::route($ruta["activarcurso"], array($value->id, 1)), 'class' => 'btnActivarCurso btn btn-xs btn-danger')) !!}
				@endif
			</td>
			<td>
			{!! Form::button('<div class="glyphicon glyphicon-list"></div> Matriculados', array('onclick' => 'modal (\''.URL::route($ruta["matriculados"], $value->id).'\', \''.$titulo_matriculados.'\', this);', 'class' => 'btn btn-xs btn-primary')) !!}
			</td>
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

<script>
	$(document).on('click', '.btnActivarCurso', function(e){
		e.preventDefault();
		var btn = $(this);
		$.ajax({
			url:btn.data('route'),
			beforeSend: function(){
				btn.html('Loading...').addClass('disabled', 'disabled');
			},
			success: function(a) {
				btn.parent().html(a);
			}
		});
	});	
</script>
