<div class="table-responsive">
	<p>TOTAL DE ALUMNOS: {{ count($resultados) }}</p>
	<table id="example1" class="table table-bordered table-striped table-condensed table-hover">
		<thead>
			<tr>
				<th>#</th>
				<th>Alumno</th>
				<th>Resultado</th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 1; ?>
			@foreach($resultados as $resultado)
				<tr>
					<td>{{ $i }}</td>
					<td>{{ $resultado->alumno }}</td>
					<td>{{ $resultado->resultado . '/' . $totalpreguntas[0]->totalpreguntas }}</td>
				</tr>
				<?php $i++; ?>
			@endforeach
		</tbody>
	</table>
</div>