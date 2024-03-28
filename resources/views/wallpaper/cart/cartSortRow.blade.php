@if(!empty($product))
    @php
        $keyId              = !empty($product->id) ? $product->id.implode('-', $arrayProductPrice) : null;
        // $eventUpdateCart    = "updateCart('js_updateCart_idWrite_".$keyId."', 'js_updateCart_total', 'js_updateCart_count', 'js_addToCart_quantity_".$keyId."')";
        $url                = null;
        $title              = config('language.'.$language.'.data.undefined');
        foreach($product->seos as $seo){
            if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language){
                $title      = $seo->infoSeo->title;
                $url        = $seo->infoSeo->slug_full;
                break;
            }
        }
        $cartToView         = \App\Http\Controllers\CartController::convertInfoCartToView($product, $arrayProductPrice, $language);
        $xhtmlPrice             = \App\Helpers\Number::getFormatPriceByLanguage($cartToView['price'], $language);
        /* action */
        $eventRemoveProductCart = "removeProductCart($product->id, '".json_encode($arrayProductPrice)."', 'js_updateCart_idWrite_".$keyId."', 'js_updateCart_total', 'js_updateCart_count')";
        /* ảnh */
        $image                  = \App\Helpers\Image::getUrlImageSmallByUrlImage($cartToView['image']);
    @endphp
    <a href="/{{ $url }}" class="cartBox_list_item_image">
        <img src="{{ $image }}" alt="{{ $title }}" title="{{ $title }}" />
    </a>
    <div class="cartBox_list_item_content">
        <a href="/{{ $url }}" class="cartBox_list_item_content_title maxLine_2">
            {{ $title }}
        </a>
        <div class="cartBox_list_item_content_price">
            {!! $xhtmlPrice !!} <span>/{!! $cartToView['option_name'] !!}</span>
        </div>
        {{-- <div class="cartBox_list_item_content_orther">
            <div class="cartBox_list_item_content_orther_quantity">
                <div class="inputQty mini">
                    <div class="inputQty_button minus" onClick="plusMinusQuantity('js_addToCart_quantity_{{ $keyId }}', 'minus');{{ $eventUpdateCart }}"><i class="fa-solid fa-minus"></i></div>
                    <input id="js_addToCart_quantity_{{ $keyId }}" type="number" name="quantity" value="{{ $product->cart['quantity'] ?? 0 }}" data-product_info_id="{{ $idProduct }}" data-product_price_id="{{ $idPrice }}" onkeyup="{{ $eventUpdateCart }}" />
                    <div class="inputQty_button plus" onClick="plusMinusQuantity('js_addToCart_quantity_{{ $keyId }}', 'plus');{{ $eventUpdateCart }}"><i class="fa-solid fa-plus"></i></div>       
                </div>
            </div>
            @if(!empty($product->cart['quantity'])&&!empty($product->price->price))
                <div class="cartBox_list_item_content_orther_total">
                    Thành tiền: <span>{!! number_format($product->cart['quantity']*$product->price->price).config('language.'.$language.'.currency') !!}</span>
                </div>
            @endif
        </div> --}}
        <div class="cartBox_list_item_content_action" onclick="{{ $eventRemoveProductCart }}">
            <img src="/storage/images/svg/icon-trash.svg" alt="{{ config('language.'.$language.'.data.remove_item_in_cart') }}" title="{{ config('language.'.$language.'.data.remove_item_in_cart') }}" />
        </div>
    </div>
@endif