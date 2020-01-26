<!-- Page-Title -->
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            {{--
            <ol class="breadcrumb pull-right">
                <li><a href="#">Minton</a></li>
                <li><a href="#">Tables</a></li>
                <li class="active">Datatable</li>
            </ol>
            --}}
            <h4 class="page-title">{{ $title }}</h4>
        </div>
    </div>
</div>

<script>
function cargarselect2(entidad){
		var select = $('#facultad_id').val();

		if(select == ''){
			$('#escuela1_id').html('<option value="" selected="selected">Todas</option>');
			return false;
		}

		route = 'encuesta/cargarselect/' + select + '?entidad=' + entidad + '&t=si';

		$.ajax({
			url: route,
			headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
			type: 'GET',
			beforeSend: function() {
				$('#escuela1_id').html('<option value="" selected="selected">Seleccione</option>');
			},
	        success: function(res){
	        	$('#escuela1_id').html(res);
	        }
		});
	}
</script>

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
						{!! Form::label('codigo', 'Código:', array('class' => 'input-sm')) !!}
						{!! Form::text('codigo', '', array('class' => 'form-control input-sm', 'id' => 'codigo')) !!}
					</div>

					<div class="form-group">
						{!! Form::label('nombre', 'Nombre:', array('class' => 'input-sm')) !!}
						{!! Form::text('nombre', '', array('class' => 'form-control input-sm', 'id' => 'nombre')) !!}
					</div>

					<div class="form-group">
						{!! Form::label('facultad_id', 'Facultad:', array('class' => 'input-sm')) !!}
						{!! Form::select('facultad_id', $cboFacultad, null, array('class' => 'form-control input-sm', 'id' => 'facultad_id', 'onchange' => 'cargarselect2("escuela")')) !!}
					</div>

					<div class="form-group">
						{!! Form::label('escuela1_id', 'Escuela:', array('class' => 'input-sm')) !!}
						{!! Form::select('escuela1_id', $cboEscuela, null, array('class' => 'form-control input-sm', 'id' => 'escuela1_id')) !!}
					</div>

					<div class="form-group">
						{!! Form::label('filas', 'Filas a mostrar:', array('class' => 'input-sm'))!!}
						{!! Form::selectRange('filas', 1, 30, 10, array('class' => 'form-control input-sm', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
					</div>
					{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-sm', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
					{!! Form::button('<i class="glyphicon glyphicon-plus"></i> Nuevo', array('class' => 'btn btn-info waves-effect waves-light m-l-10 btn-sm', 'id' => 'btnNuevo', 'onclick' => 'modal (\''.URL::route($ruta["create"], array('listar'=>'SI')).'\', \''.$titulo_registrar.'\', this);')) !!}
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
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="name"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});
	});
</script>
