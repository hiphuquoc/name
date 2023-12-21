@php
    $user               = Auth::user();
@endphp

<div id="modalPaymentMethod" class="modalBox">
    <div class="modalBox_bg" onClick="openCloseModal('modalPaymentMethod');"></div>
    <div class="modalBox_box">
        <form id="formModalPaymentMethod" method="get" style="width:100%;">
            <!-- hidden -->
            @if(empty($language)||$language=='vi')
                <div class="formModalBox_box_head">Bước 1: Bạn vui lòng nhập email</div>
            @else 
                <div class="formModalBox_box_head">Step 1: Please enter your email</div>
            @endif
            <div class="formModalBox_box_body">
                @if(empty($language)||$language=='vi')
                    <div style="margin-top:-0.5rem;">Nếu nhập email bạn sẽ được gửi thêm một bản để lưu trữ.</div>
                @else 
                    <div style="margin-top:-0.5rem;">If you enter your email, you will be sent a copy for storage.</div>
                @endif
                <div class="inputWithLabelInside">
                    @if(empty($language)||$language=='vi')
                        <label for="email">Email dùng nhận ảnh</label>
                    @else 
                        <label for="email">Email to receive photos</label>
                    @endif
                    <input type="text" id="email" name="email" onkeyup="validateWhenType(this, 'email')" value="{{ $user->email ?? null }}" />
                </div>
            </div>
            @if(empty($language)||$language=='vi')
                <div class="formModalBox_box_head">Bước 2: Chọn hình thức thanh toán</div>
            @else 
                <div class="formModalBox_box_head">Step 2: Choose payment method</div>
            @endif
            <div class="formModalBox_box_body">
                
                <div class="paymentMethodBox">
                    @include('wallpaper.template.paymentMethod', [
                        'language'  => $language,
                        'action'    => 'paymentNow'
                    ])
                </div>

            </div>
            {{-- <div class="formModalBox_box_footer">
                <div class="formModalBox_box_footer_item button" tabindex="6" onclick="submitForm('formModalSubmit')">
                    Gửi yêu cầu
                </div>
            </div> --}}
        </form>
        <div class="modalBox_box_close" onClick="openCloseModal('modalPaymentMethod');">
            <i class="fa-sharp fa-solid fa-xmark"></i>
        </div>
    </div>
</div>
@push('scriptCustom')
    <script src="{{ asset('sources/admin/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ asset('sources/admin/app-assets/js/scripts/forms/form-select2.min.js') }}"></script>
    <script type="text/javascript">
        function submitForm(idForm){
            event.preventDefault();
            const error     = validateForm(idForm);
            if(error==''){
                const data  = $('#'+idForm).serialize();
                $.ajax({
                    url         : '{{ route("ajax.registrySeller") }}',
                    type        : 'get',
                    dataType    : 'json',
                    data        : data,
                    success     : function(response){
                        /* tắt modal form đăng ký */
                        openCloseModal('modalRegistrySeller');
                        /* bật thông báo */
                        setMessageModal(response.title, response.content);
                    }
                });
            }else {
                /* thêm class thông báo lỗi cho label của input */
                for(let i = 0;i<error.length;++i){
                    const idInput = $('#'+idForm).find('[name='+error[i]+']').attr('id');
                    if(idInput!=''){
                        const elementLabel = $('#'+idForm).find('[for='+idInput+']');
                        elementLabel.addClass('error');
                    }
                }
            }
        }

        let clicked = false;
        function paymentNow(element, idMethod) {
            if (!clicked) {
                clicked = true;
                const email     = $('#email').val();
                const idProduct = $('#product_info_id').val();
                var idPrice     = 0;
                $(document).find('[data-product_price_id]').each(function(){
                    if($(this).hasClass('selected')){
                        idPrice = $(this).attr('data-product_price_id');
                        return false;
                    }
                })
                if(idPrice!=0&&idProduct!=''&&idMethod!=''){
                    let token   = "{{ csrf_token() }}";
                    $.ajax({
                        url         : '{{ route("main.paymentNow") }}',
                        type        : 'get',
                        dataType    : 'json',
                        data        : {
                            product_info_id         : idProduct,
                            product_price_id        : idPrice,
                            payment_method_info_id  : idMethod,
                            email
                        },
                        success     : function(response){
                            /* redirect qua trang thanh toán */
                            window.location.href = response.url;
                        }
                    });
                }
                // Reset the click status after a certain amount of time
                setTimeout(() => {
                    clicked = false;
                }, 1500);
            }
        }
    </script>
@endpush