

<div class="searchViewBeforeOfBlog_boxItem customScrollBar-y">
    @foreach($blogs as $blog)
        @foreach($blog->seos as $seo)
            @if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language)
                @php
                    $title      = $seo->infoSeo->title;
                    $url        = $seo->infoSeo->slug_full;
                    /* giá */
                    $image      = Storage::url(config('image.loading_main_gif'));
                    if(!empty($seo->infoSeo->image)) $image = \App\Helpers\Image::getUrlImageLargeByUrlImage($seo->infoSeo->image);
                @endphp
                <a href="/{{ $url }}" class="searchViewBeforeOfBlog_boxItem_item">
                    <div class="searchViewBeforeOfBlog_boxItem_item_image">
                        <img src="{{ $image }}" alt="{{ $title }}" title="{{ $title }}" />
                    </div>
                    <div class="searchViewBeforeOfBlog_boxItem_item_content">
                        <div class="searchViewBeforeOfBlog_boxItem_item_content_title maxLine_2">
                            {{ $title }}
                        </div>
                        <div class="searchViewBeforeOfBlog_boxItem_item_content_info">
                            <div class="searchViewBeforeOfBlog_boxItem_item_content_info_item maxLine_1">
                                <i class="fa-solid fa-user"></i>Name Admin
                            </div>
                            <div class="searchViewBeforeOfBlog_boxItem_item_content_info_item maxLine_1">
                                <i class="fa-regular fa-clock"></i>{{ date('d \t\h\á\n\g m, Y', strtotime($seo->infoSeo->created_at)) }}
                            </div>
                            {{-- <div class="searchViewBeforeOfBlog_boxItem_item_content_info_item maxLine_1">
                                <i class="fa-solid fa-eye"></i>{{ $blog->viewed }}
                            </div>  --}}
                            {{-- <div class="searchViewBeforeOfBlog_boxItem_item_content_info_item maxLine_1">
                                <i class="fa-solid fa-share"></i>{{ $blog->shared }}
                            </div> --}}
                        </div>
                    </div>
                </a>
                @break;
            @endif
        @endforeach
    @endforeach
</div>
<!-- button viewAll -->
@php
    $url    = route('routing', ['slug' => config('language.'.$language.'.slug_page_premium_wallpaper')]).'?search='.$keySearch;
@endphp
<a href="{{ $url }}" class="searchViewBeforeOfBlog_viewAll">
    <div>{{ config('language.'.$language.'.data.view_all') }} (<span>{{ $count }}</span>)</div>
    <i class="fa-solid fa-angles-right"></i>
</a>