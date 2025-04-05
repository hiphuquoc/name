@if(!empty($user))
    @php
        $myDownloadsByLanguage = config('data_language_1.'.$language.'.my_downloads');
    @endphp
    <div onClick="toggleMenuListMobile();">
        <div class="headerBottom_item_icon">
            @php
                $icon = file_get_contents('storage/images/svg/icon-user.svg');
            @endphp
            {!! $icon !!}
        </div>
        <div class="headerBottom_item_text">
            <div class="maxLine_1">{{ $user->name ?? ''}}</div>
            <div class="headerBottom_item_text_modal">
                <a href="{{ route('main.account.orders') }}" class="loginBox_list_item">
                    <i class="fa-solid fa-download"></i>
                    <div>{{ $myDownloadsByLanguage }}</div>
                </a> 
                <a href="{{ route('admin.logout') }}" class="loginBox_list_item">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <div>{{ config('data_language_1.'.$language.'.logout') }}</div>
                </a>
            </div>
            <div class="headerBottom_item_text_background"></div>
            <script type="text/javascript">
                function toggleMenuListMobile(){
                    const flagShow = $('.headerBottom_item_text_modal').css('display');
                    if(flagShow=='none'){
                        $('.headerBottom_item_text_modal').css('display', 'block');
                        $('.headerBottom_item_text_background').css('display', 'block');
                    }else {
                        $('.headerBottom_item_text_modal').css('display', 'none');
                        $('.headerBottom_item_text_background').css('display', 'none');
                    }
                }
            </script>
        </div>
    </div>
@else 
    @php
        $loginByLanguage = config('data_language_1.'.$language.'.login');
    @endphp
    <div onClick="toggleModalCustomerLoginForm('modalLoginFormCustomerBox');">
        <div class="headerBottom_item_icon">
            @php
                $icon = file_get_contents('storage/images/svg/sign-in-alt.svg');
            @endphp
            {!! $icon !!}
        </div>
        <div class="headerBottom_item_text">
            {{ $loginByLanguage }}
        </div>
    </div>
@endif