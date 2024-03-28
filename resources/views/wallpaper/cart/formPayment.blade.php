@php
    $user               = Auth::user();
@endphp

<div class="pageCartBox_left_item">
    <div class="pageCartBox_left_item_head">{{ config('language.'.$language.'.data.information') }}</div>
    <div class="pageCartBox_left_item_body">
        <div style="margin-top:-0.5rem;">{{ config('language.'.$language.'.data.email_prompt') }}</div>
        <div class="inputWithLabelInside">
            <label for="email">{{ config('language.'.$language.'.data.email_receive') }}</label>
            <input type="text" id="email" name="email" value="{{ $user->email ?? null }}" onkeyup="validateWhenType(this, 'email')">
        </div>
    </div>
</div>

<div class="pageCartBox_left_item">
    <label class="pageCartBox_left_item_head" for="payment_method_info_id">{{ config('language.'.$language.'.data.payment_method') }}</label>
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