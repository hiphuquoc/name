<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Models\PaymentMethod;
use App\Models\Province;

class CheckoutController extends Controller{

    public static function index(Request $request){
        $payments       = PaymentMethod::all();
        $provinces      = Province::all();
        $products       = \App\Http\Controllers\CartController::getCollectionProducts();
        $productsCart   = json_decode(Cookie::get('cart'), true);
        return view('main.checkout.index', compact('payments', 'provinces', 'products', 'productsCart'));
    }

}
