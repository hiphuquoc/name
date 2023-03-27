<script type="application/ld+json">
    {
        "@context": "https://schema.org/",
        "@type": "CreativeWorkSeries",
        "name": "{{ $item->seo->seo_title ?? $item->seo->title ?? null }}",
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "{{ $item->seo->rating_aggregate_star ?? '5' }}",
            "bestRating": "5",
            "ratingCount": "{{ $item->seo->rating_aggregate_count ?? '120' }}"
        }
    }
</script>