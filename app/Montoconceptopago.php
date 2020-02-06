<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Montoconceptopago extends Model
{
    use SoftDeletes;
    protected $table = 'montoconceptopago';
    protected $dates = ['deleted_at'];

    public function local()
	{
		return $this->belongsTo('App\Local', 'local_id');
    }

    public function conceptopago()
	{
		return $this->belongsTo('App\Conceptopago', 'conceptopago_id');
    }
}
