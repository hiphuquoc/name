<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Blog;

class ReindexSearchDataBlog extends Command {

    protected $signature    = 'search:reindex-blog';
    protected $description  = 'Re-index all Blogs into Meilisearch';

    public function handle() {
        $this->info('⏳ Bắt đầu re-index toàn bộ blogs...');

        Blog::with(['seo', 'seos.infoSeo', 'categories.infoCategory'])
            ->chunk(50, function ($blogs) {
                $blogs->each->searchable(); // Laravel Scout xử lý
                $this->info('✅ Đã index thêm ' . $blogs->count() . ' blogs');
            });

        $this->info('🎉 Hoàn tất re-index!');
    }

}
