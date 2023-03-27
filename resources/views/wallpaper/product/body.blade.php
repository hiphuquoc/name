@php
    $title = $item->name ?? $item->seo->title ?? null;
@endphp
<!-- Product Detail Box -->
<div class="pageProductDetailBox">
    <div class="pageProductDetailBox_left">
        @php
            $flagHasImage = false;
            foreach($item->prices as $price){
                foreach($price->files as $file){
                    if(file_exists(Storage::path($file->file_path))) {
                        $flagHasImage = true;
                        break;
                    }
                }
            }
        @endphp
        @if($flagHasImage)
            <!-- Gallery Desktop -->
            @include('wallpaper.product.gallery', ['prices' => $item->prices])
        @endif
        <!-- giải thích thanh toán và nhận ảnh => desktop -->
        <div class="show-1199">
            <div>Sau khi thanh toán bằng <span class="highLight">Momo /Zalopay</span>, Bạn sẽ nhận được link ảnh gốc để có thể tải xuống và sử dụng.</div>
        </div>
        <!-- Content -->
        @include('main.product.content', ['contents' => $item->contents])
    </div>
    <div class="pageProductDetailBox_right">
        <div class="productDetailBox">
            <div class="productDetailBox_detail">
                <!-- id hidden -->
                <input type="hidden" id="product_info_id" name="product_info_id" value="{{ $item->id ?? null }}" />
                <!-- tiêu đề -->
                <h1 class="productDetailBox_detail_title">{{ $title }}</h1>
                <!-- đánh giá -->
                <div class="productDetailBox_detail_rating">
                    <div class="ratingBox">
                        @if(!empty($item->sold))
                            <div class="ratingBox_numberSell">
                                Đã bán <span>{{ $item->sold }}</span>
                            </div>
                        @endif
                        @if(!empty($item->seo->rating_aggregate_star)&&!empty($item->seo->rating_aggregate_count))
                            <div class="ratingBox_star">
                                <div class="ratingBox_star_box">
                                    <span class="ratingBox_star_box_on"><i class="fas fa-star"></i></span>
                                    <span class="ratingBox_star_box_on"><i class="fas fa-star"></i></span>
                                    <span class="ratingBox_star_box_on"><i class="fas fa-star"></i></span>
                                    <span class="ratingBox_star_box_on"><i class="fas fa-star"></i></span>
                                    <span class="ratingBox_star_box_on"><i class="fas fa-star-half-alt"></i></span>
                                </div>
                                <div class="ratingBox_star_total">
                                    <span>{{ $item->seo->rating_aggregate_star }}</span> sao/<span>{{ $item->seo->rating_aggregate_count }}</span> đánh giá
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <!-- mô tả sản phẩm -->
                @if(!empty($item->description))
                    <div id="js_viewMoreContent_content" class="productDetailBox_detail_description">
                        {!! $item->description !!}
                    </div>
                @endif
                <!-- option -->
                <div class="productDetailBox_detail_option hide-1199">
                    @include('wallpaper.product.optionBox', compact('item'))
                </div>
                <!-- giá -->
                <div class="productDetailBox_detail_price hide-1199">
                    @include('wallpaper.product.priceBox', ['prices' => $item->prices])
                </div>
                <!-- button đặt hàng -->
                <div class="productDetailBox_detail_checkout">
                    {{-- <div class="productDetailBox_detail_checkout_qty">
                        <div class="inputQty">
                            <div class="inputQty_button minus" onClick="plusMinusQuantity('js_addToCart_quantity', 'minus');"><i class="fa-solid fa-minus"></i></div>
                            <input id="js_addToCart_quantity" type="number" name="quantity" value="1" />
                            <div class="inputQty_button plus" onClick="plusMinusQuantity('js_addToCart_quantity', 'plus');"><i class="fa-solid fa-plus"></i></div>       
                        </div>                 
                    </div> --}}
                    <div class="productDetailBox_detail_checkout_button" style="margin-left:0;">
                        <div class="show-1199">
                            <!-- dùng giá option đầu tiên -->
                            <div class="priceMobile">{!! !empty($item->prices[0]->price)&&is_int($item->prices[0]->price) ? number_format($item->prices[0]->price).config('main.currency_unit') : '-' !!}</div>
                        </div>
                        <button type="button" class="button secondary maxLine_1" onClick="addToCart();" aria-label="Thêm giỏ hàng">
                            <img src="{{ Storage::url('images/svg/shopping-cart.png') }}" alt="thêm giỏ hàng" title="thêm giỏ hàng" />
                            <div>Thêm giỏ hàng</div>
                        </button>
                        <button type="button" class="button maxLine_1" onClick="openCloseModal('modalPaymentMethod');" aria-label="Mua ngay">
                            <img src="{{ Storage::url('images/svg/money-bill-wave.png') }}" alt="mua ngay" title="mua ngay" />
                            <div>Mua ngay</div>
                        </button>
                    </div>
                </div>
                <!-- giải thích thanh toán và nhận ảnh => desktop -->
                <div class="hide-1199">
                    <div>Sau khi thanh toán bằng <span class="highLight">Momo /Zalopay</span>, Bạn sẽ nhận được link ảnh gốc để có thể tải xuống và sử dụng.</div>
                </div>
            </div>
        </div>
    </div>
</div>