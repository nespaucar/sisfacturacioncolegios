<?php 
	use App\Usuario;
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
		<tr  >
			<td style="padding:5px;margin:5px;font-size:13px;">{{ $contador }}</td>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ $value->dni }}</td>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ $value->apellidopaterno.' '.$value->apellidomaterno . ' ' . $value->nombres  }}</td>		
			<td style="padding:5px;margin:5px;font-size:13px;">{{ $value->email or  '-'  }}</td>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ $value->estado=="H"?"HABILITADO":"DESHABILITADO" }}</td>
			<?php 

				//BUSCAMOS INFORMACIÓN DEL APODERADO
				$inforapoderado = "-";
				if(count($value->apoderados)>0) {
					foreach ($value->apoderados as $a) {
						$inforapoderado = $a->apoderado->apellidopaterno . " " . $a->apoderado->apellidomaterno . " " . $a->apoderado->nombres . " (" . $a->apoderado->telefono . ")";
						//$usuapoderado = Usuario::where("persona_id", "=", $a->telefono)->first();
					}
				}

			?>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ $inforapoderado }}</td>
			<td class="text-center">{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Editar', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-xs btn-warning')) !!}</td>
			@if($value->estado=="I")
				<td style="padding:5px;margin:5px;font-size:13px;" class="text-center">{!! Form::button('<div class="glyphicon glyphicon-check"></div> Habilitar', array('onclick' => 'modal (\''.URL::route($ruta["alterarestado"], array("id=".$value->id, 'listarLuego=SI', "estado=H")).'\', \'Habilitar Alumno\', this);', 'class' => 'btn btn-xs btn-success')) !!}</td>
			@else
				<td style="padding:5px;margin:5px;font-size:13px;" class="text-center">{!! Form::button('<div class="glyphicon glyphicon-remove"></div> Deshabilitar', array('onclick' => 'modal (\''.URL::route($ruta["alterarestado"], array("id=".$value->id, 'listarLuego=SI', "estado=I")).'\', \'Deshabilitar Alumno\', this);', 'class' => 'btn btn-xs btn-warning')) !!}</td>
			@endif
			<td style="padding:5px;margin:5px;font-size:13px;" class="text-center">{!! Form::button('<div class="glyphicon glyphicon-remove"></div> Eliminar', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-xs btn-danger')) !!}</td>
			<td style="padding:5px;margin:5px;font-size:13px;" class="text-center">{!! Form::button('<div class="fa fa-eye"></div> Apoderado', array('onclick' => 'modal (\''.URL::route('alumno.createapoderado', array("id=".$value->id, 'listar=SI')).'\', \'Apoderado del alumno '.$value->apellidopaterno.' '.$value->apellidomaterno.' '.$value->nombres.'\', this);', 'class' => 'btn btn-xs btn-primary')) !!}
			@if(count($value->apoderados)>0)
				&nbsp;&nbsp; SI
			@else
				&nbsp;&nbsp; NO
			@endif
			</td>
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