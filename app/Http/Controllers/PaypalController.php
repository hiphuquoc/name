<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;


class PaypalController extends Controller{

    public static function create($infoOrder){

        $data = [];
        /* danh sách sản phẩm */
        $data['item']       = [];
        foreach($infoOrder->products as $product){
            $data['item'][] = [
                'name'  => $product->infoProduct->en_name,
                'price' => $product->price,
                'qty'   => $product->quantity
            ];
        }
        $data['invoice_id']             = $infoOrder->code ?? null;
        $data['invoice_description']    = "Order ".$data['invoice_id']." Invoice";
        $data['return_url']             = 'https://name.dev';
        $data['cancel_url']             = 'https://name.dev';
        $data['total']                  = $infoOrder->total;

        $provider = new PayPalClient;
        // Through facade. No need to import namespaces
        $provider = \PayPal::setProvider();
        $provider->getAccessToken();
        $provider->setCurrency('USD');
        
        // Create a payment

        // Create a payment
        $response = $provider->createOrder($data);

        dd($response);
        // Get the approval link
        $approvalLink = $response['links'][0]['href'];

        // dd($approvalLink);

        // Redirect the user to the approval link
        return redirect($approvalLink);
    }

}
