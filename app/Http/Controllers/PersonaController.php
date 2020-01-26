<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Persona;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PersonaController extends Controller
{
    public function personasautocompleting($searching) {
        $entidad    = 'Persona';
        $mdlPerson = new Persona();
        $resultado = Persona::where(DB::raw('CONCAT(apellidopaterno," ",apellidomaterno," ",nombres)'), 'LIKE', '%'.strtoupper($searching).'%')->orderBy('apellidopaterno', 'ASC')->select('persona.*');
        $list      = $resultado->get();
        $data = array();
        foreach ($list as $key => $value) {
            $data[] = array(
                            'label' => $value->apellidopaterno.' '.$value->apellidomaterno.' '.$value->nombres,
                            'id'    => $value->id,
                            'value' => $value->apellidopaterno.' '.$value->apellidomaterno.' '.$value->nombres,
                        );
        }
        return json_encode($data);
    }

}
