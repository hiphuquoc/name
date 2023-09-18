<div class="wallpaperBox_item_source">
    <img data-google-cloud="{{ config('main.google_cloud_storage.sources').$item->file_name }}" data-size="400" />
    <div class="wallpaperBox_item_source_info">
        <div>{{ $item->width }}x{{ $item->height }} px - {{ round($item->file_size/(1024*1024), 2) }} Mb</div>
    </div>
</div>
<div class="wallpaperBox_item_wallpaper">
    <img src="{{ $item->file_url_hosting }}" />
    <div class="wallpaperBox_item_wallpaper_info">
        <div>{{ $item->width }}x{{ $item->height }} px - {{ round($item->file_size/(1024*1024), 2) }} Mb</div>
    </div>
</div>
<div class="wallpaperBox_item_info">
    <div>{{ $item->name }}</div>
    <div>{{ $item->file_name }}</div>
</div>
<div class="wallpaperBox_item_action">
    {{-- <!-- copy đường dẫn -->
    <div id="js_copyClipboard_anh-dep-tour-du-lich-tho-chu-11-750x460-type-manager-upload" data-bs-placement="top" data-bs-original-title="Đã copy ảnh!"><i class="fa-regular fa-folder-open"></i></div> --}}
    {{-- <!-- thay ảnh -->
    <div data-bs-toggle="modal" data-bs-target="#modalImage" onclick="loadModal('changeImage', 'anh-dep-tour-du-lich-tho-chu-11-750x460-type-manager-upload.webp');">
        <i class="fa-solid fa-arrow-right-arrow-left"></i>
        <div>Thay ảnh</div>
    </div> --}}
    <!-- thay tên ảnh -->
    <div data-bs-toggle="modal" data-bs-toggle="modal" data-bs-target="#modalFormWallpaper" onclick="loadModalUploadAndEdit({{ $item->id }});">
        <i class="fa-solid fa-pen-to-square"></i>
        <div>Chỉnh sửa</div>
    </div>
    <!-- xóa ảnh -->
    <div class="remove" onclick="deleteWallpaperAndSource('js_deleteWallpaperAndSource_{{ $item->id }}', {{ $item->id }});">
        <i class="fa-solid fa-trash-can"></i>
        <div>Xóa ảnh</div>
    </div>
</div>