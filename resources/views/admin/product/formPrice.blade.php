<div class="card" data-repeater-item>
    <div class="card-header border-bottom">
        <h4 class="card-title">
            Phiên bản của sản phẩm
            <i class="fa-solid fa-circle-xmark" data-repeater-delete></i>
        </h4>
    </div>
    <div class="card-body">
        <input type="hidden" name="id" value="{{ $price['id'] ?? null }}" />
        <div class="formBox">

            <div class="formBox_full flexBox">
                <div class="flexBox_item">
                    <div class="formBox_full_item">
                        <label class="form-label inputRequired" for="name">Tiêu đề</label>
                        <input class="form-control" name="name" type="text" value="{{ $price['name'] ?? null }}" required />
                    </div>
                </div>
                <div class="flexBox_item">
                    <div class="formBox_full_item">
                        <label class="form-label inputRequired" for="en_name">Title (En)</label>
                        <input class="form-control" name="en_name" type="text" value="{{ $price['en_name'] ?? null }}" required />
                    </div>
                </div>
            </div>
            
            <div class="formBox_full flexBox">
                <div class="flexBox_item">
                    <div class="formBox_full_item">
                        <label class="form-label inputRequired" for="price">Giá bán $</label>
                        <input class="form-control" name="price" type="number" value="{{ $price['price'] ?? null }}" required />
                    </div>
                </div>
                <div class="flexBox_item">
                    <div class="formBox_full_item">
                        <label class="form-label" for="price_before_promotion">Giá trước KM $</label>
                        <input class="form-control" name="price_before_promotion" type="number" value="{{ $price['price_before_promotion'] ?? null }}" />
                    </div>
                </div>
            </div>
            
            @if(!empty($price->id)&&$type=='edit') 
                <div id="js_loadWallpaperByProductPrice_{{ $price->id }}" class="formBox_full">
                    <!-- load Ajax -->
                </div>
            @endif

        </div>
    </div>
</div>

@if(!empty($price->id)&&$type=='edit')
    <!-- form modal chọn wallpaper -->
    <form id="formSearchWallpapers" method="POST" action="#">
    @csrf
    <div class="modal fade" id="formModal_{{ $price->id }}" tabindex="-1" aria-labelledby="addNewCardTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="margin:0 auto;">
            <div class="modal-content">
                <div class="modal-body">
                    
                    <div class="searchViewBefore">
                        <div class="searchViewBefore_input">
                            <!-- value = null không lưu giá trị search cũ -->
                            <input type="text" placeholder="Tìm wallpaper..." value="" data-product-price-id="{{ $price->id }}" onkeyup="searchWallpapers(this)" autocomplete="off" />
                            <div>
                                <img src="/storage/images/svg/search.svg" alt="" title="Tìm kiếm hình nền điện thoại">
                            </div>
                        </div>
                        <div class="searchViewBefore_selectbox">
                            {{-- @foreach($wallpapers as $wallpaper)
                                @php
                                    $selected       = false;
                                    foreach($price->wallpapers as $w){
                                        if($wallpaper->id==$w->infoWallpaper->id) $selected = 'selected';
                                    }
                                @endphp
                                @include('admin.product.oneRowSearchWallpaper', [
                                    'wallpaper'         => $wallpaper,
                                    'idProductPrice'    => $price->id,
                                    'selected'          => $selected
                                ])
                            @endforeach --}}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </form>
@endif
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