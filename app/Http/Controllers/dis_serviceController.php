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
use App\Dis_service;
use App\patient_personal;
use Excel;
use App\City;
use App\Area;
use DateTime;

class dis_serviceController extends Controller {

    public function index($donor_id = null) {

        return view('dis_service');
    }

    public function service_excel(Request $request) {
        $this->validate($request, [
            'name' => 'required'
        ]);
        set_time_limit(0);
        if (!$request->hasFile('name')) {
            return response(['message' => 'fail'], 403);
        }

        $extension = File::extension($request->name->getClientOriginalName());
        if ($extension !== "xlsx") {
            return response(['message' => 'fail'], 403);
        }

        \DB::beginTransaction();
        try {
            $excel = [];
            Excel::load($request->name->getRealPath(), function($reader) use (&$excel) {
                $objExcel = $reader->getExcel();
                $sheet = $objExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                for ($row = 0; $row <= $highestRow; $row++) {
                    $excel[] = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE)[0];
                }
            });

            for ($counter = 0; $counter < sizeof($excel); $counter++) {
                $row = $excel[$counter];

                $wepon = ['' => 6, 'رصاص متفجر' => 1, 'رصاص حي' => 2, 'رصاص مطاطي' => 3, 'قنبلة غاز' => 4, 'شظايا' => 5, 'أخرى' => 6, 'أخري' => 6];
                $sex = ['أنثى' => 1, 'ذكر' => 2, 'انثى' => 1, '' => 0, 'غير ذلك' => 0];
                $social_situt = ['اعزب' => 1, 'أعزب' => 1, 'متزوج' => 2, 'أرمل' => 3, 'مطلق' => 4, '' => 0];
                $qualification = ['إبتدائي' => 1, 'ابتدائي' => 1, 'إعدادي' => 2, 'ثانوي' => 3, 'جامعي' => 4, 'أخرى' => 5, 'غير ذلك' => 5, '' => 0];
                $economic_situt = ['موظف' => 1, 'غير موظف' => 2, '' => 0, 'غير ذلك' => 0];

                if (is_numeric($row[1])) {

                    if (trim($row[2]) == null) {
                        $row[2] = '--';
                    }
                    if (trim($row[3]) == null) {
                        $row[3] = '--';
                    }
                    $city = City::where(["city_name" => $row[2]])->first();
                    $area = Area::where(["area_name" => $row[3]])->first();

                    if (!$city) {
                        $city = City::create(["city_name" => $row[2]]);
                    }
                    if (!$area) {
                        $area = Area::create(["area_name" => $row[3]]);
                    }
                    $row[6] = strtr($row[6], '/', '-');

                    $row[6] = date('Y-m-d', strtotime($row[6]));


                    $patient = patient_personal::updateOrCreate(['identity' => trim($row[1]),], [
                                "name" => trim($row[0]),
                                'identity' => trim($row[1]),
                                "city_id" => $city->id,
                                "area_id" => $area->id,
                                'created_by' => 1,
                    ]);




                    $service = Service::firstOrCreate(["service_name" => trim($row[5])]);




                    $dis_service = Dis_service::updateOrCreate(['service_id' => $service->id, "service_date" => trim($row[6]), "personal_id" => $patient->id,], [
                                'service_id' => $service->id,
                                "service_date" => trim($row[6]),
                                "value" => trim($row[4]),
                                "personal_id" => $patient->id,
                                'created_by' => 1,
                    ]);
                }
            }
            \DB::commit();
            return response(['message' => 'تم بنجاح', 'success' => true]);
        } catch (\Exception $e) {
            \DB::rollback();
            return response(['message' => $e->getMessage()], 403);
        }

        return response(['message' => 'fail'], 403);
    }

    public function getDis_service(Request $request) {

        $data = $request->all();

        foreach ($data as $key => $value) {

            if ($value == '' || $key == 'columns' || $key == '_token' || $key == 'order' || $key == 'search' || $key == 'length' || $key == 'start' || $key == 'draw' || $key == '_') {
                unset($data[$key]);
            }
        }

        $users = Dis_service::query()->leftJoin('patient_personal', function($join) {
                    $join->on('dis_service.personal_id', '=', 'patient_personal.id');
                })->leftJoin('services', function($join) {
                    $join->on('dis_service.service_id', '=', 'services.id');
                })->select('dis_service.*', 'services.service_name as s_name', 'patient_personal.name as p_name', 'patient_personal.identity');

        $users->WhereHas('PatientPersonal', function ($medical) use($data) {

            if (isset($data['identity'])) {
                $medical->where("identity", $data['identity']);
                unset($data['identity']);
            }

            if (isset($data['city'])) {
                $medical->where("city_id", $data['city']);
                unset($data['city']);
            }

            if (isset($data['area'])) {

                $medical->where("area_id", $data['area']);
                unset($data['area']);
            }

            if (isset($data['economic_situt'])) {
                $medical->where("economic_situt", $data['economic_situt']);
                unset($data['economic_situt']);
            }
        });

        if ($data) {
            $users->where($data);
        }
        return Datatables::of($users)
                        ->editColumn('place', function($ctr) {
                            $city = ['', 'البريد', 'البنك', 'مكتب الحركة'];

                            return $city[$ctr->place];
                        })
                        ->addColumn('action', function($ctr) {

                            return '<div class="btn-group">
                                                        <button  class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> إجراء
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul  class="dropdown-menu" role="menu">
                                                           <li>
                                                                <a onclick="dis_serviceModal(' . $ctr->id . ')" href="javascript:;">
                                                                    <i class="icon-pencil"></i> تعديل </a>
                                                            </li>
                                                            <li>
                                                                <a onclick="deleteService(' . $ctr->id . ')"  href="javascript:;">
                                                                    <i class="icon-trash"></i> حذف  </a>
                                                            </li>
                                                            </ul>
                                                    </div>';
                        })
                        ->rawColumns(['action' => 'action', 'city' => 'city', 'name' => 'name'])
                        ->make();
    }

    public function showDis_service(Request $request) {

        $data = $request->all();

        unset($data['_token']);

        $Service = Dis_service::find($data['id']);

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

    public function edit_dis_service(Request $request) {

        $data = $request->all();
        unset($data['_token']);

        $serviceUpdate = Dis_service::find($data['id'])->update($data);

        if ($serviceUpdate) {

            return response(['success' => true, 'message' => "تم التعديل بنجاح"]);
        }
        return response(['message' => 'fail'], 403);
    }

    public function deleteDis_service(Request $request) {

        $data = $request->all();

        \DB::beginTransaction();
        try {
            $Service = Dis_service::find($data['id'])->delete();

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

    public function add_dis_service(Request $request) {

        $data = $request->all();
        unset($data['_token']);
        $data['created_by'] = 1;
        $serviceadd = Dis_service::create($data);

        if ($serviceadd) {

            return response(['success' => true, 'message' => "تمت الإضافة"]);
        }
        return response(['message' => 'fail'], 403);
    }

}
