<?php

//php artisan krlove:generate:model Billing --table-name=price

namespace App\Http\Controllers;

use App\Price;
use App\Billing;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Enginges\EloquentEngine;
use App\DataTables\BillingDataTable;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller {

    public function __construct() {
        $categories = Billing::all();
        return view('billing');
    }

    public function show(BillingDataTable $dataTable) {

        return $dataTable->render('Billing');
    }

    public function addPrice(Request $request) {

        $data = $request->all();
        unset($data['_token']);
        $data['is_enable'] = 1;
        $edit_enabling = Price::where("fk_ctg", $data['fk_ctg'])->update(['is_enable' => 0]);
        $add_ctg = Price::insert($data);

        if ($add_ctg && $edit_enabling) {
            return response()->json([
                        'success' => TRUE,
            ]);
        } else {
            return response()->json([
                        'success' => FALSE,
            ]);
        }
    }

    public function setEnablePrice(Request $request) {

        $data = $request->all();
        unset($data['_token']);

        $edit_enabling = Price::where("fk_ctg", $data['fk_ctg'])->update(['is_enable' => 0]);
        $set_enabling = Price::where("pk_id", $data['pk_id'])->update(['is_enable' => 1]);

        if ($edit_enabling && $set_enabling) {
            return response()->json([
                        'success' => TRUE,
            ]);
        } else {
            return response()->json([
                        'success' => FALSE,
            ]);
        }
    }

    public function pricesDetail(Request $request) {

        $data = $request->all();
        unset($data['_token']);

        $prices = Price::where($data)->orderBy('pk_id', 'desc')->get();

        if ($prices) {
            return response()->json([
                        'success' => TRUE,
                        'page' => view('priceDetail')->with(['price' => $prices, 'fk_ctg' => $data['fk_ctg']])->render()
            ]);
        }
    }

    public function addBilling(Request $request) {

        $data = $request->all();
        $ctg_id = Billing::max('pk_id');
        foreach ($data['group-b'] as $value) {
            $value['ctg_total'] = $value['ctg_quantity'] * $value['ctg_purch_price'];
            $price['price'] = $value['ctg_purch_price'];
            $price['is_enable'] = 1;

            $add_ctg = Billing::insert($value);
            if ($add_ctg) {
                $price['fk_ctg'] = Billing::max('pk_id');
                $add_price = Price::insert($price);
            }
        }
        if ($add_ctg) {
            return response()->json([
                        'success' => TRUE,
            ]);
        } else {
            return response()->json([
                        'success' => FALSE,
            ]);
        }
    }

    public function showBilling(Request $request) {
        $data = $request->all();

        unset($data['_token']);
        $ctmr = Billing::find($data['pk_id']);

        return response()->json([
                    'success' => TRUE,
                    'ctg_data' => $ctmr
        ]);
    }

    public function editBilling(Request $request) {
        $data = $request->all();

        unset($data['_token']);

        $ctmr = Billing::find($data['pk_id']);
        foreach ($data['group-b'] as $value) {
            $edit_ctg = $ctmr->update($value);
        }
        if ($edit_ctg) {
            return response()->json([
                        'success' => TRUE,
            ]);
        } else {
            return response()->json([
                        'success' => false,
            ]);
        }
    }

    public function destroy(Request $request) {
        $data = $request->all();

        $remove_cstmr = Billing::find($data['pk_id'])->delete();
        if (!$remove_cstmr) {

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
