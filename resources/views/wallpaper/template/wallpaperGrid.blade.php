<div id="js_loadMore_box" class="wallpaperGridBox">
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
</div>