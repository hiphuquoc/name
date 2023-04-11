{{-- @if(!empty($products)&&$products->isNotEmpty())
    
@else 
    <div id="js_loadMore_box" class="wallpaperGridBox">
        <div>Không có sản phẩm phù hợp!</div>
    </div>
@endif --}}
<div id="js_loadMore_box" class="wallpaperGridBox">
    @foreach($products as $product)
        @include('wallpaper.template.wallpaperItem', compact('product'))
    @endforeach
</div>