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

	public function scopelistar($query, $seccion_id)
    {
        return $query->where(function($subquery) use($seccion_id)
        {
            if (!is_null($seccion_id)) {
                $subquery->where('seccion.id','=' , $seccion_id);
            }
        })
        ->orderBy('id', 'DESC');
    }
}
