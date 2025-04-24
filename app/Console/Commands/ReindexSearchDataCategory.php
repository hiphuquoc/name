<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;

class ReindexSearchDataCategory extends Command {

    protected $signature    = 'search:reindex-category';
    protected $description  = 'Re-index all Categories into Meilisearch';

    public function handle() {
        $this->info('⏳ Bắt đầu re-index toàn bộ categories...');

        Category::with(['seo', 'seos.infoSeo', 'tags.infoTag', 'products.infoProduct', 'freeWallpapers.infoFreeWallpaper'])
            ->chunk(50, function ($categories) {
                $categories->each->searchable(); // Laravel Scout xử lý
                $this->info('✅ Đã index thêm ' . $categories->count() . ' categories');
            });

        $this->info('🎉 Hoàn tất re-index!');
    }

}
