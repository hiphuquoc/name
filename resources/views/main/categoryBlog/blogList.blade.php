@if(!empty($blogs)&&$blogs->isNotEmpty())
<div class="articleBox">
    @foreach($blogs as $blog)
        @php
            $title  = $blog->name ?? $blog->seo->title ?? $blog->seo->seo_title ?? null;
            $image  = !empty($blog->seo->image_small)&&file_exists(Storage::path($blog->seo->image_small)) ? Storage::url($blog->seo->image_small) : config('image.default');
        @endphp
        <div class="articleBox_item">
            <a href="/{{ $blog->seo->slug_full ?? null }}" title="{{ $title }}" class="articleBox_item_image">
                <img src="{{ Storage::url(config('image.loading_main_gif')) }}" data-src="{{ $image }}" alt="{{ $title }}" title="{{ $title }}" />
            </a>
            <div class="articleBox_item_content">
                <a href="/{{ $blog->seo->slug_full ?? null }}" title="{{ $title }}" class="articleBox_item_content_title">
                    <h2 class="maxLine_2">{{ $title }}</h2>
                </a>
                <div class="articleBox_item_content_subtitle">
                    @if(!empty($blog->seo->updated_at))
                    <span>
                        <i class="fa-regular fa-clock"></i>
                        {{ date('H:i\, d/m/Y', strtotime($blog->seo->updated_at)) }}
                    </span>
                    @endif
                    <span class="articleBox_item_content_subtitle_author">
                        <i class="fa-solid fa-user-pen"></i>
                        {{ $blog->seo->user->name ?? config('main.author_name') }}
                    </span>
                </div>
                <div class="articleBox_item_content_des maxLine_3">
                    {{ $blog->description ?? $blog->seo->description ?? null }}
                </div>
            </div>
        </div>
    @endforeach
</div>
@else 
    <div style="color:rgb(0,123,255);">Hiện chưa có bài viết nào trong chuyên mục này!</div>
@endif