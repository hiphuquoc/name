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
    @pushonce('scriptCustom')
        <script type="text/javascript">

            function chooseWallpaperForProductPrice(itemBox){
                const idWallpaper       = $(itemBox).data('wallpaper-id');
                const idProductPrice    = $(itemBox).data('product-price-id');
                var action              = 'create';
                if($(itemBox).hasClass('selected')) action = 'delete';
                $.ajax({
                    url: "{{ route('admin.productPrice.addWallpaperToProductPrice') }}",
                    type: "post",
                    dataType: 'json',
                    data: {
                        wallpaper_id : idWallpaper,
                        product_price_id : idProductPrice,
                        action
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                }).done(function (response) {
                    if(response){
                        if(action=='create'){
                            $(itemBox).addClass('selected');
                        }else if(action=='delete'){
                            $(itemBox).removeClass('selected');
                        }
                        loadWallpaperByProductPrice(idProductPrice);
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.error("Ajax request failed: " + textStatus, errorThrown);
                });
            }

            function searchWallpapers(input){
                const valueInput        = $(input).val();
                const idProductPrice    = $(input).data('product-price-id');
                $.ajax({
                    url: "{{ route('admin.wallpaper.searchWallpapers') }}",
                    type: "post",
                    dataType: 'html',
                    data: {
                        key_search : valueInput,
                        product_price_id : idProductPrice
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                }).done(function (response) {
                    const selectBox = $(input).parent().parent().find('.searchViewBefore_selectbox');
                    selectBox.html(response);
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.error("Ajax request failed: " + textStatus, errorThrown);
                });
            }

        </script>
    @endpushonce
@endif