<?php

namespace App\Http\Controllers;

use App\counter_view;
use App\Customer;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Enginges\EloquentEngine;
use App\DataTables\CustomerDataTable;
use App\DataTables\AreaDataTable;
use Illuminate\Support\Facades\DB;
use App\receipt_view;
use App\Transaction;
use App\customers_view;

class CustomerDetailController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id = NULL)
    {
    
        $cus_name = customers_view::find($id);
        return view('customerDetail')->with(['id' => $id, 'cus_name' => $cus_name]);
    }

    public function getCounterDataTables(Request $request)
    {
        $data = $request->all();

        if ($data['from'] && $data['to']) {
            $users = counter_view::query()->where('ctr_customer_id', $request->id)->orderBy('pk_id', 'asc')->whereBetween('ctr_date', [$data['from'], $data['to']]);
        } else {
            $users = counter_view::query()->where('ctr_customer_id', $request->id)->orderBy('pk_id', 'asc');
        }
        return Datatables::of($users)->make(true);
    }

    public function getTransactionDataTables(Request $request)
    {
        $data = $request->all();
        $users = Transaction::query()->where('fk_customer', $request->id)->orderBy('pk_id', 'asc');

        return Datatables::of($users)
            ->addColumn('statment', function ($ctr) {

                if($ctr->t_date!=null)
                {
                    return $ctr->statment.'--'.'إستحقاق تاريخ'.'--'.$ctr->t_date;

                }

                return $ctr->statment;


            })
            ->make(true);
    }

    public function getReceiptDataTables(Request $request)
    {
        $data = $request->all();
        if ($data['from'] && $data['to']) {
            $users = receipt_view::query()->where('fk_customer', $request->id)->orderBy('pk_id', 'asc')->whereBetween('r_date', [$data['from'], $data['to']]);
        } else {
            $users = receipt_view::query()->where('fk_customer', $request->id)->orderBy('pk_id', 'asc');
        }
        return Datatables::of($users)->make(true);
    }

    public function addFinancialMovement(Request $request)
    {

        $data = $request->all();
        $prev_balance = Transaction::select("t_balance")->where('pk_id', Transaction::where('fk_customer', $data['fk_customer'])->max('pk_id'))->first()['t_balance'];
        $statment = [4 => "أجرة كهربائي", 5 => "تسوية", 6 => "رسوم ساعة", 7 => "متأخرات"];
        if (!$prev_balance) {
            $prev_balance = 0;
        }
        $addTransaction = new Transaction();
        if ($data['is_credit'] == 1) {
            $addTransaction->t_debit = $data['value'];
            $addTransaction->t_balance = $prev_balance + $data['value'];
        } else {
            $addTransaction->t_credit = $data['value'];
            $addTransaction->t_balance = $prev_balance - $data['value'];


        }

        $addTransaction->statment = $statment[$data['type']];
        $addTransaction->type = $data['type'];
        $addTransaction->fk_customer = $data['fk_customer'];
        if ($addTransaction->save()) {
            return response()->json([
                'success' => true,
            ]);
        }
        return response()->json([
            'success' => false,
        ]);

    }
}
