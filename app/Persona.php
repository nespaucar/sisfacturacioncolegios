<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Usuario;

class Persona extends Model
{
    use SoftDeletes;
    protected $table = 'persona';
    protected $dates = ['deleted_at'];

    public function local(){
        return $this->belongsTo('App\Local', 'local_id');
    }
}
