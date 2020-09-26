<?php

//php artisan krlove:generate:model Receipt --table-name=price

namespace App\Http\Controllers;

use App\Price;
use App\Receipt;
use App\Customer;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Enginges\EloquentEngine;
use App\DataTables\ReceiptDataTable;
use Illuminate\Support\Facades\DB;
use App\Transaction;
use App\receipt_view;

class ReceiptController extends Controller
{

    public function index(ReceiptDataTable $dataTable)
    {
/*        foreach ($cus as $value) {

            $fk_customer = $value->pk_id;
            $r_amount_paid = Transaction::where('fk_customer', $fk_customer)
                ->whereBetween('t_date', ['2018-01-01', '2020-06-01'])->sum('t_debit');
            $prev_balance = Transaction::where('fk_customer', $fk_customer)->orderBy('pk_id', 'desc')->first()['t_balance'];


            if (!$prev_balance) {

                $prev_balance = 0;
            }
            $addTransaction = new Transaction();

            $addTransaction->fk_customer = $fk_customer;

            $addTransaction->statment = "تسوية 2018-01-01 - 2020-06-01";


            $addTransaction->t_credit = $r_amount_paid;

            $addTransaction->t_balance = $prev_balance - $r_amount_paid;
            $addTransaction->type = 0;


            $addTransaction->save();





        }*/

        $cus = Customer::select('pk_id', 'cs_name')->get();
        return $dataTable->render('receipt', ['customers' => $cus]);

    }
        public
        function getReceiptDataTables(Request $request)
        {

            $data = $request->all();


            if ($data['from'] && $data['to']) {

                $users = receipt_view::query()->orderBy('pk_id', 'asc')->whereDate('created_at', '>=', $data['from'])->whereDate('created_at', '<=', $data['to']);
            } else {

                $users = receipt_view::query()->orderBy('pk_id', 'asc');
            }


            return Datatables::of($users)
                ->addColumn('action', function ($ctr) {

                    return '<a onclick="deleteRecipt(' . $ctr->pk_id . ',' . $ctr->fk_transaction . ')" class="btn btn-outline btn-circle dark btn-sm black">

                                               <i class="fa fa-trash-o"></i>   </a>

<a onclick="receiptModal(' . $ctr->pk_id . ')" class="btn btn-outline btn-circle blue btn-sm black">

                                               <i class="fa fa-edit"></i>   </a>';
                })
                ->make(true);
        }

        public
        function addReceipt(Request $request)
        {


            $data = $request->all();

            unset($data['_token']);

            unset($data['pk_id']);

            $fk_customer = $data['fk_customer'];

            $r_amount_paid = $data['r_amount_paid'];

            $r_recp_no = $data['r_recp_no'];


            $r_recp_book_no = $data['r_recp_book_no'];

            $r_date = $data['r_date'];
            \DB::beginTransaction();
            try {

                for ($i = 0; $i < count($fk_customer); $i++) {


                    $prev_balance = Transaction::where('fk_customer', $fk_customer[$i])->orderBy('pk_id', 'desc')->first()['t_balance'];


                    if (!$prev_balance) {

                        $prev_balance = 0;
                    }

                    $addTransaction = new Transaction();


                    $addTransaction->fk_customer = $fk_customer[$i];

                    $addTransaction->statment = "سند قبض" . "رقم السند" . $r_recp_no[$i] . "رقم الدفتر" . $r_recp_book_no[$i];


                    $addTransaction->t_date = $r_date[$i];

                    $addTransaction->t_credit = $r_amount_paid[$i];

                    $addTransaction->t_balance = $prev_balance - $r_amount_paid[$i];
                    $addTransaction->type = 2;


                    $addTransaction->save();

                    $addReceipt = new Receipt;

                    $addReceipt->fk_customer = $fk_customer[$i];

                    $addReceipt->r_statement = "سند قبض  " . " " . "رقم السند " . " " . $r_recp_no[$i] . " " . " رقم الدفتر" . " " . $r_recp_book_no[$i];

                    $addReceipt->r_recp_no = $r_recp_no[$i];

                    $addReceipt->r_recp_book_no = $r_recp_book_no[$i];

                    $addReceipt->r_date = $r_date[$i];

                    $addReceipt->fk_transaction = $addTransaction->pk_id;
                    $addReceipt->r_statement = "سند قبض  " . " " . "رقم السند " . " " . $r_recp_no[$i] . " " . " رقم الدفتر" . " " . $r_recp_book_no[$i];


                    $addReceipt->save();
                }


                \DB::commit();
                return response(['success' => true]);
            } catch (\Exception $e) {
                \DB::rollback();
                return response($e, 403);
                return response(['message' => $e->getMessage()], 403);
            }


        }

        public
        function showReceipt(Request $request)
        {

            $data = $request->all();


            unset($data['_token']);

            $ctr = Receipt::find($data['pk_id']);


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

        public
        function showPreviousMonth(Request $request)
        {

            $data = $request->all();

            unset($data['_token']);

            $ctr = Receipt::select("*")->where('pk_id', Receipt::where('ctr_customer_id', $data['ctr_customer_id'])->max('pk_id'))->first();


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

        public
        function editReceipt(Request $request)
        {


            $data = $request->all();


            unset($data['_token']);


            $editReceipt = Receipt::find($data['pk_id']);

            $editReceipt->fk_customer = $data['fk_customer'][0];

            $editReceipt->r_statement = "سند قبض  " . " " . "رقم السند " . " " . $data['r_recp_no'][0] . " " . " رقم الدفتر" . " " . $data['r_recp_book_no'][0];


            $editReceipt->r_recp_no = $data['r_recp_no'][0];

            $editReceipt->r_recp_book_no = $data['r_recp_book_no'][0];

            $editReceipt->r_date = $data['r_date'][0];


            $editReceipt->save();

            $prev_balance = Transaction::where('fk_customer', $data['fk_customer'])->where('pk_id', '<', $editReceipt->fk_transaction)->orderBy('pk_id', 'desc')->first()['t_balance'];

            if (!$prev_balance) {


                $prev_balance = 0;
            }


            $editTransaction = Transaction::find($editReceipt->fk_transaction);


            $increment_value = $data['r_amount_paid'][0] - $editTransaction->t_credit;
            $editTransaction->t_credit = $data['r_amount_paid'][0];

            $editTransaction->t_date = $data['r_date'][0];
            $editTransaction->type = 2;
            $editTransaction->statment = "سند قبض  " . " " . "رقم السند " . " " . $data['r_recp_no'][0] . " " . " رقم الدفتر" . " " . $data['r_recp_book_no'][0];

            $editTransaction->save();


            Transaction::where('fk_customer', $data['fk_customer'][0])->where('pk_id', '>=', $editReceipt->fk_transaction)->decrement('t_balance', $increment_value);


            if (!$editReceipt && $editTransaction) {


                return response()->json([

                    'success' => FALSE,
                ]);
            } else {


                return response()->json([

                    'success' => TRUE,
                ]);
            }
        }

        public
        function deleteReceipt(Request $request)
        {

            $data = $request->all();


            DB::beginTransaction();


            try {


                $get_row_delete = Transaction::find($data['fk_transaction']);


                $update_balance = Transaction::where('fk_customer', $get_row_delete['fk_customer'])->where('pk_id', '>=', $data['fk_transaction'])->increment('t_balance', $get_row_delete['t_credit']);

                $remove_cntr = Receipt::find($data['pk_id'])->delete();

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
