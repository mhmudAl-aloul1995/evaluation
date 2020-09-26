<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Donor;
use App\Orphan;
use App\Donororph;
use Illuminate\Http\UploadedFile;
use Yajra\Datatables\Datatables;
use File;
use DB;

class indexController extends Controller {

  public function index() {
   
/*
    $donors=Donor::leftJoin('donororphs', 'donors.pk_id', '=', 'donororphs.donor_id')
    ->select("donors.donor_name",DB::raw('count(donororphs.orphan_id) as orph_no'))
    ->groupBy('donors.pk_id','donors.donor_name')->get();
*/
/*
        $orphan_load = Orphan::where('is_done', 0)->orderBy('created_at', 'DESC')->limit(5)->get();
        $orphan_no=Orphan::count();*/
       
        return view('index');
    }

    


}
