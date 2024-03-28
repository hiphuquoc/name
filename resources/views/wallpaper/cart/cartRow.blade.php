@php
    $idProduct              = $product->id;
    $keyId                  = $product->id.implode('-', $arrayProductPrice);
    // $eventUpdateCart        = "updateCart('js_updateCart_idWrite_$keyId', 'js_updateCart_total', 'js_updateCart_count', 'js_addToCart_quantity_".$keyId."', 'cartMain')";
    $eventRemoveProductCart = "removeProductCart($idProduct, '".json_encode($arrayProductPrice)."', 'js_updateCart_idWrite_".$keyId."', 'js_updateCart_total', 'js_updateCart_count')";
    $url                    = null;
    $title                  = config('language.'.$language.'.data.undefined');
    foreach($product->seos as $seo){
        if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language){
            $title      = $seo->infoSeo->title;
            $url        = $seo->infoSeo->slug_full;
            break;
        }
    }
    $cartToView             = \App\Http\Controllers\CartController::convertInfoCartToView($product, $arrayProductPrice, $language);
    $xhtmlPrice             = \App\Helpers\Number::getFormatPriceByLanguage($cartToView['price'], $language);
    /* ảnh */
    $image                  = \App\Helpers\Image::getUrlImageSmallByUrlImage($cartToView['image']);
@endphp

<div class="cartProductBox_body_item_info">
    <a href="/{{ $url }}" class="cartProductBox_body_item_info_image">
        <img src="{{ $image }}" alt="{{ $title }}" title="{{ $title }}" />
    </a>
    <div class="cartProductBox_body_item_info_content">
        <a href="/{{ $url }}" class="cartProductBox_body_item_info_content_title maxLine_2">
            {{ $title }}
        </a>
        <div class="cartProductBox_body_item_info_content_option"> 
            {{ $cartToView['option_name'] }}
        </div>
    </div>
</div>
<div class="cartProductBox_body_item_price">
    <div class="cartProductBox_body_item_price_now">{!! $xhtmlPrice !!}</div>
    {{-- <div class="cartProductBox_body_item_price_old">
        <div class="cartProductBox_body_item_price_old_percent">
            -{{ 20 }}%
        </div>
        <div class="cartProductBox_body_item_price_old_number">
            {!! 123 !!}
        </div>
    </div> --}}
    <div class="cartProductBox_body_item_price_icon" onClick="{{ $eventRemoveProductCart }}"><img src="{{ Storage::url('images/svg/icon-trash.svg') }}" alt="xóa sản phẩm khỏi giỏ hàng" title="xóa sản phẩm khỏi giỏ hàng" /></div>
</div>
{{-- <div class="cartProductBox_body_item_quantity">
    <div class="inputQty small">
        <div class="inputQty_button minus" onClick="plusMinusQuantity('js_addToCart_quantity_{{ $keyId }}', 'minus');{{ $eventUpdateCart }}"><i class="fa-solid fa-minus"></i></div>
        <input id="js_addToCart_quantity_{{ $keyId }}" type="number" name="quantity" value="{{ $product->cart['quantity'] ?? 0 }}" data-product_info_id="{{ $idProduct }}" data-product_price_id="{{ $idPrice }}" onkeyup="{{ $eventUpdateCart }}" />
        <div class="inputQty_button plus" onClick="plusMinusQuantity('js_addToCart_quantity_{{ $keyId }}', 'plus');{{ $eventUpdateCart }}"><i class="fa-solid fa-plus"></i></div>       
    </div>
</div> --}}
{{-- @php
    $money = 0;
    if(!empty($product->price->price)&&!empty($product->cart['quantity'])) $money = number_format($product->price->price*$product->cart['quantity']);
@endphp
<div class="cartProductBox_body_item_money">
    <div>{!! $money.config('language.'.$language.'.currency') !!}</div>
    <div class="cartProductBox_body_item_money_icon" onClick="{{ $eventRemoveProductCart }}"><img src="{{ Storage::url('images/svg/icon-trash.svg') }}" alt="xóa sản phẩm khỏi giỏ hàng" title="xóa sản phẩm khỏi giỏ hàng" /></div>
</div> --}}