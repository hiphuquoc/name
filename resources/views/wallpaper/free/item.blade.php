@php
    $altImage = empty($language)||$language=='vi' ? $wallpaper->name : $wallpaper->en_name;
@endphp
<div id="js_calculatorPosition_item_{{ $wallpaper->id }}" class="freeWallpaperBox_item" data-id="{{ $wallpaper->id }}">
    <img class="lazyload" src="{{ \App\Helpers\Image::getUrlImageMiniByUrlImage($wallpaper->file_cloud) }}" data-src="{{ config('main.google_cloud_storage.default_domain').$wallpaper->file_cloud }}" alt="{{ $altImage }}" title="{{ $altImage }}" />
</div>