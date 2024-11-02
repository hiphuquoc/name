@if(!empty($wallpaper->infoWallpaper))
    @php
        /* lấy ảnh Small */
        $imageMini  = \App\Helpers\Image::getUrlImageMiniByUrlImage($wallpaper->infoWallpaper->file_cloud_wallpaper);
        $imageSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($wallpaper->infoWallpaper->file_cloud_wallpaper);
    @endphp
    <div class="wallpaperGridBox_item" data-key="" data-price="2">
        <div id="js_changeOption_1851790" class="show">
            <a href="{{ $link }}" class="wallpaperGridBox_item_image" aria-label="{{ $productName }}">
                <div class="zIndexHide">
                    @if(!empty($lazyload)&&$lazyload==true)
                        <img class="lazyload" src="{{ $imageMini }}" data-src="{{ $imageSmall }}" alt="{{ $productName }}" title="{{ $productName }}" style="filter:blur(8px);" />
                    @else 
                        <div class="wallpaperGridBox_item_image_backgroundImage" style="background:url('{{ $imageSmall }}') no-repeat center center / cover;" ></div>
                    @endif

                </div>
                <div class="wallpaperGridBox_item_image_background"></div>
                <div class="wallpaperGridBox_item_image_backgroundTopMore"></div>
                <div class="wallpaperGridBox_item_image_backgroundSideMore"></div>
            </a>
        </div>
        <!-- thêm vào giỏ hành nhanh -->
        <div class="wallpaperGridBox_item_action">
            <div class="heart"></div>
            <div class="addToCart" onClick="addToCart('{{ $idProduct }}', '{{ $idPrice }}', 'per');"></div>
        </div>
    </div>
@endif