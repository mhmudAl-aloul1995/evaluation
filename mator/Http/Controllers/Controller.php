<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    public function __construct()
    {
        http_response_code(500);

    }


use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function postEdit(Request $request){
        $data = $request->all();
        unset($data['_token']);
        $mall = TMall::find($data['pk_i_id']);

        $mall->update($data);

        return response()->json([
            'success'=>TRUE,
            'update_mall'=>TRUE,
        ]);

    }
}
