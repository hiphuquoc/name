<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Định nghĩa các hằng số từ file .env
$jobPerTime = env('MAX_CONCURRENT_JOBS', 40); // Số job tối đa chạy đồng thời
$jobPerCall = env('JOBS_BATCH_SIZE', 10); // Số job tối đa gọi mỗi lần
$maxTime = env('JOB_TIMEOUT_SECONDS', 300); // Thời gian timeout cho mỗi job (giây)

// Khóa file để tránh việc chạy chồng chéo
$lockFile = __DIR__ . '/cron.lock';
$fp = fopen($lockFile, 'c+');

if (!$fp) {
    die("Không thể mở lock file.\n");
}

// Kiểm tra nếu có tiến trình khác đang chạy
if (!flock($fp, LOCK_EX | LOCK_NB)) {
    fclose($fp);
    die("Cron job đang chạy, bỏ qua lần chạy này.\n");
}

try {
    $i = 0; // Biến đếm số lần chạy
    while ($i < 2) { // Chạy tối đa 2 lần
        // Đếm số job đang chạy dựa vào cột reserved_at (job đang chạy có reserved_at khác null)
        $runningJobsCount = DB::table('jobs')
            ->whereNotNull('reserved_at')
            ->count();

        // Nếu số job đang chạy ít hơn giới hạn, tính số job cần khởi chạy thêm
        if ($runningJobsCount < $jobPerTime) {
            $jobsToDispatch = min($jobPerTime - $runningJobsCount, $jobPerCall);

            // Lấy các job chưa được reserve (reserved_at IS NULL) theo số lượng cần thiết
            $jobs = DB::table('jobs')
                ->whereNull('reserved_at')
                ->where('attempts', '<', 1)
                ->orderBy('id', 'desc')
                ->limit($jobsToDispatch)
                ->get();

            foreach ($jobs as $job) {
                // Cập nhật cột reserved_at thành thời gian hiện tại (UNIX timestamp)
                DB::table('jobs')
                    ->where('id', $job->id)
                    ->update(['reserved_at' => time()]);

                // Gọi artisan command tùy chỉnh để xử lý job với id cụ thể
                $command = sprintf(
                    'php %s/artisan queue:work-job %d --timeout=%d >> /dev/null 2>&1 &',
                    __DIR__,
                    $job->id,
                    $maxTime
                );
                exec($command);
            }
        }

        $i++; // Tăng biến đếm số lần chạy
        if ($i >= 2) {
            break;
        }

        // Chờ 15 giây trước khi chạy lần tiếp theo
        sleep(15);
    }
} finally {
    // Giải phóng khóa và đóng file
    flock($fp, LOCK_UN);
    fclose($fp);
}