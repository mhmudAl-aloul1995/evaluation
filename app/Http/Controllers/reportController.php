<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Yajra\Datatables\Datatables;
use File;
use App\patient_personal;
use App\patient_medical;
use Excel;
use App\Committee;
use App\CommitteeClass;
use App\CommitteePatient;
use DB;
use App\needs;
class reportController extends Controller {

    function remove_element(&$array, $value) {
        if (($key = array_search($value, $array)) !== false) {
            unset($array[$key]);
        }
    }

    public function index(Request $request) {
        $data = $request->all();
        $ctg_super = false;
        if (in_array('ctg_super', $data['options'])) {
            $ctg_super = true;
        }
        if (($key = array_search('ctg_super', $data['options'])) !== false) {
            unset($data['options'][$key]);
        }
        $options = $data['options'];


        $tables = [];
        $wherePatient = [];
        $names = explode(",", $data['names']);
        if (($key = array_search('التصنيف الأقل-التصنيف الأعلى', $names)) !== false) {
            unset($names[$key]);
        }
        if (($key = array_search('الكل', $names)) !== false) {
            unset($names[$key]);
        }
        if (($key = array_search('all', $options)) !== false) {
            unset($options[$key]);
        }
        $dataOthers = $request->except(['export', 'names', 'options', 'super_class', 'total_ammount']);

        $export = null;
        $relations = ['city_id' => "patient_personal",
            'area_id' => "patient_personal",
            'created_by' => "patient_personal",
            'name' => "patient_personal",
            'identity' => "patient_personal",
            'phone' => "patient_personal",
            'dob' => "patient_personal",
            'sex' => "patient_personal",
            'masjisd' => "patient_personal",
            'year_graduation' => "patient_personal",
            'child_no' => "patient_personal",
            'qualification' => "patient_personal",
            'grade' => "patient_personal",
            'economic_situt' => "patient_personal",
            'social_situt' => "patient_personal",
            'weapon_id' => 'patient_medical',
            'status_desc_id' => 'patient_medical',
            'service_date_from' => 'dis_service',
            'service_date_to' => 'dis_service',
            'medical_date_to' => 'patient_medical',
            'medical_date_to' => 'patient_medical',
            'place_id' => 'patient_medical',
            'date' => 'patient_medical',
            'committee_id' => 'committee_patient',
            'treatment_duration' => 'committee_patient',
            'committee_id' => 'committee_patient',
            'need_id' => 'committee_patient',
            'committee_class_id' => 'committee_patient',
            'agent_name' => 'agents',
            'agent_identity' => 'agents',
            'agent_phone' => 'agents',
            'service_id' => 'dis_service',
            'service_date' => 'dis_service',
            'value' => 'dis_service',
            'diagnosis' => 'patient_travel',
            'travel_back_date' => 'patient_travel',
            'travel_date_comit' => 'patient_travel',
            'travel_place' => 'patient_travel',
            'travel_date' => 'patient_travel',
            'committee_decision_id' => 'patient_travel',
            'app_type_id' => 'patient_travel',
            'age_from' => 'patient_medical',
            'age_to' => 'patient_medical',
        ];


        foreach ($dataOthers as $key => $row) {

            if ($row == null) {


                unset($dataOthers[$key]);
            } else {
                array_push($tables, $relations[$key]);
            }
        }

        $users = patient_personal::orderBy('patient_personal.name', 'asc');



        $optionsFilterd = array_filter($options, function ($var) {
            return (strpos($var, 'total_ammount') === false);
        });

        foreach ($optionsFilterd as $key => $value) {


            array_push($tables, $relations[$value]);
        }
        array_push($optionsFilterd, 'patient_personal.id');

        $users->select($optionsFilterd);
        $unique_tables = array_unique($tables);

        foreach ($unique_tables as $key => $table) {

            if ($table != 'patient_personal') {

                $users->leftjoin($table, 'patient_personal.id', '=', $table . '.personal_id');
            }
        }

        foreach ($dataOthers as $key => $value) {

            if ($key == 'medical_date_from' || $key == 'medical_date_to') {
                $users->whereBetween("date", [$dataOthers['medical_date_from'], $dataOthers['medical_date_to']]);
                continue;
            }
            if ($key == 'service_date_from' || $key == 'service_date_to') {
                $users->whereBetween("service_date", [$dataOthers['service_date_from'], $dataOthers['service_date_to']]);
                continue;
            }
            if ($key == 'age_from' || $key == 'age_to') {
                $users->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR,patient_personal.dob,CURDATE())'), [$dataOthers['age_from'], $dataOthers['age_to']]);
                continue;
            }
            if (is_array($value)) {

                $users->whereIn($relations[$key] . '.' . $key, $value);
            } else {
                $users->where($relations[$key] . '.' . $key, $value);
            }
        }

        if ($data['export'] == 'xlxs') {

            Excel::create('itsolutionstuff_example', function ($excel) use ($users, $options, $names, $ctg_super) {
                $excel->sheet('mySheet', function ($sheet) use ($users, $options, $names, $ctg_super) {


                    $i = 2;

                    $excel_array;
                    if ($ctg_super) {
                        array_push($names, 'التصنيف الأقل');
                        array_push($names, 'فترة العلاج');
                        array_push($names, 'التصنيف الأعلى');
                        array_push($names, 'فترة العلاج');
                    }
                    $sheet->row(1, $names);
                    $lastElementKey = array_search(end($options), $options);
                    foreach ($users->get() as $row) {
                        $key;
                        foreach ($options as $key => $col) {
                            if ($col == 'total_ammount') {

                                $row[$col] = \App\Dis_service::where('personal_id', $row->id)->sum('value') . ' $';
                            }

                            if ($col == 'service_id') {


                                $row[$col] = \App\Service::find($row[$col])['name'];
                            }
                            if ($col == 'qualification') {

                                $qualification = ['أخرى', 'إبتدائي', 'إعدادي', 'ثانوي', 'جامعي', 'أخرى'];

                                $row[$col] = $qualification[$row[$col]];
                            }
                            if ($col == 'status_desc_id') {


                                $row[$col] = \App\status_desc::find($row[$col])['name'];
                            }
                            if ($col == 'place_id') {


                                $row[$col] = \App\Place::find($row[$col])['name'];
                            }
                            if ($col == 'economic_situt') {

                                $economic_situt = ['', 'اعزب', 'متزوج', 'أرمل', 'مطلق'];

                                $row[$col] = $economic_situt[$row[$col]];
                            }
                            if ($col == 'sex') {

                                $sex = ['', 'ذكر', 'أنثى'];

                                $row[$col] = $sex[$row[$col]];
                            }
                            if ($col == 'social_situt') {

                                $social_situt = ['', 'موظف', 'غير موظف'];

                                $row[$col] = $social_situt[$row[$col]];
                            }
                            if ($col == 'city_id') {


                                $row[$col] = $row['city']['city_name'];
                            }
                            if ($col == 'area_id') {


                                $row[$col] = $row['area']['area_name'];
                            }
                            if ($col == 'need_id') {

                                $row[$col] = needs::find($row->need_id)['need_name'];
                            }
                            if ($col == 'committee_class_id') {

                                $row[$col] = CommitteeClass::find($row->committee_class_id)['committee_class_name'];
                            }

                            if ($col == 'committee_id') {


                                $row[$col] = Committee::find($row->committee_id)['comit_date'];
                            }


                            if ($col == 'committee_decision_id') {


                                $row[$col] = \App\committee_decision::find($row->committee_decision_id)['committee_decision_name'];
                            }


                            if ($col == 'app_type_id') {


                                $row[$col] = \App\app_type::find($row->app_type_id)['app_type_name'];
                            }


                            $excel_array[$key] = $row[$col];
                        }

                        if ($ctg_super) {



                            $ctg_modern = \App\CommitteePatient::where(['personal_id' => $row->id])->with('committeeClass');

                            if ($ctg_modern->count()) {
                                $ctg_modern = $ctg_modern->WhereHas('committee', function($com) {
                                            $com->whereRaw('comit_date = (select max(`comit_date`) from committees)');
                                        })->first();
                                $excel_array[$lastElementKey + 1] = $ctg_modern['committeeClass']['committee_class_name'];
                                $excel_array[$lastElementKey + 2] = $ctg_modern['treatment_duration'];
                            } else {
                                $excel_array[$lastElementKey + 1] = '';
                                $excel_array[$lastElementKey + 2] = '';
                            }

                            $ctg_old = \App\CommitteePatient::where(['personal_id' => $row->id])->with('committeeClass');

                            if ($ctg_old->count()) {
                                $ctg_old = $ctg_old->WhereHas('committee', function($com) {
                                            $com->whereRaw('comit_date = (select min(`comit_date`) from committees)');
                                        })->first();
                                $excel_array[$lastElementKey + 3] = $ctg_old['committeeClass']['committee_class_name'];
                                $excel_array[$lastElementKey + 4] = $ctg_old['treatment_duration'];
                            } else {
                                $excel_array[$lastElementKey + 3] = '';
                                $excel_array[$lastElementKey + 4] = '';
                            }
                        }
                        $sheet->row($i, $excel_array);

                        $i++;
                    }

                    $sheet->setAutoFilter();
                });
            })->download('xlsx');
        }

        return view('report')->with(['ctg_super' => $ctg_super, 'names' => $names, 'options' => $options, 'users' => $users->get()]);
    }

}
