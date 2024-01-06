
<!-- của trọn bộ -->
@php
    $xhtmlPrice         = $item->price.config('main.currency_unit_en');
    if(empty($language)||$language=='vi'){
        $xhtmlPrice     = number_format(\App\Helpers\Number::convertUSDToVND($item->price)).config('main.currency_unit');
    }
    $xhtmlPriceOld      = null;
    if(!empty($item->price_before_promotion)&&$item->price_before_promotion!=$item->price){
        $priceOld       = $item->price_before_promotion;
        if(empty($language)||$language=='vi'){
            $priceOld   = number_format(\App\Helpers\Number::convertUSDToVND($item->price_before_promotion));
        }
    }
    $xhtmlPriceOld  = '<div class="productDetailBox_detail_price_item_old">'.$priceOld.'</div>';
    /* chuỗi json thay đổi price hiển thị chi chọn option */
    $tmp            = [];
    foreach($item->prices as $price) $tmp[] = $price->id;
    $idKey          = implode('-', $tmp);
@endphp
<div id="{{ $idKey }}" class="productDetailBox_detail_price_item selected">
    <div class="productDetailBox_detail_price_item_real">{!! $xhtmlPrice !!}</div>
    {!! $xhtmlPriceOld !!}
    @if(!empty($item->sale_off))
        <div class="productDetailBox_detail_price_item_saleoff">- {{ $item->sale_off }}%</div>
    @endif
</div>

@foreach($prices as $price)
    @php
        $xhtmlPrice     = $price->price.config('main.currency_unit_en');
        if(empty($language)||$language=='vi'){
            $xhtmlPrice = number_format(\App\Helpers\Number::convertUSDToVND($price->price)).config('main.currency_unit');
        }
        $xhtmlPriceOld  = null;
        if(!empty($price->price_before_promotion)&&$price->price_before_promotion!=$price->price){
            $priceOld   = $price->price_before_promotion;
            if(empty($language)||$language=='vi'){
                $priceOld   = number_format(\App\Helpers\Number::convertUSDToVND($price->price_before_promotion));
            }
        }
        $xhtmlPriceOld  = '<div class="productDetailBox_detail_price_item_old">'.$priceOld.'</div>';
    @endphp
    <!-- của từng giá -->
    <div id="{{ $price->id }}" class="productDetailBox_detail_price_item">
        <div class="productDetailBox_detail_price_item_real">{!! $xhtmlPrice !!}</div>
        {!! $xhtmlPriceOld !!}
        @if(!empty($price->sale_off))
            <div class="productDetailBox_detail_price_item_saleoff">- {{ $price->sale_off }}%</div>
        @endif
    </div>
@endforeach

