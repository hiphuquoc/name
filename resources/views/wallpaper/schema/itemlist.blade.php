@if(!empty($data)&&$data->isNotEmpty())
    @php
        $xhtml              = null;
        $i                  = 1;
        foreach($data as $d){
            if(!empty($d->seo->slug_full)){
                if($i!=1) $xhtml .= ', ';
                if(!empty($language)&&$language=='en'&&!empty($d->en_seo->slug_full)){
                    $url    = env('APP_URL').'/'.$d->en_seo->slug_full;
                }else {
                    $url    = env('APP_URL').'/'.$d->seo->slug_full;
                }
                $xhtml  .= '{
                            "@type": "ListItem",
                            "position": '.$i.',
                            "url": "'.$url.'"
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