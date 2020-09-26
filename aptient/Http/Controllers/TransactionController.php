<?php

//php artisan krlove:generate:model Transactiont --table-name=price

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Enginges\EloquentEngine;
use Illuminate\Support\Facades\DB;
use App\Transaction;
use App\DataTables\TransactionDataTable;

class TransactionController extends Controller {

    public function index(TransactionDataTable $dataTable) {

        return $dataTable->render('transaction');
    }

    public function addTransactiont(Request $request) {


        $data = $request->all();
        unset($data['_token']);
        unset($data['pk_id']);
        $fk_customer = $data['fk_customer'];
        $r_amount_paid = $data['r_amount_paid'];
        $r_recp_no = $data['r_recp_no'];
        $r_statement = $data['r_statement'];
        $r_recp_book_no = $data['r_recp_book_no'];

        for ($i = 0; $i < count($fk_customer); $i++) {

            $prev_balance = Transaction::select("t_balance")->where('pk_id', Transaction::where('fk_customer', $fk_customer[$i])->max('pk_id'))->first()['t_balance'];

            if (!$prev_balance) {
                $prev_balance = 0;
            }
            $addTransaction = new Transaction();
            $addTransaction->fk_customer = $fk_customer[$i];
            $addTransaction->t_credit = $r_amount_paid[$i];
            $addTransaction->t_balance = $prev_balance - $r_amount_paid[$i];
            $addTransaction->save();
            $addTransactiont = new Transactiont;
            $addTransactiont->fk_customer = $fk_customer[$i];
            $addTransactiont->r_statement = $data['r_statement'][$i];
            $addTransactiont->r_recp_no = $r_recp_no[$i];
            $addTransactiont->r_recp_book_no = $r_recp_book_no[$i];
            $addTransactiont->fk_transaction = $addTransaction->pk_id;

            $addTransactiont->save();

            $addTransactiont->pk_id;
        }
        if (!$addTransactiont) {

            return response()->json([
                        'success' => FALSE,
            ]);
        } else {

            return response()->json([
                        'success' => TRUE,
            ]);
        }
    }

    public function showTransactiont(Request $request) {
        $data = $request->all();

        unset($data['_token']);
        $ctr = Transactiont::find($data['pk_id']);

        $transaction = Transaction::find($ctr['fk_transaction'])['t_credit'];

        $Customer = Customer::select("pk_id", "cs_name")
                ->where('pk_id', '=', $ctr['fk_customer'])
                ->first();

        if (!$ctr && !$Customer && !$transaction) {

            return response()->json([
                        'success' => false,
            ]);
        } else {

            return response()->json([
                        'success' => TRUE,
                        'ctr' => $ctr,
                        'customer' => $Customer,
                        'transaction' => $transaction
            ]);
        }
    }

    public function showPreviousMonth(Request $request) {
        $data = $request->all();
        unset($data['_token']);
        $ctr = Transactiont::select("*")->where('pk_id', Transactiont::where('ctr_customer_id', $data['ctr_customer_id'])->max('pk_id'))->first();

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

    public function editTransactiont(Request $request) {

        $data = $request->all();

        unset($data['_token']);

        $prev_balance = Transaction::select("t_balance")->where('pk_id', Transaction::where('fk_customer', $data['fk_customer'])->max('pk_id'))->first()['t_balance'];
        if (!$prev_balance) {

            $prev_balance = 0;
        }


        $editTransactiont = Transactiont::find($data['pk_id']);
        $editTransactiont->fk_customer = $data['fk_customer'][0];
        $editTransactiont->r_statement = $data['r_statement'][0];
        $editTransactiont->r_recp_no = $data['r_recp_no'][0];
        $editTransactiont->r_recp_book_no = $data['r_recp_book_no'][0];

        $editTransactiont->save();

        $editTransaction = Transaction::find($data['t_credit']);
        $increment_value = $data['r_amount_paid'][0] - $editTransaction->t_credit;
        $editTransaction->t_credit = $data['r_amount_paid'][0];
        $editTransaction->save();
        Transaction::where('fk_customer', $data['fk_customer'][0])->where('pk_id', '>=', $data['t_credit'][0])->decrement('t_balance', $increment_value);

        if (!$editTransactiont && $editTransaction) {

            return response()->json([
                        'success' => FALSE,
            ]);
        } else {

            return response()->json([
                        'success' => TRUE,
            ]);
        }
    }

    public function deleteTransactiont(Request $request) {
        $data = $request->all();

        DB::beginTransaction();


        try {

            $get_row_delete = Transaction::find($data['ctr_fk_debit']);
            $update_balance = Transaction::where('fk_customer', $get_row_delete['fk_customer'])->where('pk_id', '>=', $data['ctr_fk_debit'])->increment('t_balance', - $get_row_delete['t_balance']);
            $remove_cntr = Transactiont::find($data['pk_id'])->delete();
            $remove_trans = $get_row_delete->delete();
            DB::commit();
            return response()->json([
                        'success' => TRUE,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                        'success' => FALSE,
            ]);
        }
    }

}
