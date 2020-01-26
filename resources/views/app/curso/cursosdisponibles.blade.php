<!-- Page-Title -->
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">{{ $title }}</h4>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="row">
    <div class="col-sm-12">
        <div class="card-box table-responsive">

            <div class="row m-b-30">
                <div class="col-sm-12">
					{!! Form::open(['route' => $ruta["search"], 'method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
					{!! Form::hidden('page', 1, array('id' => 'page')) !!}
					{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
					<div class="form-group">
						{!! Form::label('nombre', 'Nombre:', array('class' => 'input-sm')) !!}
						{!! Form::text('nombre', '', array('class' => 'form-control input-sm', 'id' => 'nombre')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('filas', 'Filas:', array('class' => 'input-sm'))!!}
						{!! Form::selectRange('filas', 1, 30, 10, array('class' => 'form-control input-sm', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
					</div>
					{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-sm input-sm', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
					{!! Form::close() !!}
				</div>
            </div>

			<div id="listado{{ $entidad }}"></div>
			
            <table id="datatable" class="table table-striped table-bordered">
            	@if(count($lista) == 0)
				<h3 class="text-warning">No hay cursos activados.</h3>
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
							<td>
							{!! Form::button('<div class="glyphicon glyphicon-list"></div> Matriculados', array('onclick' => 'modal (\''.URL::route($ruta["matriculados"], $value->id).'\', \''.$titulo_matricular.'\', this);', 'class' => 'btn btn-xs btn-success')) !!}
							</td>
						</tr>
						<?php
						$contador = $contador + 1;
						?>
						@endforeach
					</tbody>
				</table>
            </table>
        </div>
    </div>
</div>

<script>
	$(document).ready(function () {
		buscar('{{ $entidad }}');
		init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="name"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});
	});
</script>
