@php
    if(empty($language)||$language=='vi'){
        $urlCart    = route('main.cart');
    }else {
        $urlCart    = route('main.enCart');
    }
    /* tổng tiền */
    $total          = 0;
    if(!empty($products)&&$products->isNotEmpty()){
        foreach($products as $product) {
            $tmp    = \App\Http\Controllers\CartController::convertInfoCartToView($product->cart, $product, $language);
            $total += $tmp['price'];
        }
    }
@endphp
<div class="cartBox">
    <a href="{{ $urlCart }}" class="cartBox_icon">
        <img src="{{ Storage::url('images/svg/icon-cart-header.svg') }}" alt="giỏ hàng" title="giỏ hàng" />
        <div id="js_updateCart_count" class="cartBox_icon_number">
            {{ !empty($products)&&$products->isNotEmpty() ? $products->count() : 0 }}
        </div>
    </a>
    <a href="{{ $urlCart }}" class="cartBox_text">{{ empty($language)||$language=='vi' ? 'Giỏ hàng' : 'Cart' }}</a>
    <div class="cartBox_list">
        @if(!empty($products)&&$products->isNotEmpty())
            <div class="customScrollBar-y" style="max-height:500px;">
                @foreach($products as $product)
                    @php
                        $keyId      = !empty($product->id) ? $product->id.implode('-', $product->cart['product_price_id']) : null;
                    @endphp
                    <div id="{{ 'js_updateCart_idWrite_'.$keyId }}" class="cartBox_list_item">
                        @include('wallpaper.cart.cartSortRow', compact('product'))
                    </div>
                @endforeach
            </div>
            <div class="cartBox_list_item buttonBox">
                <div class="total">{{ empty($language)||$language=='vi' ? 'Tổng' : 'Total' }}: <span id="js_updateCart_total">{!! \App\Helpers\Number::getFormatPriceByLanguage($total, $language) !!}</span></div>
                <a href="{{ $urlCart }}" class="button">{{ empty($language)||$language=='vi' ? 'Xem giỏ hàng' : 'View cart' }}</a>
            </div>
        @else 
            <div class="emptyCart">
                <img src="{{ Storage::url('images/svg/icon-blank-cart.svg') }}" alt="danh sách sản phẩm trong giỏ hàng" title="danh sách sản phẩm trong giỏ hàng" />
                <div class="emptyCart_text">{{ empty($language)||$language=='vi' ? 'Giỏ hàng của bạn trống' : 'Your shopping cart is empty' }}!</div> 
            </div>
        @endif
        
    </div>
    <div class="cartBox_background"></div>
</div>