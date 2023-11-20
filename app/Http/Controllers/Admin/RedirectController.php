<?php

namespace App\Http\Controllers\Admin;

use App\Models\RedirectInfo;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;

class RedirectController extends Controller {
    
    public function list(Request $request){
        $list           = RedirectInfo::select('*')
                            ->orderBy('id', 'DESC')
                            ->get();
        return view('admin.redirect.list', compact('list'));
    }

    public function create(Request $request){
        $id             = 0;
        /* Message */
        $message        = [
            'type'      => 'danger',
            'message'   => '<strong>Thất bại!</strong> Có lỗi xảy ra, vui lòng thử lại'
        ];
        if(!empty($request->get('old_url'))&&!empty($request->get('new_url'))){
            $urlOld     = self::filterUrl($request->get('old_url'));
            $urlNew     = self::filterUrl($request->get('new_url'));
            /* kiểm tra trùng url cũ */
            $tmp        = RedirectInfo::select('*')
                                    ->where('old_url', $urlOld)
                                    ->first();
            if(empty($tmp)){
                /* insert */
                $id         = RedirectInfo::insertItem([
                    'old_url'   => $urlOld,
                    'new_url'   => $urlNew
                ]);
                if(!empty($id)){
                    /* Message */
                    $message        = [
                        'type'      => 'success',
                        'message'   => '<strong>Thành công!</strong> Đã thêm redirect mới'
                    ];
                }
            }else {
                /* Message */
                $message        = [
                    'type'      => 'danger',
                    'message'   => '<strong>Thất bại!</strong> Url này đã được chỉ định redirect trước đó'
                ];
            }
        }
        $request->session()->put('message', $message);
        return redirect()->route('admin.redirect.list', ['id' => $id]);
    }

    public function delete(Request $request){
        $flag       = false;
        if(!empty($request->get('id'))){
            $flag   = RedirectInfo::find($request->get('id'))->delete();
        }
        echo $flag;
    }

    private static function filterUrl($input){
        $output     = null;
        if(!empty($input)){
            /* bỏ tên miền */
            $output     = str_replace(env('APP_URL'), '', $input);
            /* thêm / nếu không có */
            if(substr($output, 0, 1)!='/') $output = '/'.$output;
        }
        return $output;
    }
    
}
