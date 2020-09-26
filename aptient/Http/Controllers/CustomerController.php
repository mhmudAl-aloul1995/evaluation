<?php


namespace App\Http\Controllers;

use App\Receipt;
use App\Customer;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Enginges\EloquentEngine;
use App\DataTables\ReceiptDataTable;
use Illuminate\Support\Facades\DB;
use App\Transaction;
use App\receipt_view;
use App\DataTables\CustomerDataTable;
use App\Area;
use App\customers_view;

class CustomerController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    /*    $cus=Customer::groupBy('pk_id')->get();

        foreach ($cus as $value) {

            $data['type'] = 8;
            $prev_balance = Transaction::select("t_balance")->where('pk_id', Transaction::where('fk_customer', $value->pk_id)->max('pk_id'))->first()['t_balance'];

            $credit = Transaction::where('fk_customer',$value->pk_id)->where('t_date','!=',null)
                ->where('t_date', '!=', '2020-07-01')->sum("t_debit");
            $statment = [4 => "أجرة كهربائي", 5 => "تسوية", 6 => "رسوم ساعة", 7 => "متأخرات", 8 => "تسوية تأسيسية للبرنامج "];
            if (!$prev_balance) {
                $prev_balance = 0;
            }
            $addTransaction = new Transaction();

            $addTransaction->fk_customer = $value->pk_id;
            $addTransaction->t_credit = $credit;
            $addTransaction->t_balance = $prev_balance - $credit;

            $addTransaction->statment = $statment[$data['type']];
            $addTransaction->type = $data['type'];
            $addTransaction->save();
        }*/
        $area = Area::select('pk_id', 'area_name')->get();
      /*  $customers = customers_view::all();
        set_time_limit(10000);
        $USER_NAME = "mator.mash";
        $PASSWORD = "12345678";
        $SENDER = "mator.mash";
        $resp = null;
        $is_sent = null;

        if ($customers->count() == 0) {
            return response()->json(['no_row' => true]);
        }

        foreach ($customers as $customer) {

            $NUMBER = $customer->cs_mobile;


            if (strlen($NUMBER) == 7) {
                $NUMBER = '059' . $NUMBER;
            }

            if (strlen($NUMBER) == 10) {
                $MESSAGE = "إلى مشتركينا الكرام نعلمكم أنه تم إنهاء عمل السيد أبو علاء دبابش لدينا";
           
                $MESSAGE = urlencode($MESSAGE);
                $url = "http://www.alqudwasms.com/api.php?comm=sendsms&user=" . $USER_NAME . "&pass=" . $PASSWORD . "&to=" . $NUMBER . "&message=" . $MESSAGE . "&sender=" . $SENDER;

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_URL => $url
                ));
                $resp = curl_exec($curl);
                curl_close($curl);
			}
		}*/
                return view('customer')->with(['area' => $area]);
            }

//UPDATE customers SET cus_index=pk_id

            public function orderPositionUp($data)
            {

                $min = min($data['indexes']);
                $max = max($data['indexes']);


                $update1 = Customer::where('pk_id', $data['ids'][0])->update(['cus_index' => $min]);


                $update = Customer::where('pk_id', '!=', $data['ids'][0])->where('cus_index', '>=', $min)->where('cus_index', '<=', $max)->increment('cus_index', 1);
            }

            public
            function orderPosition(Request $request)
            {
                $data = $request->all();
                $sub = $data['indexes'][0] - $data['indexes'][1];
                if ($sub > 0) {

                    $this->orderPositionUp($data);
                    return response()->json([
                        'success' => true,
                    ]);
                }

                $update1 = Customer::where('pk_id', $data['ids'][0])->update(['cus_index' => $data['indexes'][1]]);


                if ($update1) {

                    $update = Customer::where('pk_id', '!=', $data['ids'][0])->where('cus_index', '>=', $data['indexes'][0])->where('cus_index', '<=', $data['indexes'][1])->decrement('cus_index', 1);
                }
                if ($update1 && $update) {
                    return response()->json([
                        'success' => TRUE,
                    ]);
                }
            }

            public
            function customerDatatabel(Request $request)
            {
                $data = $request->all();

                $users = customers_view::query()->orderBy('cus_index', 'asc');


                return Datatables::of($users)
                    ->addColumn('action', function ($cus) {
                        if ($cus->p_enabled == 1) {
                            $active = 'fa-check';
                        } else {
                            $active = 'fa-close';
                        }
                        return '<a onclick="deleteCstmr(' . $cus->pk_id . ',' . $cus->cus_index . ')" class="btn btn-outline btn-circle dark btn-sm ">
                                               <i class="fa fa-trash-o"></i>   </a>
<a onclick="cusModal(' . $cus->pk_id . ')" class="btn btn-outline btn-circle green btn-sm "><i class="fa fa-edit"></i></a>
    <a onclick="ch_st(' . $cus->pk_id . ')" class="btn btn-outline btn-circle red btn-sm "><i id="ch_' . $cus->pk_id . '" class="fa ' . $active . '"></i></a>';
                    })
                    ->setRowId(function ($ctr) {
                        return "index_" . $ctr->pk_id;
                    })
                    ->make(true);
            }

            public
            function showCustomer(Request $request)
            {
                $data = $request->all();
                unset($data['_token']);
                $cstmr = Customer::find($data['pk_id']);
                return response()->json([
                    'success' => TRUE,
                    'cstmr' => $cstmr
                ]);
            }

            public
            function addCustomer(Request $request)
            {
                $data = $request->all();
                unset($data['_token']);
                unset($data['pk_id']);
                $last_index = Customer::where('cs_fk_area', $data['cs_fk_area'])->orderBy('cus_index', 'desc')->first()['cus_index'];
                if (!$last_index) {
                    $last_index = Customer::orderBy('cus_index', 'desc')->first()['cus_index'];
                }
                $data['cus_index'] = $last_index + 1;

                $addCustomer = Customer::insert($data);

                $update = Customer::where('cus_index', '>=', $data['cus_index'])->where('pk_id', '!=', Customer::max('pk_id'))->increment('cus_index', 1);

                if (!$addCustomer && !$update) {

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
            function editCustomer(Request $request)
            {
                $data = $request->all();

                unset($data['_token']);
                $ctmr = Customer::find($data['pk_id']);
                $ctmr->update($data);

                return response()->json([
                    'success' => TRUE,
                ]);
            }

            public
            function deleteCustomer(Request $request)
            {
                $data = $request->all();
                $decrement = Customer::where('cus_index', '>', $data['cus_index'])->decrement('cus_index', 1);
                $remove_cstmr = Customer::find($data['pk_id'])->delete();

                if (!$remove_cstmr && !$decrement) {

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
            function area_search(Request $request)
            {
                $data = [];

                if ($request->has('q')) {
                    $search = $request->q;
                    $data = Area::select("pk_id", "area_name")
                        ->where('area_name', 'LIKE', "%$search%")
                        ->get();
                }

                return response()->json($data);
            }

            public
            function cus_search(Request $request)
            {
                $data = [];

                if ($request->has('q')) {
                    $search = $request->q;
                    $data = Customer::select("pk_id", "cs_name")
                        ->where('p_enabled', 1)
                        ->where('cs_name', 'LIKE', "%$search%")
                        ->get();
                }

                return response()->json($data);
            }

            public
            function area_one_search(Request $request)
            {
                $data = [];

                if ($request->has('pk_id')) {
                    $search = $request->pk_id;
                    $data = DB::table("areas")
                        ->select("pk_id", "area_name")
                        ->where('pk_id', '=', $search)
                        ->first();
                }

                return response()->json($data);
            }

            public
            function p_enabled(Request $request)
            {
                $data = $request->all();


                $isEnabled = Customer::where('pk_id', $data['pk_id'])->first();

                if ($isEnabled->p_enabled == 0) {
                    Customer::where(['pk_id' => $data['pk_id'], 'p_enabled' => 0])->update(['p_enabled' => 1]);

                    return response()->json([
                        'success' => TRUE,
                        'status' => 'fa fa-check'
                    ]);
                }
                if ($isEnabled->p_enabled == 1) {

                    Customer::where(['pk_id' => $data['pk_id'], 'p_enabled' => 1])->update(['p_enabled' => 0]);

                    return response()->json([
                        'success' => TRUE,
                        'status' => 'fa-close'
                    ]);
                }
                return response()->json([

                    'success' => FALSE,
                ]);
            }

        }
