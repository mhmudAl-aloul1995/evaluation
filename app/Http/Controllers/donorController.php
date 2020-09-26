<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Donor;
use App\Donororph;
use Illuminate\Http\UploadedFile;
use Yajra\Datatables\Datatables;
use File;

class donorController extends Controller {

    public function index() {

        return view('donor');
        /// $this->creatOrphanFile();
    }

    public function donor_file(Request $request) {

        $data = $request->all();
        unset($data['_token']);


        $addDonor=  Donor::create($data);

        if ($addDonor) {
            return response()->json([
                        'success' => true,
            ]);
        }
        return response()->json([
                    'success' => FALSE,
        ]);
    }

    public function getDonor(Request $request) {

        $data = $request->all();


        $users = Donor::query()->where('pk_id','!=',0)->orderBy('pk_id', 'desc');



        return Datatables::of($users)
                 ->editColumn('donor_name', function($ctr) {
                            return "<a target='_blank' href=' " . url('donorOrphans') . '/' . $ctr->pk_id . "'>{$ctr->donor_name}</a> ";
                        })
                        ->addColumn('action', function($ctr) {

                            return '<div class="btn-group">
                                                        <button  class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> إجراء
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul  class="dropdown-menu" role="menu">
                                                            <li>
                                                                <a onclick="donorModal(' . $ctr->pk_id . ')" href="javascript:;">
                                                                    <i class="icon-pencil"></i> تعديل </a>
                                                            </li>
                                                            <li>
                                                                <a onclick="deletedonor(' . $ctr->pk_id . ')"  href="javascript:;">
                                                                    <i class="icon-trash"></i> حذف  </a>
                                                            </li>
                                                            </ul>
                                                    </div>';
                        })
                                       ->rawColumns(['action' => 'action', 'donors' => 'donors', 'donor_name' => 'donor_name'])

                        ->make(true);
    }

    public function showDonor(Request $request) {

        $data = $request->all();

        unset($data['_token']);

        $Donor = Donor::find($data['pk_id']);

        if (!$Donor ) {

            return response()->json([

                        'success' => false,
            ]);
        }

        return response()->json([

                    'success' => TRUE,
                    'donor' => $Donor,
        ]);
    }

    public function editDonor(Request $request) {

        $data = $request->all();
        unset($data['_token']);

        $donorUpdate=Donor::where('pk_id',$data['pk_id'])->update($data);

        if ($donorUpdate) {
            return response()->json([
                        'success' => true,
            ]);
        }
        return response()->json([
                    'success' => FALSE,
        ]);
    }

    public function deleteDonor(Request $request) {

        $data = $request->all();

        $Donor = Donor::find($data['pk_id'])->delete();

        if (!$Donor) {

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
