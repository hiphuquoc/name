<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminSubdomain {
    public function handle(Request $request, Closure $next){
        $host       = $request->getHost();
        $domainName = env('DOMAIN_NAME');
        // Kiểm tra xem subdomain có phải là admin hay không
        if ($host !== 'admin.'.$domainName) {
            abort(403, 'Unauthorized access');
        }
        return $next($request);
    }
}

