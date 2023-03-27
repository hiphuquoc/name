<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
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

    // use AuthenticatesUsers;

    public static function create(){
        User::create([
            'name'      => 'admin',
            'email'     => 'websitekiengiang@gmail.com',
            'password'  => Hash::make('hitourVN@mk123')
        ]);
        return redirect()->route('admin.loginForm');
    }

    public function loginForm(): View {
        
        return view('layouts.loginForm');
    }

    public function loginAdmin(Request $request){
        // /* trực tiếp */
        // if(Auth::attempt($request->only('email', 'password'))){
        //     return redirect()->route('admin.booking.list');
        // }
        // session()->flash('error', 'Email và Password đăng nhập không hợp lệ!');
        // return back();
        /* ajax */
        $dataForm   = [];
            foreach($request->get('data') as $value){
                $dataForm[$value['name']]   = $value['value'];
            }
        $flag       = false;
        if(Auth::attempt($dataForm)){
            $flag   = true;
        }
        echo $flag;
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('admin.showLoginForm');
    }
}
