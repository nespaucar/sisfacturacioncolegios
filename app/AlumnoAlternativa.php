<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlumnoAlternativa extends Model
{
    use SoftDeletes;
    protected $table = 'alumno_alternativa';
    protected $dates = ['deleted_at'];

    public function alumno()
	{
		return $this->belongsTo('App\Alumno', 'alumno_id');
    }
    
    public function alternativa()
	{
		return $this->belongsTo('App\Alternativa', 'alternativa_id');
	}
}
