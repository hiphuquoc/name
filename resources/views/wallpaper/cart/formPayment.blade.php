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
                    @php
                        $nameMethodPayment          = empty($language)||$language=='vi' ? $method->name : $method->en_name;
                        $descriptionMethodPayment   = empty($language)||$language=='vi' ? $method->description : $method->en_description;
                    @endphp
                    <div class="paymentMethodBox_item"  onclick="chooseOptionPayment(this, {{ $method->id }});">
                        <div class="paymentMethodBox_item_logo">
                            <img src="{{ Storage::url($method->icon) }}" alt="{{ $nameMethodPayment ?? null }}" title="{{ $nameMethodPayment ?? null }}" />
                        </div>
                        <div class="paymentMethodBox_item_content">
                            <div class="paymentMethodBox_item_content_title">{{ $nameMethodPayment ?? null }}</div>
                            <div class="paymentMethodBox_item_content_desc maxLine_1">{{ $descriptionMethodPayment ?? null }}</div>
                        </div>
                    </div>

                @endforeach
            </div>
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