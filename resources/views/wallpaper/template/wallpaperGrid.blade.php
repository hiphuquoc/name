<div id="js_loadMore_box" class="wallpaperGridBox">
    @if(!empty($products)&&$products->isNotEmpty())

        @if(!empty($viewBy)&&$viewBy=='set')
            @foreach($products as $product)
                @php
                    if($loop->index>4) break;
                    $lazyload   = false;
                    if($loop->index>4) $lazyload   = true;
                @endphp
                @include('wallpaper.template.wallpaperItem', [
                    'product'   => $product, 
                    'language'  => $language,
                    'lazyload'  => $lazyload
                ])
            @endforeach
        @else
            @foreach($products as $product)
                @php
                    $link           = empty($language)||$language=='vi' ? '/'.$product->seo->slug_full : '/'.$product->en_seo->slug_full;
                    $productName    = $product->name ?? null;
                    $lazyload       = false;
                    if($loop->index>4) $lazyload   = true;
                @endphp
                @foreach($product->prices as $prices)
                    @foreach($prices->wallpapers as $wallpaper)
                        @include('wallpaper.template.perWallpaperItem', [
                            'wallpaper' => $wallpaper, 
                            'productName'   => $productName,
                            'link'      => $link,
                            'language'  => $language,
                            'lazyload'  => $lazyload
                        ])
                    @endforeach
                @endforeach
            @endforeach
        @endif 
    @endif
    <!-- thông báo không có kết quả (cần thiêt) -->
    @if(!$products->isNotEmpty()&&!empty($contentEmpty))
        @if(empty($language)||$language=='vi')
            {{ config('main.message.vi.product_empty') }}
        @else 
            {{ config('main.message.en.product_empty') }}
        @endif
    @endif
</div>