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
						{!! Form::label('nivel_id', 'Nivel:', array('class' => 'input-xs')) !!}
						{!! Form::select("nivel_id", $cboNiveles, null, array("class" => "form-control input-xs", "id" => "nivel_id", 'onchange' => 'grados_nivel(\''.$entidad.'\', this.value);')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('grado_id', 'Grado:', array('class' => 'input-xs')) !!}
						{!! Form::select("grado_id", array(""=>"--TODOS--"), null, array("class" => "form-control input-xs", "id" => "grado_id", 'onchange' => 'secciones_grado(\''.$entidad.'\', this.value)')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('seccion_id', 'Seccion:', array('class' => 'input-xs')) !!}
						{!! Form::select("seccion_id", array(""=>"--TODOS--"), null, array("class" => "form-control input-xs", "id" => "seccion_id", 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
					</div>
					{{--<div class="form-group">
						{!! Form::label('anoescolar', 'Año escolar:', array('class' => 'input-xs')) !!}
						{!! Form::selectRange('anoescolar', date("Y"), 2050, 10, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
					</div>--}}
					<div class="form-group">
						{!! Form::label('filas', 'Filas:', array('class' => 'input-xs'))!!}
						{!! Form::selectRange('filas', 1, 30, 10, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
					</div>
					{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-sm input-xs', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
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
		grados_nivel('{{ $entidad }}', "");
	});

	function grados_nivel(entidad, id) {
		$.ajax({
			url: "grado/grados?nivel_id="+id,
			success: function(e) {
				$("#grado_id").html(e);
				secciones_grado(entidad, "");
			}
		});
	}

	function secciones_grado(entidad, id) {
		$.ajax({
			url: "grado/seccionesM?grado_id="+id,
			success: function(e) {
				$("#seccion_id").html(e);
				buscar(entidad);
			}
		});
	}
</script>
