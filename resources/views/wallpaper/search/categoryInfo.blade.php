

<div class="searchViewBeforeOfCategory_boxItem customScrollBar-y">
    <div class="">
        @foreach($categories as $category)
            @foreach($category->seos as $seo)
                @if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language)
                    <a href="/{{ $seo->infoSeo->slug_full }}" class="searchViewBeforeOfCategory_boxItem_item">
                        <div>{{ $seo->infoSeo->title }}</div> 
                        <div>{{ $category->products->count() }} bộ hình nền</div>
                    </a>
                    @break
                @endif
            @endforeach
        @endforeach
    </div>
</div>