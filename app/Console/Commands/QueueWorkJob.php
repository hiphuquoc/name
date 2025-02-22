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

    // QueueWorkJob.php
    public function handle() {
        $jobId = $this->argument('jobId');
        
        // Sử dụng transaction để đảm bảo atomicity
        DB::beginTransaction();
        try {
            $jobRecord = DB::table('jobs')
                ->where('id', $jobId)
                ->lockForUpdate() // Chặn concurrent access
                ->first();

            if (!$jobRecord) {
                $this->error("Job không tồn tại");
                DB::commit();
                return 1;
            }

            DB::table('jobs')
                ->where('id', $jobId)
                ->increment('attempts');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Lỗi database: " . $e->getMessage());
            return 1;
        }

        // Xử lý job trực tiếp không qua process con
        try {
            $this->info("Đang xử lý job id: {$jobId}");
            $this->fireJob($jobId);
            
            DB::table('jobs')->where('id', $jobId)->delete();
            $this->info("Job {$jobId} thành công");
        } catch (\Exception $e) {
            $this->error("Lỗi xử lý job: " . $e->getMessage());
            // Xử lý retry/logic failed job nếu cần
        }

        // Giải phóng kết nối
        DB::disconnect();
        return 0;
    }

    private function fireJob($jobId) {
        // Sử dụng lại logic từ InternalFireJob
        $jobRecord = DB::table('jobs')->find($jobId);
        
        $connection = config('queue.default');
        $queueName = $jobRecord->queue ?? 'default';
        
        $databaseQueue = app('queue')->connection($connection);
        
        $databaseJob = new DatabaseJob(
            app(),
            $databaseQueue,
            (object) $jobRecord,
            $connection,
            $queueName
        );
        
        $databaseJob->fire();
    }
}