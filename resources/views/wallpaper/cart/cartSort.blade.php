@php
    $urlCart    = route('main.cart');
@endphp
<div class="cartBox">
    <a href="{{ $urlCart }}" class="cartBox_icon">
        <img src="{{ Storage::url('images/svg/icon-cart-header.svg') }}" alt="{{ config('language.'.$language.'.data.cart') }}" title="{{ config('language.'.$language.'.data.cart') }}" />
        <div id="js_updateCart_count" class="cartBox_icon_number">
            {{ $detailCart['count'] ?? 0 }}
        </div>
    </a>
    <a href="{{ $urlCart }}" class="cartBox_text">{{ config('language.'.$language.'.data.cart') }}</a>
    <div id="js_checkEmptyCart_idWrite" class="cartBox_list">
        @if(!empty($products)&&$products->isNotEmpty())
            <div class="customScrollBar-y" style="max-height:420px;">
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
                <div class="total">{{ empty($language)||$language=='vi' ? 'Tổng' : 'Total' }}: <span id="js_updateCart_total">{!! \App\Helpers\Number::getFormatPriceByLanguage($detailCart['total'], $language) !!}</span></div>
                <a href="{{ $urlCart }}" class="button">{{ empty($language)||$language=='vi' ? 'Xem giỏ hàng' : 'View cart' }}</a>
            </div>
        @else
            @include('wallpaper.cart.emptyCart', compact('language'))
        @endif
        
    </div>
    <div class="cartBox_background"></div>
</div>