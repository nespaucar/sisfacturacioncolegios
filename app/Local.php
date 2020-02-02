<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Local extends Model
{
    use SoftDeletes;
    protected $table = 'local';
    protected $dates = ['deleted_at'];

    public function scopelistar($query, $serie, $nombre, $tipo, $local_id)
    {
        return $query->where(function($subquery) use($serie, $nombre, $tipo, $local_id)
        {
            if (!is_null($serie)) {
                $subquery->where('serie', 'LIKE', '%'.$serie.'%');
            }
            if (!is_null($nombre)) {
                $subquery->where('nombre', 'LIKE', '%'.$nombre.'%');
            }
            if (!is_null($tipo)) {
                $subquery->where('tipo', '=', $tipo);
            }
            if (!is_null($local_id)) {
                $subquery->where('id', '=', $local_id)->orWhere("local_id", "=", $local_id);
            }
        })
        ->orderBy('id', 'ASC');
    }

    public function local()
	{
		return $this->belongsTo('App\Local', 'local_id');
	}

    public function niveles()
    {
        return $this->hasMany('App\Nivel');
    }
}
