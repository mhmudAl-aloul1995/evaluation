<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\patient_travel;
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
use App\patient_personal;

class patientTravelController extends Controller {

    public function index(Request $request) {
        $data = $request->all();


        return view('patientTravel');
    }

    public function excelPatient(Request $request) {
        $excel = [];

        Excel::load($request->name->getRealPath(), function($reader) use (&$excel) {
            $objExcel = $reader->getExcel();
            $sheet = $objExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            for ($row = 0; $row <= $highestRow; $row++) {
                $excel[] = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE)[0];
            }
        })->formatDates(true, 'Y-m-d');




        for ($counter = 0; $counter < sizeof($excel); $counter++) {
            $row = $excel[$counter];

            if (is_numeric($row[0])) {
            
                if (trim($row[2]) == null) {
                    $row[2] = '--';
                }
                if (trim($row[3]) == null) {
                    $row[3] = '--';
                }
                $row[4] = strtr($row[4], '/', '-');

                $row[4] = date('Y-m-d', strtotime($row[4]));

                $app_type_id = \App\app_type::firstOrCreate(["app_type_name" => $row[2]]);
                $committee_decision_id = \App\committee_decision::firstOrCreate(["committee_decision_name" => $row[3]]);



                $patient = patient_personal::where(['identity' => trim($row[0])])->first();
                if ($patient) {
                    $travel = \App\patient_travel::updateOrCreate(["personal_id" => $patient->id], [
                                "diagnosis" => trim($row[1]),
                                "travel_date" => $request->travel_date,
                                "app_type_id" => $app_type_id->id,
                                "committee_decision_id" => $committee_decision_id->id,
                                "personal_id" => $patient->id,
                                'created_by' => 1,
                    ]);
                }
            }
        }
        return response(['success' => true, 'message' => 'تم الإضافة بنجاح']);
    }

    public function addPatientTravel(Request $request) {

        $data = $request->all();
        {

            $data['created_by'] = 1;

            $addPatient_travel = patient_travel::create($data);
            if ($addPatient_travel) {
                return response(['success' => true, 'message' => 'تم الإضافة بنجاح']);
            }
        }

        return response(['message' => 'fail'], 403);
    }

    public function getPatientTravel(Request $request) {

        $data = $request->all();
        foreach ($data as $key => $value) {

            if ($value == '' || $key == 'columns' || $key == '_token' || $key == 'order' || $key == 'search' || $key == 'length' || $key == 'start' || $key == 'draw' || $key == '_') {
                unset($data[$key]);
            }
        }

        $users = patient_travel::query()->where($data)
                        ->leftJoin('patient_personal', function($join) {
                            $join->on('patient_travel.personal_id', '=', 'patient_personal.id');
                        })->leftJoin('committee_decision', function($join) {
                    $join->on('committee_decision.id', '=', 'patient_travel.committee_decision_id');
                })->leftJoin('app_type', function($join) {
                    $join->on('app_type.id', '=', 'patient_travel.app_type_id');
                })->select('patient_personal.name as p_name', 'patient_travel.*', 'committee_decision.committee_decision_name', 'app_type.app_type_name')->orderBy('patient_travel.id', 'desc');


        return Datatables::of($users)
                        ->addColumn('action', function($ctr) {

                            return '<div  class="btn-group">
                                                        <button  class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> إجراء
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul  class="dropdown-menu" role="menu">
                                                            <li>
                                                                <a onclick="travelPatientModal(' . $ctr->id . ')" href="javascript:;">
                                                                    <i class="icon-pencil"></i> تعديل </a>
                                                            </li>
                                                            <li>
                                                                <a onclick="deletePatientTravel(' . $ctr->id . ')"  href="javascript:;">
                                                                    <i class="icon-trash"></i> حذف  </a>
                                                            </li>
                                                            </ul>
                                                    </div>';
                        })
                        ->rawColumns(['action' => 'action', 'city' => 'city', 'name' => 'name'])
                        ->make(true);
    }

    public function showPatientTravel(Request $request) {

        $data = $request->all();

        unset($data['_token']);

        $patient_travel = patient_travel::find($data['id']);

        if (!$patient_travel) {

            return response()->json([
                        'success' => false,
            ]);
        }

        return response()->json([
                    'success' => TRUE,
                    'data' => $patient_travel,
        ]);
    }

    public function editPatientTravel(Request $request) {

        $data = $request->all();
        unset($data['_token']);

        $travelUpdate = patient_travel::find($data['id'])->update($data);

        if ($travelUpdate) {

            return response(['success' => true, 'message' => "تم التعديل بنجاح"]);
        }
        return response(['message' => 'fail'], 403);
    }

    public function deletePatientTravel(Request $request) {

        $data = $request->all();

        \DB::beginTransaction();
        try {
            $patient_travel = patient_travel::find($data['id'])->delete();

            if ($patient_travel == null) {
                \DB::rollback();
                return response()->json(['success' => FALSE]);
            } else {
                \DB::commit();

                return response()->json(['success' => true]);
            }
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['success' => FALSE]);
        }
        return response()->json(['success' => FALSE]);
    }

    function checkColumn(Request $request) {
        $data = $request->all();

        unset($data['_token']);
        unset($data['table']);

        if ($data['id'] > 0) {

            $pk = $data['id'];
            unset($data['id']);

            $Tabel = patient_travel::where('id', '!=', $pk)->where($data)->first();
        } else {

            unset($data['id']);
            $Tabel = patient_travel::where($data)->first();
        }
        if ($Tabel == null) {
            return response()->json(['valid' => True]);
        } else {
            return response()->json(['valid' => FALSE]);
        }
    }

}
