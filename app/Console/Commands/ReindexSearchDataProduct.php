<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class ReindexSearchDataProduct extends Command {

    protected $signature    = 'search:reindex-product';
    protected $description  = 'Re-index all Products into Meilisearch';

    public function handle() {
        $this->info('⏳ Bắt đầu re-index toàn bộ sản phẩm...');

        Product::with(['seo', 'seos.infoSeo', 'tags.infoTag', 'categories.infoCategory'])
            ->chunk(50, function ($products) {
                $products->each->searchable(); // Laravel Scout xử lý
                $this->info('✅ Đã index thêm ' . $products->count() . ' sản phẩm');
            });

        $this->info('🎉 Hoàn tất re-index!');
    }

}
