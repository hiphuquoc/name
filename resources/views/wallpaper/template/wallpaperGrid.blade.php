<div id="js_loadMore_box" class="wallpaperGridBox">
    @if(!empty($viewBy)&&$viewBy=='set')
        @if(!empty($products)&&$products->isNotEmpty())
            @foreach($products as $product)
                @php
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
            <div>Hiện chưa có hình nền nào trong danh mục này!</div>
        @endif
    @else
        @if(!empty($products)&&$products->isNotEmpty())
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
        @else 
            <div>Hiện chưa có hình nền nào trong danh mục này!</div>
        @endif
    @endif
</div>