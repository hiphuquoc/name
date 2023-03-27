@if(!empty($item))
    @php
        $infoSize       = getimagesize($item);
        $infoImage      = pathinfo($item);
        $fileSize       = round(filesize($item)/1024, 2);
        $urlImage       = Storage::url(config('image.folder_upload').basename($item));
        /* phân loại ảnh */
        $arrayAction    = \App\Helpers\Image::getActionImageByType($item);
        /* chức năng copy to clipboard */
        $idContent      = 'js_copyToClipboard_content_'.rand(0, 1000000);
    @endphp
    <div id="{{ $infoImage['filename'] }}" class="imageBox_item" style="{{ $style ?? null }}">
        <div class="imageBox_item_image">
            <img src="{{ $urlImage }}?{{ time() }}" alt="" title="" />
        </div>
        <div class="imageBox_item_content">
            <div class="imageBox_item_content_text">
                <textarea id="{{ $idContent }}" cols="2" style="margin-bottom:0.5rem;background:transparent;border:none;width:100%;resize:none;" disabled>{{ $urlImage }}</textarea>
                <div>width: {{ !empty($infoSize[0]) ? $infoSize[0].'px' : '-' }}</div>
                <div>height: {{ !empty($infoSize[1]) ? $infoSize[1].'px' : '-' }}</div>
                <div>size: {{ $fileSize }}kb</div>
            </div>
            <div class="imageBox_item_content_action">
                <!-- copy đường dẫn -->
                @if(in_array('copy_url', $arrayAction))
                    <div data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Copy ảnh" onClick="copyToClipboard('{{ $idContent }}')"><i class="fa-regular fa-copy"></i></div>
                @else 
                    <i class="fa-regular fa-copy"></i>
                @endif
                <!-- thay ảnh -->
                @if(in_array('change_image', $arrayAction))
                    <div data-bs-toggle="modal" data-bs-target="#modalImage" onClick="loadModal('changeImage', '{{ basename($item) }}');"><i class="fa-solid fa-arrow-right-arrow-left"></i></div>
                @else 
                    <div class="disable" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Ảnh này không được phép dùng chức năng này!"><i class="fa-solid fa-arrow-right-arrow-left"></i></div>
                @endif
                <!-- thay tên ảnh -->
                @if(in_array('change_name', $arrayAction))
                    <div data-bs-toggle="modal" data-bs-target="#modalImage" onClick="loadModal('changeName', '{{ basename($item) }}');"><i class="fa-solid fa-pen-to-square"></i></div>
                @else 
                    <div class="disable" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Ảnh này không được phép dùng chức năng này!"><i class="fa-solid fa-pen-to-square"></i></div>
                @endif
                <!-- xóa ảnh -->
                @if(in_array('delete', $arrayAction))
                    <div class="remove" onClick="removeImage('{{ $infoImage['filename'] }}', '{{ basename($item) }}');"><i class="fa-solid fa-trash-can"></i></div>
                @else 
                    <div class="disable" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Ảnh này không được phép dùng chức năng này!"><i class="fa-solid fa-trash-can"></i></div>
                @endif
            </div>
        </div>
    </div>
    {{-- @pushonce('scriptCustom')
        <script type="text/javascript">
            function tests(elemt){
                
                $(elemt).html();
                console.log($(elemt).html());
                $(elemt).tooltip();
            }

        </script>
    @endpushonce --}}
@endif