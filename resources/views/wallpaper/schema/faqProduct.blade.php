@if(!empty($dataFaq))
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                @foreach($dataFaq as $faq)
                    @if($loop->index!=0) 
                        ,
                    @endif
                    {
                        "@type": "Question",
                        "name": "{!! $faq['question'] !!}",
                        "acceptedAnswer": {
                            "@type": "Answer",
                            "text": "{!! $faq['answer'] !!}",
                        }
                    }
                @endforeach
            ]
        }
    </script>
@endif