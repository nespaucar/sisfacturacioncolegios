<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Curso;
use App\AlumnoCurso;
use App\AlumnoExamen;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\DB;

class Examen extends Model
{
    use SoftDeletes;
    protected $table = 'examen';
    protected $dates = ['deleted_at'];

    protected $fillable = [
       'id','descripcion', 'curso_id', 'estado'
    ];

    public function alumno_examen()
    {
        return $this->hasMany('App\AlumnoExamen');
    }

    public function preguntas()
    {
        return $this->hasMany('App\Pregunta');
    }

    public function curso()
    {
        return $this->belongsTo('App\Curso', 'curso_id');
    }

    public function scopelistar($query, $descripcion, $curso_id, $profesor_id, $filtro)
    {
        return $query->where(function($subquery) use($descripcion, $curso_id, $profesor_id, $filtro){
            $cursos = Curso::select('id')->where('profesor_id', $profesor_id)->where('estado', 1)->get();  
            if(count($cursos) == 0) {
                $subquery->where('curso_id', 0);
            } else {
                if (!is_null($curso_id)) {                                
                    foreach ($cursos as $curso) {
                        if($curso_id == $curso['id']) {
                            $subquery->where('curso_id', $curso_id);
                            break;
                        }
                    }                    
                } else {
                    if (!is_null($filtro)) {     
                        $array = array();
                        if(count($cursos) > 0) {
                            foreach ($cursos as $curso) {
                                $array[] = $curso['id'];                            
                            }
                            $subquery->where(function ($query) use ($array) {
                                for ($i=0; $i < count($array); $i++) { 
                                    $query->orWhere('curso_id', $array[$i]);
                                }
                            });
                        }  
                    }  
                }   
                if (!is_null($descripcion)) {
                    $subquery->where('descripcion', 'LIKE', '%'.$descripcion.'%');
                }                
            }     
        })->orderBy('descripcion', 'ASC');
    }

    public function scopelistar2($query, $descripcion, $curso_id, $alumno_id, $filtro)
    {
        return $query->where(function($subquery) use($descripcion, $curso_id, $alumno_id, $filtro){

            $cursos = Curso::select('curso.id')
            ->distinct()
            ->join('alumno_curso', 'alumno_curso.curso_id', '=', 'curso.id')
            ->where('alumno_id', $alumno_id)
            ->where('curso.estado', true)
            ->where('alumno_curso.deleted_at', null)
            ->get();  

            if(count($cursos) == 0) {
                $subquery->where('curso_id', 0);
            } else {
                if (!is_null($curso_id)) {                               
                    foreach ($cursos as $curso) {
                        if($curso_id == $curso['id']) {
                            $subquery->where('curso_id', $curso_id);
                            break;
                        }
                    }                    
                } else {
                    if (!is_null($filtro)) {     
                        $array = array();
                        if(count($cursos) > 0) {
                            foreach ($cursos as $curso) {
                                $array[] = $curso['id'];                            
                            }
                            $subquery->where(function ($query) use ($array) {
                                for ($i=0; $i < count($array); $i++) { 
                                    $query->orWhere('curso_id', $array[$i]);
                                }
                            });
                        }                        
                    }  
                }  
                if (!is_null($descripcion)) {
                    $subquery->where('descripcion', 'LIKE', '%'.$descripcion.'%');            
                }
            }                
        })->orderBy('descripcion', 'ASC');
    }
}
