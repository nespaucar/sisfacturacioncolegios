<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Seccion extends Model
{
    use SoftDeletes;
    protected $table = 'seccion';
    protected $dates = ['deleted_at'];

    public function grado()
	{
		return $this->belongsTo('App\Grado', 'grado_id');
	}

	public function scopelistar($query, $seccion_id, $local_id)
    {
        $query->where(function($subquery) use($seccion_id, $local_id)
        {
            if (!is_null($seccion_id)) {
                $subquery->where('seccion.id','=' , $seccion_id);
            }
        })
        ->orderBy('seccion.id', 'DESC')
        ->join("grado", "grado.id", "=", "seccion.grado_id")
        ->join("nivel", "nivel.id", "=", "grado.nivel_id")
        ->select("seccion.id", "seccion.descripcion", "seccion.grado_id", "grado.nivel_id")
        ->where("nivel.local_id", "=", $local_id);
        return $query;
    }
}
