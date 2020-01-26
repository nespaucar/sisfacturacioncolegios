<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlumnoCuota extends Model
{
    use SoftDeletes;
    protected $table = 'alumno_cuota';
    protected $dates = ['deleted_at'];
}
