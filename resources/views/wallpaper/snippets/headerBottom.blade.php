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
    @endphp
    <a class="headerBottom_item" href="https://zalo.me/{{ $phoneCustomerService }}">
        <div class="headerBottom_item_icon">
            <img src="{{ Storage::url('images/svg/headphones.svg') }}" alt="Thông tin hỗ trợ Name.com.vn" title="Thông tin hỗ trợ Name.com.vn">
        </div>
        <div class="headerBottom_item_text">
            Hỗ trợ
        </div>
    </a>
    <a class="headerBottom_item" href="#">
        <div class="headerBottom_item_icon">
            <img src="{{ Storage::url('images/svg/book-open-cover.svg') }}" alt="mua ngay" title="mua ngay" />
        </div>
        <div class="headerBottom_item_text">
            Hướng dẫn
        </div>
    </a>
    <div id="js_checkLoginAndSetShow_buttonMobile" class="headerBottom_item">
        
    </div>
</div>