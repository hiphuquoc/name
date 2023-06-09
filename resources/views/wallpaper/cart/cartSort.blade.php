@php
    $language   = !empty($language) ? $language : 'vi';
    if($language=='en'){
        $urlCart    = route('main.enCart');
    }else {
        $urlCart    = route('main.cart');
    }
@endphp
<div class="cartBox">
    <a href="{{ $urlCart }}" class="cartBox_icon">
        <img src="{{ Storage::url('images/svg/icon-cart-header.svg') }}" alt="giỏ hàng" title="giỏ hàng" />
        <div id="js_updateCart_count" class="cartBox_icon_number">
            {{ !empty($products)&&$products->isNotEmpty() ? $products->count() : 0 }}
        </div>
    </a>
    <a href="{{ $urlCart }}" class="cartBox_text">{{ !empty($language)&&$language=='en' ? 'Cart' : 'Giỏ hàng' }}</a>
    <div class="cartBox_list">
        @if(!empty($products)&&$products->isNotEmpty())
            @php
                $total = 0;
            @endphp
            <div class="customScrollBar-y" style="max-height:500px;">
                @foreach($products as $product)
                    @php
                        /* cộng tổng */
                        $total      += $product->price->price ?? $product->price_all;
                        $idProduct  = $product->id ?? 0;
                        $keyId      = !empty($product->id)&&!empty($product->price->id) ? $product->id.$product->price->id : null;
                    @endphp
                    <div id="{{ 'js_updateCart_idWrite_'.$keyId }}" class="cartBox_list_item">
                        @include('wallpaper.cart.cartSortRow', compact('product', 'language'))
                    </div>
                @endforeach
            </div>
            <div class="cartBox_list_item buttonBox">
                <div class="total">{{ !empty($language)&&$language=='en' ? 'Total' : 'Tổng' }}: <span id="js_updateCart_total">{!! number_format($total).config('main.currency_unit') !!}</span></div>
                <a href="{{ $urlCart }}" class="button">{{ !empty($language)&&$language=='en' ? 'View cart' : 'Xem giỏ hàng' }}</a>
            </div>
        @else 
            <div class="emptyCart">
                <img src="{{ Storage::url('images/svg/icon-blank-cart.svg') }}" alt="danh sách sản phẩm trong giỏ hàng" title="danh sách sản phẩm trong giỏ hàng" />
                <div class="emptyCart_text">{{ !empty($language)&&$language=='en' ? 'Your shopping cart is empty' : 'Giỏ hàng của bạn trống' }}!</div> 
            </div>
        @endif
        
    </div>
    <div class="cartBox_background"></div>
</div>