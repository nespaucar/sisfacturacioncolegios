<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Configuracionpago extends Model
{
    use SoftDeletes;
    protected $table = 'configuracionpago';
    protected $dates = ['deleted_at'];

    public function scopelistar($query, $alumno_id, $nivel_id, $grado_id, $seccion_id, $local_id)
    {
        $query = $query->where(function($subquery) use($alumno_id, $nivel_id, $grado_id, $seccion_id)
        {
            if (!is_null($alumno_id)) {
                $subquery->whereNotNull('alumno_id');
            }
            if (!is_null($nivel_id)) {
                $subquery->whereNotNull('nivel_id');
            }
            if (!is_null($grado_id)) {
                $subquery->whereNotNull('grado_id');
            }
            if (!is_null($seccion_id)) {
                $subquery->whereNotNull('seccion_id');
            }            
        });

        if (!is_null($alumno_id)) {
            $query->join("persona", "persona.id", "=", "alumno_id")
            ->orderBy(DB::raw("CONCAT(persona.apellidopaterno, ' ', persona.apellidomaterno, ' ', persona.nombres)"));
        }
        if (!is_null($nivel_id)) {
            $query->join("nivel", "nivel.id", "=", "nivel_id")
            ->orderBy("nivel.descripcion");
        }
        if (!is_null($grado_id)) {
            $query->join("grado", "grado.id", "=", "grado_id")
            ->orderBy("grado.descripcion");
        }
        if (!is_null($seccion_id)) {
            $query->join("seccion", "seccion.id", "=", "seccion_id")
            ->orderBy("seccion.descripcion");
        }
        if (!is_null($local_id)) {
            $query->where('configuracionpago.local_id', "=", $local_id);
        }

        return $query->select("configuracionpago.*");

    }

    public function alumno()
    {
        return $this->belongsTo('App\Persona', 'alumno_id');
    }

    public function nivel()
    {
        return $this->belongsTo('App\Nivel', 'nivel_id');
    }

    public function grado()
    {
        return $this->belongsTo('App\Grado', 'grado_id');
    }

    public function seccion()
    {
        return $this->belongsTo('App\Seccion', 'seccion_id');
    }
}