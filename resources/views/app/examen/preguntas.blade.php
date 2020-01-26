<script>
	function gestionpa(num, tipo, id, idpadre){
		if(num == 1){
			if(!$('#' + tipo).val()) {
				$('#' + tipo).focus()
				return false;
			}
			route = 'examen/nueva' + tipo + '/' + idpadre;
		} else if(num == 2){
			route = 'examen/eliminar' + tipo + '/' + id + '/' + idpadre;
		} else {
			route = 'examen/listar' + tipo + 's/' + idpadre;
		}

		$.ajax({
			url: route,
			headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
			type: 'GET',
			data: $('#formnueva' + tipo).serialize(),
			beforeSend: function(){
				$('#tabla' + tipo + 's').html(imgCargando());
				$('.correcto').addClass('hidden');
	        },
	        success: function(res){
	        	$('#tabla' + tipo + 's').html(res);
				$('#' + tipo).val('').focus();
				$('.correcto').removeClass('hidden');
				if(num == 3) {
					$('.correcto').addClass('hidden');
				} else {
					buscar('Examen');
				}				
	        }
		});
	}

	function correcto(alternativa_id, pregunta_id) {
		var route = 'examen/alternativacorrecta?alternativa_id=' + alternativa_id + '&pregunta_id=' + pregunta_id;
		$.ajax({
			url: route,
			headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
			type: 'GET',
	        success: function(res){
	        	$('.respuesta').html('<div class="glyphicon glyphicon-remove"></div>').removeClass('btn-success').addClass('btn-danger');
				$('#respuesta' + alternativa_id).html('<div class="glyphicon glyphicon-ok"></div>').removeClass('btn-danger').addClass('btn-success');
	        }
		});
	}

	$('.carousel').carousel({
  		pause: true,
    	interval: false,    	
	});
</script>

<style>
	.modal-dialog {
		width: 800px;
	}
</style>

<div class="row">
	<div class="col-sm-12">
		<div id="carousel-ejemplo" class="carousel slide" data-ride="carousel">
  				<div class="carousel-inner" role="listbox">
    				<div class="item active">
					    <div class="col-sm-12">
				            <div class="row">
				                <div class="col-sm-12">
									{!! Form::open(['route' => null, 'method' => 'GET', 'onsubmit' => 'return false;', 'class' => 'form-horizontal', 'id' => 'formnuevapregunta']) !!}
									<div class="form-group">
										{!! Form::label('pregunta', 'Pregunta:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label input-sm')) !!}
											<div class="col-lg-6 col-md-6 col-sm-6">
												{!! Form::text('pregunta', '', array('class' => 'form-control input-sm', 'id' => 'pregunta')) !!}
											</div>
											<div class="col-lg-1 col-md-1 col-sm-1">
												{!! Form::button('<i class="glyphicon glyphicon-plus"></i>', array('class' => 'btn btn-info input-sm waves-effect waves-light m-l-10 btn-md btnAnadir', 'onclick' => 'gestionpa(1, "pregunta", "", ' . $examen_id . ');')) !!}
											</div>
											<div class="col-lg-2 col-md-2 col-sm-2">
												{!! Form::button('<i class="glyphicon glyphicon-check"></i> ¡Bien!', array('class' => 'correcto btn btn-success input-sm waves-effect waves-light m-l-10 btn-md hidden', 'onclick' => '#')) !!}
											</div>
									</div>					
									{!! Form::close() !!}
				                </div>
				            </div>
							<br>
				            <div id="tablapreguntas">
					            @if(count($lista) == 0)
								<h4 class="text-warning">No se encontraron preguntas.</h4>
								@else
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
											<td>{{ $value->nombre }}</td>
											<td>
												<a href="#carousel-ejemplo" class="btn btn-default btn-xs" data-slide="next" onclick='gestionpa(3, "alternativa", "", {{ $value->id }}); $(".correcto").addClass("hidden");'><div class="glyphicon glyphicon-list"></div> Alt.</a>
											</td>
											<td>{!! Form::button('<div class="glyphicon glyphicon-remove"></div> Eliminar', array('onclick' => 'gestionpa(2, "pregunta", ' . $value->id . ', ' . $examen_id . ');', 'class' => 'btn btn-xs btn-danger')) !!}</td>												
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
							</div>
					    </div>
    				</div>
    
				    <div class="item">
				      	<a href="#carousel-ejemplo" class="btn btn-default btn-xs" data-slide="prev" onclick="$('.correcto').addClass('hidden');"><div class="retorno glyphicon glyphicon-chevron-left"></div> Atrás</a>
				      	<hr>
					    <div class="col-sm-12">
				            <div id="tablaalternativas"></div>
					    </div>
				    </div>           
  				</div>
  			</div>
  		</div>
  	</div>
</div>
