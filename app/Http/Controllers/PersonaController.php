<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Persona;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PersonaController extends Controller
{
    public function personasautocompleting($searching) {
        $user      = Auth::user();
        $local_id  = $user->persona->local_id;
        $resultado = Persona::where(DB::raw('CONCAT(apellidopaterno," ",apellidomaterno," ",nombres)'), 'LIKE', '%'.strtoupper($searching).'%')
            ->orderBy('apellidopaterno', 'ASC')
            ->where("local_id", "=", $local_id)
            ->select('persona.*');
        $list      = $resultado->get();
        $data = array();
        foreach ($list as $key => $value) {
            $data[] = array(
                'label' => $value->apellidopaterno.' '.$value->apellidomaterno.' '.$value->nombres,
                'id'    => $value->id,
                'value' => $value->apellidopaterno.' '.$value->apellidomaterno.' '.$value->nombres,
                'dni'   => $value->dni,
            );
        }
        return json_encode($data);
    }

}
