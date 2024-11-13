@if(!empty($user))
    @php
        $myDownloadsByLanguage = config('language.'.$language.'.data.my_downloads');
    @endphp
    <div class="headerBottom_item" onClick="toggleMenuListMobile();">
        <div class="headerBottom_item_icon">
            <img src="{{ Storage::url('images/svg/icon-user.svg') }}" alt="{{ $myDownloadsByLanguage }}" title="$myDownloadsByLanguage" />
        </div>
        <div class="headerBottom_item_text">
            <div class="maxLine_1" style="max-width:120px;">{{ $user->email ?? ''}}</div>
            <div class="headerBottom_item_text_modal">
                <a href="{{ route('main.account.orders') }}" class="loginBox_list_item">
                    <i class="fa-solid fa-download"></i>
                    <div>{{ $myDownloadsByLanguage }}</div>
                </a> 
                <a href="{{ route('admin.logout') }}" class="loginBox_list_item">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <div>{{ config('language.'.$language.'.data.logout') }}</div>
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
        $loginByLanguage = config('language.'.$language.'.data.login');
    @endphp
    <div class="headerBottom_item" onClick="toggleModalCustomerLoginForm('modalLoginFormCustomerBox');">
        <div class="headerBottom_item_icon">
            <img src="{{ Storage::url('images/svg/sign-in-alt.svg') }}" alt="{{ $loginByLanguage }}" title="{{ $loginByLanguage }}" />
        </div>
        <div class="headerBottom_item_text">
            {{ $loginByLanguage }}
        </div>
    </div>
@endif