@php
    $provinces      = \App\Models\Province::all();
@endphp

<div id="modalPaymentMethod" class="modalBox">
    <div class="modalBox_bg" onClick="openCloseModal('modalPaymentMethod');"></div>
    <div class="modalBox_box">
        <form id="formModalPaymentMethod" method="get" style="width:100%;">
            <!-- hidden -->
            <div class="formModalBox_box_head">Chọn hình thức thanh toán</div>
            <div class="formModalBox_box_body">
                
                <div class="paymentMethodBox">
                    <div class="paymentMethodBox_item" onClick="noticeContrustion();">
                        <div class="paymentMethodBox_item_logo">
                            <img src="{{ Storage::url('images/icon-payment-momo.png') }}" alt="thanh toán momo" title="thanh toán momo" />
                        </div>
                        <div class="paymentMethodBox_item_content">
                            <div class="paymentMethodBox_item_content_title">Thanh toán MOMO</div>
                            <div class="paymentMethodBox_item_content_desc maxLine_1">Quét mã QR nhanh chóng, tiện lợi.</div>
                        </div>
                    </div>
                    <div class="paymentMethodBox_item" onClick="noticeContrustion();">
                        <div class="paymentMethodBox_item_logo">
                            <img src="{{ Storage::url('images/icon-payment-zalopay.png') }}" alt="thanh toán zalopay" title="thanh toán zalopay" />
                        </div>
                        <div class="paymentMethodBox_item_content">
                            <div class="paymentMethodBox_item_content_title">Thanh toán Zalopay</div>
                            <div class="paymentMethodBox_item_content_desc maxLine_1">Quét mã QR nhanh chóng, tiện lợi.</div>
                        </div>
                    </div>
                </div>

            </div>
            {{-- <div class="formModalBox_box_footer">
                <div class="formModalBox_box_footer_item button" tabindex="6" onclick="submitForm('formModalSubmit')">
                    Gửi yêu cầu
                </div>
            </div> --}}
        </form>
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
        
    </script>
@endpush