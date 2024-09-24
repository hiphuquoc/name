<div class="headerBottom">
    @php
        $contacts               = config('main_'.env('APP_NAME').'.info.'.env('APP_NAME').'.contacts');
        $phoneCustomerService   = null;
        foreach($contacts as $contact){
            if($contact['type']=='customer service') {
                $phoneCustomerService = $contact['phone'];
                break;
            }
        }
        $nameSupport            = config('language.'.$language.'.data.support');
        $nameGuide              = config('language.'.$language.'.data.download_guide');
    @endphp
    <a class="headerBottom_item" href="https://zalo.me/{{ $phoneCustomerService }}">
        <div class="headerBottom_item_icon">
            <img src="{{ Storage::url('images/svg/headphones.svg') }}" alt="{{ $nameSupport }}" title="{{ $nameSupport }}">
        </div>
        <div class="headerBottom_item_text">
            {{ $nameSupport }}
        </div>
    </a>
    <a id="js_loadLinkDownloadGuide" class="headerBottom_item" href="/">
        <div class="headerBottom_item_icon">
            <img src="{{ Storage::url('images/svg/book-open-cover.svg') }}" alt="{{ $nameGuide }}" title="{{ $nameGuide }}" />
        </div>
        <div class="headerBottom_item_text">
            {{ $nameGuide }}
        </div>
    </a>
    <div id="js_checkLoginAndSetShow_buttonMobile" class="headerBottom_item"></div>
</div>
@push('scriptCustom')
    <script defer type="text/javascript">
        $(window).ready(function(){
            loadLinkDownloadGuide('{{ $language }}');
        });

        function loadLinkDownloadGuide(language){
            let dataForm = {};
            dataForm.language = language;            
            const queryString = new URLSearchParams(dataForm).toString();
            fetch("/loadLinkDownloadGuide?" + queryString, {
                method: 'GET',
                mode: 'cors',
                // headers: {
                //     'Content-Type': 'application/json',
                //     'X-CSRF-TOKEN': '{{ csrf_token() }}'
                // }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                $('#js_loadLinkDownloadGuide').attr('href', data);
            })
            .catch(error => {
                console.error("Fetch request failed:", error);
            });
        }
    </script>
@endpush