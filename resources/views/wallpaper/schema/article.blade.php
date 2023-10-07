@php
    if(!empty($language)&&$language=='en'){
        $title          = $item->en_seo->seo_title ?? $item->en_seo->title ?? null;
        $description    = $item->en_seo->seo_description ?? $item->en_seo->description ?? null;
    }else {
        $title          = $item->seo->seo_title ?? $item->seo->title ?? null;
        $description    = $item->seo->seo_description ?? $item->seo->description ?? null;
    }
    /* author */
    $author             = $item->seo->rating_author_name ?? config('main.author_name');
    $imagePage          = env('APP_URL').Storage::url($item->seo->image);
    $widthImagePage     = 750;
    $heigtImagePage     = 460;
    if(file_exists($imagePage)){
        $infoImagePage      = getimagesize($imagePage);
        $widthImagePage     = $infoImagePage[0];
        $heigtImagePage     = $infoImagePage[1];
    }
    
    $imageAuthor        = env('APP_URL').Storage::url(config('main.logo_main'));
    $widthImageAuthor   = 500;
    $heightImageAuthor  = 500;
    if(file_exists($imageAuthor)){
        $infoImageAuthor    = getimagesize($imageAuthor);
        $widthImageAuthor   = $infoImageAuthor[0];
        $heightImageAuthor  = $infoImageAuthor[1];
    }
@endphp

<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Article",
        "@id": "{{ URL::current() }}#website",
        "inLanguage": "vi",
        "headline": "{{ $author }} Article",
        "datePublished": "{{ !empty($item->seo->created_at) ? date('c', strtotime($item->seo->created_at)) : null }}",
        "dateModified": "{{ !empty($item->seo->updated_at) ? date('c', strtotime($item->seo->updated_at)) : null }}",
        "name": "{{ $title }}",
        "description": "{{ $description }}",
        "url": "{{ URL::current() }}",
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "{{ URL::current() }}"
        },
        "author":{
            "@type": "Organization",
            "name": "{{ $author }}",
            "url": "{{ env('APP_URL') }}"
        },
        "image":{
            "@type": "ImageObject",
            "url": "{{ $imagePage }}",
            "width": "{{ $widthImagePage }}",
            "height": "{{ $heigtImagePage }}"
        },
        "publisher": {
            "@type": "Organization",
            "name": "{{ $author }}",
            "logo": {
                "@type": "ImageObject",
                "url": "{{ $imageAuthor }}",
                "width": "{{ $widthImageAuthor }}",
                "height": "{{ $heightImageAuthor }}"
            }
        },
        "potentialAction": {
            "@type": "ReadAction",
            "target": [
                {
                    "@type": "EntryPoint",
                    "urlTemplate": "{{ URL::current() }}"
                }
            ]
        }
    }
</script>