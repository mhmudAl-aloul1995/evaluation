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
use Excel;

class CounterController extends Controller
{
  
    public function index()
    {
        $data = counter_view::whereDate('ctr_date', '2020-1-1')->get()->toArray();


        $area = Area::select('pk_id', 'area_name')->get();
        $cus = Customer::select('pk_id', 'cs_name')->get();


        return view('counter')->with(['area' => $area, 'customers' => $cus]);
    }

    public static function sendsms($customers, $msg)

    {

        set_time_limit(10000);
        $USER_NAME = "mator.mash";
        $PASSWORD = "1844404";
        $SENDER = "mator.mash";
        $resp = null;
        $is_sent = null;

        if ($customers->count() == 0) {
            return response()->json(['no_row' => true]);
        }


        foreach ($customers as $customer) {
            $NUMBER = $customer->cs_mobile;

            $date = date("m", strtotime('-1 month', strtotime($customer->ctr_date)));

            if (strlen($NUMBER) == 7) {
                $NUMBER = '059' . $NUMBER;
            }
            if (strlen($NUMBER) == 10) {

                if (trim($msg) == null) {
                    $arrears = Transaction::where(["fk_customer" => $customer->ctr_customer_id])->where('pk_id', Transaction::where('fk_customer', $customer->ctr_customer_id)->max("pk_id"))->first()['t_balance'] - $customer->money_qy;

                    if ($arrears < 0) {
                        $arrears = 0;
                    }
                    $total = $arrears + $customer->money_qy;

                    if ($arrears > 0) {
                        $MESSAGE = "المبلغ المستحق(" . $total . ")" . "ش " . "حساب شهر" . (int)$date . "({$customer->money_qy})ش" . "(سابق {$customer->ctr_previous}/حالي {$customer->ctr_current})" . "حساب متراكم ({$arrears})ش";

                    } else {
                        $MESSAGE = "حساب شهر" . (int)$date . "({$customer->money_qy})ش" . "(سابق {$customer->ctr_previous}/حالي {$customer->ctr_current})";

                    }

                } else {
                    $MESSAGE = trim($msg);

                }
                $MESSAGE = urlencode($MESSAGE);
                $url = "http://www.alqudwasms.com/api.php?comm=sendsms&user=" . $USER_NAME . "&pass=" . $PASSWORD . "&to=" . $NUMBER . "&message=" . $MESSAGE . "&sender=" . $SENDER;

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_URL => $url
                ));
                $resp = curl_exec($curl);
                curl_close($curl);


                if (($resp != -100) && ($resp != -115) && ($resp != -113) && ($resp != -110) && ($resp != -116)) {
                    $is_sent = Messages_sent::create(['counter_date' => $customer->ctr_date, 'messages' => urldecode($MESSAGE), "fk_customer" => $customer->ctr_customer_id]);
                } else {
                    return response()->json(['status' => $resp]);
                }
            }

        }

        if ($is_sent) {
            return response()->json(['success' => true]);
        }
        return response()->json(['status' => $resp]);
    }

    public function getCounterDataTables(Request $request)
    {
        $data = $request->all();

        $users = counter_view::query()->orderBy('cus_index', 'asc');
        if ($data['from'] && $data['to'] && $data['cs_fk_area']) {
            $users->whereBetween('ctr_date', [$data['from'], $data['to']])->where('cs_fk_area', $data['cs_fk_area']);
        } elseif ($data['from'] && $data['to']) {
            $users->whereBetween('ctr_date', [$data['from'], $data['to']]);
        } elseif ($data['cs_fk_area']) {
            $users->whereIn('cs_fk_area', $data['cs_fk_area']);
        }



  
        return Datatables::of($users)
  
            ->addColumn('action', function($ctr) {

                return '<div  class="btn-group">
                                                        <button  class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> إجراء
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul  class="dropdown-menu" role="menu">
                                                            <li>
                                                                <a onclick="counterModal(' . $ctr->pk_id . ')" href="javascript:;">
                                                                    <i class="icon-pencil"></i> تعديل </a>
                                                            </li>
                                                     
                                                            <li>
                                                                <a onclick="deleteCounter(' . $ctr->pk_id . ',' . $ctr->ctr_fk_debit . ',' . $ctr->fk_transaction_discount . ')"  href="javascript:;">
                                                                    <i class="icon-trash"></i> حذف  </a>
                                                            </li>
                                                            </ul>
                                                    </div>';
            })
            /*  ->addColumn('cs_name', function ($ctr) {
                  return '<a href="/customerDetail/' . $ctr->ctr_customer_id . '" > '.$ctr->cs_name.' </a>';

              })*/ ->addColumn('paid', function ($ctr) {
                $paid = Transaction::where(['type' => 2, "fk_customer" => $ctr->ctr_customer_id, "t_date" => $ctr->ctr_date])->sum("t_credit");
                return $paid;
            })
            ->addColumn('residuum', function ($ctr) {


                $paid = Transaction::where(['type' => 2, "fk_customer" => $ctr->ctr_customer_id, "t_date" => $ctr->ctr_date])->sum("t_credit");

                return $ctr->money_qy - ($paid + $ctr->discount);
            })
            ->addColumn('r_recp_no', function ($ctr) {


                $paid = Receipt::where(["fk_customer" => $ctr->ctr_customer_id, "r_date" => $ctr->ctr_date])->first()['r_recp_no'];

                return $paid;
            })
            ->addColumn('r_recp_book_no', function ($ctr) {


                $paid = Receipt::where(["fk_customer" => $ctr->ctr_customer_id, "r_date" => $ctr->ctr_date])->first()['r_recp_book_no'];

                return $paid;
            })
         /*   ->editColumn('cus_index', function ($ctr) {


                return Area::find($ctr->cs_fk_area)['area_name'];

            })*/
            ->addColumn('arrears', function ($ctr) {
                $paid = Transaction::where(['type' => 2, "fk_customer" => $ctr->ctr_customer_id, "t_date" => $ctr->ctr_date])->sum("t_credit");
                $arrears = Transaction::where(["fk_customer" => $ctr->ctr_customer_id])->orderBy('pk_id', 'desc')
                        ->first()['t_balance'] - $ctr->money_qy + $paid;

                if ($arrears < 0) {
                    $arrears = 0;
                }
                return $arrears;
            })
          ->addColumn('total', function ($ctr) {
                $total = Transaction::where([ "fk_customer" => $ctr->ctr_customer_id])->orderBy('pk_id','desc')->first()['t_balance'];

                return $total;
            })
            ->setRowClass(function ($ctr) {
                $credit = Transaction::where(['type' => 2, "fk_customer" => $ctr->ctr_customer_id, "t_date" => $ctr->ctr_date])->sum("t_credit");


                if ($ctr->p_enabled == 0) {
                    return 'active';
                } elseif ($credit + $ctr->discount == $ctr->money_qy && $ctr->money_qy != 0) {
                    return 'success';
                } elseif ($credit == 0) {
                    return 'danger';
                } elseif ($credit < $ctr->money_qy) {
                    return 'warning';
                }
            })
            ->setRowId(function ($ctr) {
                return "counters_" . $ctr->ctr_customer_id;
            })
            ->make(true);
    }

    public static function exportexcel()
    {

        $data = counter_view::get()->toArray();
        return Excel::create('itsolutionstuff_example', function ($excel) use ($data) {
            $excel->sheet('mySheet', function ($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        })->download();
    }

    public function getCustomersDataTables(Request $request)
    {
        $data = $request->all();
        $date = date("y-m-d", strtotime('-1 month', strtotime($data['dateQuic'])));

        $users = counter_view::query()->whereDate('ctr_date', $date)->orderBy('cus_index', 'asc');

        $dateQui = $data['dateQuic'];
        return Datatables::of($users)
            ->editColumn('action', function ($user) use ($dateQui) {

                return Counter::where('ctr_customer_id', $user->ctr_customer_id)->whereDate('ctr_date', $dateQui)->first()['ctr_current'];
            })
            ->setRowId(function ($user) {
                return "counter_" . $user->ctr_customer_id;
            })
            ->make(true);
    }

    public function getCounterMessagesDataTables(Request $request)
    {
        $users = Messages_sent::leftjoin('customers', 'messages_sent.fk_customer', '=', 'customers.pk_id')
            ->select('messages_sent.counter_date', 'messages_sent.messages', 'messages_sent.created_at', 'customers.cs_name')->orderBy("messages_sent.pk_id", "desc");
        return Datatables::of($users)->make(true);
    }

    public function addCounter(Request $request)
    {

        $data = $request->all();
        unset($data['_token']);
        unset($data['pk_id']);
        $debit = ($data['ctr_current'] - $data['ctr_previous']) * $data['ctr_price'];
        if ($debit < $data['ctr_minimum']) {
            $debit = $data['ctr_minimum'];
        }
        $prev_balance = Transaction::where('fk_customer', $data['ctr_customer_id'])->orderBy('pk_id', 'desc')->first()['t_balance'];

        if (!$prev_balance) {
            $prev_balance = 0;
        }
        $addTransaction = new Transaction();
        $addTransaction->fk_customer = $data['ctr_customer_id'];
        $addTransaction->t_debit = $debit;
        $addTransaction->t_balance = $prev_balance + $debit;
        $addTransaction->statment = $data['ctr_previous'] . 'ـــ' . $data['ctr_current'];
        $addTransaction->t_date = $data['ctr_date'];

        $addTransaction->save();
        $addCounter = new Counter;
        $addCounter->ctr_date = $data['ctr_date'];
        $addCounter->ctr_previous = $data['ctr_previous'];
        $addCounter->ctr_current = $data['ctr_current'];
        $addCounter->ctr_price = $data['ctr_price'];
        $addCounter->ctr_ampair = $data['ctr_ampair'];
        $addCounter->ctr_minimum = $data['ctr_minimum'];
        $addCounter->ctr_customer_id = $data['ctr_customer_id'];
        $addCounter->ctr_fk_debit = $addTransaction->pk_id;

        $addCounter->save();

        $addCounter->pk_id;

        if (!$addCounter) {

            return response()->json([
                'success' => FALSE,
            ]);
        } else {

            return response()->json([
                'success' => TRUE,
            ]);
        }
    }

    public function addCounterQuickly($data = null, $fk_customer = null, $date = NULL, $current = NULL)
    {

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
        \DB::beginTransaction();
        try {
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


            \DB::commit();
            return response(['success' => true]);
        } catch (\Exception $e) {
            \DB::rollback();
            return response($e, 403);
            return response(['message' => $e->getMessage()], 403);
        }


    }


    function checkDate(Request $request)
    {
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


    function setCurrent(Request $request)
    {
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


    function setDiscount(Request $request)
    {
        $data = $request->all();

        $last_balance = Transaction::where('fk_customer', $data['fk_customer'])->orderBy('pk_id', 'desc')->first()['t_balance'];
        $balance = $last_balance - $data['discount'];
        $editTransaction = Transaction::find($data['fk_transaction_discount']);
        //   var_dump($editTransaction);exit();
        if ($data['discount'] == 0 && $data['fk_transaction_discount']) {

            $delete_discount = Transaction::find($data['fk_transaction_discount'])->delete();
        } elseif ($data['discount'] == 0) {
            return response()->json([
                'success' => true,
            ]);
        }
        $counter = Counter::find($data['pk_id']);
        if ($editTransaction) {
            $increment_value = $data['discount'] - $editTransaction->t_credit;
            $editTransaction->t_credit = $data['discount'];
            $editTransaction->statment = "خصم عن تاريخ إستحقاق " . $counter->ctr_date;
            $editTransaction->t_date = $counter->ctr_date;

            $editTransaction->type = 3;
            $editTransaction->save();


            $increment = Transaction::where('fk_customer', $data['fk_customer'])->where('pk_id', '>=', $data['fk_transaction_discount'])->decrement('t_balance', $increment_value);

            if ($counter && $editTransaction) {
                return response()->json([
                    'success' => true,
                ]);
            }
        } else {
            $transaction = Transaction::Create(['t_date' => $counter->ctr_date, 'statment' => "خصم عن تاريخ إستحقاق " . $counter->ctr_date, 'fk_customer' => $data['fk_customer'], 'type' => 3, 't_credit' => $data['discount'], 't_balance' => $balance]);
            $update_counter_discount = Counter::where('pk_id', $data['pk_id'])->update(['fk_transaction_discount' => $transaction->pk_id]);
            $updated_balance = Transaction::where('fk_customer', $data['fk_customer'])->where('pk_id', '>', $transaction->pk_id)->increment('t_balance', $data['discount']);

            if ($transaction) {
                return response()->json([
                    'success' => true,
                ]);
            }
        }


        return response()->json([
            'success' => false,
        ]);
    }


    function showPreviousMonthQuickly($data, $date)
    {


        $ctr = counter_view::whereDate('ctr_date', $date)->where('ctr_customer_id', $data)->first();
        if ($ctr) {

            return $ctr;
        }

        return null;
    }


    function showCounter(Request $request)
    {
        $data = $request->all();

        unset($data['_token']);
        $ctr = Counter::find($data['pk_id']);
        $Customer = Customer::select("pk_id", "cs_name")
            ->where('pk_id', '=', $ctr['ctr_customer_id'])
            ->first();

        if (!$ctr && !$Customer) {

            return response()->json([
                'success' => false,
            ]);
        } else {

            return response()->json([
                'success' => TRUE,
                'ctr' => $ctr,
                'customer' => $Customer
            ]);
        }
    }


    function showPreviousMonth(Request $request)
    {
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


    function editCounterQuickly($date, $customer, $current)
    {
        $editCounter = Counter::whereDate('ctr_date', $date)->where('ctr_customer_id', $customer)->first();
        $editCounter->ctr_current = $current;

        \DB::beginTransaction();
        try {

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

            \DB::commit();
            return response(['success' => true]);
        } catch (\Exception $e) {
            \DB::rollback();
            return response($e, 403);
            return response(['message' => $e->getMessage()], 403);
        }


    }


    function editCounter(Request $request)
    {

        $data = $request->all();

        unset($data['_token']);
        $editCounter = Counter::find($data['pk_id']);

        $editCounter->ctr_date = $data['ctr_date'];
        $editCounter->ctr_previous = $data['ctr_previous'];
        $editCounter->ctr_current = $data['ctr_current'];
        $editCounter->ctr_price = $data['ctr_price'];
        $editCounter->ctr_ampair = $data['ctr_ampair'];
        $editCounter->ctr_minimum = $data['ctr_minimum'];
        $editCounter->ctr_customer_id = $data['ctr_customer_id'];
        $editCounter->ctr_fk_debit = $data['ctr_fk_debit'];

        $editCounter->save();
        $debit = ($data['ctr_current'] - $data['ctr_previous']) * $data['ctr_price'];
        if ($debit < $data['ctr_minimum']) {
            $debit = $data['ctr_minimum'];
        }

        $prev_balance = Transaction::where('fk_customer', $editCounter->ctr_customer_id)->where('pk_id', '<', $editCounter->ctr_fk_debit)->orderBy('pk_id', 'desc')->first()['t_balance'];
        /*   var_dump($prev_balance);
          exit(); */

        if (!$prev_balance) {

            $prev_balance = 0;
        }


        $editTransaction = Transaction::find($data['ctr_fk_debit']);
        if ($editTransaction) {
            $increment_value = $debit - $editTransaction->t_debit;
            $editTransaction->t_debit = $debit;
            $editTransaction->statment = $data['ctr_previous'] . 'ـــ' . $data['ctr_current'];
            $editTransaction->t_date = $data['ctr_date'];

            $editTransaction->save();


            Transaction::where('fk_customer', $data['ctr_customer_id'])->where('pk_id', '>=', $data['ctr_fk_debit'])->increment('t_balance', $increment_value);
        }
        if (!$editCounter && $editTransaction) {

            return response()->json([
                'success' => FALSE,
            ]);
        } else {

            return response()->json([
                'success' => TRUE,
            ]);
        }
    }


    function deleteCounter(Request $request)
    {
        $data = $request->all();

        $get_row_delete = Transaction::find($data['ctr_fk_debit']);
        $get_row_delete_discount = Transaction::find($data['discount']);

        $balance_counter = $get_row_delete['t_debit'];
        $balance_discount = $get_row_delete_discount['t_credit'];
        if ($balance_discount != null) {

            $update_balance_discount = Transaction::where('fk_customer', $get_row_delete['fk_customer'])->where('pk_id', '>=', $data['discount'])->increment('t_balance', $balance_discount);

        }
        $update_balance_counter = Transaction::where('fk_customer', $get_row_delete['fk_customer'])->where('pk_id', '>=', $data['ctr_fk_debit'])->decrement('t_balance', $balance_counter);

        $remove_cntr = Counter::find($data['pk_id'])->delete();
        $remove_trans = Transaction::destroy([$data['discount'], $data['ctr_fk_debit']]);


        if ($get_row_delete && $remove_trans && $remove_cntr) {
            return response()->json([
                'success' => TRUE,
            ]);
        }
        return response()->json([
            'success' => FALSE,
        ]);
    }


    function sendMessages(Request $request)
    {

        $data = $request->all();
        $messge = $data['message'];
        if (!$data['counter_date']) {

            return response()->json(['status' => 'invalid_date']);
        } elseif ($data['customize_customer']) {
            $get_counters = counter_view::where('ctr_date', $data['counter_date'])->whereIn('ctr_customer_id', $data['customize_customer'])->get();
        } elseif ($data['except_customer']) {
            $get_counters = counter_view::where('ctr_date', $data['counter_date'])->whereNotIn('ctr_customer_id', $data['except_customer'])->get();
        } elseif ($data['is_all']) {
            $get_counters = counter_view::where('ctr_date', $data['counter_date'])->get();
        }


        $response = self::sendsms($get_counters, $messge);

        return $response;
    }

}
