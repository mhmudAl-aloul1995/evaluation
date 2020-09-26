<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\placeorph;
use Illuminate\Http\UploadedFile;
use Yajra\Datatables\Datatables;
use File;
use App\place;
use App\status_desc;
use App\needs;

class basicsController extends Controller {

    public function index() {

        return view('basics');
/// $this->creatOrphanFile();
    }

    public function addPlace(Request $request) {

        $data = $request->all();
        unset($data['_token']);

        $this->validate($request, [
            'name' => 'required|max:255',
        ]);
        $data['name'] = trim($data['name']);

        $addPlace = place::firstOrCreate(['name' => $data['name']]);

        if ($addPlace) {
            return response(['success' => true, 'message' => 'تم الإضافة بنجاح']);
        }

        return response(['message' => 'fail'], 403);
    }

    public function getPlace(Request $request) {

        $data = $request->all();


        $users = Place::query()->where('id', '!=', 0)->orderBy('id', 'desc');



        return Datatables::of($users)
                        ->addColumn('action', function($ctr) {

                            return '<div class="btn-group">
                                                        <button  class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> إجراء
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul  class="dropdown-menu" role="menu">
                                                            <li>
                                                                <a onclick="placeModal(' . $ctr->id . ')" href="javascript:;">
                                                                    <i class="icon-pencil"></i> تعديل </a>
                                                            </li>
                                                            <li>
                                                                <a onclick="deletePlace(' . $ctr->id . ')"  href="javascript:;">
                                                                    <i class="icon-trash"></i> حذف  </a>
                                                            </li>
                                                            </ul>
                                                    </div>';
                        })
                        ->rawColumns(['action' => 'action'])
                        ->make(true);
    }

    public function showPlace(Request $request) {

        $data = $request->all();

        unset($data['_token']);

        $Place = Place::find($data['id']);

        if (!$Place) {
            return response(['message' => 'fail'], 403);
        }
        return response(['success' => true, 'place' => $Place]);
    }

    public function editPlace(Request $request) {

        $data = $request->all();
        unset($data['_token']);
        $placeCheck = place::where('id', '!=', $data['id'])->where(['name' => $data['name']])->first();
        if ($placeCheck) {
            return response(['success' => false, 'message' => "الإسم موجود مسبقاً"]);
        }
        $placeUpdate = place::find($data['id'])->update(['name' => $data['name']]);

        if ($placeUpdate) {

            return response(['success' => true, 'message' => "تم التعديل بنجاح"]);
        }
        return response(['message' => 'fail'], 403);
    }

    public function deletePlace(Request $request) {

        $data = $request->all();

        $place_has_child = place::has('patientMedicals')->first();

        if ($place_has_child) {
            return response(['is_delete' => FALSE]);
        }
        $place = place::find($data['id'])->delete();

        if (!$place) {

            return response()->json([
                        'success' => FALSE,
            ]);
        } else {

            return response()->json([
                        'success' => TRUE,
            ]);
        }
    }
  
    /*********************************************/
    
    public function addStatus_desc(Request $request) {

        $data = $request->all();
        unset($data['_token']);

        $this->validate($request, [
            'name' => 'required|max:255',
        ]);
        $data['name'] = trim($data['name']);

        $addStatus_desc = status_desc::firstOrCreate(['name' => $data['name']]);

        if ($addStatus_desc) {
            return response(['success' => true, 'message' => 'تم الإضافة بنجاح']);
        }

        return response(['message' => 'fail'], 403);
    }

    public function getStatus_desc(Request $request) {

        $data = $request->all();


        $users = Status_desc::query()->where('id', '!=', 0)->orderBy('id', 'desc');



        return Datatables::of($users)
                        ->addColumn('action', function($ctr) {

                            return '<div class="btn-group">
                                                        <button  class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> إجراء
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul  class="dropdown-menu" role="menu">
                                                            <li>
                                                                <a onclick="status_descModal(' . $ctr->id . ')" href="javascript:;">
                                                                    <i class="icon-pencil"></i> تعديل </a>
                                                            </li>
                                                            <li>
                                                                <a onclick="deleteStatus_desc(' . $ctr->id . ')"  href="javascript:;">
                                                                    <i class="icon-trash"></i> حذف  </a>
                                                            </li>
                                                            </ul>
                                                    </div>';
                        })
                        ->rawColumns(['action' => 'action'])
                        ->make(true);
    }

    public function showStatus_desc(Request $request) {

        $data = $request->all();

        unset($data['_token']);

        $Status_desc = Status_desc::find($data['id']);

        if (!$Status_desc) {
            return response(['message' => 'fail'], 403);
        }
        return response(['success' => true, 'status_desc' => $Status_desc]);
    }

    public function editStatus_desc(Request $request) {

        $data = $request->all();
        unset($data['_token']);
        $status_descCheck = status_desc::where('id', '!=', $data['id'])->where(['name' => $data['name']])->first();
        if ($status_descCheck) {
            return response(['success' => false, 'message' => "الإسم موجود مسبقاً"]);
        }
        $status_descUpdate = status_desc::find($data['id'])->update(['name' => $data['name']]);

        if ($status_descUpdate) {

            return response(['success' => true, 'message' => "تم التعديل بنجاح"]);
        }
        return response(['message' => 'fail'], 403);
    }

    public function deleteStatus_desc(Request $request) {

        $data = $request->all();

        $status_desc_has_child = status_desc::has('patientMedicals')->first();

        if ($status_desc_has_child) {
            return response(['is_delete' => FALSE]);
        }
        $status_desc = status_desc::find($data['id'])->delete();

        if (!$status_desc) {

            return response()->json([
                        'success' => FALSE,
            ]);
        } else {

            return response()->json([
                        'success' => TRUE,
            ]);
        }
    }

    
    /****************************************************/
        public function addNeeds(Request $request) {

        $data = $request->all();
        unset($data['_token']);

        $this->validate($request, [
            'name' => 'required|max:255',
        ]);
        $data['name'] = trim($data['name']);

        $addNeeds = needs::firstOrCreate(['name' => $data['name']]);

        if ($addNeeds) {
            return response(['success' => true, 'message' => 'تم الإضافة بنجاح']);
        }

        return response(['message' => 'fail'], 403);
    }

    public function getNeeds(Request $request) {

        $data = $request->all();


        $users = Needs::query()->where('id', '!=', 0)->orderBy('id', 'desc');



        return Datatables::of($users)
                        ->addColumn('action', function($ctr) {

                            return '<div class="btn-group">
                                                        <button  class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> إجراء
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul  class="dropdown-menu" role="menu">
                                                            <li>
                                                                <a onclick="needsModal(' . $ctr->id . ')" href="javascript:;">
                                                                    <i class="icon-pencil"></i> تعديل </a>
                                                            </li>
                                                            <li>
                                                                <a onclick="deleteNeeds(' . $ctr->id . ')"  href="javascript:;">
                                                                    <i class="icon-trash"></i> حذف  </a>
                                                            </li>
                                                            </ul>
                                                    </div>';
                        })
                        ->rawColumns(['action' => 'action'])
                        ->make(true);
    }

    public function showNeeds(Request $request) {

        $data = $request->all();

        unset($data['_token']);

        $Needs = Needs::find($data['id']);

        if (!$Needs) {
            return response(['message' => 'fail'], 403);
        }
        return response(['success' => true, 'needs' => $Needs]);
    }

    public function editNeeds(Request $request) {

        $data = $request->all();
        unset($data['_token']);
        $needsCheck = needs::where('id', '!=', $data['id'])->where(['name' => $data['name']])->first();
        if ($needsCheck) {
            return response(['success' => false, 'message' => "الإسم موجود مسبقاً"]);
        }
        $needsUpdate = needs::find($data['id'])->update(['name' => $data['name']]);

        if ($needsUpdate) {

            return response(['success' => true, 'message' => "تم التعديل بنجاح"]);
        }
        return response(['message' => 'fail'], 403);
    }

    public function deleteNeeds(Request $request) {

        $data = $request->all();

        $needs_has_child = needs::has('patientMedicals')->first();

        if ($needs_has_child) {
            return response(['is_delete' => FALSE]);
        }
        $needs = needs::find($data['id'])->delete();

        if (!$needs) {

            return response()->json([
                        'success' => FALSE,
            ]);
        } else {

            return response()->json([
                        'success' => TRUE,
            ]);
        }
    }

}
