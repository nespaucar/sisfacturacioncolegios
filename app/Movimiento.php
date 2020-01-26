<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Movimiento extends Model
{    
	use SoftDeletes;
    protected $table = 'movimiento';
    protected $dates = ['deleted_at'];

    public function scopelistaranoescolar($query, $ano)
    {
        return $query->where(function($subquery) use($ano)
        {
            if (!is_null($ano)) {
                $subquery->where(DB::raw('YEAR(fecha)'), '>=', '%'.$ano.'%');
            }
        })
        ->orderBy('id', 'DESC');
    }

    public function scopeNumeroSigue($query, $tipomovimiento_id){
        $rs=$query->select(DB::raw("MAX((CASE WHEN numero IS NULL THEN 0 ELSE numero END)*1) AS maximo"))
                ->where("tipomovimiento_id", "=", $tipomovimiento_id)
                ->first();
        return str_pad($rs->maximo+1,8,'0',STR_PAD_LEFT);
    }

    public function persona()
    {
        return $this->belongsTo('App\Persona', 'persona_id');
    }

    public function responsable()
    {
        return $this->belongsTo('App\Persona', 'responsable_id');
    }

    public function conceptopago()
    {
        return $this->belongsTo('App\Conceptopago', 'conceptopago_id');
    }

    public function tipomovimiento()
    {
        return $this->belongsTo('App\Tipomovimiento', 'tipomovimiento_id');
    }

    public function tipodocumento()
    {
        return $this->belongsTo('App\Tipodocumento', 'tipodocumento_id');
    }

    public function movimiento()
    {
        return $this->belongsTo('App\Movimiento', 'movimiento_id');
    }
}
