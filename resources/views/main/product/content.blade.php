@foreach($contents as $content)
    <div class="sectionProductBox">
        <div class="sectionProductBox_title">
            <h2>{{ $content->name }}</h2>
        </div>
        <div class="sectionProductBox_content">
            {!! $content->content !!}
        </div>    
    </div>
@endforeach
