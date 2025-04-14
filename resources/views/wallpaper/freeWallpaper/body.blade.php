<div class="freeWallpaperDetailBox">
    <div class="freeWallpaperDetailBox_image">
        @php
            $imageMini      = \App\Helpers\Image::getUrlImageMiniByUrlImage($item->file_cloud);
            $imageLarge     = \App\Helpers\Image::getUrlImageLargeByUrlImage($item->file_cloud);
        @endphp
        <img class="lazyload" src="{{ $imageMini }}" data-src="{{ $imageLarge }}" alt="{{ $itemSeo->title }}" title="{{ $itemSeo->title }}" style="filter: blur(8px);" />
    </div>
    <div class="freeWallpaperDetailBox_content distanceBetweenSubbox">
        <h1 class="titlePage">{{ $itemSeo->title ?? null }}</h1>
        <!-- Action -->
        <div class="freeWallpaperDetailBox_content_action">
            @php
                $flagHeart = false;
                if(!empty($item->feeling)&&$item->feeling->type=='heart') $flagHeart = true;
            @endphp
            <div class="freeWallpaperDetailBox_content_action_item heart {{ $flagHeart==true ? 'selected' : '' }}" onclick="toogleHeartFeelingFreeWallpaper({{ $item->id }});">
                @php
                    $icon = file_get_contents('storage/images/svg/icon-heart-2.svg');
                @endphp
                {!! $icon !!}
                <span class="maxLine_1">Yêu thích</span>
            </div>
            <div class="freeWallpaperDetailBox_content_action_item">
                @php
                    $icon = file_get_contents('storage/images/svg/icon-share-1.svg');
                @endphp
                {!! $icon !!}
                <span class="maxLine_1">Chia sẻ</span>
            </div>
            <a href="{{ route('ajax.downloadImgFreeWallpaper', ['file_cloud' => $item->file_cloud]) }}" class="freeWallpaperDetailBox_content_action_item" download>
                @php
                    $icon = file_get_contents('storage/images/svg/icon_download.svg');
                @endphp
                {!! $icon !!}
                <span class="maxLine_1">Tải miễn phí</span>
            </a>
        </div>
        <!-- Content -->
        @include('wallpaper.freeWallpaper.content')
    </div>
</div>