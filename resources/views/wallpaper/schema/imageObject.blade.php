@if(!empty($data)&&$data->isNotEmpty())
    @php
        $xhtml              = null;
        $i                  = 1;
        foreach($data as $d){
            if($i!=1) $xhtml .= ', ';
            $urlImage        = env('APP_URL').$d->file_url_hosting;
            // if(empty($language)||$language=='vi'){
            //     $name           = $d->name;
            //     $description    = $d->description ?? $d->name;
            // }
            $name           = $d->name;
            $description    = $d->description ?? $d->name;
            $xhtml          .= '{
                                "@type": "ImageObject",
                                "contentUrl": "'.$urlImage.'",
                                "name": "'.$name.'",
                                "description": "'.$description.'"
                            }';
            ++$i;
        }
    @endphp
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ItemList",
        "itemListElement": [
            {!! $xhtml !!}
        ]
    }
    </script>
@endif