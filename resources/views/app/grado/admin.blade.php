<!-- Page-Title -->
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">{{ $title }}</h4>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="row boxfondo">
    <div class="col-sm-12">
        <div class="card-box table-responsive">

            <div class="row m-b-30">
                <div class="col-sm-12">
					{!! Form::open(['route' => $ruta["search"], 'method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
					{!! Form::hidden('page', 1, array('id' => 'page')) !!}
					{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
					<div class="form-group">
						{!! Form::label('descripcion', 'Descripción:', array('class' => 'input-xs')) !!}
						{!! Form::text('descripcion', '', array('class' => 'form-control input-xs', 'id' => 'descripcion', 'onkeyup' => 'buscar(\''.$entidad.'\')')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('filas', 'Filas:', array('class' => 'input-xs'))!!}
						{!! Form::selectRange('filas', 10, 100, 20, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
					</div>
					{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-sm input-xs', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
					{!! Form::button('<i class="glyphicon glyphicon-plus"></i> Nuevo', array('class' => 'btn btn-info waves-effect waves-light m-l-10 btn-sm input-xs', 'id' => 'btnNuevo', 'onclick' => 'modal (\''.URL::route($ruta["create"], array('listar'=>'SI')).'\', \''.$titulo_registrar.'\', this);')) !!}
					{!! Form::close() !!}
				</div>
            </div>

			<div id="listado{{ $entidad }}"></div>
			
            <table id="datatable" class="table table-striped table-bordered">
            </table>
        </div>
    </div>
</div>

<script>
	$(document).ready(function () {
		buscar('{{ $entidad }}');
		init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
	});
</script>
