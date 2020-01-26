<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grado extends Model
{
    use SoftDeletes;
    protected $table = 'grado';
    protected $dates = ['deleted_at'];

    public function scopelistar($query, $descripcion, $local_id)
    {
        return $query->where(function($subquery) use($descripcion, $local_id)
        {
            if (!is_null($descripcion)) {
                $subquery->where('grado.descripcion', 'LIKE', '%'.$descripcion.'%');
            }
            if (!is_null($local_id)) {
                $subquery->where('nivel.local_id', '=', $local_id);
            }
        })
        ->join("nivel", "nivel.id", "=", "grado.nivel_id")
        ->select('grado.*')
        ->orderBy('local_id', 'ASC');
    }

    public function nivel()
	{
		return $this->belongsTo('App\Nivel', 'nivel_id');
	}

    public function secciones()
    {
        return $this->hasMany('App\Seccion');
    }
}
