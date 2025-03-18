<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\Tmp;

class RunTmpJob extends Command {
    
    protected $signature = 'run:tmp-job';

    protected $description = 'Chạy job Tmp thông qua command';

    public function handle() {
        // Gọi job Tmp để thực thi
        Tmp::dispatch();

        $this->info('Đã thực hiện job Tmp thành công!');
        return 0;
    }
}
