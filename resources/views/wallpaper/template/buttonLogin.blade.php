@if(!empty($user))
    <div class="loginBox" onClick="toggleModalCustomerLoginForm('modalLoginFormCustomerBox');" style="min-width:116px;">
        <div class="loginBox_iconAvatar">
            <img src="{{ Storage::url('images/svg/icon-user.svg') }}" alt="thông tin tài khoản" title="thông tin tài khoản" />
        </div>
        <div class="maxLine_1">{{ $user->name ?? 'Tài khoản' }}</div>
    </div>
@else 
    <div class="loginBox" onClick="toggleModalCustomerLoginForm('modalLoginFormCustomerBox');">
        <img src="{{ Storage::url('images/svg/sign-in-alt.svg') }}" alt="đăng nhập {{ config('main.company_name') }}" title="đăng nhập {{ config('main.company_name') }}" />
        <div>Đăng nhập</div>
    </div>
@endif