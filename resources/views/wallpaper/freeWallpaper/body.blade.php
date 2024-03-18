<div class="freeWallpaperDetailBox">
    <div class="freeWallpaperDetailBox_image">
        @php
            $image = \App\Helpers\Image::getUrlImageLargeByUrlImage($item->file_cloud);
        @endphp
        <img src="{{ $image }}" alt="{{ $itemSeo->title }}" title="{{ $itemSeo->title }}" />
    </div>
    <div class="freeWallpaperDetailBox_content">
        <h1>{{ $itemSeo->title ?? null }}</h1>
        <!-- Action -->
        <div class="freeWallpaperDetailBox_content_action">
            @php
                $flagHeart = false;
                if(!empty($item->feeling)&&$item->feeling->type=='heart') $flagHeart = true;
            @endphp
            <div class="freeWallpaperDetailBox_content_action_item heart {{ $flagHeart==true ? 'selected' : '' }}" onclick="toogleHeartFeelingFreeWallpaper({{ $item->id }});">
                <i class="fa-solid fa-heart"></i><span class="maxLine_1">Yêu thích</span>
            </div>
            <div class="freeWallpaperDetailBox_content_action_item">
                <i class="fa-solid fa-share-nodes"></i><span class="maxLine_1">Chia sẻ bạn bè</span>
            </div>
            <a href="{{ route('ajax.downloadImgFreeWallpaper', ['file_cloud' => $item->file_cloud]) }}" class="freeWallpaperDetailBox_content_action_item" download>
                <i class="fa-solid fa-download"></i><span class="maxLine_1">Tải miễn phí</span>
            </a>
        </div>
        <!-- Content -->
        @include('wallpaper.freeWallpaper.content')
    </div>
</div>