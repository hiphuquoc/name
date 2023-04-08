<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Google\Client as Google_Client;

class ProviderController extends Controller {

    public function callback(Request $request){
        if ($_COOKIE['g_csrf_token'] !== $request->input('g_csrf_token')) {
            // Invalid CSRF token
            return back();
        }
        
        $idToken = $request->input('credential');
            
        $client = new Google_Client([
            'client_id' => env('GOOGLE_CLIENT_ID')
        ]);
        
        $payload = $client->verifyIdToken($idToken);
        
        if (!$payload) {
            // Invalid ID token
            return back();
        }
        
        dd($payload);
        
    }

}
