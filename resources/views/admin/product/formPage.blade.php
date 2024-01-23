<input type="hidden" id="product_info_id" name="product_info_id" value="{{ !empty($item->id)&&$type!='copy' ? $item->id : null }}" />
<div class="formBox">
    <div class="formBox_full">
        <!-- One Row -->
        <div class="formBox_column2_item_row">
            <div class="inputWithNumberChacractor">
                <span data-toggle="tooltip" data-placement="top" title="
                    Đây là Tiêu đề được hiển thị trên website
                ">
                    <i class="explainInput" data-feather='alert-circle'></i>
                    <label class="form-label inputRequired" for="name">Tiêu đề Trang</label>
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
                    Đây là Tiêu đề được hiển thị trên website (Bản tiếng Anh)
                ">
                    <i class="explainInput" data-feather='alert-circle'></i>
                    <label class="form-label inputRequired" for="en_name">Title (En)</label>
                </span>
                <div class="inputWithNumberChacractor_count" data-charactor="en_name">
                    {{ !empty($item->en_name) ? mb_strlen($item->en_name) : 0 }}
                </div>
            </div>
            <input type="text" class="form-control" id="en_name" name="en_name" value="{{ old('en_name') ?? $item->en_name ?? null }}" required>
            <div class="invalid-feedback">{{ config('admin.massage_validate.not_empty') }}</div>
        </div>
        <!-- One Row -->
        <div class="formBox_column2_item_row">
            <div class="inputWithNumberChacractor">
                <span data-toggle="tooltip" data-placement="top" title="
                    Đây là Mô tả được hiển thị trên website
                ">
                    <i class="explainInput" data-feather='alert-circle'></i>
                    <label class="form-label inputRequired" for="description">Mô tả Trang</label>
                </span>
                <div class="inputWithNumberChacractor_count" data-charactor="description">
                    {{ !empty($item->description) ? mb_strlen($item->description) : 0 }}
                </div>
            </div>
            <textarea class="form-control" id="description"  name="description" rows="5" required>{{ old('description') ?? $item->description ?? null }}</textarea>
            <div class="invalid-feedback">{{ config('admin.massage_validate.not_empty') }}</div>
        </div>
        <!-- One Row -->
        <div class="formBox_column2_item_row">
            <div class="inputWithNumberChacractor">
                <span data-toggle="tooltip" data-placement="top" title="
                    Đây là Mô tả được hiển thị trên website
                ">
                    <i class="explainInput" data-feather='alert-circle'></i>
                    <label class="form-label inputRequired" for="en_description">Desription (En)</label>
                </span>
                <div class="inputWithNumberChacractor_count" data-charactor="en_description">
                    {{ !empty($item->en_description) ? mb_strlen($item->en_description) : 0 }}
                </div>
            </div>
            <textarea class="form-control" id="en_description"  name="en_description" rows="5" required>{{ old('en_description') ?? $item->en_description ?? null }}</textarea>
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
                            <option value="{{ $category->id }}"{{ $selected }}>{{ $category->name }}</option>
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
                            <option value="{{ $category->id }}"{{ $selected }}>{{ $category->name }}</option>
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
                            <option value="{{ $category->id }}"{{ $selected }}>{{ $category->name }}</option>
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
    </div>
</div>

@push('scripts-custom')
    <script type="text/javascript">
        

    </script>
@endpush