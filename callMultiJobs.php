<?php
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$i = 0; // Biến đếm số lần chạy

while ($i < 2) { // Chạy tối đa 2 lần
    // Đếm số job đang chạy dựa vào cột reserved_at (job đang chạy có reserved_at khác null)
    $runningJobsCount = DB::table('jobs')
        ->whereNotNull('reserved_at')
        ->count();

    // Nếu số job đang chạy ít hơn 10, ta tính số job cần khởi chạy thêm
    $jobPerTime = 10;
    if ($runningJobsCount < $jobPerTime) {
        $jobsToDispatch = $jobPerTime - $runningJobsCount;

        // Lấy các job chưa được reserve (reserved_at IS NULL) theo số lượng cần thiết
        $ordering = $i==0 ? 'asc' : 'desc';
        $jobs = DB::table('jobs')
            ->whereNull('reserved_at')
            ->orderBy('id', $ordering)
            ->limit($jobsToDispatch)
            ->get();

        foreach ($jobs as $job) {
            // Cập nhật cột reserved_at thành thời gian hiện tại (UNIX timestamp)
            DB::table('jobs')
                ->where('id', $job->id)
                ->update(['reserved_at' => time()]);

            // Gọi artisan command tùy chỉnh để xử lý job với id cụ thể
            $command = 'php ' . __DIR__ . '/artisan queue:work-job ' . $job->id . ' --timeout=8000';
            // Chạy lệnh ở background
            exec($command . ' >> /dev/null 2>&1 &');
        }
    }

    $i++; // Tăng biến đếm số lần chạy

    // Nếu đã chạy 2 lần thì thoát
    if ($i >= 2) {
        break;
    }

    // Chờ 30 giây trước khi chạy lần tiếp theo
    sleep(30);
}
