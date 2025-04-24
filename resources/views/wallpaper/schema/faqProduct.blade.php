@if(!empty($dataFaq))
    <script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        @foreach($dataFaq as $index => $faq)
        {
            "@type": "Question",
            "name": "{{ addslashes($faq['question']) }}",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "{!! addslashes($faq['answer']) !!}"
            }
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
    </script>
@endif