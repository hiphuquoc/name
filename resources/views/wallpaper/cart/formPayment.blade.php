@php
    $paymentMethods      = \App\Models\PaymentMethod::select('*')
                            ->where('code', '!=', 'cod')
                            ->get();
@endphp


    <div class="pageCartBox_left_item">
        <div class="pageCartBox_left_item_head">Thông tin</div>
        <div class="pageCartBox_left_item_body">
            <div style="margin-top:-0.5rem;">Nếu nhập email bạn sẽ được gửi thêm một bản để lưu trữ.</div>
            <div class="inputWithLabelInside">
                <label for="email">Email dùng nhận ảnh</label>
                <input type="text" id="email" name="email" onkeyup="validateWhenType(this, 'email')">
            </div>
        </div>
    </div>

    <div class="pageCartBox_left_item">
        <label class="pageCartBox_left_item_head" for="payment_method_info_id">Hình thức thanh toán</label>
        <input type="hidden" id="payment_method_info_id" name="payment_method_info_id" value="" required />
        <div class="pageCartBox_left_item_body">

            <div class="paymentMethodBox twoColumn">
                @foreach($paymentMethods as $method)
                    {{-- @php
                        $selected = null;
                        if($loop->index==0) $selected = 'selected';
                    @endphp --}}
                    <div class="paymentMethodBox_item"  onclick="chooseOptionPayment(this, {{ $method->id }});">
                        <div class="paymentMethodBox_item_logo">
                            <img src="{{ Storage::url($method->icon) }}" alt="{{ $method->name ?? null }}" title="{{ $method->name ?? null }}" />
                        </div>
                        <div class="paymentMethodBox_item_content">
                            <div class="paymentMethodBox_item_content_title">{{ $method->name ?? null }}</div>
                            <div class="paymentMethodBox_item_content_desc maxLine_1">{{ $method->description ?? null }}</div>
                        </div>
                    </div>

                @endforeach
            </div>

            {{-- <div class="paymentMethodBox twoColumn">
                <div class="paymentMethodBox_item selected" onclick="chooseOptionPayment();">
                    <div class="paymentMethodBox_item_logo">
                        <img src="/storage/images/icon-payment-zalopay.png" alt="Thanh toán qua Zalopay" title="Thanh toán qua Zalopay">
                    </div>
                    <div class="paymentMethodBox_item_content">
                        <div class="paymentMethodBox_item_content_title">Thanh toán qua Zalopay</div>
                        <div class="paymentMethodBox_item_content_desc maxLine_1">Quét mã QR /thanh toán ATM /Visa /Mastercard...</div>
                        <input type="hidden" name="payment_method_info_id" value="" />
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
@push('scriptCustom')
    <script type="text/javascript">
        function chooseOptionPayment(element, idMethod){
            const parentElement = $(element).parent();
            /* xóa selected tất cả phần tử */
            parentElement.children().each(function(){
                if($(this).hasClass('selected')) $(this).removeClass('selected');
            })
            /* bật lại cho phần tử được click */
            $(element).addClass('selected');
            $('#payment_method_info_id').val(idMethod);
        }
    </script>
@endpush