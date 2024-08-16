
@php
    $categorySeo            = new \stdClass;
    foreach($category->seos as $seo){
        if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language){
            $categorySeo    = $seo;
            break;
        }
    }
    $categoryName           = $categorySeo->infoSeo->title ?? null;
    $categoryUrl            = env('APP_URL').'/'.$categorySeo->infoSeo->slug_full;       
@endphp
<a href="{{ $categoryUrl }}" class="categoryGrid_box_item_image">
    @if(!empty($category->files)&&$category->files->count()>0)
        @foreach($category->files as $file)
            @php
                $categoryThumbMini  = \App\Helpers\Image::getUrlImageMiniByUrlImage($file->file_path); 
                $categoryThumbSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($file->file_path); 
                $active             = $loop->index == 0 ? ' active' : '';
            @endphp
            <img class="lazyload {{ $active }}" src="{{ $categoryThumbMini }}" data-src="{{ $categoryThumbSmall }}" alt="{{ $categoryName }}" title="{{ $categoryName }}" />
        @endforeach
    @endif
</a>
<div class="categoryGrid_box_item_content">
    <a href="{{ $categoryUrl }}" class="categoryGrid_box_item_content_title">
        <h2>{{ $categoryName }}</h2>
    </a>
    @if(!empty($category->tags)&&$category->tags->isNotEmpty())
        <div class="categoryGrid_box_item_content_list">
            @foreach($category->tags as $tag)
                @foreach($tag->infoTag->seos as $tagSeo)
                    @if($tagSeo->infoSeo->language==$language)
                        <a href="/{{ $tagSeo->infoSeo->slug_full }}">{{ $tagSeo->infoSeo->title }}</a>
                        @break
                    @endif
                @endforeach
            @endforeach
        </div>
    @endif
</div>