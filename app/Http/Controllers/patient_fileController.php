<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Yajra\Datatables\Datatables;
use File;
use App\patient_personal;
use App\patient_medical;

class patient_fileController extends Controller {

    public function index($id = null, $donor_id = null) {
        $economic_situt = ['', 'موظف', 'غير موظف'];
        $social_situt = ['', 'اعزب', 'متزوج', 'أرمل', 'مطلق'];

        $qualification = ['غير ذلك', 'إبتدائي', 'إعدادي', 'ثانوي', 'جامعي'];

        $patient = patient_personal::find($id);

        $patient->economic_situt = $economic_situt[$patient->economic_situt];
        $patient->social_situt = $social_situt[$patient->social_situt];
        $patient->qualification = $qualification[$patient->qualification];
        if (property_exists($patient, $patient->city) && property_exists($patient, $patient->area)) {
            $patient->address = $patient->city->city_name . '-' . $patient->area->area_name;
        } else {
            $patient->address = '--';
        }




        return view('patient_info')->with([ "patient" => $patient]);
    }

}
