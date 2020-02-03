<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cuota extends Model
{
    use SoftDeletes;
    protected $table = 'cuota';
    protected $dates = ['deleted_at'];

    public function secciones()
    {
        return $this->hasMany('App\AlumnoCuota');
    }

    public function cicloacademico()
    {
        return $this->belongsTo('App\Cicloacademico', 'cicloacademico_id');
    }
}
