<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cicloacademico extends Model
{
    use SoftDeletes;
    protected $table = 'cicloacademico';
    protected $dates = ['deleted_at'];

    public function local()
	{
		return $this->belongsTo('App\Local', 'local_id');
	}
}
