<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\RedirectInfo;
use Illuminate\Support\Facades\Redirect;

class CheckRedirect {
    public function handle($request, Closure $next) {
        $path = '/'.$request->path();
        // Tìm đường dẫn cũ trong cơ sở dữ liệu
        $redirectInfo = RedirectInfo::where('old_url', $path)->first();

        if ($redirectInfo) {
            // Nếu tìm thấy, thực hiện redirect 301
            return Redirect::to($redirectInfo->new_url, 301);
        }

        // Nếu không tìm thấy, chuyển request đến hệ thống xử lý route thông thường
        return $next($request);
    }
}
