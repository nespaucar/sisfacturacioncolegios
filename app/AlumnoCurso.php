<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlumnoCurso extends Model
{
    use SoftDeletes;
    protected $table = 'alumno_curso';
    protected $dates = ['deleted_at'];

    protected $fillable = [
       'id','alumno_id', 'curso_id', 'estado'
    ];

    public function alumno()
	{
		return $this->belongsTo('App\Persona', 'alumno_id');
    }

    public function curso()
    {
        return $this->belongsTo('App\Curso', 'curso_id');
    }
}
