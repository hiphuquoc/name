<!-- One Row -->
<div class="formBox_full_item">
    <span data-toggle="tooltip" data-placement="top" title="
        Đây là ảnh dùng làm slider hiển thị ở phần giới thiệu và phần ảnh đẹp của Tour
    ">
        <i class="explainInput" data-feather='alert-circle'></i>
        <label class="form-label">
            Ảnh Gallery 
            {{-- <span data-bs-toggle="modal" data-bs-target="#formModalDownloadImageHotelInfo" onClick="loadFormDownloadImageHotelInfo();" style="color:#26cf8e;font-size:1rem;">
                <i class="fa-solid fa-download"></i> Tải tự động
            </span> --}}
        </label>
    </span>
    <input class="form-control" type="file" id="galleries" name="galleries[]" onchange="readURL(this, 'galleryUpload');" multiple />
    <div class="imageUpload">
        @foreach($item->files as $file)
            @if($file->file_type=='gallery'&&$file->file_path)
                <img id="galleryUpload" src="{{ \App\Helpers\Image::getUrlImageSmallByUrlImage($file->file_path) }}" style="aspect-ratio:800/533;" />
            @endif
        @endforeach
    </div>
</div>

@push('scripts-custom')
    <script type="text/javascript">

        

    </script>
@endpush