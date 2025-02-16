<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Container\Container;
use Illuminate\Queue\Jobs\DatabaseJob;

class QueueWorkJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:work-job {jobId} {--timeout=8000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xử lý một job cụ thể (theo id) từ database queue';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $jobId   = $this->argument('jobId');
        $timeout = (int) $this->option('timeout');

        // Lấy record của job theo id từ bảng jobs
        $jobRecord = DB::table('jobs')->where('id', $jobId)->first();

        if (!$jobRecord) {
            $this->error("Không tìm thấy job với id: {$jobId}");
            return 1;
        }

        // Lấy kết nối queue từ config (giả sử sử dụng database driver)
        $connection = config('queue.default', 'database');
        // Lấy tên queue từ record, nếu có (mặc định 'default')
        $queueName = isset($jobRecord->queue) ? $jobRecord->queue : 'default';

        // Lấy instance của queue connection (DatabaseQueue)
        $databaseQueue = app('queue')->connection($connection);

        // Chuyển record job thành object (nếu chưa)
        $jobObject = is_object($jobRecord) ? $jobRecord : (object)$jobRecord;

        // Tạo instance của DatabaseJob với 5 tham số
        $databaseJob = new DatabaseJob(
            Container::getInstance(),
            $databaseQueue,
            $jobObject,
            $connection,   // Tham số connection name
            $queueName
        );

        $this->info("Đang xử lý job id: {$jobId}");

        try {
            // Gọi phương thức xử lý job (lưu ý: tùy phiên bản Laravel, phương thức fire() có thể cần thay đổi)
            $databaseJob->fire();

            // Sau khi xử lý thành công, xóa job khỏi bảng
            $databaseJob->delete();
            $this->info("Job {$jobId} đã được xử lý thành công và xóa khỏi bảng.");
        } catch (\Exception $e) {
            $this->error("Xử lý job {$jobId} thất bại: " . $e->getMessage());
        
            // Tăng số lần thử lên 1
            DB::table('jobs')->where('id', $jobId)->increment('attempts');
        
            // Phát hành lại job để thử lại sau
            $databaseJob->release($timeout);
        }

        return 0;
    }

}
