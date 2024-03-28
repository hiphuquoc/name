<!-- thành tiền -->
<div class="cartSectionBox_body_item">
    <div>{{ config('language.'.$language.'.data.total_price') }}</div> 
    <div>{!! \App\Helpers\Number::getFormatPriceByLanguage($intoMoney, $language) !!}</div>
</div>
<!-- phí thanh toán (nếu có) -->
<div class="cartSectionBox_body_item" style="padding-top:0;border-top:0;">
    @if(!empty($taxNumber)&&$taxNumber>0)
        <div>{{ config('language.'.$language.'.data.payment_fee') }}</div> 
        <div>{!! \App\Helpers\Number::getFormatPriceByLanguage($taxNumber, $language) !!}</div>
    @endif
</div>
<!-- tổng cộng -->
<div class="cartSectionBox_body_item total">
    {{ config('language.'.$language.'.data.total') }}
    <div class="total_number">
        <span id="js_updateCart_total">
            {!! \App\Helpers\Number::getFormatPriceByLanguage($total, $language) !!}
        </span>
    </div>
</div>