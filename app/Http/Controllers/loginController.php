<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Hash;
class loginController extends Controller {
    /*
      |--------------------------------------------------------------------------
      | Login Controller
      |--------------------------------------------------------------------------
      |
      | This controller handles authenticating users for the application and
      | redirecting them to your home screen. The controller uses a trait
      | to conveniently provide its functionality to your applications.
      |
     */


    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/index';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest')->except( 'logout');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return Response
     */
    public function index() {

        return view('login');
    }

    public function login(Request $request) {


        $credentials = $request->only('name', 'password');

        if (Auth::attempt($credentials)) {

            return response(['success' => true]);
        }
        return response(['success' => false,'message'=>"خطأ في إسم المستخدم وكلمة المرور"]);

    }

    public function logout(Request $request) {
        Auth::logout();
        return redirect('/');
    }

}
