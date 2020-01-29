<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlumnoSeccion extends Model
{
    use SoftDeletes;
    protected $table = 'alumno_seccion';
    protected $dates = ['deleted_at'];

    public function alumno()
	{
		return $this->belongsTo('App\Persona', 'alumno_id');
    }

    public function cicloacademico()
	{
		return $this->belongsTo('App\Cicloacademico', 'cicloacademico_id');
    }

    public function seccion()
	{
		return $this->belongsTo('App\Seccion', 'seccion_id');
    }
}
