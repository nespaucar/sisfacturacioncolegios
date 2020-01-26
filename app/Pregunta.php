<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pregunta extends Model
{
    
	use SoftDeletes;
    protected $table = 'pregunta';
    protected $dates = ['deleted_at'];

    public function scopelistar($query, $encuesta_id)
    {
        return $query->where(function($subquery) use($encuesta_id)
        {
            if (!is_null($encuesta_id)) {
                $subquery->where('examen_id','=' , $encuesta_id);
            }
        })
        ->orderBy('id', 'DESC');
    }

    public function encuesta()
	{
		return $this->belongsTo('App\Examen', 'examen_id');
	}

	public function alternativas()
	{
		return $this->hasMany('App\Alternativa');
	}
}
