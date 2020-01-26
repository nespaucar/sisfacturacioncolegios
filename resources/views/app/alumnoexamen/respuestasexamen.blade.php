<?php 
use App\AlumnoAlternativa;
use App\Alternativa;
use Illuminate\Support\Facades\Auth;
?>
<div class="form-group border">
	<?php $i = 1; $cantidadcorrectas = 0; ?>
	@foreach($preguntas as $pregunta)
		{!! '<div class="list-group">
		<a href="#" class="list-group-item active" style="color:blue;font-weight:bold">'.$i. '. ' .$pregunta->nombre . '</a>' !!}
		<?php 
			$alternativas = Alternativa::select('id', 'nombre', 'correcta')->where('pregunta_id', '=', $pregunta->id)->get();
			$correcto = false;
			foreach ($alternativas as $alternativa) {
				$respuesta = AlumnoAlternativa::select('id')->where('alternativa_id', '=', $alternativa->id)->where('alumno_id', Auth::user()->persona_id)->get();
				if(count($respuesta) > 0) {
					if(!$alternativa->correcta) {
						echo '<a href="#" class="list-group-item" style="color:red;font-weight:bold"><span class="badge badge-danger"><i class="glyphicon glyphicon-remove"></i></span>'.$alternativa->nombre . '</a>';		
						$correcto = false;				
					} else {
						echo '<a href="#" class="list-group-item" style="color:green;font-weight:bold"><span class="badge badge-success"><i class="glyphicon glyphicon-ok"></i></span>'.$alternativa->nombre . '</a>';
						$correcto = true;
						$cantidadcorrectas++;
					}
				} else {
					if(!$alternativa->correcta) {
						echo '<a href="#" class="list-group-item">'.$alternativa->nombre . '</a>';
					} else {
						echo '<a href="#" class="list-group-item" style="color:gray;font-weight:bold"><span class="badge badge-default"><i class="glyphicon glyphicon-ok"></i></span>'.$alternativa->nombre . '</a>';
					}					
				}
			}
		if($correcto) {
			echo '<center><a href="#" class="list-group-item" style="color:green;font-weight:bold">¡Correcto! +1</a></center>';
		} else {
			echo '<center><a href="#" class="list-group-item" style="color:red;font-weight:bold">¡Incorrecto! 0</a></center>';
		}		
		$i++; ?>
		{!! '</div>' !!}
	@endforeach
	{!! '<div class="list-group">
		<center><a href="#" class="list-group-item active" style="color:blue;font-weight:bold">NOTA FINAL: <h3>' . $cantidadcorrectas . '/' . count($preguntas) . '</h3></a></center>' !!}
</div>
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('400');
}); 
</script>

