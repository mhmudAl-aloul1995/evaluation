<?php

namespace App\Http\Controllers;

use App\AllocationDonor;
use App\Product;
use App\Category;
use App\Donor;
use App\Municipality;
use App\Stage;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Project;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Enginges\EloquentEngine;
use Illuminate\Support\Facades\DB;
use View;
use Illuminate\Support\Facades\Auth;
use App\AllocationDonorMunicple;
use App\AllocationMunicple;

class productController extends Controller
{


    public function index()
    {


        return View::make('product');

    }


    public function datatableProduct(Request $request)
    {
        $data = $request->all();

        $users = Product::query()->with('user');

        return Datatables::of($users)
            ->addColumn('product_img', function ($ctr) {

                return '<img width="150" height="150" src="' . url('public/product_img') . '/' . $ctr->product_img . '"/>';
            })
            ->addColumn('action', function ($ctr) {

                return '<div class="btn-group">
                                                        <button  class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> إجراء
                                                            <i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul  class="dropdown-menu" role="menu">
                                                            <li>
                                                                <a onclick="showModal(`product`,' . $ctr->id . ')" href="javascript:;">
                                                                    <i class="icon-pencil"></i> تعديل </a>
                                                            </li>
                                                            <li>
                                                                <a onclick="deleteThis(`product`,' . $ctr->id . ')"  href="javascript:;">
                                                                    <i class="icon-trash"></i> حذف  </a>
                                                            </li>
                                                            </ul>
                                                    </div>';
            })
            ->rawColumns(['action' => 'action', 'product_img' => 'product_img'])
            ->make(true);
    }

    public function show(Request $request, $id)
    {

        $product = Product::find($id);
        if($product){
            return response()->json([
                'product' => $product
            ]);
        }
        return response(['message' => 'فشلت العملية'], 500);

    }

    public function store(Request $request)
    {
        $data = $request->all();

        $product = new Product;
        $product->prdct_name = $data['prdct_name'];
        $product->user_id = Auth::id();
        $product->save();

        if (!$product->save()) {

            return response()->json([
                'success' => FALSE,
                'message' => "حدث حطأ أثناء الإدخال"

            ]);
        }
        return response()->json([
            'success' => TRUE,
            'message' => "تم الإدخال بنجاح"

        ]);
    }

    public function update_product(Request $request)
    {
        $data = $request->all();
        $product = Product::find($data['id']);
        $product->prdct_name = $data['prdct_name'];
        $product->save();

        if (!$product) {
            return response()->json([
                'success' => TRUE,
                'message' => "حدث حطأ أثناء التعديل"

            ]);
        }
        return response()->json([
            'success' => TRUE,
            'message' => "تم التعديل بنجاح"
        ]);
    }

    public function destroy(Request $request, $id)
    {
        if (Product::find($id)->delete()) {
            return response()->json([
                'message' => 'تمت العملية بنجاح',
            ]);
        }

        return response(['message' => 'فشلت العملية'], 500);
    }


}
