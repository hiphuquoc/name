<?php
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Đếm số job đang chạy dựa vào cột reserved_at (job đang chạy có reserved_at khác null)
$runningJobsCount = DB::table('jobs')
    ->whereNotNull('reserved_at')
    ->count();

// Nếu số job đang chạy ít hơn 5, tìm job chưa được reserve (reserved_at IS NULL) với id cũ nhất
if ($runningJobsCount < 5) {
    $job = DB::table('jobs')
        ->whereNull('reserved_at')
        ->orderBy('id', 'asc')
        ->first();

    if ($job) {
        // Cập nhật cột reserved_at thành thời gian hiện tại để đánh dấu job này đang được xử lý
        // Sử dụng helper time() của Laravel để lấy thời gian hiện tại
        DB::table('jobs')
            ->where('id', $job->id)
            ->update(['reserved_at' => time()]);

        // Gọi artisan command tùy chỉnh để xử lý job với id cụ thể
        $command = 'php ' . __DIR__ . '/artisan queue:work-job ' . $job->id . ' --timeout=8000';
        // Chạy lệnh ở background
        exec($command . ' >> /dev/null 2>&1 &');
    }
}
