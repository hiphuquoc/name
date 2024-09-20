
<div id="cartMessage" class="cartMessage">
    <div class="cartMessage_bg" onClick="openCloseModal('cartMessage');"></div> 
    <div class="cartMessage_box">
        <div class="cartMessage_box_head">
            {{ config('language.'.$language.'.data.added_to_cart') }}
        </div>
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
                <div class="cartMessage_box_body_content_title maxLine_2">{{ $title ?? config('language.'.$language.'.data.undefined') }}</div>
                <div class="cartMessage_box_body_content_price">{!! $xhtmlPrice !!} <span>/{{ $option }}</span></div>
                {{-- <div class="cartMessage_box_body_content_type">{{ $option }}</div> --}}
            </div>
        </div>
        <div class="cartMessage_box_footer">
            <a href="{{ $urlPageCart ?? '/' }}" class="button">{{ config('language.'.$language.'.data.view_cart') }}</a>
        </div>
    </div>
</div>