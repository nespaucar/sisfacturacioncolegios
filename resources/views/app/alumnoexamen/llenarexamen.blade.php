<?php 
use App\Pregunta;
use App\Alternativa;
?>
@if(!$acceso)
<div class="row">
    <div class="col-sm-12">
        <div class="card-box table-responsive">
            <div class="row m-b-30">
                <div class="col-sm-12">
                	<h3 class="text-warning">No está autorizado para llenar este examen.</h3>
				</div>
				<div class="col-md-12">
					{!! Form::button('<div class="glyphicon glyphicon-chevron-left"></div> Regresar', array('class' => 'btn btn-info waves-effect waves-light m-l-10 btn-sm', 'onclick' => 'cargarRuta(\'alumnoexamen\', \'container\');')) !!}
				</div>
            </div>
        </div>
    </div>
</div>

@else

<style>
     fieldset 
    {
        border: 1px solid #ddd !important;
        margin: 0;
        xmin-width: 0;
        padding: 10px;       
        position: relative;
        border-radius:4px;
        background-color:#f5f5f5;
        padding-left:10px!important;
    }   
    
    legend
    {
        font-size:20px;
        font-weight:bold;
        margin-bottom: 0px; 
        width: 100%; 
        border: 1px solid #ddd;
        border-radius: 4px; 
        padding: 5px 5px 5px 10px; 
        background-color: #ffffff;
    }
    label{
        position: relative;
        cursor: pointer;
        font-size: 20px;
    }

    input[type="radio"]{
        position: absolute;
        right: 9000px;
    }

    input[type="radio"] + .label-text:before{
        content: "\f10c";
        font-family: "FontAwesome";
        speak: none;
        font-style: normal;
        font-weight: normal;
        font-variant: normal;
        text-transform: none;
        line-height: 1;
        -webkit-font-smoothing:antialiased;
        width: 1em;
        display: inline-block;
        margin-right: 5px;
    }

    input[type="radio"]:checked + .label-text:before{
        content: "\f192";
        color: #27C811;
        animation: effect 250ms ease-in;
    }

    input[type="radio"]:disabled + .label-text{
        color: #aaa;
    }

    input[type="radio"]:disabled + .label-text:before{
        content: "\f111";
        color: #ccc;
    }

    @keyframes effect{
        0%{transform: scale(0);}
        25%{transform: scale(1.3);}
        75%{transform: scale(1.4);}
        100%{transform: scale(1);}
    }

    h3 {color: #FF3333;}

    .label-text {color: #373232;}

</style>

<script>
    function recorrermarcadas(){
        var completo = true;
        $(".alternativa").each(function(){
            if($(this).val() == '') {
                completo = false;
            }
        });
        if(completo){
            $('#btnAviso').addClass('hidden');
            $.ajax({
                url: 'alumnoexamen/guardarexamen',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                type: 'POST',
                data: $('#formLlenadoExamen').serialize(),
                beforeSend: function(){
                    $('#btnGuardar').html('Cargando...');
                    $('#btnGuardar').attr('disabled', 'disabled');
                },
                success: function(res){
                    $('#btnGuardar').hide();
                    $('#btnCancelar').hide();
                    $('.panel-body').html('<div class="col-sm-12"><h3 class="text-success">Felicitaciones, completaste tu examen.</h3></div>');                    
                }
            }).fail(function() {
                $('.panel-body').html('<div class="col-sm-12"><h3 class="text-danger">Ocurrió un error, no has podido llenar tu Examen.</h3></div>');
            });
        } else {
            $('#btnAviso').removeClass('hidden');
        }
    };

    function marcarcorrecto(idpregunta, idalternativa) {
        $('#alternativa' + idpregunta).val(idalternativa);
    };
</script>

<form action="#" id="formLlenadoExamen">
    <fieldset class="col-md-12"  style="margin-bottom: 10px;">
        <legend>{!! Form::button('<div class="glyphicon glyphicon-chevron-left"></div> Regresar', array('class' => 'btn btn-info waves-effect waves-light m-l-10 btn-sm', 'onclick' => 'cargarRuta(\'alumnoexamen\', \'container\');')) !!}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $examen->descripcion }}</legend>
        <div class="panel panel-default" style="margin-bottom: 10px;">
            <div class="panel-body">
            <?php $i = 1; 
                $preguntas = Pregunta::select('id', 'nombre')->where('examen_id', $examen->id)->inRandomOrder()->get();
            ?>
                @foreach($preguntas as $pregunta)
                <div class="row" style="margin-top: 10px">
                    <div class="col-md-12">
                        <h3>{{ $i }}. {{ $pregunta->nombre }}</h3>
                        <?php 

                        $alternativas = Alternativa::select('id', 'nombre')->where('pregunta_id', $pregunta->id)->inRandomOrder()->get();
                        //$alternativas = Alternativa::select('id', 'nombre')->where('pregunta_id', $pregunta->id)->get();

                        ?>
                        @foreach($alternativas as $alternativa)
                            <div class="form-check">
                                <label>
                                    <input type="radio" name="radio{{ $pregunta->id }}" id="radio{{ $pregunta->id }}" class="alternativacorrecta" onclick="marcarcorrecto({{ $pregunta->id }}, {{ $alternativa->id }});"> <span class="label-text">{{ $alternativa->nombre }}</span>
                                </label>
                            </div>                                
                        @endforeach
                        {!! Form::hidden('alternativa' . $i, '', array('id' => 'alternativa' . $pregunta->id, 'class' => 'alternativa')) !!}
                    </div>
                </div>
                <?php $i++; ?>
                @endforeach
                {!! Form::hidden('cantpreguntas', count($examen->preguntas), array('id' => 'cantpreguntas')) !!}
                {!! Form::hidden('examen_id', $examen->id, array('id' => 'examen_id')) !!}
            </div>
        </div>
    </fieldset>
    <div class="col-12">
        <div class="form-group text-right">
            {!! Form::button('<i class="fa fa-remove fa-lg"></i> Contesta todas las preguntas', array('class' => 'btn btn-danger btn-sm hidden', 'id' => 'btnAviso')) !!}
            {!! Form::button('<i class="fa fa-check fa-lg"></i> Guardar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'recorrermarcadas();')) !!}
            {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar', 'onclick' => 'cargarRuta(\'alumnoexamen\', \'container\');')) !!}
        </div>
    </div>
</form>
@endif
