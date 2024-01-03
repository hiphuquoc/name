@if(!empty($data)&&$data->isNotEmpty())
    @php
        $xhtml              = null;
        $i                  = 1;
        foreach($data as $d){
            if(!empty($d)){
                if($i!=1) $xhtml .= ', ';
                $urlImage        = config('main.google_cloud_storage.default_domain').$wallpaper->infoWallpaper->file_cloud_wallpaperle_url_hosting;
                $name           = $d->name ?? null;
                $description    = $d->description ?? $d->name;
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