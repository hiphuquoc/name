<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Container\Container;
use Illuminate\Queue\Jobs\DatabaseJob;

class InternalFireJob extends Command {
    protected $signature = 'internal:job-fire {jobId}';
    protected $description = 'Nội bộ: Xử lý job';

    public function handle() {
        $jobId = $this->argument('jobId');
        $jobRecord = DB::table('jobs')->where('id', $jobId)->whereNull('reserved_at')->first();

        if (!$jobRecord) {
            $this->error("Job không tồn tại");
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

        try {
            $databaseJob->fire();
            return 0;
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }
}