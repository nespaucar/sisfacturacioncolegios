<div class="table-responsive">
	<p>CURSO: {{ $curso->descripcion }}</p>
	<p>TOTAL DE ALUMNOS: {{ count($curso->alumnocursos) }}</p>
	<table id="example1" class="table table-bordered table-striped table-condensed table-hover">
		<thead>
			<tr>
				<th>#</th>
				<th>Nombre</th>
				<th>DNI</th>
				<th>Dirección</th>
			</tr>
		</thead>
		<tbody>
			<?php $contador = 1 ?>
			@foreach($curso->alumnocursos as $alumnocurso)
				<tr>
					<td>{{ $contador }}</td>
					<td>{{ $alumnocurso->alumno->nombres }} {{ $alumnocurso->alumno->apellidopaterno }} {{ $alumnocurso->alumno->apellidomaterno }}</td>
					<td>{{ $alumnocurso->alumno->dni }}</td>
					<td>{{ $alumnocurso->alumno->direccion }}</td>
					<?php $contador = $contador + 1; ?>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>