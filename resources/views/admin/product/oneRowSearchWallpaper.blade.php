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
            <div>
                {{ $wallpaper->file_name }}
            </div>
        </div>
        <div class="searchViewBefore_selectbox_item_message">
            <div><i class="fa-solid fa-check"></i> Đã chọn</div>
        </div>
        {{-- <div class="searchViewBefore_selectbox_item_cancel">
            <i class="fa-solid fa-xmark"></i>
        </div> --}}
    </div>
@endif