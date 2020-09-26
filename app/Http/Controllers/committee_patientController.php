<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\CommitteePatient;
use Illuminate\Http\UploadedFile;
use Yajra\Datatables\Datatables;
use File;
use Illuminate\Support\Facades\DB;
use Excel;
use App\patient_personal;

class committee_patientController extends Controller {

    public function index($donor_id = null) {


        return view('committeePatient');
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
        })->formatDates(true, 'Y-m-d');




        for ($counter = 0; $counter < sizeof($excel); $counter++) {
            $row = $excel[$counter];

            if (is_numeric($row[0])) {

                if (trim($row[1]) == null) {
                    $row[1] = '--';
                }
                if (trim($row[2]) == null) {
                    $row[2] = '--';
                }
                if (trim($row[4]) == null) {
                    $row[4] = '--';
                }
                $committee_id = \App\Committee::firstOrCreate(["comit_name" => $row[1]]);
                $committeeClass_id = \App\CommitteeClass::firstOrCreate(["committee_class_name" => $row[2]]);
                $need = \App\needs::firstOrCreate(["need_name" => $row[4]]);


                $patient = patient_personal::where(['identity' => trim($row[0])])->first();

                if ($patient) {

                    $medical = \App\CommitteePatient::firstOrCreate([
                                "treatment_duration" => trim($row[3]),
                                "need_id" => $need->id,
                                "committee_class_id" => $committeeClass_id->id,
                                "committee_id" => $committee_id->id,
                                "personal_id" => $patient->id,
                                'created_by' => 1,
                    ]);
                }
            }
        }
        return response(['success' => true, 'message' => 'تم الإضافة بنجاح']);
    }

    public function add_committeePatient(Request $request) {

        $data = $request->all();
        {


            $data['created_by'] = 1;

            $check_committeePatient = CommitteePatient::where(['personal_id' => $data['personal_id'], 'committee_id' => $data['committee_id'],])->first();
            if ($check_committeePatient) {
                return response(['success' => false, 'message' => 'هذا الجريح لديه بيانات طبية مسبقة في نفس اللجنة']);
            }
            $addCommitteePatient_committeePatient = CommitteePatient::create($data);
            if ($addCommitteePatient_committeePatient) {
                return response(['success' => true, 'message' => 'تم الإضافة بنجاح']);
            }
        }

        return response(['message' => 'fail'], 403);
    }

    public function getCommitteePatient(Request $request) {

        $data = $request->all();

        foreach ($data as $key => $value) {

            if ($value == '' || $key == 'columns' || $key == '_token' || $key == 'order' || $key == 'search' || $key == 'length' || $key == 'start' || $key == 'draw' || $key == '_') {
                unset($data[$key]);
            }
        }
        $users = CommitteePatient::query()
                        ->leftJoin('patient_personal', function($join) {
                            $join->on('committee_patient.personal_id', '=', 'patient_personal.id');
                        })->leftJoin('needs', function($join) {
                    $join->on('needs.id', '=', 'committee_patient.need_id');
                })->leftJoin('committees', function($join) {
                    $join->on('committees.id', '=', 'committee_patient.committee_id');
                })->leftJoin('committee_class', function($join) {
                    $join->on('committee_class.id', '=', 'committee_patient.committee_class_id');
                })->select('committee_patient.*', 'patient_personal.name as p_name', 'committees.comit_date', 'needs.need_name', 'committee_class.committee_class_name')->orderBy('committee_patient.id', 'desc');
       $users->where($data);



        return Datatables::of($users)
                        ->addColumn('action', function($ctr) {

                            return '<div class="btn-group">
                                                        <button  class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> إجراء
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul  class="dropdown-menu" role="menu">
                                                            <li>
                                                                <a onclick="committeePatientModal(' . $ctr->id . ')" href="javascript:;">
                                                                    <i class="icon-pencil"></i> تعديل </a>
                                                            </li>
                                                            <li>
                                                                <a onclick="deleteCommitteePatient(' . $ctr->id . ')"  href="javascript:;">
                                                                    <i class="icon-trash"></i> حذف  </a>
                                                            </li>
                                                            </ul>
                                                    </div>';
                        })
                        ->rawColumns(['action' => 'action'])
                        ->make(true);
    }

    public function showCommitteePatient(Request $request) {

        $data = $request->all();

        unset($data['_token']);

        $CommitteePatient = CommitteePatient::find($data['pk_id']);

        if (!$CommitteePatient) {

            return response()->json([
                        'success' => false,
            ]);
        }

        return response()->json([
                    'success' => TRUE,
                    'data' => $CommitteePatient,
        ]);
    }

    public function edit_committeePatient(Request $request) {

        $data = $request->all();
        unset($data['_token']);

        $committeePatientUpdate = CommitteePatient::find($data['id'])->update($data);

        if ($committeePatientUpdate) {

            return response(['success' => true, 'message' => "تم التعديل بنجاح"]);
        }
        return response(['message' => 'fail'], 403);
    }

    public function deleteCommitteePatient(Request $request) {

        $data = $request->all();

        \DB::beginTransaction();
        try {
            $CommitteePatient = CommitteePatient::find($data['id'])->delete();

            if ($CommitteePatient == null) {
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

            $Tabel = CommitteePatient::where('id', '!=', $pk)->where($data)->first();
        } else {

            unset($data['id']);
            $Tabel = CommitteePatient::where($data)->first();
        }
        if ($Tabel == null) {
            return response()->json(['valid' => True]);
        } else {
            return response()->json(['valid' => FALSE]);
        }
    }

}
