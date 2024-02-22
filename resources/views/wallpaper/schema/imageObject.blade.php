@if(!empty($data)&&$data->isNotEmpty())
    @php
        $xhtml              = null;
        $i                  = 1;
        foreach($data as $d){
            if(!empty($d)){
                if($i!=1) $xhtml .= ', ';
                $urlImage        = config('main.google_cloud_storage.default_domain').$d->file_cloud_wallpaper;
                $name           = $d->name ?? null;
                $description    = $name;
                $xhtml          .= '{
                                    "@type": "ImageObject",
                                    "contentUrl": "'.$urlImage.'",
                                    "name": "'.$name.'",
                                    "description": "'.$description.'"
                                }';
                ++$i;
            }
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