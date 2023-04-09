@if(!empty($user))
    <div class="headerBottom_item">
        <div class="headerBottom_item_icon">
            <img src="{{ Storage::url('images/svg/icon-user.svg') }}" alt="mua ngay" title="mua ngay" style="width:22px;" />
        </div>
        <div class="headerBottom_item_text maxLine_1">
            {{ $user->name ?? 'Tài khoản' }}
        </div>
    </div>
@else 
    <div class="headerBottom_item" onClick="toggleModalCustomerLoginForm('modalLoginFormCustomerBox');">
        <div class="headerBottom_item_icon">
            <img src="{{ Storage::url('images/svg/sign-in-alt.svg') }}" alt="mua ngay" title="mua ngay" />
        </div>
        <div class="headerBottom_item_text">
            Đăng nhập
        </div>
    </div>
@endif