@include('principal.header_start')

{!! Html::style('plugins/jquery-circliful/css/jquery.circliful.css') !!}

@include('principal.header_end')

{{-- aquí va el contenido --}}

@include('principal.footer_start')

@include('principal.footer_end')

<script>
    $(document).ready(function() {
        $.ajax({
        	url: "mensualidad/envioBoletas",
        	method: "GET",
        	success: function(e) {
        		$.Notification.autoHideNotify('success', 'top right', "¡ÉXITO!", 'Alumno registrado correctamente.');
        	},
        	error: function() {
        		$.Notification.autoHideNotify('error', 'top right', "¡ERROR!", 'Hubo un problema al crear las boletas.');
        	}
        });
    });
</script>