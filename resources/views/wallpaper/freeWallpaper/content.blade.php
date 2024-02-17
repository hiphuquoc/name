@foreach($contents as $content)
    @php
        $titleBoxContent        = null;
        $contentBoxContent      = null;
        if(empty($language)||$language=='vi'){
            if(!empty($content->name)&&!empty($content->content)){
                $titleBoxContent    = $content->name;
                $contentBoxContent  = $content->content;
            }
        }else {
            if(!empty($content->en_name)&&!empty($content->en_content)){
                $titleBoxContent    = $content->en_name;
                $contentBoxContent  = $content->en_content;
            }
        }
    @endphp
    @if(!empty($titleBoxContent)&&!empty($contentBoxContent))
        <div class="sectionProductBox">
            <div class="sectionProductBox_title">
                <h2>{!! $titleBoxContent !!}</h2>
            </div>
            <div class="sectionProductBox_content">
                {!! $contentBoxContent !!}
            </div>    
        </div>
    @endif
@endforeach
