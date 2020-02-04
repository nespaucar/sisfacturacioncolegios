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

    public function scopelistaranoescolar($query, $cicloacademico_id, $local_id)
    {
        return $query->where(function($subquery) use($cicloacademico_id, $local_id)
        {
            if (!is_null($cicloacademico_id)) {
                $subquery->where('cicloacademico_id', '=', $cicloacademico_id);
            }
            if (!is_null($local_id)) {
                $subquery->where('local_id', '=', $local_id);
            }
        })
        ->whereNull("tipodocumento_id")
        ->orderBy('id', 'DESC');
    }

    public function scopelistardocumentoventa($query, $fecha, $numero, $serie, $tipomovimiento_id, $tipodocumento_id, $estado, $local_id)
    {
        return $query->where(function($subquery) use($fecha, $numero, $serie, $tipomovimiento_id, $tipodocumento_id, $estado, $local_id)
        {
            if (!is_null($fecha)) {
                $subquery->where('fecha', '=', $fecha);
            }
            if (!is_null($numero)) {
                $subquery->where('numero', '=', $numero);
            }
            if (!is_null($serie)) {
                $subquery->where('serie', '=', $serie);
            }
            if (!is_null($tipomovimiento_id)) {
                $subquery->where('tipomovimiento_id', '=', $tipomovimiento_id);
            }
            if (!is_null($tipodocumento_id)) {
                $subquery->where('tipodocumento_id', '=', $tipodocumento_id);
            }
            if (!is_null($estado)) {
                $subquery->where('estado', '=', $estado);
            }
            if (!is_null($local_id)) {
                $subquery->where('local_id', '=', $local_id);
            }
        })
        ->orderBy('id', 'DESC');
    }

    public function scopeNumeroSigue($query, $tipomovimiento_id, $tipodocumento_id, $local_id) {    

        $rs = $query->select(DB::raw("max((CASE WHEN numero IS NULL THEN 0 ELSE numero END)*1) AS maximo"));
        if (!is_null($tipomovimiento_id)) {
            $rs = $rs->where('tipomovimiento_id', '=', $tipomovimiento_id);
        }
        if (!is_null($tipodocumento_id)) {
            $rs = $rs->where('tipodocumento_id', '=', $tipodocumento_id);
        }
        if (!is_null($local_id)) {
            $rs = $rs->where('local_id', '=', $local_id);
        } 
        $rs = $rs->first();
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

    public function local()
    {
        return $this->belongsTo('App\Local', 'local_id');
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

    public function cuota()
    {
        return $this->belongsTo('App\Cuota', 'cuota_id');
    }
}
