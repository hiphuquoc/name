@php
    if(!empty($language)&&$language=='en'){
        $title  = $item->en_seo->seo_title ?? $item->en_seo->title ?? null;
    }else {
        $title  = $item->seo->seo_title ?? $item->seo->title ?? null;
    }
@endphp
<script type="application/ld+json">
    {
        "@context": "https://schema.org/",
        "@type": "CreativeWorkSeries",
        "name": "{{ $title }}",
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "{{ $item->seo->rating_aggregate_star ?? '5' }}",
            "bestRating": "5",
            "ratingCount": "{{ $item->seo->rating_aggregate_count ?? '120' }}"
        }
    }
</script>