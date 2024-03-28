<div class="formBox">
    <div class="formBox_full">
        <!-- One Row -->
        @php
            $chatgptDataAndEvent = [];
            foreach($prompts as $prompt){
                if($language=='vi'){
                    if($prompt->reference_name=='title'&&$prompt->type=='auto_content'){
                        $chatgptDataAndEvent = \App\Helpers\Charactor::generateChatgptDataAndEvent($item, $prompt, $language, 'title');
                        break;
                    }
                }else {
                    if($prompt->reference_name=='title'&&$prompt->type=='translate_content'){
                        $chatgptDataAndEvent = \App\Helpers\Charactor::generateChatgptDataAndEvent($item, $prompt, $language, 'title');
                        break;
                    }
                }
            }
        @endphp
        <div class="formBox_column2_item_row">
            <div class="inputWithNumberChacractor">
                <span data-toggle="tooltip" data-placement="top" title="
                    Đây là Tiêu đề được hiển thị trên website
                ">
                    <i class="explainInput" data-feather='alert-circle'></i>
                    <label class="form-label inputRequired" for="title">Tiêu đề Trang</label>
                    @if(!empty($chatgptDataAndEvent['eventChatgpt']))
                        <i class="fa-solid fa-arrow-rotate-left reloadContentIcon" onclick="{{ $chatgptDataAndEvent['eventChatgpt'] ?? null }}"></i>
                    @endif
                </span>
                <div class="inputWithNumberChacractor_count" data-charactor="title">
                    {{ !empty($itemSeo->title) ? mb_strlen($itemSeo->title) : 0 }}
                </div>
            </div>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') ?? $itemSeo->title ?? null }}" {{ $chatgptDataAndEvent['dataChatgpt'] ?? null }} required>
            <div class="invalid-feedback">{{ config('admin.massage_validate.not_empty') }}</div>
        </div>

        @if($language=='vi')
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
        <!-- One Row -->
        <div class="formBox_full_item">
            <span data-toggle="tooltip" data-placement="top" title="
                Đây là mã sản phẩm dùng để tìm sản phẩm trên kho
            ">
                <i class="explainInput" data-feather='alert-circle'></i>
                <label class="form-label inputRequired" for="code">Mã sản phẩm</label>
            </span>
            <input type="number" min="0" id="code" class="form-control" name="code" value="{{ old('code') ?? $item->code ?? null }}" required />
        </div>
        <!-- One Row -->
        <div class="formBox_full_item">
            <label class="form-label inputRequired">Chủ đề</label>
            <select class="select2 form-select select2-hidden-accessible" name="categories[]" multiple="true">
                <option value="">- Lựa chọn -</option>
                @if(!empty($categories))
                    @foreach($categories as $category)
                        @if(!empty($category->seo->type)&&$category->seo->type=='category_info')
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
                            @endphp
                            <option value="{{ $category->id }}"{{ $selected }}>{{ $category->seo->title }}</option>
                        @endif
                    @endforeach
                @endif
            </select>
        </div>
        <!-- One Row -->
        <div class="formBox_full_item">
            <label class="form-label inputRequired">Phong cách</label>
            <select class="select2 form-select select2-hidden-accessible" name="categories[]" multiple="true">
                <option value="">- Lựa chọn -</option>
                @if(!empty($categories))
                    @foreach($categories as $category)
                        @if(!empty($category->seo->type)&&$category->seo->type=='style_info')
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
                            @endphp
                            <option value="{{ $category->id }}"{{ $selected }}>{{ $category->seo->title }}</option>
                        @endif
                    @endforeach
                @endif
            </select>
        </div>
        <!-- One Row -->
        <div class="formBox_full_item">
            <label class="form-label inputRequired">Sự kiện</label>
            <select class="select2 form-select select2-hidden-accessible" name="categories[]" multiple="true">
                <option value="">- Lựa chọn -</option>
                @if(!empty($categories))
                    @foreach($categories as $category)
                        @if(!empty($category->seo->type)&&$category->seo->type=='event_info')
                            @php
                                $selected   = null;
                                if(!empty($item->categories)){
                                    foreach($item->categories as $c) {
                                        if(!empty($c->infoCategory->seo->type)&&$c->infoCategory->seo->type=='event_info'&&!empty($c->infoCategory->id)&&$c->infoCategory->id==$category->id) {
                                            $selected = ' selected';
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            <option value="{{ $category->id }}"{{ $selected }}>{{ $category->seo->title }}</option>
                        @endif
                    @endforeach
                @endif
            </select>
        </div>
        <!-- One Row -->
        <div class="flexBox">
            <div class="flexBox_item">
                <div class="formBox_full_item">
                    <label class="form-label inputRequired" for="price">Giá trọn bộ $</label>
                    <input type="text" class="form-control" id="price" name="price" value="{{ old('price') ?? $item->price ?? null }}" required />
                </div>
            </div>
            <div class="flexBox_item">
                <div class="formBox_full_item">
                    <label class="form-label" for="price_before_promotion">Giá trước KM $</label>
                    <input type="text" class="form-control" id="price_before_promotion" name="price_before_promotion" value="{{ old('price_before_promotion') ?? $item->price_before_promotion ?? null }}" />
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts-custom')
    <script type="text/javascript">
        

    </script>
@endpush