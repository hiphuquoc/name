@php
    // /* background source */
    // $backgroundSource       = null;
    // if(!empty($wallpaper->file_name)){
    //     $url                = config('main.google_cloud_storage.sources').$wallpaper->file_name;
    //     $size               = 400;
    //     $response           = config('admin.images.default_750x460');
    //     $contentImage       = Storage::disk('gcs')->get($url);
    //     if(!empty($contentImage)){
    //         $thumbnail      = \Intervention\Image\ImageManagerStatic::make($contentImage)->resize($size, null, function ($constraint) {
    //             $constraint->aspectRatio();
    //         })->encode();
    //     }
    //     $base64Image        = 'data:image/jpeg;base64,'.base64_encode($thumbnail);
    //     $backgroundSource   = "background:url('".$base64Image."') no-repeat;background-size:100% auto;";
    // }
    // /* background wallpaper */
    // $backgroundWallpaper    = null;
    // if(!empty($wallpaper->file_cloud_wallpaper)){
    //     $backgroundWallpaper   = "background:url('".\App\Helpers\Image::getUrlImageSmallByUrlImage($wallpaper->file_cloud_wallpaper)."') no-repeat;background-size:100% auto;";
    // }
@endphp
<div class="formFreeWallpaperBox_item">
    <div id="js_addFormUpload_wallpaper_{{ $idBox }}" class="formFreeWallpaperBox_item_gallery">
        {{-- @if(!empty($wallpaper))
            <input class="form-control" type="file" name="wallpapers[]" onChange="readImageWhenChoose(this);" />
        @endif --}}
    </div>
    <div class="formFreeWallpaperBox_item_form">
        <div class="formBox">
            <div class="formBox_full">
                <div class="formBox_full_item">
                    <label class="form-label inputRequired" for="name">Alt ảnh</label>
                    <textarea class="form-control" name="name[{{ $idBox }}]" rows="1" required>{{ $wallpaper->name ?? null }}</textarea>
                </div>
                <div class="formBox_full_item">
                    <label class="form-label inputRequired" for="name">Alt ảnh EN</label>
                    <textarea class="form-control" name="en_name[{{ $idBox }}]" rows="1" required>{{ $wallpaper->en_name ?? null }}</textarea>
                </div>
                <div class="formBox_full_item">
                    <label class="form-label" for="description">Mô tả ngắn</label>
                    <textarea class="form-control" name="description[{{ $idBox }}]" rows="2"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>