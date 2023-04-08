<div id="modalLoginFormCustomerBox" class="modalLoginFormCustomerBox">
    <!-- modal background -->
    <div class="modalLoginFormCustomerBox_bg" onClick="toggleModalCustomerLoginForm('modalLoginFormCustomerBox');"></div>
    <!-- modal box -->
    <div class="modalLoginFormCustomerBox_box">
        <div class="modalLoginFormCustomerBox_box_left" style="background:url('https://name.com.vn/storage/images/upload/54-hinh-nen-dien-thoai-wallpaper-mobile-khung-canh-tuyet-tuyet-dep-source-1MJQKAITHF2PGNL.png') no-repeat;background-size: 100% 100%;">
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
                                    <input type="text" id="login_name" name="login_name" onkeyup="validateWhenType(this)" required />
                                </div>
                            </div>
                            <div class="formBox_item">
                                <div class="inputWithLabelInside">
                                    <label for="password">Mật khẩu</label>
                                    <input type="password" id="password" name="password" onkeyup="validateWhenType(this)" required />
                                </div>
                            </div>
                            <div class="formBox_item">
                                <label class="checkBox" for="remember">
                                    <input type="checkbox" id="remember" name="remember" onkeyup="validateWhenType(this, 'remember')" />
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
                                    @for($i=0;$i<2;++$i)
                                        <div class="loginFormSocial_body_item">

                                            <div id="g_id_onload"
                                                data-client_id="{{ env('GOOGLE_DRIVE_CLIENT_ID') }}"
                                                data-_token="{{ csrf_token() }}" 
                                                data-context="signin"
                                                data-ux_mode="popup"
                                                data-login_uri="https://name.dev/auth/google/callback" 
                                                data-auto_prompt="true"
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
                                    @endfor
                            </div>
                        </div>

                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
</div>
@push('scriptCustom')
    <script type="text/javascript">
        // $(document).ready(function(){
            
        // });

        // function loginSuccess(){
        //     $.ajax({
        //         url: '{{ route("main.google.callback") }}',
        //         type: 'POST',
        //         data: {
        //             '_token'    : '{{ csrf_token() }}',
        //             // Thêm các thông tin cần thiết khác
        //         },
        //         success: function(response) {
        //             // Xử lý phản hồi từ server
        //         },
        //         error: function(jqXHR, textStatus, errorThrown) {
        //             // Xử lý lỗi
        //         }
        //     });
        // }

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
@endpush