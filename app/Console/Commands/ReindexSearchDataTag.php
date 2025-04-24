<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tag;

class ReindexSearchDataTag extends Command {

    protected $signature    = 'search:reindex-tag';
    protected $description  = 'Re-index all Tags into Meilisearch';

    public function handle() {
        $this->info('⏳ Bắt đầu re-index toàn bộ tags...');

        Tag::with(['seo', 'seos.infoSeo', 'categories.infoCategory', 'products.infoProduct', 'freeWallpapers.infoFreeWallpaper'])
            ->chunk(50, function ($tags) {
                $tags->each->searchable(); // Laravel Scout xử lý
                $this->info('✅ Đã index thêm ' . $tags->count() . ' tags');
            });

        $this->info('🎉 Hoàn tất re-index!');
    }

}
