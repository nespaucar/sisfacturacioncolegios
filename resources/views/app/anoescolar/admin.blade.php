<?php 
	use App\Movimiento;
	use Illuminate\Support\Facades\DB;

	$anoactual = date("Y");

	//BUSCAMOS LA APERTURA DE CAJA, 
	//SI LA ENCONTRAMOS DESHABILITAMOS EL BOTON DE APERTURA Y HABILITAMOS EL DE CIERRE
	//SI NO LA ENCONTRAMOS HACEMOS EL PROCESO INVERSO
	$apertura = Movimiento::where("tipomovimiento_id", "=", 5)
				->where(DB::raw("YEAR(fecha)"), "=", $anoactual)
				->first();

?>

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
						{!! Form::selectRange('filas', 1, 30, 10, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
					</div>

					@if($apertura==NULL)

						{!! Form::button('<i class="glyphicon glyphicon-plus"></i> Apertura de Año Escolar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-sm input-xs', 'id' => 'btnApertura', 'onclick' => 'modal (\''.URL::route($ruta["create"], array('listar'=>'SI')).'\', \''.$titulo_registrar.'\', this);')) !!}

						{!! Form::button('<i class="glyphicon glyphicon-remove-circle"></i> Cierre de Año Escolar', array('class' => 'btn btn-danger waves-effect waves-light m-l-10 btn-sm input-xs', 'id' => 'btnCierre', 'disabled' => 'true')) !!}

					@else

						{!! Form::button('<i class="glyphicon glyphicon-plus"></i> Apertura de Año Escolar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-sm input-xs', 'id' => 'btnApertura', 'disabled' => 'true')) !!}

						{!! Form::button('<i class="glyphicon glyphicon-remove-circle"></i> Cierre de Año Escolar', array('class' => 'btn btn-danger waves-effect waves-light m-l-10 btn-sm input-xs', 'id' => 'btnCierre', 'onclick' => 'modal (\''.URL::route($ruta["create"], array('listar'=>'SI')).'\', \''.$titulo_registrar.'\', this);')) !!}

					@endif

					{!! Form::close() !!}
				</div>
            </div>

			<div id="listado{{ $entidad }}"></div>
        </div>
    </div>
</div>

<script>
	$(document).ready(function () {
		buscar('{{ $entidad }}');
		init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
	});
</script>
