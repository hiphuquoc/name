<script type="application/ld+json">
    @php
        /* title */
        $title          = $item->seo->seo_title ?? $item->seo->title ?? null;

        /* description */
        $description    = $item->seo->seo_description ?? $item->seo->description ?? null;

        /* author */
        $author         = $item->seo->rating_author_name ?? config('main.author_name');
    @endphp
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
            "@type": "Person",
            "name": "{{ $author }}"
        },
        "image":{
            "@type": "ImageObject",
            "url": "{{ env('APP_URL').Storage::url($item->seo->image) }}",
            "width": "750",
            "height": "460"
        },
        "publisher": {
            "@type": "Organization",
            "name": "{{ $author }}",
            "logo": {
                "@type": "ImageObject",
                "url": "{{ config('main.logo') }}",
                "width": "750",
                "height": "460"
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