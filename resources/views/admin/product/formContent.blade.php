<div class="formBox">
    <div class="formBox_full">
        <!-- One Row -->
        <div class="formBox_full_item">
            <label class="form-label" for="name">Tiêu đề</label>
            <input class="form-control" name="name" value="{{ $content['name'] ?? '' }}" />
        </div>
        <!-- One Row -->
        <div class="formBox_full_item">
            <label class="form-label" for="en_name">Title (En)</label>
            <input class="form-control" name="en_name" value="{{ $content['en_name'] ?? '' }}" />
        </div>
        <!-- One Row -->
        <div class="formBox_full_item">
            <label class="form-label" for="content">Nội dung</label>
            <textarea class="form-control" name="content" rows="5">{{ $content['content'] ?? '' }}</textarea>
        </div>

        <!-- One Row -->
        <div class="formBox_full_item">
            <label class="form-label" for="en_content">Content (En)</label>
            <textarea class="form-control" name="en_content" rows="5">{{ $content['en_content'] ?? '' }}</textarea>
        </div>
    </div>
</div>