@if(!empty($user))
    @php
        $accountInformationByLanguage = config('data_language_1.'.$language.'.account_information');
    @endphp
    <div class="loginBox" onClick="toggleModalCustomerLoginForm('modalLoginFormCustomerBox');">
        <div class="loginBox_show">
            @php
                $icon = file_get_contents('storage/images/svg/icon-user.svg');
            @endphp
            {!! $icon !!}
            <div class="loginBox_show_text maxLine_1">{{ $user->name ?? '' }}</div>
        </div>
        <div class="loginBox_list">
            {{-- <div class="loginBox_list_item">
                <i class="fa-solid fa-key"></i>
                <div>Đổi mật khẩu</div>
            </div>  --}}
            <a href="/{{ $urlMyDownload }}" class="loginBox_list_item">
                <i class="fa-solid fa-download"></i>
                <div class="maxLine_1">{{ config('data_language_1.'.$language.'.my_downloads') }}</div>
            </a> 
            <a href="{{ route('admin.logout') }}" class="loginBox_list_item">
                <i class="fa-solid fa-right-from-bracket"></i>
                <div class="maxLine_1">{{ config('data_language_1.'.$language.'.logout') }}</div>
            </a>
        </div>
        <div class="loginBox_background">

        </div>
    </div>
@else 
    <div class="loginBox" onClick="toggleModalCustomerLoginForm('modalLoginFormCustomerBox');">
        @php
            $loginByLanguage = config('data_language_1.'.$language.'.login');
            $icon = file_get_contents('storage/images/svg/sign-in-alt.svg');
        @endphp
        {!! $icon !!}
        <div class="maxLine_1">{{ $loginByLanguage }}</div>
    </div>
@endif