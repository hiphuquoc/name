@if(!empty($prices))

    {{-- <!-- ảnh của all => đặt ẩn để addtocart -->
    <div style="display:none;">
        <img src="{{ Storage::url($prices[0]->files[0]->file_path) }}" alt="{{ $title }}" title="{{ $title }}" data-option="js_addToCart_option_all" />
    </div> --}}
    <!-- ảnh của từng biến thể -->
    <div class="galleryProductBox">
        @php
            $i = 0;
        @endphp
        @foreach($prices as $price)
            @foreach($price->files as $file)
                @php
                    /* lấy ảnh mini */
                    $fileInfo       = pathinfo($file->file_path);
                    $imageMini      = Storage::url($fileInfo['dirname'].'/'.$fileInfo['filename'].'-mini'.'.'.$fileInfo['extension']);
                    $imageMiddle    = Storage::url($fileInfo['dirname'].'/'.$fileInfo['filename'].'-middle'.'.'.$fileInfo['extension']);
                @endphp
                <div class="galleryProductBox_item">
                    @if($i<4)
                        <!-- thêm src và data-option để mô phỏng thẻ image dùng cho tính năng addToCart -->
                        <div class="galleryProductBox_item_backgroundImage lazyload" src="{{ $imageMiddle }}" data-src="{{ $imageMiddle }}" data-option="js_addToCart_option_{{ $price->id }}" onClick="toogleModalViewImageFull('{{ $loop->index }}');" style="background:url('{{ $imageMini }}') no-repeat center center / cover;filter:blur(10px);"></div>
                    @else 
                        <img class="lazyload" data-src="{{ $imageMiddle }}" alt="{{ $title }}" title="{{ $title }}" data-option="js_addToCart_option_{{ $price->id }}" onClick="toogleModalViewImageFull('{{ $loop->index }}');" />
                    @endif
                    {{-- <div class="galleryProductBox_item_note">{{ $price->name }}</div> --}}
                </div>
                @php
                    ++$i;
                @endphp
            @endforeach
        @endforeach
    </div>
    
@endif