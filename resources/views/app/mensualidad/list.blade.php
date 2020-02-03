<?php 
	use App\AlumnoSeccion;
?>
@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion or '' !!}
<table id="example1" class="table table-bordered table-striped table-condensed table-hover">

	<thead>
		<tr>
			@foreach($cabecera as $key => $value)
				<th style="padding:5px;margin:5px;font-size:13px;" @if($value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<?php
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
		<tr>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ $contador }}</td>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ $value->seccion->grado->nivel->descripcion or '-' }}</td>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ $value->seccion->grado->descripcion or '-' }}</td>
			<td style="padding:5px;margin:5px;font-size:13px;" class="text-center">{{ $value->seccion->descripcion or '-' }}</td>
			<td style="padding:5px;margin:5px;font-size:13px;">{{ $value->alumno==NULL?"-":($value->alumno->apellidopaterno." ".$value->alumno->apellidomaterno." ".$value->alumno->nombres) }}</td>
			<td style="padding:5px;margin:5px;font-size:13px;" class="text-center">
				{!! Form::button('<div class="fa fa-check"></div>', array('onclick' => 'modal (\''.URL::route('mensualidad.conceptopago', array("id=".$value->id, 'listar=SIS')).'\', \'Historial de Pagos\', this);', 'class' => 'btn btn-xs btn-success')) !!}
			</td>
			<td style="padding:5px;margin:5px;font-size:13px;" class="text-center">
				{!! Form::button('<div class="fa fa-remove"></div>', array('onclick' => 'modal (\''.URL::route('mensualidad.conceptopago', array("id=".$value->id, 'listar=SI')).'\', \'Historial de Pagos\', this);', 'class' => 'btn btn-xs btn-danger')) !!}
			</td>
			<td style="padding:5px;margin:5px;font-size:13px;" class="text-center">
				{!! Form::button('<div class="fa fa-remove"></div>', array('onclick' => 'modal (\''.URL::route('mensualidad.conceptopago', array("id=".$value->id, 'listar=SI')).'\', \'Historial de Pagos\', this);', 'class' => 'btn btn-xs btn-danger')) !!}
			</td>
			<td style="padding:5px;margin:5px;font-size:13px;" class="text-center">
				{!! Form::button('<div class="fa fa-remove"></div>', array('onclick' => 'modal (\''.URL::route('mensualidad.conceptopago', array("id=".$value->id, 'listar=SI')).'\', \'Historial de Pagos\', this);', 'class' => 'btn btn-xs btn-danger')) !!}
			</td>
			<td style="padding:5px;margin:5px;font-size:13px;" class="text-center">
				{!! Form::button('<div class="fa fa-remove"></div>', array('onclick' => 'modal (\''.URL::route('mensualidad.conceptopago', array("id=".$value->id, 'listar=SI')).'\', \'Historial de Pagos\', this);', 'class' => 'btn btn-xs btn-danger')) !!}
			</td>
			<td style="padding:5px;margin:5px;font-size:13px;" class="text-center">
				{!! Form::button('<div class="fa fa-remove"></div>', array('onclick' => 'modal (\''.URL::route('mensualidad.conceptopago', array("id=".$value->id, 'listar=SI')).'\', \'Historial de Pagos\', this);', 'class' => 'btn btn-xs btn-danger')) !!}
			</td>
			<td style="padding:5px;margin:5px;font-size:13px;" class="text-center">
				{!! Form::button('<div class="fa fa-remove"></div>', array('onclick' => 'modal (\''.URL::route('mensualidad.conceptopago', array("id=".$value->id, 'listar=SI')).'\', \'Historial de Pagos\', this);', 'class' => 'btn btn-xs btn-danger')) !!}
			</td>
			<td style="padding:5px;margin:5px;font-size:13px;" class="text-center">
				{!! Form::button('<div class="fa fa-remove"></div>', array('onclick' => 'modal (\''.URL::route('mensualidad.conceptopago', array("id=".$value->id, 'listar=SI')).'\', \'Historial de Pagos\', this);', 'class' => 'btn btn-xs btn-danger')) !!}
			</td>
			<td style="padding:5px;margin:5px;font-size:13px;" class="text-center">
				{!! Form::button('<div class="fa fa-remove"></div>', array('onclick' => 'modal (\''.URL::route('mensualidad.conceptopago', array("id=".$value->id, 'listar=SI')).'\', \'Historial de Pagos\', this);', 'class' => 'btn btn-xs btn-danger')) !!}
			</td>
			<td style="padding:5px;margin:5px;font-size:13px;" class="text-center">
				{!! Form::button('<div class="fa fa-remove"></div>', array('onclick' => 'modal (\''.URL::route('mensualidad.conceptopago', array("id=".$value->id, 'listar=SI')).'\', \'Historial de Pagos\', this);', 'class' => 'btn btn-xs btn-danger')) !!}
			</td>
			<td style="padding:5px;margin:5px;font-size:13px;" class="text-center">
				{!! Form::button('<div class="fa fa-remove"></div>', array('onclick' => 'modal (\''.URL::route('mensualidad.conceptopago', array("id=".$value->id, 'listar=SI')).'\', \'Historial de Pagos\', this);', 'class' => 'btn btn-xs btn-danger')) !!}
			</td>
			<td style="padding:5px;margin:5px;font-size:13px;" class="text-center">
				{!! Form::button('<div class="fa fa-remove"></div>', array('onclick' => 'modal (\''.URL::route('mensualidad.conceptopago', array("id=".$value->id, 'listar=SI')).'\', \'Historial de Pagos\', this);', 'class' => 'btn btn-xs btn-danger')) !!}
			</td>
			<td style="padding:5px;margin:5px;font-size:13px;" class="text-center">
				{!! Form::button('<div class="fa fa-remove"></div>', array('onclick' => 'modal (\''.URL::route('mensualidad.conceptopago', array("id=".$value->id, 'listar=SI')).'\', \'Historial de Pagos\', this);', 'class' => 'btn btn-xs btn-danger')) !!}
			</td>
			<td style="padding:5px;margin:5px;font-size:13px;" class="text-center">{!! Form::button('<div class="fa fa-eye"></div>', array('onclick' => 'modal (\''.URL::route('mensualidad.conceptopago', array("id=".$value->id, 'listar=SI')).'\', \'Historial de Pagos\', this);', 'class' => 'btn btn-xs btn-primary')) !!}
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