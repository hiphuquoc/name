<div class="emptyCartBox">
    @php
        $icon = file_get_contents('storage/images/svg/icon-blank-cart.svg');
    @endphp
    {!! $icon !!}
    <div class="emptyCart_text">{{ config('data_language_1.'.$language.'.your_cart_is_empty') }}</div> 
    <a href="/{{ config('language.'.$language.'.key') }}" class="emptyCartBox_button button" aria-label="{{ config('data_language_1.'.$language.'.continue_shopping') }}">{{ config('data_language_1.'.$language.'.continue_shopping') }}</a>
</div>