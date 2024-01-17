<form id="formViewBy" action="{{ route('ajax.settingViewBy') }}" method="GET">
    @include('wallpaper.template.sortContent', [
        'language'      => $language ?? 'vi',
        'totalSet'      => $totalSet,
        'totalWallpaper'    => $totalWallpaper,
        'viewBy'        => $viewBy,
        'categories'    => $categories ?? null,
        'styles'        => $styles ?? null,
        'events'        => $events ?? null,
        'categoryChoose'    => $categoryChoose ?? null,
        'styleChoose'    => $styleChoose ?? null,
        'eventChoose'    => $eventChoose ?? null
    ])
</form>

@pushonce('scriptCustom')
    <script type="text/javascript">
        $(document).ready(function () {
            showSortBox();
        });

        function showSortBox(){
            const type      = "{{ $item->seo->type ?? 'set' }}";
            const id        = "{{ $item->id ?? 0 }}";
            const totalSet  = "{{ $totalSet ?? 0 }}";
            const totalWallpaper = "{{ $totalWallpaper ?? 0 }}";
            $.ajax({
                url: "{{ route('ajax.showSortBox') }}",
                type: 'get',
                dataType: 'html',
                data: {
                    type, 
                    id,
                    totalSet,
                    totalWallpaper
                },
            }).done(function (response) {
                $('#formViewBy').html(response);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Ajax request failed: " + textStatus, errorThrown);
            });
        }
    </script>
@endpushonce