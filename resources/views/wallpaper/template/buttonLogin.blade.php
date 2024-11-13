@if(!empty($user))
    @php
        $accountInformationByLanguage = config('language.'.$language.'.data.account_information');
    @endphp
    <div class="loginBox" onClick="toggleModalCustomerLoginForm('modalLoginFormCustomerBox');">
        <div class="loginBox_iconAvatar">
            <img src="{{ Storage::url('images/svg/icon-user.svg') }}" alt="{{ $accountInformationByLanguage }}" title="{{ $accountInformationByLanguage }}" />
        </div>
        <div class="maxLine_1" style="max-width:120px;">{{ $user->email ?? '' }}</div>
        <div class="loginBox_list">
            {{-- <div class="loginBox_list_item">
                <i class="fa-solid fa-key"></i>
                <div>Đổi mật khẩu</div>
            </div>  --}}
            <a href="/{{ $urlMyDownload }}" class="loginBox_list_item">
                <i class="fa-solid fa-download"></i>
                <div class="maxLine_1">{{ config('language.'.$language.'.data.my_downloads') }}</div>
            </a> 
            <a href="{{ route('admin.logout') }}" class="loginBox_list_item">
                <i class="fa-solid fa-right-from-bracket"></i>
                <div class="maxLine_1">{{ config('language.'.$language.'.data.logout') }}</div>
            </a>
        </div>
        <div class="loginBox_background">

        </div>
    </div>
@else 
    <div class="loginBox" onClick="toggleModalCustomerLoginForm('modalLoginFormCustomerBox');">
        @php
            $loginByLanguage = config('language.'.$language.'.data.login');
        @endphp
        <img src="{{ Storage::url('images/svg/sign-in-alt.svg') }}" alt="{{ $loginByLanguage.' '.config('main_'.env('APP_NAME').'.info.'.env('APP_NAME').'.company_name') }}" title="{{ $loginByLanguage.' '.config('main_'.env('APP_NAME').'.info.'.env('APP_NAME').'.company_name') }}" />
        <div class="maxLine_1">{{ $loginByLanguage }}</div>
    </div>
@endif