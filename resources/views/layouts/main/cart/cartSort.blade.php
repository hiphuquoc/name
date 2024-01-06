@php
    $urlCart = route('main.cart');
@endphp
<div class="cartBox">
    <a href="{{ $urlCart }}" class="cartBox_icon">
        <img src="{{ Storage::url('images/svg/icon-cart-header.svg') }}" alt="giỏ hàng" title="giỏ hàng" />
        <div id="js_updateCart_count" class="cartBox_icon_number">
            @php
                $count = 0;
                if(!empty($products)) foreach($products as $product) $count += $product->cart['quantity'];
            @endphp
            {{ $count }}
        </div>
    </a>
    <a href="{{ $urlCart }}" class="cartBox_text" style="text-transform: uppercase;">Giỏ hàng</a>
    <div class="cartBox_list">
        @if(!empty($products)&&$products->isNotEmpty())
            @php
                $total = 0;
            @endphp
            <div class="customScrollBar-y" style="max-height:500px;">
                @foreach($products as $product)
                    @php
                        /* cộng tổng */
                        $total      += $product->cart['quantity']*$product->price->price;
                        $idProduct  = $product->id ?? 0;
                        $keyId      = !empty($product->id)&&!empty($product->price->id) ? $product->id.$product->price->id : null;
                    @endphp
                    <div id="{{ 'js_updateCart_idWrite_'.$keyId }}" class="cartBox_list_item">
                        @include('main.cart.cartSortRow', compact('product'))
                    </div>
                @endforeach
            </div>
            <div class="cartBox_list_item total">
                <div>Tổng cộng:</div> <span id="js_updateCart_total">{!! number_format($total).config('main.currency_unit') !!}</span>
            </div>
            <div class="cartBox_list_item buttonBox">
                <a href="{{ $urlCart }}" class="button">Thanh toán</a>
            </div>
        @else 
            <div class="emptyCart">
                <img src="{{ Storage::url('images/svg/icon-blank-cart.svg') }}" alt="danh sách sản phẩm trong giỏ hàng" title="danh sách sản phẩm trong giỏ hàng" />
                <div class="emptyCart_text">Giỏ hảng của bạn trống!</div> 
            </div>
        @endif
    </div>
    <div class="cartBox_background"></div>
</div>