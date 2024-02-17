<input type="hidden" id="free_wallpaper_info_id" name="free_wallpaper_info_id" value="{{ !empty($item->id)&&$type!='copy' ? $item->id : null }}" />
<div class="formBox">
    <div class="formBox_full">
        <!-- One Row -->
        <div class="formBox_column2_item_row">
            <div class="inputWithNumberChacractor">
                <span data-toggle="tooltip" data-placement="top" title="
                    Đây là thẻ Alt mô tả của ảnh
                ">
                    <i class="explainInput" data-feather='alert-circle'></i>
                    <label class="form-label inputRequired" for="name">Thẻ Alt</label>
                </span>
                <div class="inputWithNumberChacractor_count" data-charactor="name">
                    {{ !empty($item->name) ? mb_strlen($item->name) : 0 }}
                </div>
            </div>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?? $item->name ?? null }}" required>
            <div class="invalid-feedback">{{ config('admin.massage_validate.not_empty') }}</div>
        </div>
        <!-- One Row -->
        <div class="formBox_column2_item_row">
            <div class="inputWithNumberChacractor">
                <span data-toggle="tooltip" data-placement="top" title="
                    Đây là thẻ Alt mô tả của ảnh (Bản tiếng Anh)
                ">
                    <i class="explainInput" data-feather='alert-circle'></i>
                    <label class="form-label inputRequired" for="en_name">Thẻ Alt (En)</label>
                </span>
                <div class="inputWithNumberChacractor_count" data-charactor="en_name">
                    {{ !empty($item->en_name) ? mb_strlen($item->en_name) : 0 }}
                </div>
            </div>
            <input type="text" class="form-control" id="en_name" name="en_name" value="{{ old('en_name') ?? $item->en_name ?? null }}" required readonly>
            <div class="invalid-feedback">{{ config('admin.massage_validate.not_empty') }}</div>
        </div>
        <!-- One Row -->
        <div class="formBox_full_item">
            <span data-toggle="tooltip" data-placement="top" title="
                Nhập vào một số để thể hiện độ ưu tiên khi hiển thị cùng các Category khác (Số càng nhỏ càng ưu tiên cao - Để trống tức là không ưu tiên)
            ">
                <i class="explainInput" data-feather='alert-circle'></i>
                <label class="form-label" for="ordering">Thứ tự</label>
            </span>
            <input type="number" min="0" id="ordering" class="form-control" name="ordering" value="{{ old('ordering') ?? $item->seo->ordering ?? null }}">
        </div>
        <!-- category/style/event -->
        @foreach(config('main.category_type') as $categoryType)
            <div class="formBox_full_item">
                <label class="form-label inputRequired">{{ $categoryType['name'] }}</label>
                <select class="select2 form-select select2-hidden-accessible" name="{{ $categoryType['key'] }}[]" multiple="true">
                    <option value="">- Lựa chọn -</option>
                    @if(!empty($categories))
                        @foreach($categories as $category)
                            @if(!empty($category->seo->type)&&$category->seo->type==$categoryType['key'])
                                @php
                                    $selected   = null;
                                    if(!empty($item->categories)){
                                        foreach($item->categories as $c) {
                                            if(!empty($c->infoCategory->id)&&$c->infoCategory->id==$category->id) {
                                                $selected = ' selected';
                                                break;
                                            }
                                        }
                                    }
                                    /* tất cả tag */
                                @endphp
                                <option value="{{ $category->id }}"{{ $selected }}>{{ $category->name }}</option>
                            @endif
                        @endforeach
                    @endif
                </select>
            </div>
        @endforeach
        <!-- One row -->
        <div class="formBox_full_item">
            @php
                $arrayTagName           = [];
                if(!empty($item->tags)){
                    foreach($item->tags as $tag){
                        if(!empty($tag->infoTag->name)) $arrayTagName[] = $tag->infoTag->name;
                    }
                }
                $strTagName             = implode(',', $arrayTagName);
            @endphp
            <label for="tag" class="form-label">Tag name</label>
            <input id="tag" name="tag" class="form-control" placeholder="Nhập tag name" value="{{ $strTagName }}">  
            {{-- onchange="autoFillNameAndEnName('ssdf');" --}}
        </div>
        <div class="formBox_full_item">
            <label class="form-label" for="description">Prompt Midjourney</label>
            <textarea class="form-control" name="description" rows="2">{{ $item->description ?? null }}</textarea>
        </div>
    </div>
</div>

@push('scriptCustom')
    <script type="text/javascript">
        var strTag = {!! json_encode($arrayTag) !!};
        new Tagify(document.querySelector("#tag"), {
            whitelist: strTag,
            maxTags: 100, // allows to select max items
            dropdown: {
                maxItems: 20, // display max items
                classname: "tags-inline", // Custom inline class
                enabled: 0,
                closeOnSelect: false
            }
        });
    </script>
@endpush