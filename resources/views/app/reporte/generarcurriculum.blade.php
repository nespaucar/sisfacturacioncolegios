<table border="0" cellspacing="3" cellpadding="3" style="margin: 50px;">
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td align="right"><img src="assets/images/unprg.png" width="40" height="50" /></td>
        <td align="center" style="font-size: 11px">UNIVERSIDAD 
        	NACIONAL 
        	PEDRO RUIZ
        	GALLO			
		</td>
    </tr>
    <tr>
    	<td align="center" colspan="2"><img src="assets/images/users/avatar-2.jpg" width="90" height="100" /></td>
    	<td align="left" style="font-size: 10px" colspan="3"><br><br>
    		<b>Nombre:</b> {{ $alumno->nombres }} {{ $alumno->apellidopaterno }} {{ $alumno->apellidomaterno }} <br>
    		<b>Email:</b> {{ $alumno->email }} <br>
    		<b>Fecha de Nacimiento:</b> {{ Date::parse($alumno->fechanacimiento)->format('d/m/y') }} <br>
    		<b>Dirección:</b> {{ $alumno->direccion }} <br>
    		<b>Teléfono:</b> {{ $alumno->telefono }}			
		</td>
    </tr>
    <tr>
    	<td align="center" colspan="5"><br><h3><u>ESTUDIOS Y CERTIFICADOS</u><br></h3></td>
    </tr>
    
	<?php $i=1; ?>
	@foreach($certificados as $certificado)
	<tr>
		<td align="left" colspan="2" style="font-size: 10px"><b>{{ $i }}.- {{ $certificado->nombre }}</b></td>
    	<td align="left" colspan="3" style="font-size: 10px">{{ $certificado->nombre_certificadora }}</td>
		<?php $i++; ?>
	</tr>
	@endforeach	

	<tr>
    	<td align="center" colspan="5"><br><br><h3><u>COMPETENCIAS Y HABILIDADES</u> <br></h3></td>
    </tr>
    
	<?php $i=1; ?>
	@foreach($competencias as $competencia)
	<tr>
		<td align="left" colspan="2" style="font-size: 10px"><b>{{ $i }}.- {{ $competencia->competencia_nombre }}</b></td>
    	<td align="left" colspan="3" style="font-size: 10px">
    		<?php 
				$calificacion = '';
				for ($e=0; $e < $competencia->calificacion; $e++) { 
					$calificacion .= ' <img src="assets/images/estrella.png"width="10" height="10"/>';
				}
				echo $calificacion;
			?>
    	</td>
		<?php $i++; ?>
	</tr>
	@endforeach	

	<tr>
    	<td align="center" colspan="5"><br><br><h3><u>EXPERIENCIAS LABORALES</u> <br></h3></td>
    </tr>
    
	<?php $i=1; ?>
	@foreach($explaborales as $explaboral)
	<tr>
		<td align="left" style="font-size: 10px"><b>{{ $i }}.- {{ $explaboral->empresa }}</b></td>
    	<td align="left" colspan="3" style="font-size: 10px">{{ $explaboral->cargo }}</td>
    	<td align="left" style="font-size: 10px">{{ Date::parse($explaboral->fechainicio)->format('d/m/y') }} - {{ Date::parse($explaboral->fechafin)->format('d/m/Y') }}</td>
		<?php $i++; ?>
	</tr>
	@endforeach		    	
    
</table>
