<div class="cartBox">
    <a href="{{ $urlPageCart ?? '/' }}" class="cartBox_show">
        <div class="cartBox_show_icon">
            <img src="{{ Storage::url('images/svg/icon-cart-header.svg') }}" alt="{{ config('language.'.$language.'.data.cart') }}" title="{{ config('language.'.$language.'.data.cart') }}" />
            <div id="js_updateCart_count" class="cartBox_show_icon_number">
                {{ $detailCart['count'] ?? 0 }}
            </div>
        </div>
        <div class="cartBox_show_text">{{ config('language.'.$language.'.data.cart') }}</div>
    </a>
    <div id="js_checkEmptyCart_idWrite" class="cartBox_list">
        @if(!empty($products)&&$products->isNotEmpty())
            <div class="customScrollBar-y">
                @foreach($products as $product)
                    <!-- trường hợp trọn bộ -->
                    @if(count($product->cart['product_price_id'])>=$product->prices->count())
                        @php
                            $arrayProductPrice  = $product->cart['product_price_id'];
                            $keyId              = !empty($product->id) ? $product->id.implode('-', $product->cart['product_price_id']) : null;
                        @endphp
                        <div id="{{ 'js_updateCart_idWrite_'.$keyId }}" class="cartBox_list_item">
                            @include('wallpaper.cart.cartSortRow', compact('product', 'arrayProductPrice','language'))
                        </div>
                    @else 
                        @foreach($product->cart['product_price_id'] as $p)
                            @php
                                $arrayProductPrice  = [$p];
                                $keyId              = !empty($product->id) ? $product->id.$p : null;
                            @endphp
                            <div id="{{ 'js_updateCart_idWrite_'.$keyId }}" class="cartBox_list_item">
                                @include('wallpaper.cart.cartSortRow', compact('product', 'arrayProductPrice', 'language'))
                            </div>
                        @endforeach
                    @endif


                @endforeach
            </div>
            <div class="cartBox_list_item buttonBox">
                <div class="total">{{ config('language.'.$language.'.data.total') }}: <span id="js_updateCart_total">{!! \App\Helpers\Number::getFormatPriceByLanguage($detailCart['total'], $language) !!}</span></div>
                <a href="{{ $urlPageCart ?? '/' }}" class="button">{{ config('language.'.$language.'.data.view_cart') }}</a>
            </div>
        @else
            @include('wallpaper.cart.emptyCart', compact('language'))
        @endif
        
    </div>
    <div class="cartBox_background"></div>
</div>