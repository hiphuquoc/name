<div class="shareSocialBox">
    {{-- <div class="shareSocialBox_item">
        <div class="shareSocialBox_item_text">
            {{ config('language.'.$language.'.data.share') }}
        </div>
    </div> --}}
    <div class="shareSocialBox_item">
        <a href="https://www.facebook.com/sharer.php?u={{ URL::current() }}" class="shareSocialBox_item_icon facebook" rel="nofollow" aria-label="{{ config('language.'.$language.'.data.share') }} facebook"></a>
    </div>
    <div class="shareSocialBox_item">
        <a href="https://twitter.com/intent/tweet?url={{ URL::current() }}" class="shareSocialBox_item_icon twitter" rel="nofollow" aria-label="{{ config('language.'.$language.'.data.share') }} twitter"></a>
    </div>
    {{-- <div class="shareSocialBox_item">
        <a href="https://www.instagram.com/?url={{ URL::current() }}" target="_blank" rel="nofollow"></a>
    </div> --}}
    {{-- <div class="shareSocialBox_item">
        <a href="" class="shareSocialBox_item_icon email" target="_blank" rel="nofollow"></a>
    </div> --}}
</div>