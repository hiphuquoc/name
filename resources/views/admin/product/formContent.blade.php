<div class="card" data-repeater-item>
    <div class="card-header border-bottom">
        <h4 class="card-title">
            Nội dung
            <i class="fa-solid fa-circle-xmark" data-repeater-delete></i>
        </h4>
    </div>
    <div class="card-body">
        <div class="formBox">
            <div class="formBox_full">
                <div class="flexBox">
                    <div class="flexBox_item">
                        <!-- One Row -->
                        <div class="formBox_full_item">
                            <label class="form-label" for="name">Tiêu đề (nẳm trong thẻ h2)</label>
                            <input class="form-control" name="name" value="{{ $content['name'] ?? '' }}" />
                        </div>
                    </div>
                    <div class="flexBox_item">
                        <!-- One Row -->
                        <div class="formBox_full_item">
                            <label class="form-label" for="en_name">Title - En (nẳm trong thẻ h2)</label>
                            <input class="form-control" name="en_name" value="{{ $content['en_name'] ?? '' }}" />
                        </div>
                    </div>
                </div>
                <div class="flexBox">
                    <div class="flexBox_item">
                        <!-- One Row -->
                        <div class="formBox_full_item">
                            <label class="form-label" for="content">Nội dung</label>
                            <textarea class="form-control" name="content" rows="5">{{ $content['content'] ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="flexBox_item">
                        <!-- One Row -->
                        <div class="formBox_full_item">
                            <label class="form-label" for="en_content">Content - En</label>
                            <textarea class="form-control" name="en_content" rows="5">{{ $content['en_content'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>