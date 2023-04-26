<div id="modalLoginFormCustomerBox" class="modalLoginFormCustomerBox">
    <!-- modal background -->
    <div class="modalLoginFormCustomerBox_bg" onClick="toggleModalCustomerLoginForm('modalLoginFormCustomerBox');"></div>
    <!-- modal box -->
    <div class="modalLoginFormCustomerBox_box">
        <div class="modalLoginFormCustomerBox_box_left" style="background:url('https://name.com.vn/storage/images/upload/banner-login-type-manager-upload.webp') no-repeat;background-size: 100% 100%;">
        </div>
        
        <div class="modalLoginFormCustomerBox_box_right">
            <div class="loginFormCustomer">
                <div class="loginFormCustomer_title">
                    Đăng nhập {{ config('main.company_name') }}
                </div>
                <div class="loginFormCustomer_body">
                    <div class="loginFormCustomer_body_item">
                        <!-- input -->
                        <div class="formBox">
                            <div class="formBox_item">
                                <div class="inputWithLabelInside">
                                    <label for="login_name">Tên đăng nhập</label>
                                    <input type="text" id="login_name" name="login_name" required />
                                </div>
                            </div>
                            <div class="formBox_item">
                                <div class="inputWithLabelInside">
                                    <label for="password">Mật khẩu</label>
                                    <input type="password" id="password" name="password" autocomplete="off" required />
                                </div>
                            </div>
                            <div class="formBox_item">
                                <label class="checkBox" for="remember">
                                    <input type="checkbox" id="remember" name="remember" />
                                    <div>Ghi nhớ tôi</div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="loginFormCustomer_body_item">
                        <!-- button -->
                        <button type="button" class="button">Đăng nhập</div>
                    </div>
                    <div class="loginFormCustomer_body_item">
                        Bạn chưa có mật khẩu? <a href="#">Đăng ký ngay</a>
                    </div>  
                    <div class="loginFormCustomer_body_item">
                        <!-- login social -->
                        <div class="loginFormSocial">
                            <div class="loginFormSocial_title">
                                hoặc đăng nhập với
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
                                    <a class="facebookButtonLogin" href="{{ route('main.facebook.redirect') }}">
                                        <img src="{{ Storage::url('images/svg/logo-facebook-fff.png') }}" alt="đăng nhập với facebook" title="đăng nhập với facebook" />
                                        <div>Đăng nhập với Facebook</div>
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
{{-- @push('scriptCustom') --}}
    <script src="https://accounts.google.com/gsi/client" async defer></script>
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
{{-- @endpush --}}