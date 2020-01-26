<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nivel extends Model
{
    use SoftDeletes;
    protected $table = 'nivel';
    protected $dates = ['deleted_at'];

    public function scopelistar($query, $descripcion, $local_id)
    {
        return $query->where(function($subquery) use($descripcion, $local_id)
        {
            if (!is_null($descripcion)) {
                $subquery->where('descripcion', 'LIKE', '%'.$descripcion.'%');
            }
            if (!is_null($local_id)) {
                $subquery->where('local_id', '=', $local_id);
            }
        })
        ->orderBy('local_id', 'ASC');
    }

    public function local()
	{
		return $this->belongsTo('App\Local', 'local_id');
	}

    public function grados()
    {
        return $this->hasMany('App\Grado');
    }
}
