<div class="sortBox">
    <div class="sortBox_left">
        <div>
            @if(empty($language)||$language=='vi')
                <span id="js_filterProduct_count" class="highLight">
                    {{ $total }}
                </span> {{ $viewBy=='set' ? 'bộ hình nền' : 'hình nền' }}
            @else 
                <span id="js_filterProduct_count" class="highLight">
                    {{ $total }}
                </span> {{ $viewBy=='set' ? 'set wallpapers' : 'wallpapers' }}
            @endif
        </div>
    </div>
    <div class="sortBox_right">
        <div class="sortBox_right_item">
            @if(empty($language)||$language=='vi')
                <div style="min-width:100px;">Duyệt theo:</div>
            @else 
                <div style="min-width:100px;">Sort by:</div>
            @endif
            <select style="max-width:100px;">
                @foreach(config('main.view_by') as $key => $value)
                    @php
                        $selected = null;
                        if($viewBy==$key) $selected = 'selected';
                    @endphp
                    <option value="{{ $key }}" {{ $selected }}>
                        @if(empty($language)||$language=='vi')
                            {{ $value['name'] }}
                        @else 
                            {{ $value['en_name'] }}
                        @endif
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>