<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\patient_personal;
use App\Donor;
use App\Donororph;
use Illuminate\Http\UploadedFile;
use Yajra\Datatables\Datatables;
use File;
use Illuminate\Support\Facades\DB;
use Excel;
use App\City;
use App\Area;
use DateTime;
use App\User;
use Illuminate\Support\Carbon;
use App\Dis_service;
use App\Upload;

class patientController extends Controller {

    public function index(Request $request) {
        $data = $request->all();


        return view('patient');
    }

    public function excel_patient(Request $request) {
        $excel = [];

        Excel::load($request->name->getRealPath(), function($reader) use (&$excel) {
            $objExcel = $reader->getExcel();
            $sheet = $objExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            for ($row = 0; $row <= $highestRow; $row++) {
                $excel[] = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE)[0];
            }
        })->setDateFormat('Y-m-d');




        for ($counter = 0; $counter < sizeof($excel); $counter++) {
            $row = $excel[$counter];
            $wepon = ['' => 6, 'رصاص متفجر' => 1, 'رصاص حي' => 2, 'رصاص مطاطي' => 3, 'قنبلة غاز' => 4, 'غاز' => 4, 'شظايا' => 5, 'أخرى' => 6, 'أخري' => 6];
            $sex = ['أنثى' => 1, 'ذكر' => 2, 'انثى' => 1, '' => 0, 'غير ذلك' => 0];
            $social_situt = ['اعزب' => 1, 'أعزب' => 1, 'متزوج' => 2, 'أرمل' => 3, 'مطلق' => 4, '' => 0, 'متعدد الزوجات' => 5];
            $qualification = ['إبتدائي' => 1, 'ابتدائي' => 1, 'إعدادي' => 2, 'ثانوي' => 3, 'جامعي' => 4, 'أخرى' => 5, 'غير ذلك' => 5, '' => 0];
            $economic_situt = ['موظف' => 1, 'غير موظف' => 2, '' => 0, 'غير ذلك' => 0];


            if (is_numeric($row[1])) {


                if (trim($row[6]) == null) {
                    $row[6] = '--';
                }
                if (trim($row[7]) == null) {
                    $row[7] = '--';
                }
                if (trim($row[15]) == null) {
                    $row[15] = '--';
                }
                if (trim($row[16]) == null) {
                    $row[16] = '--';
                }
                if (trim($row[17]) == null) {
                    $row[17] = '--';
                }
                if (trim($row[23]) == null) {
                    $row[23] = '--';
                }
                if (trim($row[24]) == null) {
                    $row[24] = '--';
                }
                if (trim($row[31]) == null) {
                    $row[31] = '--';
                }

                $row[2] = ($row[2] == null) ? '0000-00-00' : DateTime::createFromFormat("m/d/Y", $row[2])->format('Y-m-d');
                $row[14] = ($row[14] == null) ? '0000-00-00' : DateTime::createFromFormat("m/d/Y", $row[14])->format('Y-m-d');
                $row[25] = ($row[25] == null) ? '0000-00-00' : DateTime::createFromFormat("m/d/Y", $row[25])->format('Y-m-d');
                $row[27] = ($row[27] == null) ? '0000-00-00' : DateTime::createFromFormat("m/d/Y", $row[27])->format('Y-m-d');
                $row[21] = ($row[21] == null) ? '0000-00-00' : DateTime::createFromFormat("m/d/Y", $row[21])->format('Y-m-d');
                $row[28] = ($row[28] == null) ? '0000-00-00' : DateTime::createFromFormat("m/d/Y", $row[28])->format('Y-m-d');



                $city = City::firstOrCreate(["city_name" => $row[6]]);
                $area = Area::firstOrCreate(["area_name" => $row[7]]);
                $status_desc = \App\status_desc::firstOrCreate(["status_desc_name" => trim($row[16])]);
                $place = \App\place::firstOrCreate(["place_name" => trim($row[15])]);
                $weapon = \App\Weapon::firstOrCreate(["weapon_name" => trim($row[17])]);
                $app_type_id = \App\app_type::firstOrCreate(["app_type_name" => $row[23]]);
                $committee_decision_id = \App\committee_decision::firstOrCreate(["committee_decision_name" => $row[24]]);
                $committee_id = \App\Committee::firstOrCreate(["comit_date" => $row[28]]);
                $committeeClass_id = \App\CommitteeClass::firstOrCreate(["committee_class_name" => $row[29]]);
                $need = \App\needs::firstOrCreate(["need_name" => $row[31]]);

                $patient = patient_personal::updateOrCreate(['identity' => trim($row[1]),], [
                            "name" => trim($row[0]),
                            'identity' => trim($row[1]),
                            'dob' => $row[2],
                            'sex' => $sex[trim($row[3])],
                            'social_situt' => $social_situt[trim($row[4])],
                            'child_no' => trim($row[5]),
                            "city_id" => $city->id,
                            "area_id" => $area->id,
                            "masjisd" => trim($row[8]),
                            "qualification" => $qualification[trim($row[9])],
                            "grade" => trim($row[10]),
                            'year_graduation' => trim($row[11]),
                            'economic_situt' => $economic_situt[trim($row[12])],
                            'phone' => trim($row[13]),
                            'created_by' => 1,
                ]);

                $medical = \App\patient_medical::updateOrCreate(["date" => trim($row[14]), "personal_id" => $patient->id,], [
                            "date" => trim($row[14]),
                            "place_id" => $place->id,
                            "status_desc_id" => $status_desc->id,
                            "weapon_id" => $weapon->id,
                            "personal_id" => $patient->id,
                            'created_by' => 1,
                ]);


                $agent = \App\Agent::updateOrCreate(["agent_identity" => trim($row[19]), "personal_id" => $patient->id,]
                                , ["created_by" => 1, "agent_name" => trim($row[18]), "identity" => trim($row[19]), "agent_phone" => trim($row[20]), "personal_id" => $patient->id,]);

                $travel = \App\patient_travel::updateOrCreate(["personal_id" => $patient->id, 'travel_date_comit' => trim($row[21])], [
                            "travel_date_comit" => trim($row[21]),
                            "diagnosis" => trim($row[22]),
                            "travel_date" => trim($row[25]),
                            "travel_place" => trim($row[26]),
                            "travel_back_date" => trim($row[27]),
                            "app_type_id" => $app_type_id->id,
                            "committee_decision_id" => $committee_decision_id->id,
                            "personal_id" => $patient->id,
                            'created_by' => 1,
                ]);

                $commit_medical = \App\CommitteePatient::updateOrCreate(["personal_id" => $patient->id, 'committee_id' => $committee_id->id], [
                            "treatment_duration" => trim($row[30]),
                            "need_id" => $need->id,
                            "committee_class_id" => $committeeClass_id->id,
                            "committee_id" => $committee_id->id,
                            "personal_id" => $patient->id,
                            'created_by' => 1,
                ]);
            }
        }

        return response(['success' => true, 'message' => 'تم الإضافة بنجاح']);
    }

    public function add_personal(Request $request) {


        $data = $request->all();
        $upload['avatar'] = $data['avatar'];
        $upload['upload_name'] = $data['upload_name'];
        unset($data['avatar']);
        unset($data['upload_name']);

        if ($request->hasFile('upload_name')) {

            $filenameUrl = time() . $upload['upload_name']->getClientOriginalName();

            $upload['upload_name']->move(public_path() . '/upload_name', $filenameUrl);
            $upload['upload_name'] = $filenameUrl;
        }
        if ($request->hasFile('avatar')) {

            $filenameUrl = time() . $upload['avatar']->getClientOriginalName();

            $upload['avatar']->move(public_path() . '/avatar', $filenameUrl);
            $upload['avatar'] = $filenameUrl;
        }
        $this->validate($request, [
            'name' => 'required:patient_personal|max:255',
            'identity' => 'required:patient_personal|max:255',
            'masjisd' => 'max:255',
        ]);
        $data['created_by'] = 1;
        $data['name'] = trim($data['name']);
        $data['identity'] = trim($data['identity']);

        $addPatient_personal = patient_personal::updateOrCreate(['identity' => $data['identity'],], $data);
        if (!empty($upload['upload_name'])) {
            $user = new Upload;

            $user->personal_id = $addPatient_personal->id;
            $user->upload_name = $upload['upload_name'];
            $user->is_avatar = 0;

            $user->save();
        }
        if (!empty($upload['avatar'])) {
            $user = new Upload;
            $user->personal_id = $addPatient_personal->id;

            $user->upload_name = $upload['avatar'];
            $user->is_avatar = 1;

            $user->save();
        }
        if ($addPatient_personal) {
            return response(['success' => true, 'message' => 'تم الإضافة بنجاح']);
        }

        return response(['message' => 'fail'], 403);
    }

    public function getPersonal(Request $request) {

        $data = $request->all();
        foreach ($data as $key => $value) {

            if ($value == '' || $key == 'columns' || $key == '_token' || $key == 'order' || $key == 'search' || $key == 'length' || $key == 'start' || $key == 'draw' || $key == '_') {
                unset($data[$key]);
            }
        }

        $users = patient_personal::query()->leftJoin('cities', function($join) {
                    $join->on('cities.id', '=', 'patient_personal.city_id');
                })->leftJoin('areas', function($join) {
                    $join->on('areas.id', '=', 'patient_personal.area_id');
                })->orderBy('patient_personal.name', 'asc')->select('patient_personal.*', 'areas.area_name', 'cities.city_name');

        if (isset($data['age_from']) || isset($data['age_to'])) {
            $users->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR,patient_personal.dob,CURDATE())'), [$data['age_from'], $data['age_to']]);
            unset($data['age_from']);
            unset($data['age_to']);
        }
        if (isset($data['medical_date_from']) || isset($data['medical_date_to'])) {
            $medical_date_from = $data['medical_date_from'];
            $medical_date_to = $data['medical_date_to'];
            unset($data['medical_date_from']);
            unset($data['medical_date_to']);

            $users->WhereHas('committee', function ($patient_medical) use($medical_date_from, $medical_date_to) {

                $patient_medical->whereBetween("date", [$medical_date_from, $medical_date_to]);
            });
        }
        if (isset($data['treatment_duration'])) {
            $treatment_duration = $data['treatment_duration'];


            unset($data['treatment_duration']);

            $users->WhereHas('committeePatients', function ($committeePatients) use($treatment_duration) {

                $committeePatients->whereIn("treatment_duration", $treatment_duration);
            });
        }
        if (isset($data['committee_class_id'])) {
            $committee_class_id = $data['committee_class_id'];
            unset($data['committee_class_id']);

            $users->WhereHas('committeePatients', function ($committeePatients) use($committee_class_id) {
                $committeePatients->whereIn("committee_class_id", $committee_class_id);
            });
        }
        if (isset($data['committee_id'])) {
            $committee_id = $data['committee_id'];
            unset($data['committee_id']);

            $users->WhereHas('committeePatients', function ($committeePatients) use($committee_id) {

                $committeePatients->where("committee_id", $committee_id);
            });
        }
        if (isset($data['place_id'])) {
            $place_id = $data['place_id'];
            unset($data['place_id']);

            $users->WhereHas('patientMedicals', function ($patient_medical) use($place_id) {

                $patient_medical->where("place_id", $place_id);
            });
        }
        if (isset($data['status_desc_id'])) {


            $status_desc_id = $data['status_desc_id'];
            unset($data['status_desc_id']);

            $users->WhereHas('patientMedicals', function ($patient_medical) use($status_desc_id) {

                $patient_medical->whereHas("statusDesc", function($status_desc) use($status_desc_id) {
                    $status_desc->where("id", $status_desc_id);
                });
            });
        }
        if (isset($data['medical_date_from']) && isset($data['medical_date_to'])) {


            $medical_date_from = $data['medical_date_from'];
            $medical_date_to = $data['medical_date_to'];
            unset($data['medical_date_to']);
            unset($data['medical_date_from']);

            $users->WhereHas('patientMedicals', function ($patient_medical) use($medical_date_from, $medical_date_to) {

                $patient_medical->whereBetween("date", [$medical_date_from, $medical_date_to]);
            });
        }

        if (isset($data['service_date'])) {

            $service_date = $data['service_date'];
            unset($data['service_date']);

            $users->WhereHas('disServices', function ($disServices) use($service_date) {

                $disServices->where("service_date", $service_date);
            });
        }
        if (isset($data['service_id'])) {

            $service_id = $data['service_id'];
            unset($data['service_id']);

            $users->WhereHas('disServices', function ($disServices) use($service_id) {

                $disServices->where("service_id", $service_id);
            });
        }




        if ($data) {

            $users->where($data);
        }

        return Datatables::of($users)
                        ->addColumn('social_situt', function($ctr) {
                            $social_situt = ['', 'اعزب', 'متزوج', 'أرمل', 'مطلق'];

                            return $social_situt[$ctr->social_situt];
                        })
                        ->addColumn('economic_situt', function($ctr) {
                            $economic_situt = ['', 'موظف', 'غير موظف'];

                            return $economic_situt[$ctr->economic_situt];
                        })
                        ->editColumn('name', function($ctr) {
                            return "<a target='_blank' href=' " . url('patient') . '/' . $ctr->id . "'>{$ctr->name}</a> ";
                        })
                        ->addColumn('action', function($ctr) {

                            return '<div  class="btn-group">
                                                        <button  class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> إجراء
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul  class="dropdown-menu" role="menu">
                                                            <li>
                                                                <a onclick="personalPatientModal(' . $ctr->id . ')" href="javascript:;">
                                                                    <i class="icon-pencil"></i> تعديل </a>
                                                            </li>
                                                            <li>
                                                                <a onclick="deletepatient_personal(' . $ctr->id . ')"  href="javascript:;">
                                                                    <i class="icon-trash"></i> حذف  </a>
                                                            </li>
                                                            <li>
                                                                <a onclick="patient_uploads(' . $ctr->id . ')"  href="javascript:;">
                                                                    <i class="icon-trash"></i> حذف  </a>
                                                            </li>
                                                            </ul>
                                                    </div>';
                        })
                        ->rawColumns(['action' => 'action', 'city' => 'city', 'name' => 'name'])
                        ->make(true);
    }

    public function showPersonal(Request $request) {

        $data = $request->all();

        unset($data['_token']);

        $patient_personal = patient_personal::find($data['id']);
        $patient_upload = Upload::where([ 'personal_id' => $patient_personal ['id']])->get();

        if (!$patient_personal) {

            return response()->json([
                        'success' => false,
            ]);
        }

        return response()->json([
                    'success' => TRUE,
                    'patient_personal' => $patient_personal,
                    'patient_upload' => $patient_upload
        ]);
    }

    public function edit_personal(Request $request) {

        $data = $request->all();
        unset($data['_token']);

        $personalUpdate = patient_personal::find($data['id'])->update($data);

        if ($personalUpdate) {

            return response(['success' => true, 'message' => "تم التعديل بنجاح"]);
        }
        return response(['message' => 'fail'], 403);
    }

    public function deletePersonal(Request $request) {

        $data = $request->all();


        $patient_personal = patient_personal::find($data['id'])->delete();
        if ($patient_personal == null) {
            return response()->json(['success' => FALSE]);
        }

        return response()->json(['success' => true]);
    }

    public function checkColumn(Request $request) {
        $data = $request->all();

        unset($data['_token']);
        unset($data['table']);

        if ($data['id'] > 0) {

            $pk = $data['id'];
            unset($data['id']);

            $Tabel = patient_personal::where('id', '!=', $pk)->where($data)->first();
        } else {

            unset($data['id']);
            $Tabel = patient_personal::where($data)->first();
        }
        if ($Tabel == null) {
            return response()->json(['valid' => True]);
        } else {
            return response()->json(['valid' => FALSE]);
        }
    }

    public function add_dis_service(Request $request) {

        $data = $request->all();
        $dis = [];

        $dis['value'] = $data['value'];
        $dis['service_date'] = $data['service_date_dis'];
        $dis['service_id'] = $data['service_id_dis'];
        foreach ($data as $key => $value) {

            if ($value == '' || $key == 'value' || $key == 'options' || $key == '_token' || $key == 'service_date_dis' || $key == 'service_id_dis') {
                unset($data[$key]);
            }
        }

        $users = patient_personal::query();
        if (isset($data['travel_date'])) {
            $travel_date = $data['travel_date'];


            unset($data['travel_date']);

            $users->WhereHas('patient_travel', function ($committeePatients) use($travel_date) {

                $committeePatients->where("travel_date", $travel_date);
            });
        }
        if (isset($data['app_type_id'])) {
            $app_type_id = $data['app_type_id'];


            unset($data['app_type_id']);

            $users->WhereHas('patient_travel', function ($committeePatients) use($app_type_id) {

                $committeePatients->whereIn("app_type_id", $app_type_id);
            });
        }
        if (isset($data['committee_decision_id'])) {
            $committee_decision_id = $data['committee_decision_id'];


            unset($data['committee_decision_id']);

            $users->WhereHas('patient_travel', function ($committeePatients) use($committee_decision_id) {

                $committeePatients->whereIn("committee_decision_id", $committee_decision_id);
            });
        }
        if (isset($data['travel_place'])) {
            $travel_place = $data['travel_place'];


            unset($data['travel_place']);

            $users->WhereHas('patient_travel', function ($committeePatients) use($travel_place) {

                $committeePatients->whereIn("travel_place", $travel_place);
            });
        }
        if (isset($data['travel_back_date'])) {
            $travel_back_date = $data['travel_back_date'];


            unset($data['travel_back_date']);

            $users->WhereHas('patient_travel', function ($committeePatients) use($travel_back_date) {

                $committeePatients->where("travel_back_date", $travel_back_date);
            });
        }
        if (isset($data['age_from']) && isset($data['age_to'])) {
            $users->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR,patient_personal.dob,CURDATE())'), [$data['age_from'], $data['age_to']]);
            unset($data['age_from']);
            unset($data['age_to']);
        }
        if (isset($data['medical_date_from']) || isset($data['medical_date_to'])) {
            $medical_date_from = $data['medical_date_from'];
            $medical_date_to = $data['medical_date_to'];
            unset($data['medical_date_from']);
            unset($data['medical_date_to']);

            $users->WhereHas('committee', function ($patient_medical) use($medical_date_from, $medical_date_to) {

                $patient_medical->whereBetween("date", [$medical_date_from, $medical_date_to]);
            });
        }
        if (isset($data['patient_travel'])) {
            $treatment_duration = $data['treatment_duration'];


            unset($data['treatment_duration']);

            $users->WhereHas('patient_travel', function ($committeePatients) use($treatment_duration) {

                $committeePatients->whereIn("treatment_duration", $treatment_duration);
            });
        }
        if (isset($data['committee_class_id'])) {
            $committee_class_id = $data['committee_class_id'];
            unset($data['committee_class_id']);

            $users->WhereHas('committeePatients', function ($committeePatients) use($committee_class_id) {
                $committeePatients->whereIn("committee_class_id", $committee_class_id);
            });
        }
        if (isset($data['committee_id'])) {
            $committee_id = $data['committee_id'];
            unset($data['committee_id']);

            $users->WhereHas('committeePatients', function ($committeePatients) use($committee_id) {

                $committeePatients->where("committee_id", $committee_id);
            });
        }
        if (isset($data['place_id'])) {
            $place_id = $data['place_id'];
            unset($data['place_id']);

            $users->WhereHas('patientMedicals', function ($patient_medical) use($place_id) {

                $patient_medical->where("place_id", $place_id);
            });
        }
        if (isset($data['status_desc_id'])) {


            $status_desc_id = $data['status_desc_id'];
            unset($data['status_desc_id']);

            $users->WhereHas('patientMedicals', function ($patient_medical) use($status_desc_id) {

                $patient_medical->whereHas("statusDesc", function($status_desc) use($status_desc_id) {
                    $status_desc->where("id", $status_desc_id);
                });
            });
        }
        if (isset($data['service_date_from']) && isset($data['service_date_to'])) {


            $medical_date_from = $data['medical_date_from'];
            $medical_date_to = $data['medical_date_to'];
            unset($data['medical_date_to']);
            unset($data['medical_date_from']);

            $users->WhereHas('patientMedicals', function ($patient_medical) use($medical_date_from, $medical_date_to) {

                $patient_medical->whereBetween("date", [$medical_date_from, $medical_date_to]);
            });
        }

        if (isset($data['service_date'])) {

            $service_date = $data['service_date'];
            unset($data['service_date']);

            $users->WhereHas('disServices', function ($disServices) use($service_date) {

                $disServices->where("service_date", $service_date);
            });
        }
        if (isset($data['service_id'])) {

            $service_id = $data['service_id'];
            unset($data['service_id']);

            $users->WhereHas('disServices', function ($disServices) use($service_id) {

                $disServices->where("service_id", $service_id);
            });
        }




        if ($data) {

            $users->where($data);
        }
        if ($users->count() == 0) {
            return response(['message' => 'لا يوجد جرحى']);
        } else {


            foreach ($users->get() as $value) {

                $check = Dis_service::where(['service_date' => $dis['service_date'], 'service_id' => $dis['service_id'], 'personal_id' => $value->id])->first();

                if (!$check) {

                    $createDis = Dis_service::create(['created_by' => 1, 'value' => $dis['value'], 'service_id' => $dis['service_id'], 'service_date' => $dis['service_date'], 'personal_id' => $value->id]);
                }
            }
            return response()->json(['success' => TRUE, 'message' => "تم بنجاح"]);
        }

        return response(['message' => 'fail'], 403);
    }

}
