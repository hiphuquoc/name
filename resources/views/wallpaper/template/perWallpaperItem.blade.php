@if(!empty($wallpaper))
    @php
        /* lấy ảnh Small */
        $imageMini  = \App\Helpers\Image::getUrlImageMiniByUrlImage($wallpaper->infoWallpaper->file_url_hosting);
        $imageSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($wallpaper->infoWallpaper->file_url_hosting);
    @endphp
    <div class="wallpaperGridBox_item" data-key="" data-price="2">
        <div id="js_changeOption_1851790" class="show">
            <a href="{{ $link }}" class="wallpaperGridBox_item_image">
                <div class="zIndexHide">
                    {{-- <!-- xử lý loadajax -->
                    <div class="wallpaperGridBox_item_image_backgroundImage" style="background:url('/storage/images/upload/hinh-nen-dien-thoai-keo-deo-1695406328-8-small.webp') no-repeat center center / cover;"></div> --}}

                    @if(!empty($lazyload)&&$lazyload==true)
                        <img class="lazyload" src="{{ $imageMini }}" data-src="{{ $imageSmall }}" alt="{{ $productName }}" title="{{ $productName }}" style="filter:blur(8px);" />
                    @else 
                        <div class="wallpaperGridBox_item_image_backgroundImage" style="background:url('{{ $imageSmall }}') no-repeat center center / cover;" ></div>
                    @endif

                </div>
            </a>
        </div>
    </div>
@endif