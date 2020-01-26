<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlumnoSeccion extends Model
{
    use SoftDeletes;
    protected $table = 'alumno_seccion';
    protected $dates = ['deleted_at'];
}
