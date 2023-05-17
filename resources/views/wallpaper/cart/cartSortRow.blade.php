@if(!empty($product))
    @php
        $idProduct          = $product->id ?? 0;
        $idPrice            = $product->price->id ?? 0;
        $keyId              = !empty($product->id)&&!empty($product->price->id) ? $product->id.$product->price->id : null;
        $eventUpdateCart    = 'updateCart("js_updateCart_idWrite_'.$keyId.'", "js_updateCart_total", "js_updateCart_count", "js_addToCart_quantity_'.$keyId.'")';
        if(!empty($language)&&$language='en'){
            $title          = $product->en_name ?? $product->en_seo->title ?? null;
        }else { 
            $title          = $product->name ?? $product->seo->title ?? null;
        }
        /* ảnh */
        $image              = config('image.default');
        
        if($product->cart['product_price_id']=='all'){
            /* của phiên bản all => lấy ảnh của phiên bản con đầu tiên */
            foreach($product->prices as $price){
                if(!empty($price->files[0]->file_path)&&file_exists(Storage::path($price->files[0]->file_path))) {
                    $image = Storage::url($price->files[0]->file_path);
                    break;
                }
            }
        }else {
            /* có mức giá cụ thể */
            if(!empty($product->price->files[0]->file_path)&&file_exists(Storage::path($product->price->files[0]->file_path))) $image = Storage::url($product->price->files[0]->file_path);
        }
        /* action */
        $eventRemoveProductCart = 'removeProductCart("'.$idProduct.'", "'.$idPrice.'", "js_updateCart_idWrite_'.$keyId.'", "js_updateCart_total", "js_updateCart_count")';
        /* đường dẫn */
        if(!empty($language)&&$language='en'){
            $url            = $product->en_seo->slug_full ?? null;
        }else { 
            $url            = $product->seo->slug_full ?? null;
        }
    @endphp
    <a href="/{{ $url }}" class="cartBox_list_item_image">
        <img src="{{ $image }}" alt="{{ $title }}" title="{{ $title }}" />
    </a>
    <div class="cartBox_list_item_content">
        <a href="/{{ $url }}" class="cartBox_list_item_content_title maxLine_2">
            {{ $title }}
        </a>
        <div class="cartBox_list_item_content_price">
            @php
                $price = 0;
                if(!empty($product->price->price)) $price = number_format($product->price->price);
                if(empty($price)) $price = number_format($product->price_all);
                if(!empty($language)&&$language=='en'){
                    $titlePrice = !empty($product->price->en_name) ? '<span>'.$product->price->en_name.'</span>' : null;
                }else {
                    $titlePrice = !empty($product->price->name) ? '<span>'.$product->price->name.'</span>' : null;
                }
                
            @endphp
            {{ $price }}đ {!! $titlePrice !!}
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
                    Thành tiền: <span>{!! number_format($product->cart['quantity']*$product->price->price).config('main.currency_unit') !!}</span>
                </div>
            @endif
        </div> --}}
        <div class="cartBox_list_item_content_action" onclick="{{ $eventRemoveProductCart }}">
            <img src="/storage/images/svg/icon-trash.svg" alt="xóa sản phẩm trong giỏ hàng" title="xóa sản phẩm trong giỏ hàng" />
        </div>
    </div>
@endif