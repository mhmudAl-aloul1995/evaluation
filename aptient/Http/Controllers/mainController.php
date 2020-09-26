<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Donor;
use App\Donororph;
use Illuminate\Http\UploadedFile;
use Yajra\Datatables\Datatables;
use File;

class mainController extends Controller {

  public function index() {
        $orphan_load = Orphan::where('is_done', 0)->orderBy('created_at', 'DESC')->limit(5)->get();

       
        return view('main')->withPosts($orphan_load);
    }

    


}
