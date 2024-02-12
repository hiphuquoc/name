<input type="hidden" id="tag_info_id" name="tag_info_id" value="{{ !empty($item->id)&&$type!='copy' ? $item->id : null }}" />
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
        {{-- <!-- One Row -->
        <div class="formBox_full_item">
            <span data-toggle="tooltip" data-placement="top" title="
                Là loại của chuyên mục phân loại theo Chủ đề - Phong cách - Sự kiện...
            ">
                <i class="explainInput" data-feather='alert-circle'></i>
                <label class="form-label" for="parent">Loại chuyên mục</label>
            </span>
            <select class="select2 form-select select2-hidden-accessible" name="type">
                @foreach(config('main.category_type') as $type)
                    @php
                        $selected   = null;
                        if(!empty($item->seo->type)&&$item->seo->type==$type['key']) $selected = 'selected';
                    @endphp
                    <option value="{{ $type['key'] }}" {{ $selected }}>{{ $type['name'] }}</option>
                @endforeach
            </select>
        </div> --}}
        <!-- One Row -->
        <div class="formBox_full_item">
            <span data-toggle="tooltip" data-placement="top" title="
                Nhập vào một số để thể hiện độ ưu tiên khi hiển thị cùng các Category khác (Số càng nhỏ càng ưu tiên cao - Để trống tức là không ưu tiên)
            ">
                <i class="explainInput" data-feather='alert-circle'></i>
                <label class="form-label" for="ordering">Thứ tự</label>
            </span>
            <input type="number" min="0" id="ordering" class="form-control" name="ordering" value="{{ old('ordering') ?? $item->seo->ordering ?? '' }}">
        </div>
        <!-- One Row -->
        <div class="formBox_column2_item_row">
            <span data-toggle="tooltip" data-placement="top" title="
                Category này kết nối Blog tin tức nào?
            ">
                <i class="explainInput" data-feather='alert-circle'></i>
                <label class="form-label" for="category_blog_info_id">Kết nối Chuyên mục blog</label>
            </span>
            <select class="select2 form-select select2-hidden-accessible" id="category_blog_info_id" name="category_blog_info_id[]" aria-hidden="true" multiple="true">
                @if(!empty($categoryBlogs))
                    @foreach($categoryBlogs as $c)
                        @php
                            $selected       = null;
                            if(!empty($item->categoryBlogs)&&$item->categoryBlogs->isNotEmpty()){
                                foreach($item->categoryBlogs as $cBlog){
                                    if($c->id==$cBlog->infoCategoryBlog->id) {
                                        $selected = 'selected';
                                        break;
                                    }
                                }
                            }
                        @endphp
                        <option value="{{ $c->id }}" {{ $selected }}>{{ $c->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>
        <!-- One Row -->
        <div class="formBox_full_item">
            <div class="form-check form-check-success">
                <input type="checkbox" class="form-check-input" name="flag_show" {{ !empty($item->flag_show)&&($item->flag_show==1) ? 'checked' : null }}>
                <label class="form-check-label" for="flag_show">Cho phép hiển thị trong danh sách</label>
            </div>
        </div>
        
    </div>
</div>