<div class="formBox">
    <div class="formBox_full">
        <!-- One Row -->
        <div class="formBox_full_item">
            <label class="form-label" for="name">Tiêu đề</label>
            <input class="form-control" name="name" value="{{ $content['name'] ?? '' }}" />
        </div>
        <!-- One Row -->
        <div class="formBox_full_item">
            <label class="form-label" for="content">Nội dung</label>
            <textarea class="form-control" name="content" rows="5">{{ $content['content'] ?? '' }}</textarea>
        </div>
    </div>
</div>