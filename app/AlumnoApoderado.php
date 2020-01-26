<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlumnoApoderado extends Model
{
    use SoftDeletes;
    protected $table = 'alumno_apoderado';
    protected $dates = ['deleted_at'];
}
