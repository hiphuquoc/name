@php
    $paymentMethods     = \App\Models\PaymentMethod::select('*')
                            ->where('code', '!=', 'cod')
                            ->get();
    $user               = Auth::user();
@endphp

    @if(!empty($language)&&$language=='en')
        <div class="pageCartBox_left_item">
            <div class="pageCartBox_left_item_head">Infomation</div>
            <div class="pageCartBox_left_item_body">
                <div style="margin-top:-0.5rem;">If you enter your email, you will be sent an additional copy to store.</div>
                <div class="inputWithLabelInside">
                    <label for="email">Email used to receive photos</label>
                    <input type="text" id="email" name="email" value="{{ $user->email ?? null }}" onkeyup="validateWhenType(this, 'email')">
                </div>
            </div>
        </div>
    @else 
        <div class="pageCartBox_left_item">
            <div class="pageCartBox_left_item_head">Thông tin</div>
            <div class="pageCartBox_left_item_body">
                <div style="margin-top:-0.5rem;">Nếu nhập email bạn sẽ được gửi thêm một bản để lưu trữ.</div>
                <div class="inputWithLabelInside">
                    <label for="email">Email dùng nhận ảnh</label>
                    <input type="text" id="email" name="email" value="{{ $user->email ?? null }}" onkeyup="validateWhenType(this, 'email')">
                </div>
            </div>
        </div>
    @endif

    <div class="pageCartBox_left_item">
        @if(!empty($language)&&$language=='en')
            <label class="pageCartBox_left_item_head" for="payment_method_info_id">Payment method</label>
        @else 
            <label class="pageCartBox_left_item_head" for="payment_method_info_id">Hình thức thanh toán</label>
        @endif
        <input type="hidden" id="payment_method_info_id" name="payment_method_info_id" value="" required />
        <div class="pageCartBox_left_item_body">

            <div class="paymentMethodBox twoColumn">
                @foreach($paymentMethods as $method)
                    {{-- @php
                        $selected = null;
                        if($loop->index==0) $selected = 'selected';
                    @endphp --}}
                    {{-- @php
                        if($method->code=='zalopay') continue;
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
        $(window).ready(function(){
            validateWhenType($('#email'), 'email');
        });

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