<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conceptopago extends Model
{
    
	use SoftDeletes;
    protected $table = 'conceptopago';
    protected $dates = ['deleted_at'];

    public function scopelistar($query, $nombre, $tipo)
    {
        return $query->where(function($subquery) use($nombre, $tipo)
        {
            if (!is_null($nombre)) {
                $subquery->where('nombre', 'LIKE', '%'.$nombre.'%');
            }
            if (!is_null($tipo)) {
                $subquery->where('tipo','=', $tipo);
            }
        })
        ->orderBy('nombre', 'ASC');
    }
}
