<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlumnoSeccion extends Model
{
    use SoftDeletes;
    protected $table = 'alumno_seccion';
    protected $dates = ['deleted_at'];

    public function alumno()
	{
		return $this->belongsTo('App\Persona', 'alumno_id');
    }

    public function cicloacademico()
	{
		return $this->belongsTo('App\Cicloacademico', 'cicloacademico_id');
    }

    public function seccion()
	{
		return $this->belongsTo('App\Seccion', 'seccion_id');
    }

    public function scopelistar($query, $seccion_id, $cicloacademico_id, $local_id)
    {
        return $query->where(function($subquery) use($seccion_id, $cicloacademico_id, $local_id)
        {
            if (!is_null($seccion_id)) {
                $subquery->where('seccion_id', '=', $seccion_id);
            }
            if (!is_null($cicloacademico_id)) {
                $subquery->where('cicloacademico_id', '=', $cicloacademico_id);
            }
        })
        ->join("cicloacademico", "cicloacademico.id", "=", "alumno_seccion.cicloacademico_id")
        ->where("cicloacademico.local_id", "=", $local_id)
        ->select("alumno_seccion.*")
        ->orderBy('alumno_seccion.id', 'DESC');
    }
}
