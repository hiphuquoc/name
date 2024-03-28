<!-- Product Detail Box -->
<div class="pageProductDetailBox">
    <div class="pageProductDetailBox_left">
        <!-- Gallery Desktop -->
        @include('wallpaper.product.gallery', ['prices' => $item->prices])
        {{-- <!-- giải thích thanh toán và nhận ảnh => desktop -->
        <div class="show-1199">
            
        </div> --}}
        <!-- Nội dung -->
        @php
            $xhtmlContent = '';
            foreach($itemSeo->contents as $content) $xhtmlContent .= $content->content;
        @endphp
        <div class="contentBox" style="margin-bottom:0 !important;">
            {!! $xhtmlContent !!}
        </div>
    </div>
    <div class="pageProductDetailBox_right">
        <div class="productDetailBox">
            <div class="productDetailBox_detail">
                <!-- id hidden -->
                <input type="hidden" id="product_info_id" name="product_info_id" value="{{ $item->id ?? null }}" />
                <!-- tiêu đề -->
                <h1 class="productDetailBox_detail_title">{{ $itemSeo->title }}</h1>
                <!-- yêu thích -->
                @php
                    $countHeart = 0;
                    foreach($item->prices as $price){
                        foreach($price->wallpapers as $wallpaper) $countHeart += $wallpaper->heart;
                    }
                @endphp
                @if($countHeart>0)
                    <div class="showFeelingBox">
                        {!! file_get_contents(public_path('storage/images/svg/icon-heart-2.svg')) !!}
                        <div>{{ $countHeart }} yêu thích</div>
                    </div>
                @endif
                <!-- mô tả sản phẩm -->
                <div id="js_viewMoreContent_content" class="productDetailBox_detail_description">
                    {!! config('language.'.$language.'.data.product_description') !!}
                </div>
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
                            @php
                                $priceMobile    = \App\Helpers\Number::getFormatPriceByLanguage($item->price, $language);
                            @endphp
                            <div class="priceMobile">{!! $priceMobile !!}</div>
                        </div>
                        @php
                            $buttonNameCart     = config('language.'.$language.'.data.add_to_cart');
                            $buttonNamePayment  = config('language.'.$language.'.data.buy_now');
                            /* chuyển array price sang key all */
                            $keyPriceAll        = [];
                            foreach($item->prices as $price) $keyPriceAll[]  = $price->id;
                            $keyPriceAll        = implode('-', $keyPriceAll);
                        @endphp
                        <button id="js_addToCart_button" type="button" class="button secondary maxLine_1" onClick="addToCart('{{ $item->id }}', '{{ $keyPriceAll }}', 'all');" aria-label="{{ $buttonNameCart }}">
                            <img src="{{ Storage::url('images/svg/shopping-cart.png') }}" alt="{{ $buttonNameCart }}" title="{{ $buttonNameCart }}" />
                            <div>{{ $buttonNameCart }}</div>
                        </button>
                        <button type="button" class="button maxLine_1" onClick="openCloseModal('modalPaymentMethod');" aria-label="{{ $buttonNamePayment }}">
                            <img src="{{ Storage::url('images/svg/money-bill-wave.png') }}" alt="{{ $buttonNamePayment }}" title="{{ $buttonNamePayment }}" />
                            <div>{{ $buttonNamePayment }}</div>
                        </button>
                    </div>
                </div>
                <!-- giải thích thanh toán và nhận ảnh => desktop -->
                <div class="hide-1199">
                    {!! config('language.'.$language.'.data.product_guide_after_payment') !!}
                </div>
            </div>
        </div>
    </div>
</div>