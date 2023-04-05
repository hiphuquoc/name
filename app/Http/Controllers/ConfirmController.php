<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\SourceFile;

class ConfirmController extends Controller {

    public static function confirm(Request $request){
        // $item       = new \Illuminate\Database\Eloquent\Collection;
        // if(!empty($request->get('code'))){
        //     $order  = Order::select('*')
        //                 ->where('code', $request->get('code'))
        //                 ->with('products')
        //                 ->first();
        //     return view('wallpaper.confirm.index', compact('item', 'order'));
        // }
        $item       = new \Illuminate\Database\Eloquent\Collection;
        $order  = Order::select('*')
                        ->orderBy('id', 'DESC')
                        ->with('products')
                        ->first();
        return view('wallpaper.confirm.index', compact('item', 'order'));
    }

    public function downloadSource(Request $request){
        $fullPath       = '';
        $fileName       = '';
        if(!empty($request->get('source_info_id'))){
            $infoSource = SourceFile::select('*')
                            ->where('id', $request->get('source_info_id'))
                            ->first();
            $fullPath   = Storage::url($infoSource->file_path);
            $fileName   = $infoSource->file_name;
        }
        $result['url']      = $fullPath;
        $result['filename'] = $fileName;
        return json_encode($result);
    }

    public function downloadSourceAll(Request $request){
        $urls           = [];
        if(!empty($request->get('code'))){
            $order      = Order::select('*')
                            ->where('code', $request->get('code'))
                            ->with('products')
                            ->first();
            foreach($order->products as $product){
                foreach($product->infoPrice->sources as $source){
                    $urls[] = env('APP_URL').Storage::url($source->file_path);
                }
            }
        }
        return json_encode($urls);
    }
    
}
