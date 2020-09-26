<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\patient_medical;
use App\Donor;
use App\Donororph;
use Illuminate\Http\UploadedFile;
use Yajra\Datatables\Datatables;
use File;
use Illuminate\Support\Facades\DB;

class medicalController extends Controller {

    public function index($donor_id = null) {

        return view('medical');
    }

    public function add_medical(Request $request) {

        $data = $request->all(); {

            if (!isset($data['place_id'])) {
                $data['place_id'] = 0;
            }

            if (!isset($data['need_id'])) {
                $data['need_id'] = 0;
            }
            $data['created_by'] = 1;

            $check_medical = patient_medical::where(['personal_id' => $data['personal_id'], 'date' => $data['date'],])->first();
            if ($check_medical) {
                return response(['success' => false, 'message' => 'هذا الجريح لديه بيانات طبية مسبقة في نفس التاريخ']);
            }
            $addMedical_medical = patient_medical::create($data);
            if ($addMedical_medical) {
                return response(['success' => true, 'message' => 'تم الإضافة بنجاح']);
            }
        }

        return response(['message' => 'fail'], 403);
    }

    public function getMedical(Request $request) {

        $data = $request->all();

        foreach ($data as $key => $value) {

            if ($value == '' || $key == 'columns' || $key == '_token' || $key == 'order' || $key == 'search' || $key == 'length' || $key == 'start' || $key == 'draw' || $key == '_') {
                unset($data[$key]);
            }
        }
        $users = patient_medical::query()->leftJoin('patient_personal', function($join) {
                            $join->on('patient_medical.personal_id', '=', 'patient_personal.id');
                        })
                        ->leftJoin('status_desc', function($join) {
                            $join->on('patient_medical.status_desc_id', '=', 'status_desc.id');
                        })
                        ->leftJoin('weapons', function($join) {
                            $join->on('weapons.id', '=', 'patient_medical.weapon_id');
                        })
                        ->leftJoin('place', function($join) {
                            $join->on('patient_medical.place_id', '=', 'place.id');
                        })->select('weapons.weapon_name as weapon_name','patient_medical.*', 'patient_personal.name as patient_name', 'status_desc_name', 'place_name');



        $users->WhereHas('patientPersonal', function ($medical) use($data) {

            if (isset($data['identity'])) {
                $medical->where("identity", $data['identity']);
                unset($data['identity']);
            }

            if (isset($data['city'])) {
                $medical->where("city", $data['city']);
                unset($data['city']);
            }

            if (isset($data['area'])) {

                $medical->where("area", $data['area']);
                unset($data['area']);
            }

            if (isset($data['economic_situt'])) {
                $medical->where("economic_situt", $data['economic_situt']);
                unset($data['economic_situt']);
            }
        });

        $users->where($data);


        return Datatables::of($users)
                        ->addColumn('action', function($ctr) {

                            return '<div class="btn-group">
                                                        <button  class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> إجراء
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul  class="dropdown-menu" role="menu">
                                                            <li>
                                                                <a onclick="medicalPatientModal(' . $ctr->id . ')" href="javascript:;">
                                                                    <i class="icon-pencil"></i> تعديل </a>
                                                            </li>
                                                            <li>
                                                                <a onclick="deletepatient_medical(' . $ctr->id . ')"  href="javascript:;">
                                                                    <i class="icon-trash"></i> حذف  </a>
                                                            </li>
                                                            </ul>
                                                    </div>';
                        })
                        ->rawColumns([ 'action' => 'action', 'city' => 'city', 'name' => 'name'])
                        ->make(true);
    }

    public function showMedical(Request $request) {

        $data = $request->all();

        unset($data['_token']);

        $patient_medical = patient_medical::find($data['pk_id']);

        if (!$patient_medical) {

            return response()->json([

                        'success' => false,
            ]);
        }

        return response()->json([

                    'success' => TRUE,
                    'patient_medical' => $patient_medical,
        ]);
    }

    public function edit_medical(Request $request) {

        $data = $request->all();
        unset($data['_token']);

        $medicalUpdate = patient_medical::find($data['id'])->update($data);

        if ($medicalUpdate) {

            return response(['success' => true, 'message' => "تم التعديل بنجاح"]);
        }
        return response(['message' => 'fail'], 403);
    }

    public function deleteMedical(Request $request) {

        $data = $request->all();

        \DB::beginTransaction();
        try {
            $patient_medical = patient_medical::find($data['id'])->delete();

            if ($patient_medical == null) {
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

            $Tabel = patient_medical::where('id', '!=', $pk)->where($data)->first();
        } else {

            unset($data['id']);
            $Tabel = patient_medical::where($data)->first();
        }
        if ($Tabel == null) {
            return response()->json(['valid' => True]);
        } else {
            return response()->json(['valid' => FALSE]);
        }
    }

}
