<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use PDF;
use Illuminate\Support\Facades\Auth;
use App\Alumno;
use App\Experiencias_Laborales;
use App\Certificado;
use App\CompetenciaAlumno;
use App\Librerias\Libreria;

class PdfController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function generarcurriculum(Request $request)
    {    
        $user         = Auth::user();
        $alumno       = Alumno::find($user->alumno_id);
        $nombrealumno = $alumno->apellidopaterno . '_' . $alumno->apellidomaterno;
        $nombrealumno = 'CV_' . $nombrealumno;
        $explaborales = Experiencias_Laborales::listartodo($user->alumno_id)->get();
        $competencias = CompetenciaAlumno::listar($user->alumno_id,'')->get();
        $certificados = Certificado::listarparacv($user->alumno_id,'')->get();

        $view = \View::make('app.reporte.generarcurriculum')->with(compact('alumno', 'explaborales', 'competencias', 'certificados'));
        $html_content = $view->render();      
 
        PDF::SetTitle($nombrealumno);
        PDF::AddPage(); 

        // set margins
        PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
        PDF::SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
        PDF::writeHTML($html_content, true, true, true, true, '');

        PDF::Output($nombrealumno.'.pdf', 'I');
    }
}
