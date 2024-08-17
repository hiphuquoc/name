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
    {{-- <input class="form-control" type="file" id="galleries" name="galleries[]" onchange="readURL(this, 'galleryUpload');" multiple /> --}}
    <div class="imageUpload" style="margin-top:0;">
        @if(!empty($item->thumnails)&&$item->thumnails->count()>0)
            @foreach($item->thumnails as $thumnail)
                @if(!empty($thumnail->infoFreewallpaper))
                    <div id="js_removeThumnailsOfCategory_{{ $thumnail->infoFreewallpaper->id }}">
                        <img id="galleryUpload" src="{{ \App\Helpers\Image::getUrlImageSmallByUrlImage($thumnail->infoFreewallpaper->file_cloud) }}" />
                        <i class="fa-solid fa-circle-xmark" onclick="removeThumnailsOfCategory({{ $thumnail->infoFreewallpaper->id }}, {{ $item->id }});"></i>
                    </div>
                    @endif
            @endforeach
        @endif
    </div>
</div>

@push('scriptCustom')
    <script type="text/javascript">

        function removeThumnailsOfCategory(idWallpaper, idCategory){
            $.ajax({
                url         : '{{ route("admin.category.removeThumnailsOfCategory") }}',
                type        : 'get',
                dataType    : 'html',
                data        : {
                    "_token": "{{ csrf_token() }}",
                    free_wallpaper_info_id  : idWallpaper,
                    category_info_id        : idCategory,
                }
            }).done(function(data){
                if(data) $('#js_removeThumnailsOfCategory_'+idWallpaper).remove();
            })
        }

    </script>
@endpush