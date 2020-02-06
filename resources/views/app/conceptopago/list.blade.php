<?php 
	
	use Illuminate\Support\Facades\Auth;
	use App\Montoconceptopago;
	$user     = Auth::user();
	$local_id = $user->persona->local_id;

?>
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
		@if($value->id == 6 || $value->id == 7)
		<tr>
			<td>{{ $contador }}</td>
			<td>{{ $value->nombre }}</td>
			<?php 
				$detallepago = Montoconceptopago::where("conceptopago_id", "=", $value->id)
	                ->where("local_id", "=", $local_id)
	                ->first();
			?>
			<td class="text-center">{{ $detallepago->monto }}</td>
			<td>{{ $value->tipo=="I"?"INGRESO":"EGRESO" }}</td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Editar', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-xs btn-warning')) !!}</td>
			<!--<td>{!! Form::button('<div class="glyphicon glyphicon-remove"></div> Eliminar', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-xs btn-danger')) !!}</td>-->
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endif
		@endforeach
	</tbody>
</table>
{!! $paginacion or '' !!}
@endif