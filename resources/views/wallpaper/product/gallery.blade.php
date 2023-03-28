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
                <div class="galleryProductBox_item">
                    @if($i<2)
                        {{-- <img src="{{ Storage::url($file->file_path) }}" alt="{{ $title }}" title="{{ $title }}" data-option="js_addToCart_option_{{ $price->id }}" onClick="toogleModalViewImageFull('{{ $loop->index }}');" /> --}}
                        <div class="galleryProductBox_item_backgroundImage" style="background:url('{{ Storage::url($file->file_path) }}') no-repeat center center / cover;"></div>
                    @else 
                        <img class="lazyload" data-src="{{ Storage::url($file->file_path) }}" alt="{{ $title }}" title="{{ $title }}" data-option="js_addToCart_option_{{ $price->id }}" onClick="toogleModalViewImageFull('{{ $loop->index }}');" />
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