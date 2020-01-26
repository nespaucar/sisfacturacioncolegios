<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlumnoExamen extends Model
{
    use SoftDeletes;
    protected $table = 'alumno_examen';
    protected $dates = ['deleted_at'];

    public function alumno()
	{
		return $this->belongsTo('App\Persona', 'alumno_id');
    }
    
    public function examen()
	{
		return $this->belongsTo('App\Examen', 'examen_id');
	}
}
