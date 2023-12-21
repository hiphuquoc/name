@php
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
                @include('wallpaper.template.paymentMethod')
            </div>
            
        </div>
    </div>
@push('scriptCustom')
    <script type="text/javascript">
        $(window).ready(function(){
            validateWhenType($('#email'), 'email');
        });
    </script>
@endpush