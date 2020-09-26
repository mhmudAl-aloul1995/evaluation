<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Donor;
use App\Donororph;
use Illuminate\Http\UploadedFile;
use Yajra\Datatables\Datatables;
use File;
use Illuminate\Support\Facades\DB;
use App\Service;

class serviceController extends Controller {

    public function index($donor_id = null) {

        return view('service');
    }

    public function add_service(Request $request) {

        $data = $request->all();


        $data['created_by'] = 1;

        $check_service = Service::where(['name' => $data['name']])->first();
        if ($check_service) {
            return response(['success' => false, 'message' => 'هذه الخدمة مكررة']);
        }
        $add_service = Service::create($data);
        if ($add_service) {
            return response(['success' => true, 'message' => 'تم الإضافة بنجاح']);
        }


        return response(['message' => 'fail'], 403);
    }

    public function getService(Request $request) {

        $data = $request->all();

        foreach ($data as $key => $value) {

            if ($value == '' || $key == 'columns' || $key == '_token' || $key == 'order' || $key == 'search' || $key == 'length' || $key == 'start' || $key == 'draw' || $key == '_') {
                unset($data[$key]);
            }
        }
        $users = Service::query()->get();




        return Datatables::of($users)
                        ->addColumn('action', function($ctr) {

                            return '<div class="btn-group">
                                                        <button  class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> إجراء
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul  class="dropdown-menu" role="menu">
                                                            <li>
                                                                <a onclick="servicePatientModal(' . $ctr->id . ')" href="javascript:;">
                                                                    <i class="icon-pencil"></i> تعديل </a>
                                                            </li>
                                                            <li>
                                                                <a onclick="deleteService(' . $ctr->id . ')"  href="javascript:;">
                                                                    <i class="icon-trash"></i> حذف  </a>
                                                            </li>
                                                            </ul>
                                                    </div>';
                        })
                        ->rawColumns([ 'action' => 'action', 'city' => 'city', 'name' => 'name'])
                        ->make(true);
    }

    public function showService(Request $request) {

        $data = $request->all();

        unset($data['_token']);

        $Service = Service::find($data['pk_id']);

        if (!$Service) {

            return response()->json([

                        'success' => false,
            ]);
        }

        return response()->json([

                    'success' => TRUE,
                    'patient_service' => $Service,
        ]);
    }

    public function edit_service(Request $request) {

        $data = $request->all();
        unset($data['_token']);

        $serviceUpdate = Service::find($data['id'])->update($data);

        if ($serviceUpdate) {

            return response(['success' => true, 'message' => "تم التعديل بنجاح"]);
        }
        return response(['message' => 'fail'], 403);
    }

    public function deleteService(Request $request) {

        $data = $request->all();

        \DB::beginTransaction();
        try {
            $Service = Service::find($data['id'])->delete();

            if ($Service == null) {
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

            $Tabel = Service::where('id', '!=', $pk)->where($data)->first();
        } else {

            unset($data['id']);
            $Tabel = Service::where($data)->first();
        }
        if ($Tabel == null) {
            return response()->json(['valid' => True]);
        } else {
            return response()->json(['valid' => FALSE]);
        }
    }

}
