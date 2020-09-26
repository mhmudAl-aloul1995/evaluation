<?php

//php artisan krlove:generate:model Counter --table-name=price

namespace App\Http\Controllers;

use App\Price;
use App\Counter;
use App\Customer;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Enginges\EloquentEngine;
use App\DataTables\CounterDataTable;
use Illuminate\Support\Facades\DB;
use App\Transaction;
use App\counter_view;
use App\Receipt;
use App\Messages_sent;
use App;
use App\Area;

class CounterQuickController extends Controller {

    public function index() {



        return view('counterQuick')->with([]);
    }

    


    public function addCounterQuickly($data = null, $fk_customer = null, $date = NULL, $current = NULL) {

        $debit = ($current - $data['ctr_current']) * $data['ctr_price'];
        if ($debit < $data['ctr_minimum']) {
            $debit = $data['ctr_minimum'];
        }
        $prev_balance = Transaction::where('fk_customer', $fk_customer)->orderBy('pk_id', 'desc')->first();

        if (empty($prev_balance->t_balance)) {
            $prev_balance = 0;
        } else {
            $prev_balance = $prev_balance->t_balance;
        }

        $addTransaction = new Transaction();
        $addTransaction->fk_customer = $fk_customer;
        $addTransaction->t_debit = $debit;
        $addTransaction->t_balance = $prev_balance + $debit;
        $addTransaction->t_date = $date;
        $addTransaction->statment = $data['ctr_previous'] . 'ـــ' . $current;


        $addTransaction->save();

        $addCounter = new Counter();
        $addCounter->ctr_date = $date;
        $addCounter->ctr_previous = $data['ctr_current'];
        $addCounter->ctr_current = $current;
        $addCounter->ctr_price = $data['ctr_price'];
        $addCounter->ctr_ampair = $data['ctr_ampair'];
        $addCounter->ctr_minimum = $data['ctr_minimum'];
        $addCounter->ctr_customer_id = $fk_customer;
        $addCounter->ctr_fk_debit = $addTransaction->pk_id;
        $addCounter->save();


        if (!empty($addCounter)) {

            return true;
        }

        return false;
    }

    public function checkDate(Request $request) {
        $data = $request->all();

        unset($data['_token']);
        if ($data['counter_pk_id'] > 0) {
            $check_counter = counter_view::where('pk_id', '!=', $data['counter_pk_id'])->where('ctr_date', $data['ctr_date'])->where('ctr_customer_id', $data['ctr_customer_id'])->first();
        } else {


            $check_counter = counter_view::where('ctr_date', $data['ctr_date'])->where('ctr_customer_id', $data['ctr_customer_id'])->first();
        }

        if (!$check_counter) {
            return response()->json([
                'valid' => true,
            ]);
        } else {
            return response()->json([
                'valid' => false,
            ]);
        }
    }

    public function setCurrent(Request $request) {
        $data = $request->all();

        $check_counter = counter_view::whereDate('ctr_date', $data['date'])->where('ctr_customer_id', $data['fk_customer'])->first();

        if ($check_counter) {
            return $this->editCounterQuickly($data['date'], $data['fk_customer'], $data['value']);
        }

        $date = date("y-m-d", strtotime('-1 month', strtotime($data['date'])));


        $previous_month = $this->showPreviousMonthQuickly($data['fk_customer'], $date);


        if (empty($previous_month)) {
            return response()->json([
                'success' => false,
            ]);
        }

        if ($this->addCounterQuickly($previous_month, $data['fk_customer'], $data['date'], $data['value'])) {

            return response()->json([
                'success' => TRUE,
                'is_done' => 'done'
            ]);
        }

        return response()->json([
            'success' => false,
        ]);
    }


    public function showPreviousMonthQuickly($data, $date) {


        $ctr = counter_view::whereDate('ctr_date', $date)->where('ctr_customer_id', $data)->first();
        if ($ctr) {

            return $ctr;
        }

        return null;
    }

    public function showPreviousMonth(Request $request) {
        $data = $request->all();
        unset($data['_token']);
        $ctr = Counter::select("*")->where('pk_id', Counter::where('ctr_customer_id', $data['ctr_customer_id'])->max('pk_id'))->first();

        if (!$ctr) {

            return response()->json([
                'success' => false,
            ]);
        } else {

            return response()->json([
                'success' => TRUE,
                'ctr' => $ctr
            ]);
        }
    }

    public function editCounterQuickly($date, $customer, $current) {


        $editCounter = Counter::whereDate('ctr_date', $date)->where('ctr_customer_id', $customer)->first();
        $editCounter->ctr_current = $current;


        $editCounter->save();
        $debit = ($editCounter->ctr_current - $editCounter->ctr_previous) * $editCounter->ctr_price;
        if ($debit < $editCounter->ctr_minimum) {
            $debit = $editCounter->ctr_minimum;
        }

        $prev_balance = Transaction::where('fk_customer', $editCounter->ctr_customer_id)->where('pk_id', '<', $editCounter->ctr_fk_debit)->orderBy('pk_id', 'desc')->first()['t_balance'];
        /*   var_dump($prev_balance);
          exit(); */

        if (!$prev_balance) {

            $prev_balance = 0;
        }


        $editTransaction = Transaction::find($editCounter->ctr_fk_debit);
        if ($editTransaction) {
            $increment_value = $debit - $editTransaction->t_debit;
            $editTransaction->t_debit = $debit;
            $editTransaction->statment = $editCounter->ctr_previous . 'ـــ' . $editCounter->ctr_current;
            $editTransaction->t_date = $editCounter->ctr_date;

            $editTransaction->save();


            Transaction::where('fk_customer', $editCounter->ctr_customer_id)->where('pk_id', '>=', $editCounter->ctr_fk_debit)->increment('t_balance', $increment_value);
        }
        if (!$editCounter && $editTransaction) {

            return response()->json([
                'success' => FALSE,
            ]);
        } else {

            return response()->json([
                'success' => TRUE,
                'is_done' => 'done'
            ]);
        }
    }



}
