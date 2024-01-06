
<div id="cartMessage" class="cartMessage">
    <div class="cartMessage_bg" onClick="openCloseModal('cartMessage');"></div> 
    <div class="cartMessage_box">
        @if(!empty($language)&&$language=='en')
            <div class="cartMessage_box_head">
                Added to cart!
            </div>
        @else 
            <div class="cartMessage_box_head">
                Đã thêm vào giỏ hàng!
            </div>
        @endif
        <div class="cartMessage_box_close" onClick="openCloseModal('cartMessage');"><i class="fa-solid fa-xmark"></i></div>
        <div class="cartMessage_box_body">
            @if(!empty($image))
                <div class="cartMessage_box_body_image">
                    <img src="{{ $image }}" alt="{{ $title ?? null }}" title="{{ $title ?? null }}" />
                </div>
            @endif
            @php
                $xhtmlPrice         = '-';
                if(!empty($price)){
                    $xhtmlPrice     = \App\Helpers\Number::getFormatPriceByLanguage($price, $language);
                }
            @endphp
            <div class="cartMessage_box_body_content">
                <div class="cartMessage_box_body_content_title maxLine_3">{{ $title ?? 'Không xác định' }}</div>
                <div class="cartMessage_box_body_content_price">{!! $xhtmlPrice !!} <span>/{{ $option }}</span></div>
                {{-- <div class="cartMessage_box_body_content_type">{{ $option }}</div> --}}
            </div>
        </div>
        @if(!empty($language)&&$language=='en')
            <div class="cartMessage_box_footer">
                <a href="{{ route('main.enCart') }}" class="button">View cart</a>
            </div>
        @else 
            <div class="cartMessage_box_footer">
                <a href="{{ route('main.cart') }}" class="button">Xem giỏ hàng</a>
            </div>
        @endif
    </div>
</div>