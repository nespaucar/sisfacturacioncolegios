<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlumnoCuota extends Model
{
    use SoftDeletes;
    protected $table = 'alumno_cuota';
    protected $dates = ['deleted_at'];

    public function cuota()
    {
        return $this->belongsTo('App\Cuota', 'cuota_id');
    }

    public function alumno()
	{
		return $this->belongsTo('App\Persona', 'alumno_id');
    }
}
