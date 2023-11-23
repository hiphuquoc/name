@if(!empty($wallpaper)&&!empty($idProductPrice))
    @php
        $selected = !empty($selected)&&$selected==true ? 'selected' : '';
    @endphp
    <div class="searchViewBefore_selectbox_item {{ $selected }}" data-wallpaper-id="{{ $wallpaper->id }}" data-product-price-id="{{ $idProductPrice }}" onClick="chooseWallpaperForProductPrice(this);">
        <div class="searchViewBefore_selectbox_item_image">
            <img src="{{ $wallpaper->file_url_hosting }}" />
        </div>
        <div class="searchViewBefore_selectbox_item_content">
            <div class="searchViewBefore_selectbox_item_content_title maxLine_2">
                {{ $wallpaper->name }}
            </div>
            @if(!empty($wallpaper->priceUses)&&$wallpaper->priceUses->isNotEmpty())
                <div>Đã dùng <span style="font-weight:bold;font-size:1.2rem;color:#7367f0;">{{ $wallpaper->priceUses->count() }}</span></div>
            @endif
        </div>
        <div class="searchViewBefore_selectbox_item_message">
            <div><i class="fa-solid fa-check"></i> Đã chọn</div>
        </div>
        {{-- <div class="searchViewBefore_selectbox_item_cancel">
            <i class="fa-solid fa-xmark"></i>
        </div> --}}
    </div>
@endif