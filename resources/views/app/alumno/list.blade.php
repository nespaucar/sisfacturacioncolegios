@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
<script>
	function cambiarsituacion(idalumno) {
		var situacion = $('#situacion' + idalumno).val();
		$.ajax({
			url: 'alumno/cambiarsituacion?idalumno=' + idalumno + '&situacion=' + situacion,
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            type: 'GET',
		}).fail(function(){
			alert('Ocurrió un error');
		});
	}
</script>
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
		<tr  >
			<td>{{ $contador }}</td>
			<td>{{ $value->dni }}</td>
			<td>{{ $value->codigo }}</td>
			<td>{{ $value->nombres.' '.$value->apellidopaterno.' '.$value->apellidomaterno  }}</td>		
			<td>{{ $value->escuela->nombre or  '-'  }}</td>
			<td>{{ $value->especialidad->nombre or '-' }}</td>
			<td>{!! Form::select('situacion' . $value->id, $cboSituacion, $value->situacion, array('class' => 'form-control input-xs', 'id' => 'situacion' . $value->id, 'onchange' => 'cambiarsituacion('. $value->id .');')) !!}</td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-lock"></div> Restablecer contraseña', array('onclick' => 'modal (\''.URL::route($ruta["password"], array($value->id, 'SI')).'\', \''.$titulo_password.'\', this);', 'class' => 'btn btn-xs btn-secondary')) !!}</td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Editar', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-xs btn-warning')) !!}</td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-remove"></div> Eliminar', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-xs btn-danger')) !!}</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
	<tfoot>
		<tr>
			@foreach($cabecera as $key => $value)
				<th @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</tfoot>
</table>
@endif
