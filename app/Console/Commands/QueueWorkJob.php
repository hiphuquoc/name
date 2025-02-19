<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Container\Container;
use Illuminate\Queue\Jobs\DatabaseJob;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessTimedOutException;

class QueueWorkJob extends Command {
    protected $signature = 'queue:work-job {jobId} {--timeout=8000}';
    protected $description = 'Xử lý một job cụ thể (theo id) từ database queue';

    public function handle() {
        $jobId   = $this->argument('jobId');
        $timeout = (int) $this->option('timeout');

        $jobRecord = DB::table('jobs')->where('id', $jobId)->first();

        if (!$jobRecord) {
            $this->error("Không tìm thấy job với id: {$jobId}");
            return 1;
        }

        $connection = config('queue.default', 'database');
        $queueName  = $jobRecord->queue ?? 'default';

        $databaseQueue = app('queue')->connection($connection);
        $jobObject    = (object)$jobRecord;

        $databaseJob = new DatabaseJob(
            Container::getInstance(),
            $databaseQueue,
            $jobObject,
            $connection,
            $queueName
        );

        $this->info("Đang xử lý job id: {$jobId}");

        try {
            // Tạo process để chạy job trong một tiến trình riêng với timeout 5 phút
            $process = new Process(['php', 'artisan', 'internal:job-fire', $jobId]);
            $process->setTimeout(300); // 300 giây = 5 phút
            $process->run();

            if ($process->isSuccessful()) {
                // Xóa job sau khi xử lý thành công
                $databaseJob->delete();
                $this->info("Job {$jobId} đã được xử lý thành công và xóa khỏi bảng.");
            } else {
                // Kiểm tra nếu lỗi do timeout
                if ($process->getExitCode() === Process::STATUS_TIMED_OUT) {
                    DB::table('jobs')->where('id', $jobId)->delete();
                    $this->error("Job {$jobId} đã bị hủy do vượt quá thời gian thực thi 5 phút.");
                } else {
                    // Phát hành lại job nếu có lỗi khác
                    $databaseJob->release($timeout);
                    $this->error("Xử lý job {$jobId} thất bại: " . $process->getErrorOutput());
                }
            }
        } catch (ProcessTimedOutException $e) {
            DB::table('jobs')->where('id', $jobId)->delete();
            $this->error("Job {$jobId} đã bị hủy do vượt quá thời gian thực thi 5 phút.");
        } catch (\Exception $e) {
            $this->error("Lỗi hệ thống: " . $e->getMessage());
            $databaseJob->release($timeout);
        }

        return 0;
    }
}