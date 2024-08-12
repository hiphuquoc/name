<!-- One Row -->
<div class="formBox_full_item">
    <span data-toggle="tooltip" data-placement="top" title="
        Đây là ảnh dùng làm slider hiển thị ở phần giới thiệu và phần ảnh đẹp của Tour
    ">
        <i class="explainInput" data-feather='alert-circle'></i>
        <label class="form-label">
            Ảnh Gallery 
        </label>
    </span>
    <input class="form-control" type="file" id="galleries" name="galleries[]" onchange="readURL(this, 'galleryUpload');" multiple />
    <div class="imageUpload">
        @foreach($item->files as $file)
            @if($file->file_type=='gallery'&&$file->file_path)
                <div id="js_removeGallery_{{ $file->id }}">
                    <img id="galleryUpload" src="{{ \App\Helpers\Image::getUrlImageSmallByUrlImage($file->file_path) }}" />
                    <i class="fa-solid fa-circle-xmark" onclick="removeGallery({{ $file->id }});"></i>
                </div>
            @endif
        @endforeach
    </div>
</div>

@push('scriptCustom')
    <script type="text/javascript">

        function removeGallery(idFile){
            $.ajax({
                url         : '{{ route("admin.gallery.remove") }}',
                type        : 'post',
                dataType    : 'html',
                data        : {
                    "_token": "{{ csrf_token() }}",
                    id_file : idFile
                }
            }).done(function(data){
                if(data) $('#js_removeGallery_'+idFile).remove();
            })
        }

    </script>
@endpush