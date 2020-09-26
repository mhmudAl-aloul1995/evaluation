<?php

//php artisan krlove:generate:model Category --table-name=price

namespace App\Http\Controllers;

use App\Customer;
use App\Price;
use App\Category;
use App\Counter;
use App\Receipt;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Enginges\EloquentEngine;
use App\DataTables\AreaDataTable;
use Illuminate\Support\Facades\DB;
use App\Area;
use App\Transaction;
use Excel;

class AreaController extends Controller
{

    public function index(AreaDataTable $dataTable1)
    {


        /*  $ids = Customer::all();
          foreach ($ids as $id) {
              $data['fk_customer'] = $id->pk_id;


              $prev_balance = Transaction::where(['fk_customer' => $data['fk_customer'], 'type' => 7])->first();

              if ($prev_balance) {

                  $increment = Transaction::where('fk_customer', $data['fk_customer'])->where('pk_id', '>=', $prev_balance['pk_id'])->decrement('t_balance', $prev_balance['t_credit']);
                  $prev_balance1 = Transaction::where(['fk_customer' => $data['fk_customer'], 'type' => 7])->delete();
              }
          }
        */
        return $dataTable1->render('area');
    }

    public function showArea(Request $request)
    {
        $data = $request->all();
        unset($data['_token']);
        $area = Area::find($data['pk_id']);
        return response()->json([
            'success' => TRUE,
            'area' => $area
        ]);
    }

    public function addArea(Request $request)
    {
        $data = $request->all();
     /*   $cus = Customer::all();

        foreach ( $cus as $item) {
            if (strlen($item->cs_mobile) == 7) {
                Customer::find($item->pk_id)->update(['cs_mobile'=>'059' . $item->cs_mobile]);

            }
        }
/*
        $excel = [];

        Excel::load($request->area_name->getRealPath(), function ($reader) use (&$excel) {
            $objExcel = $reader->getExcel();
            $sheet = $objExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            for ($row = 0; $row <= $highestRow; $row++) {
                $excel[] = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE)[0];
            }
        })->formatDates(true, 'Y-m-d');

        $error1 = array();
        for ($counter = 1; $counter < sizeof($excel); $counter++) {
            $row = $excel[$counter];


            $id = Customer::where("cs_name", trim($row[1]))->first()['pk_id'];


            if ($id) {

                $data['ctr_current'] = $row[6];
                $data['ctr_previous'] = $row[5];
                $data['ctr_minimum'] = $row[4];
                $data['ctr_price'] = 4;
                $data['ctr_ampair'] = 0;
                $data['ctr_date'] = "2020-07-01";
                $data['ctr_customer_id'] = $id;
                $debit = ($data['ctr_current'] - $data['ctr_previous']) * $data['ctr_price'];
                if (Counter::where(['ctr_previous' => $data['ctr_previous'], 'ctr_current' => $data['ctr_current']])->first() == null) {
                    array_push($error1, trim($row[1]));

                }
            }
            /* if ($debit < $data['ctr_minimum']) {
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
             $addTransaction->statment = $data['ctr_previous'] . 'ـــ' . $data['ctr_current'] . 'ـــ' . "2020-07-01";
             $addTransaction->t_date = $data['ctr_date'];

             $addTransaction->save();
             $addCounter = new Counter();
             $addCounter->ctr_date = $data['ctr_date'];
             $addCounter->ctr_previous = $data['ctr_previous'];
             $addCounter->ctr_current = $data['ctr_current'];
             $addCounter->ctr_price = $data['ctr_price'];
             $addCounter->ctr_ampair = $data['ctr_ampair'];
             $addCounter->ctr_minimum = $data['ctr_minimum'];
             $addCounter->ctr_customer_id = $data['ctr_customer_id'];
             $addCounter->ctr_fk_debit = $addTransaction->pk_id;

             $addCounter->save();

             if ($row[9] > 0) {
                 $addTransaction2 = new Transaction();


                 $addTransaction2->fk_customer = $data['ctr_customer_id'];

                 $addTransaction2->statment = "سند قبض  " . " " . "رقم السند " . " " . $row[13] . " " . " رقم الدفتر" . " " . $row[14];


                 $addTransaction2->t_date = "2020-07-01";

                 $addTransaction2->t_credit = $row[9];

                 $addTransaction2->t_balance = $addTransaction->t_balance - $row[9];
                 $addTransaction2->type = 2;


                 $addTransaction2->save();

                 $addReceipt = new Receipt;

                 $addReceipt->fk_customer = $data['ctr_customer_id'];

                 $addReceipt->r_statement = "سند قبض  " . " " . "رقم السند " . " " . $row[13] . " " . " رقم الدفتر" . " " . $row[14];

                 $addReceipt->r_recp_no = $row[13];

                 $addReceipt->r_recp_book_no = $row[14];

                 $addReceipt->r_date = "2020-07-01";

                 $addReceipt->fk_transaction = $addTransaction2->pk_id;
                 $addReceipt->r_statement = "سند قبض  " . " " . "رقم السند " . " " . $row[13] . " " . " رقم الدفتر" . " " . $row[14];


                 $addReceipt->save();
             }

             if ($row[11] > 0 ) {
                 $addTransaction1 = new Transaction();

                 $addTransaction1->fk_customer = $id;
                 $addTransaction1->t_debit = $row[11];
                 $addTransaction1->t_balance = Transaction::where(['fk_customer'=>$id])->orderBy('pk_id','desc')->first()['t_balance']+ $row[11];
                 $addTransaction1->statment = "متأخرات";
                 $addTransaction1->type = 7;

                 $addTransaction1->save();
             }

         }*/
        }

  //  }
    /*      return response()->json([
              'success' => TRUE,
              'area_name' => $data['area_name']
          ]);
          unset($data['_token']);
          unset($data['pk_id']);

          $addArea = Area::insert($data);

          if (!$addArea) {

              return response()->json([
                  'success' => FALSE,
              ]);
          } else {

              return response()->json([
                  'success' => TRUE,
                  'area_name' => $data['area_name']
              ]);
          }*/


    /*    public function addArea(Request $request)
        {
            $data = $request->all();


            $excel = [];

            Excel::load($request->area_name->getRealPath(), function ($reader) use (&$excel) {
                $objExcel = $reader->getExcel();
                $sheet = $objExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                for ($row = 0; $row <= $highestRow; $row++) {
                    $excel[] = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE)[0];
                }
            })->formatDates(true, 'Y-m-d');


            for ($counter = 1; $counter < sizeof($excel); $counter++) {
                $row = $excel[$counter];


                $id = Customer::where("cs_name", trim($row[1]))->first()['pk_id'];
                if ($id) {

                    $data['fk_customer'] = $id;
                    $data['type'] = 7;
                    $data['is_credit'] = 1;
                    $prev_balance = Transaction::select("t_balance")->where('pk_id', Transaction::where('fk_customer', $data['fk_customer'])->max('pk_id'))->first()['t_balance'];
                    $statment = [4 => "أجرة كهربائي", 5 => "تسوية", 6 => "رسوم ساعة", 7 => "متأخرات"];
                    if (!$prev_balance) {
                        $prev_balance = 0;
                    }
                    $addTransaction = new Transaction();
                    if ($data['is_credit'] == 1) {
                        $addTransaction->t_debit = (int)$row[11];
                        $addTransaction->t_balance = $prev_balance + (int)$row[11];
                    } else {
                        $addTransaction->t_credit = (int)$row[0];
                        $addTransaction->t_balance = $prev_balance - (int)$row[11];


                    }

                    $addTransaction->statment = $statment[$data['type']];
                    $addTransaction->type = $data['type'];
                    $addTransaction->fk_customer = $data['fk_customer'];
                    $addTransaction->save();
                }


            }
            return response()->json([
                'success' => TRUE,
                'area_name' => $data['area_name']
            ]);
            unset($data['_token']);
            unset($data['pk_id']);

            $addArea = Area::insert($data);

            if (!$addArea) {

                return response()->json([
                    'success' => FALSE,
                ]);
            } else {

                return response()->json([
                    'success' => TRUE,
                    'area_name' => $data['area_name']
                ]);
            }

        }*/

    public function editArea(Request $request)
    {
        $data = $request->all();

        unset($data['_token']);
        $Area = Area::find($data['pk_id']);
        $Area->update($data);

        return response()->json([
            'success' => TRUE,
        ]);
    }

    public function deleteArea(Request $request)
    {
        $data = $request->all();

        $remove_area = Area::find($data['pk_id'])->delete();
        if (!$remove_area) {

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
