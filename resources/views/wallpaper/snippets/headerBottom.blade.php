<div class="headerBottom">
    @php
        $contacts               = config('main.contacts');
        $phoneCustomerService   = null;
        foreach($contacts as $contact){
            if($contact['type']=='customer service') {
                $phoneCustomerService = $contact['phone'];
                break;
            }
        }
        if(empty($language)||$language=='vi'){
            $altSupport     = 'Thông tin hỗ trợ Name.com.vn';
            $nameSupport    = 'Hỗ trợ';
            $altGuide       = 'Hướng dẫn đặt hình nền điện thoại';
            $nameGuide      = 'Hướng dẫn';
            $linkGuide      = '/huong-dan-tai-hinh-nen-dien-thoai';
        }else {
            $altSupport     = 'Support information Name.com.vn';
            $nameSupport    = 'Support';
            $altGuide       = 'Instructions for setting phone wallpapers';
            $nameGuide      = 'Guide';
            $linkGuide      = '/guide-to-download-phone-wallpapers';
        }
    @endphp
    <a class="headerBottom_item" href="https://zalo.me/{{ $phoneCustomerService }}">
        <div class="headerBottom_item_icon">
            <img src="{{ Storage::url('images/svg/headphones.svg') }}" alt="{{ $altSupport }}" title="{{ $altSupport }}">
        </div>
        <div class="headerBottom_item_text">
            {{ $nameSupport }}
        </div>
    </a>
    <a class="headerBottom_item" href="{{ env('APP_URL').$linkGuide }}">
        <div class="headerBottom_item_icon">
            <img src="{{ Storage::url('images/svg/book-open-cover.svg') }}" alt="{{ $altGuide }}" title="{{ $altGuide }}" />
        </div>
        <div class="headerBottom_item_text">
            {{ $nameGuide }}
        </div>
    </a>
    <div id="js_checkLoginAndSetShow_buttonMobile" class="headerBottom_item"></div>
</div>