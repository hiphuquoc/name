<!-- thành tiền -->
<div class="cartSectionBox_body_item">
    <div>{{ empty($language)||$language=='vi' ? 'Thành tiền' : 'Amount' }}</div> 
    <div>{!! \App\Helpers\Number::getFormatPriceByLanguage($total, $language) !!}</div>
</div>
<!-- phí thanh toán (nếu có) -->
<div class="cartSectionBox_body_item" style="padding-top:0;border-top:0;">
    @if(!empty($taxName)&&!empty($taxNumber)&&$taxNumber>0)
        <div>{{ $taxName }}:</div> 
        <div>{!! \App\Helpers\Number::getFormatPriceByLanguage($taxNumber, $language) !!}</div>
    @endif
</div>
<!-- tổng cộng -->
<div class="cartSectionBox_body_item total">
    {!! $titleTotal !!}
    <div class="total_number">
        <span id="js_updateCart_total">
            {!! \App\Helpers\Number::getFormatPriceByLanguage($total + $taxNumber, $language) !!}
        </span>
    </div>
</div>