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
						{!! Form::label('fecha', 'Fecha Inicial:', array('class'=>'input-sm')) !!}
						{!! Form::date('fecha', date("Y-m-d",strtotime(date("Y-m-d")."- 1 month")), array('class' => 'form-control input-sm', 'id' => 'fecha', 'onkeyup' => 'buscar(\''.$entidad.'\')')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('fecha2', 'Fecha Final:', array('class'=>'input-sm')) !!}
						{!! Form::date('fecha2', date("Y-m-d"), array('class' => 'form-control input-sm', 'id' => 'fecha2', 'onkeyup' => 'buscar(\''.$entidad.'\')')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('serie', 'Serie:', array('class'=>'input-sm')) !!}
						{!! Form::text('serie', null, array('class' => 'form-control input-sm', 'id' => 'serie', 'onkeyup' => 'buscar(\''.$entidad.'\')')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('numero', 'Número:', array('class'=>'input-sm')) !!}
						{!! Form::text('numero', null, array('class' => 'form-control input-sm', 'id' => 'numero', 'onkeyup' => 'buscar(\''.$entidad.'\')')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('tipodocumento_id', 'Tipo de documento:', array('class'=>'input-sm')) !!}
						{!! Form::select('tipodocumento_id', array("" => "--TODOS--", 1 => "BOLETA", 2 => "FACTURA"), null, array('class' => 'form-control input-sm', 'id' => 'tipodocumento_id', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('estado', 'Estado:', array('class'=>'input-sm')) !!}
						{!! Form::select('estado', array("" => "--TODOS--", "P" => "PAGADO", "A" => "ANULADO"), null, array('class' => 'form-control input-sm', 'id' => 'estado', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('estado', 'Filas:', array('class'=>'input-sm')) !!}
						{!! Form::selectRange('filas', 1, 30, 10, array('class' => 'form-control input-sm', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
					</div>
					{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-sm', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}

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
