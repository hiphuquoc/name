<div class="distanceBetweenSubbox">
    <!-- breadcrumb -->
    @include('wallpaper.template.breadcrumb')

    <!-- Product Detail Box -->
    <div class="pageProductDetailBox">
        <div class="pageProductDetailBox_left distanceBetweenBox">
            <!-- Gallery Desktop -->
            @include('wallpaper.product.gallery', ['prices' => $item->prices])
            {{-- <!-- giải thích thanh toán và nhận ảnh => desktop -->
            <div class="show-991">
                
            </div> --}}
            <!-- Nội dung -->
            @php
                $xhtmlContent = '';
                foreach($itemSeo->contents as $content) $xhtmlContent .= $content->content;
            @endphp
            <!-- một thẻ div bao bọc để tránh bị display: flex; gap... -->
            <div class="contentBox">
                {!! $xhtmlContent !!}
            </div>
        </div>
        <div class="pageProductDetailBox_right">
            <div class="productDetailBox">
                <div class="productDetailBox_detail distanceBetweenSubbox">
                    <!-- id hidden -->
                    <input type="hidden" id="product_info_id" name="product_info_id" value="{{ $item->id ?? null }}" />
                    <!-- tiêu đề -->
                    <h1 class="titlePage">{{ $itemSeo->title }}</h1>
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
                            <div>{{ 200 }} yêu thích</div>
                        </div>
                    @endif
                    <div class="distanceBetweenBox">
                        <!-- mô tả sản phẩm -->
                        <div id="js_viewMoreContent_content" class="productDetailBox_detail_description">
                            {!! config('language.'.$language.'.data.product_description.'.env('APP_NAME')) !!}
                        </div>

                        <!-- option -->
                        <div class="productDetailBox_detail_option hide-991">
                            @include('wallpaper.product.optionBox', compact('item'))
                        </div>
                        <!-- giá -->
                        <div id="js_loadProductPrice" class="productDetailBox_detail_price hide-991">
                            <!-- load ajax (loadProductPrice) nên truyền collection rỗng -->
                            @include('wallpaper.product.priceBox', [
                                'item'      => new \Illuminate\Database\Eloquent\Collection,
                                'prices'    => new \Illuminate\Database\Eloquent\Collection,
                            ])
                        </div>

                        <!-- button đặt hàng -->
                        <div class="productDetailBox_detail_checkout">
                            <div class="productDetailBox_detail_checkout_button">
                                <div class="show-991">
                                    <!-- load ajax (loadProductPrice) -->
                                    <div id="js_loadProductPrice_priceAllMobile" class="priceMobile">--</div>
                                </div>
                                @php
                                    $buttonNameCart     = config('language.'.$language.'.data.add_to_cart');
                                    $altCart            = \App\Helpers\Charactor::concatenateWords([$buttonNameCart, $itemSeo->title], $language);
                                    $buttonNamePayment  = config('language.'.$language.'.data.buy_now');
                                    $altPayment         = \App\Helpers\Charactor::concatenateWords([$buttonNamePayment, $itemSeo->title], $language);
                                    /* chuyển array price sang key all */
                                    $keyPriceAll        = [];
                                    foreach($item->prices as $price) $keyPriceAll[]  = $price->id;
                                    $keyPriceAll        = implode('-', $keyPriceAll);
                                @endphp
                                <button id="js_addToCart_button" type="button" class="button secondary" onClick="addToCart('{{ $item->id }}', '{{ $keyPriceAll }}', 'all');" aria-label="{{ $altCart }}">
                                    <img src="{{ Storage::url('images/svg/shopping-cart.png') }}" alt="{{ $altCart }}" title="{{ $altCart }}" />
                                    <div class="maxLine_1">{{ $buttonNameCart }}</div>
                                </button>
                                <button type="button" class="button" onClick="openCloseModal('modalPaymentMethod');" aria-label="{{ $altPayment }}">
                                    <img src="{{ Storage::url('images/svg/money-bill-wave.png') }}" alt="{{ $altPayment }}" title="{{ $altPayment }}" />
                                    <div class="maxLine_1">{{ $buttonNamePayment }}</div>
                                </button>
                            </div>
                        </div>
                        <!-- giải thích thanh toán và nhận ảnh => desktop -->
                        <div class="hide-991">
                            {!! config('language.'.$language.'.data.product_guide_after_payment') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>