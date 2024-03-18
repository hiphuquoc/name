@if(!empty($itemSeo->contents))
    @foreach($itemSeo->contents as $content)
        <div class="sectionProductBox">
            {{-- <div class="sectionProductBox_title">
                <h2>{!! $titleBoxContent !!}</h2>
            </div> --}}
            <div class="sectionProductBox_content">
                {!! $content->content !!}
            </div>    
        </div>
    @endforeach
@endif
