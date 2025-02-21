<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Container\Container;
use Illuminate\Queue\Jobs\DatabaseJob;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessTimedOutException;

class QueueWorkJob extends Command {
    protected $signature = 'queue:work-job {jobId} {--timeout=300}'; // Mặc định 300s = 5p
    protected $description = 'Xử lý một job cụ thể (theo id) từ database queue';

    public function handle() {
        $jobId = $this->argument('jobId');
        $timeoutSeconds = (int) $this->option('timeout');

        // Lấy job và kiểm tra tồn tại
        $jobRecord = DB::table('jobs')->where('id', $jobId)->first();
        if (!$jobRecord) {
            $this->error("Không tìm thấy job với id: {$jobId}");
            return 1;
        }else {
            // Tăng số lần thử lên 1
            DB::table('jobs')->where('id', $jobId)->increment('attempts');
        }

        // Khởi tạo process với timeout 5 phút
        $process = new Process(['php', 'artisan', 'internal:job-fire', $jobId]);
        $process->setTimeout($timeoutSeconds); // Sử dụng timeout từ option

        try {
            $this->info("Đang xử lý job id: {$jobId}");
            $process->run();

            // XÓA JOB TRONG MỌI TRƯỜNG HỢP SAU KHI CHẠY
            DB::table('jobs')->where('id', $jobId)->delete();

            if ($process->isSuccessful()) {
                $this->info("Job {$jobId} đã xử lý thành công");
            } else {
                $this->error("Job {$jobId} thất bại: " . $process->getErrorOutput());
            }

        } catch (ProcessTimedOutException $e) {
            $this->error("Job {$jobId} bị hủy do timeout 5 phút");
        } catch (\Exception $e) {
            $this->error("Lỗi hệ thống: " . $e->getMessage());
        }

        return 0;
    }
}