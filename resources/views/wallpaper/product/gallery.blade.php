@if(!empty($prices)&&$prices->isNotEmpty())
    <!-- ảnh của all => đặt ẩn để addtocart -->
    <div style="display:none;">
        @php
            $imageAll = config('image.default');
            if(!empty($prices[0]->wallpapers[0]->infoWallpaper->file_cloud_wallpaper)){
                $imageAll = config('main.google_cloud_storage.default_domain').$prices[0]->wallpapers[0]->infoWallpaper->file_cloud_wallpaper;
            }
        @endphp
        <img src="{{ $imageAll }}" alt="{{ $title }}" title="{{ $title }}" data-option="js_addToCart_option_all" />
    </div>
    <!-- ảnh của từng biến thể -->
    <div class="galleryProductBox">
        @php
            $i = 0;
        @endphp
        @foreach($prices as $price)
            @foreach($price->wallpapers as $wallpaper)
                @if(!empty($wallpaper->infoWallpaper))
                    @php
                        /* lấy ảnh mini */
                        $imageMini      = \App\Helpers\Image::getUrlImageMiniByUrlImage($wallpaper->infoWallpaper->file_cloud_wallpaper);
                        $image          = config('main.google_cloud_storage.default_domain').$wallpaper->infoWallpaper->file_cloud_wallpaper;
                    @endphp
                    <div class="galleryProductBox_item">
                        {{-- @if($i<2)
                            <!-- thêm src và data-option để mô phỏng thẻ image dùng cho tính năng addToCart -->
                            <div class="galleryProductBox_item_backgroundImage" src="{{ $image }}" data-src="{{ $image }}" data-option="js_addToCart_option_{{ $price->id }}" onClick="toogleModalViewImageFull('{{ $i }}');" style="background:url('{{ $image }}') no-repeat center center / cover;"></div>
                        @else  --}}
                            <img class="lazyload" src="{{ $imageMini }}" data-src="{{ $image }}" alt="{{ $title }}" title="{{ $title }}" data-option="js_addToCart_option_{{ $price->id }}" onClick="toogleModalViewImageFull('{{ $i }}');" style="filter:blur(8px);" />
                        {{-- @endif --}}
                    </div>
                    @php
                        ++$i;
                    @endphp
                @endif
            @endforeach
        @endforeach
    </div>
    
@endif