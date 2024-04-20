@php
    $title              = $itemSeo->seo_title ?? $item->seo->seo_title ?? null;
    $description        = $itemSeo->seo_description ?? $item->seo->seo_description ?? null;
    /* author */
    $author             = config('main.author_name');
    /* image */
    $imagePage          = public_path(config('image.default'));
    if(!empty($item->seo->image)) $imagePage = \App\Helpers\Image::getUrlImageCloud($item->seo->image);
    $size               = getimagesize($imagePage);
    $widthImagePage     = $size[0];
    $heigtImagePage     = $size[1];
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
        "inLanguage": "{{ $language }}",
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