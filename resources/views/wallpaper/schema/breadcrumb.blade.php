@php
    $nameHome   = !empty($language)&&$language=='en' ? 'Home' : 'Trang chủ';
    $xhtml      = '{
                    "@type": "ListItem",
                    "position": 1,
                    "name": "'.$nameHome.'",
                    "item": "'.env('APP_URL').'"
                }';
    $i          = 2;
    foreach($breadcrumb as $b){
        // dd($b);
        $xhtml .= ', ';
        $title  = $b->title ?? $b->seo_title;
        $slug   = $b->slug_full ?? null;
        $xhtml .= '{
                        "@type": "ListItem",
                        "position": '.$i.',
                        "name": "'.$title.'",
                        "item": "'.env('APP_URL').'/'.$slug.'"
                    }';
        ++$i;
    }
@endphp
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {!! $xhtml !!}
        ]
    }
</script>
