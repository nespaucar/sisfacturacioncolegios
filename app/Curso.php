<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AlumnoCurso;
use Illuminate\Database\Eloquent\SoftDeletes;

class Curso extends Model
{
    use SoftDeletes;
    protected $table = 'curso';
    protected $dates = ['deleted_at'];

    protected $fillable = [
       'id','descripcion', 'apertura', 'profesor_id', 'estado'
    ];

    public function scopelistar($query, $descripcion, $profesor_id, $estado, $alumno_id, $operador)
    {
        return $query->where(function($subquery) use($descripcion, $profesor_id, $estado, $alumno_id, $operador)
        {
            if (!is_null($descripcion)) {
                $subquery->where('descripcion', 'LIKE', '%'.$descripcion.'%');
            }
            if (!is_null($profesor_id)) {
                $subquery->where('profesor_id', $profesor_id);
            }
            if (!is_null($estado)) {
                $subquery->where('estado', $estado);
            }
            if (!is_null($alumno_id)) {
                $alumno_curso = AlumnoCurso::select('curso_id')
                ->where('alumno_id', $alumno_id)
                ->distinct()
                ->get();
                if(count($alumno_curso) > 0) {
                    $array = array();
                    foreach ($alumno_curso as $ac) {
                        $array[] = $ac['curso_id'];                        
                    }
                    $subquery->where(function ($query) use ($array, $operador) {
                        if($operador == '!=') {
                            for ($i=0; $i < count($array); $i++) { 
                                $query->where('id', '!=', $array[$i]);
                            }
                        } else {
                            for ($i=0; $i < count($array); $i++) { 
                                $query->orWhere('id', '!=', $array[$i]);
                            }
                        }                        
                    });
                } else {
                    $subquery->where('id', $operador, 0);
                }                              
            }
        })
        ->orderBy('descripcion', 'ASC');
    }

    public function profesor()
    {
        return $this->belongsTo('App\Persona', 'profesor_id');
    }

    public function alumnocursos(){
        return $this->hasMany('App\AlumnoCurso');
    }
}
