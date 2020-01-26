<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tipomovimiento extends Model
{
    
	use SoftDeletes;
    protected $table = 'tipomovimiento';
    protected $dates = ['deleted_at'];

    public function scopelistar($query, $nombre)
    {
        return $query->where(function($subquery) use($nombre)
        {
            if (!is_null($nombre)) {
                $subquery->where('nombre', 'LIKE', '%'.$nombre.'%');
            }
        })
        ->orderBy('nombre', 'ASC');
    }
}
