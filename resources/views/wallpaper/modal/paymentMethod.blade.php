@php
    $user               = Auth::user();
@endphp

<div id="modalPaymentMethod" class="modalBox">
    <div class="modalBox_bg" onClick="openCloseModal('modalPaymentMethod');"></div>
    <div class="modalBox_box">
        <form id="formModalPaymentMethod" method="get" style="width:100%;">
            <!-- hidden -->
            <div class="formModalBox_box_head">{{ config('language.'.$language.'.data.step_1_buy_now') }}</div>
            <div class="formModalBox_box_body">
                <div style="margin-top:-0.5rem;">{{ config('language.'.$language.'.data.note_fill_email') }}</div>
                <div class="inputWithLabelInside">  
                    <label for="email">{{ config('language.'.$language.'.data.email') }}</label>
                    <input type="text" id="email" name="email" onkeyup="validateWhenType(this, 'email')" value="{{ $user->email ?? null }}" />
                </div>
            </div>
            <div class="formModalBox_box_head">{{ config('language.'.$language.'.data.step_2_buy_now') }}</div>
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
    {{-- <script src="{{ asset('sources/admin/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ asset('sources/admin/app-assets/js/scripts/forms/form-select2.min.js') }}"></script> --}}
    <script type="text/javascript">

        let clicked = false;
        function paymentNow(element, idMethod) {
            if (!clicked) {
                clicked = true;
                const email = $('#email').val();
                const idProduct = $('#product_info_id').val();
                const language_sub = $('#language').val();
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
                        email,
                        language_sub, /* đặt tên để đồng bộ với tính năng paymentCart */
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