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

        let clicked = false;
        function paymentNow(element, idMethod) {
            if (!clicked) {
                clicked = true;
                const email = $('#email').val();
                const idProduct = $('#product_info_id').val();
                let idPrice = 0;
                $(document).find('[data-product_price_id]').each(function() {
                    if ($(this).hasClass('selected')) {
                        idPrice = $(this).attr('data-product_price_id');
                        return false;
                    }
                });
                if (idPrice != 0 && idProduct != '' && idMethod != '') {
                    const queryParams = new URLSearchParams({
                        product_info_id: idProduct,
                        product_price_id: idPrice,
                        payment_method_info_id: idMethod,
                        email: email
                    }).toString();

                    fetch("{{ route('main.paymentNow') }}?" + queryParams, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        /* redirect qua trang thanh toán */
                        window.location.href = data.url;
                    })
                    .catch(error => {
                        console.error("Fetch request failed:", error);
                    });

                    // Reset the click status after a certain amount of time
                    setTimeout(() => {
                        clicked = false;
                    }, 1500);
                }
            }
        }
    </script>
@endpush