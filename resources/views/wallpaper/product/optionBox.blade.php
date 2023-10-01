{{-- <div class="productDetailBox_detail_option_title">
    Tùy chọn 
</div> --}}
<div id="js_addToCart_options" class="productDetailBox_detail_option_box">
    <div class="productDetailBox_detail_option_box_item" style="background:none;font-size:0.95rem;padding:0;margin:0 10px 0 0;">
        {{ empty($language)&&$language=='vi' ? 'Tùy chọn :' : 'Options :' }}
    </div>
    <!-- của all -->
    <div id="js_addToCart_option_all" class="productDetailBox_detail_option_box_item selected"
        data-product_price_id="all" 
        data-option_name="all" 
        data-price="{{ $item->price }}"
        onClick="setOptionProduct('all');">
        Trọn bộ
    </div>  
    <!-- của từng ảnh con -->
    @foreach($item->prices as $option)
        @php
            $optionName = !empty($language)&&$language=='en' ? $option->en_name : $option->name;
        @endphp
        <div id="js_addToCart_option_{{ $option->id }}" class="productDetailBox_detail_option_box_item"
            data-product_price_id="{{ $option->id }}" 
            data-option_name="{{ $optionName }}" 
            data-price="{{ $option->price }}"
            onClick="setOptionProduct('{{ $option->id }}');">
            {{ $optionName }}
        </div>  
    @endforeach
</div>