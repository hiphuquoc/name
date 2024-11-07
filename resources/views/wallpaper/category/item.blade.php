@php
    $itemSeoWallpaper = null;
    foreach($wallpaper->seos as $seo){
        if($seo->infoSeo->language==$language){
            $itemSeoWallpaper   = $seo->infoSeo;
            break;
        }
    }
    $altImage    = $itemSeoWallpaper->title ?? $wallpaper->seo->title ?? null;
    /* lấy ảnh Small */
    $imageMini  = \App\Helpers\Image::getUrlImageMiniByUrlImage($wallpaper->file_cloud);
    $imageSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($wallpaper->file_cloud);
    // $imageLarge = \App\Helpers\Image::getUrlImageLargeByUrlImage($wallpaper->file_cloud);
    // Tính tỷ lệ và số hàng cần chiếm (chỉ tính tương đối để không nhảy hàng - có hàm khác bằng jquery tính và cập nhật lại)
    $aspectRatio = $wallpaper->height / $wallpaper->width;
    $imageHeight = $aspectRatio * 280; // Giả sử ảnh hiển thị rộng 280px
    $rows = ceil($imageHeight / 55); // Tính số hàng cần chiếm 40 của grid-auto-rows và 15 của gap
@endphp
<div id="js_calculatorPosition_item_{{ $wallpaper->id }}" class="freeWallpaperBox_item" data-id="{{ $wallpaper->id }}" data-width="{{ $wallpaper->width }}" data-height="{{ $wallpaper->height }}" style="grid-row-end: span {{ $rows }};">
    <div class="freeWallpaperBox_item_image">
        <picture>
            <source media="(max-width: 767px)" srcset="{{ $imageSmall }}">
            <img class="lazyload" 
                src="{{ $imageMini }}" 
                data-src="{{ $imageSmall }}" 
                alt="{{ $altImage }}" 
                title="{{ $altImage }}" 
                loading="lazy" 
            />
        </picture>
        
    </div>
    <div class="freeWallpaperBox_item_box">
        <div class="freeWallpaperBox_item_box_item">
            <div class="author">
                <div class="author_image">
                    <img src="https://name.com.vn/storage/images/upload/logo-type-manager-upload.webp" alt="wallpaperdienthoai@gmail.com" title="wallpaperdienthoai@gmail.com" />
                </div>
                <div class="author_name maxLine_1">
                    Admin Name
                </div>
            </div>
        </div>
        
        <div class="freeWallpaperBox_item_box_item" style="min-width:132px;">
            <!-- feeling -->
            <div class="feeling">
                @foreach(config('main_'.env('APP_NAME').'.feeling_type') as $feeling)
                    @if(!empty($user->id))
                        <div class="feeling_item" onclick="setFeelingFreeWallpaper(this, {{ $wallpaper->id }}, '{{ $feeling['key'] }}');">
                            {!! file_get_contents(public_path($feeling['icon'])) !!}
                        </div>
                    @else 
                        <div class="feeling_item" onclick="toggleModalCustomerLoginForm('modalLoginFormCustomerBox');">
                            {!! file_get_contents(public_path($feeling['icon'])) !!}
                        </div>
                    @endif
                @endforeach
            </div>
            <!-- action -->
            <div class="action">
                {{-- <a href="{{ route('search.searchByImage', ['free_wallpaper_info_id' => $wallpaper->id]) }}" class="action_item"> --}}
                @if(!empty($wallpaper->seo))
                    <a href="/{{ $itemSeoWallpaper->slug_full ?? $wallpaper->seo->slug_full }}" class="action_item" aria-label="viewmore">
                        <i class="fa-solid fa-image"></i>
                    </a>
                @endif
                <div class="action_item" onclick="showBoxFeeling(this);">
                    <i class="fa-regular fa-thumbs-up"></i>
                </div>
                <a class="action_item download" href="{{ route('ajax.downloadImgFreeWallpaper', ['file_cloud' => $wallpaper->file_cloud]) }}" download aria-label="download">
                    <i class="fa-solid fa-download"></i>
                </a>
                
            </div>
        </div>
    </div>
    @if(!empty($wallpaper->feeling))
        @php
            $icon = null;
            foreach(config('main_'.env('APP_NAME').'.feeling_type') as $feeling){
                if($wallpaper->feeling->type==$feeling['key']) $icon = $feeling['icon'];
            }
        @endphp
        <div class="freeWallpaperBox_item_icon">
            {!! file_get_contents(public_path($icon)) !!}
        </div>
    @endif
    @if(!empty($wallpaper->seo))
        <a href="/{{ $itemSeoWallpaper->slug_full ?? $wallpaper->seo->slug_full }}" class="freeWallpaperBox_item_preventClick" aria-label="{{ $altImage }}"></a>
    @else 
        <div class="freeWallpaperBox_item_preventClick"></div>
    @endif
</div>