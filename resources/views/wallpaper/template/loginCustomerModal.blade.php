@php
    $randomWallpaper = \App\Models\FreeWallpaper::inRandomOrder()->first();
@endphp
<form id="formLogin" method="get" style="width:100%;">
<div id="modalLoginFormCustomerBox" class="modalLoginFormCustomerBox">
    <!-- modal background -->
    <div class="modalLoginFormCustomerBox_bg" onClick="toggleModalCustomerLoginForm('modalLoginFormCustomerBox');"></div>
    <!-- modal box -->
    <div class="modalLoginFormCustomerBox_box">
        <div class="modalLoginFormCustomerBox_box_left" style="background:url('{{ \App\Helpers\Image::getUrlImageCloud($randomWallpaper->file_cloud) }}') no-repeat center;background-size: cover;">
        </div>
        <div class="modalLoginFormCustomerBox_box_right">
            <!-- close -->
            <div class="modalLoginFormCustomerBox_box_right_close" onClick="toggleModalCustomerLoginForm('modalLoginFormCustomerBox');"><i class="fa-solid fa-xmark"></i></div>
            <!-- form -->
            @php
                $loginByLanguage = config('language.'.$language.'.data.login');
            @endphp
            <div class="loginFormCustomer">
                <div class="loginFormCustomer_title">
                    {{ $loginByLanguage.' '.config('main_'.env('APP_NAME').'.info.'.env('APP_NAME').'.company_name') }}
                </div>
                <div class="loginFormCustomer_body">
                    <div class="loginFormCustomer_body_item">
                        <!-- input -->
                        <div class="formBox">
                            <div class="formBox_item">
                                <div class="inputWithLabelInside">
                                    <label>{{ config('language.'.$language.'.data.login_email') }}</label>
                                    <input type="text" name="email" required />
                                </div>
                            </div>
                            <div class="formBox_item">
                                <div class="inputWithLabelInside">
                                    <label>{{ config('language.'.$language.'.data.password') }}</label>
                                    <input type="password" name="password" autocomplete="off" required />
                                </div>
                            </div>
                            <div class="formBox_item" style="display:flex;justify-content:space-between;align-item:flex-end;">
                                <label class="checkBox" for="remember" style="font-size:0.85rem;">
                                    <input type="checkbox" id="remember" name="remember" checked />
                                    <div>{{ config('language.'.$language.'.data.remember_me') }}</div>
                                </label>
                                <div id="noticeLogin" class="noticeLogin"> 
                                    <!-- thông báo đăng nhập -->
                                    {{-- Tên đăng nhập và mật khẩu không khớp! --}}
                                </div>
                            </div>
                        </div>
                        <div class="loginFormCustomer_body_item">
                            <!-- button -->
                            <button type="button" class="button" onClick="submitFormLogin('formLogin');">{{ $loginByLanguage }}</div>
                            <!-- đăng nhập google -->
                            <!-- login social -->
                            <div class="loginFormSocial">
                                <div class="loginFormSocial_title">
                                    {{ config('language.'.$language.'.data.or_login_with') }}
                                </div>
                                <div class="loginFormSocial_body">
                                    <div class="loginFormSocial_body_item">
                                        <div id="g_id_onload" 
                                            data-_token="{{ csrf_token() }}" 
                                            data-client_id="{{ env('GOOGLE_DRIVE_CLIENT_ID') }}" 
                                            data-context="signin"
                                            data-ux_mode="popup"
                                            data-login_uri="{{ env('APP_URL') }}/auth/google/callback" 
                                            data-auto_prompt="false"
                                            data-auto_select="true"
                                            data-itp_support="true">
                                        </div>

                                        <div class="g_id_signin"
                                            data-type="standard"
                                            data-shape="rectangular"
                                            data-theme="filled_white"
                                            data-text="signin_with"
                                            data-size="large"
                                            data-logo_alignment="left">
                                        </div>
                                    </div>
                                    <div class="loginFormSocial_body_item">
                                        @php
                                            if(!empty($language)&&$language=='en'){
                                                $buttonTitleLoginWithFB = 'Login with Facebook';
                                            }else {
                                                $buttonTitleLoginWithFB = 'Đăng nhập với Facebook';
                                            }
                                        @endphp
                                        <a class="facebookButtonLogin" href="{{ route('main.facebook.redirect') }}">
                                            <img src="{{ Storage::url('images/svg/logo-facebook-fff.png') }}" alt="{{ $buttonTitleLoginWithFB }}" title="{{ $buttonTitleLoginWithFB }}" />
                                            <div>{{ $buttonTitleLoginWithFB }}</div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
</div>
</form>
{{-- @push('scriptCustom')
    <script type="text/javascript">
        function toggleModalCustomerLoginForm(idElement){
            const element   = $('#'+idElement);
            const displayE  = element.css('display');
            if(displayE=='none'){
                /* hiển thị */
                element.css('display', 'flex');
                $('body').css('overflow', 'hidden');
                $('#js_openCloseModal_blur').addClass('blurBackground');
            }else {
                element.css('display', 'none');
                $('body').css('overflow', 'unset');
                $('#js_openCloseModal_blur').removeClass('blurBackground');
            }
        }
    </script>
@endpush --}}