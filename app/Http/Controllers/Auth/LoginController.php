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
        $flag       = false;
        $message    = 'Email và Password không hợp lệ!';
        $dataForm   = [];
        foreach($request->get('data') as $value){
            $dataForm[$value['name']] = $value['value'];
        }
        // Đăng nhập
        if(Auth::attempt($dataForm)){
            $user       = Auth::user();
            if($user->hasRole('admin')){
                $flag   = true;
            } else {
                $flag       = false;
                $message    = 'Bạn không có quyền truy cập vào khu vực này!';
                Auth::logout();
            }
        }
        $result['flag']     = $flag;
        $result['message']  = $message;
        return json_encode($result);
    }

    public function loginCustomer(Request $request){
        $flag       = false;
        $message    = 'Email và Password không hợp lệ!';
        $dataForm   = [];
        foreach($request->get('data') as $value){
            $dataForm[$value['name']] = $value['value'];
        }
        /* đăng nhập */
        if(Auth::attempt($dataForm)) $flag   = true;
        $result['flag']     = $flag;
        $result['message']  = $message;
        return json_encode($result);
    }

    public static function logout(){
        Auth::logout();
        return redirect($_SERVER['HTTP_REFERER']);
    }
}
