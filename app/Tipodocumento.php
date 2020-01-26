<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tipodocumento extends Model
{
    
	use SoftDeletes;
    protected $table = 'tipodocumento';
    protected $dates = ['deleted_at'];

    public function scopelistar($query, $nombre, $abreviatura)
    {
        return $query->where(function($subquery) use($nombre, $abreviatura)
        {
            if (!is_null($nombre)) {
                $subquery->where('nombre', 'LIKE', '%'.$nombre.'%');
            }
            if (!is_null($abreviatura)) {
                $subquery->where('abreviatura','LIKE', '%'.$abreviatura.'%');
            }
        })
        ->orderBy('abreviatura', 'ASC');
    }
}
