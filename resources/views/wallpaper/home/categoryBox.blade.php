<div class="categoryBox">
    {{-- <div class="container"> --}}
        <div class="categoryBox_title">
            <h2>{!! $title ?? null !!}</h2>
        </div>
        <div class="categoryBox_box">
        @include('wallpaper.template.wallpaperGrid', compact('products'))
        </div>
    {{-- </div> --}}
</div>