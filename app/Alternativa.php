<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alternativa extends Model
{
    
	use SoftDeletes;
    protected $table = 'alternativa';
    protected $dates = ['deleted_at'];


    public function scopelistar($query, $pregunta_id)
    {
        return $query->where(function($subquery) use($pregunta_id)
        {
            if (!is_null($pregunta_id)) {
                $subquery->where('pregunta_id','=' , $pregunta_id);
            }
        })
        ->orderBy('id', 'DESC');
    }

    public function pregunta()
	{
		return $this->belongsTo('App\Pregunta', 'pregunta_id');
	}

	public function respuestas()
	{
		return $this->hasMany('App\Respuesta');
	}
}
