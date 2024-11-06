<div class="pageAdminWithRightSidebar_main_rightSidebar_item buttonAction">
    @if(!empty($itemSeo->slug_full))
        <a href="/{{ $itemSeo->slug_full }}" target="_blank" style="font-size:1.4rem;"><i class="fa-regular fa-eye"></i></a>
    @endif
    <a href="{{ route($routeBack) }}" type="button" class="btn btn-secondary waves-effect waves-float waves-light">Quay lại</a>
    <button type="submit" class="btn btn-success waves-effect waves-float waves-light" aria-label="Lưu">Lưu</button>
    <div class="form-check form-check-success" style="margin-top:0.75rem;">
        <input type="checkbox" class="form-check-input" id="index_google" name="index_google" />
        <label class="form-check-label" for="index_google">Báo google index</label>
    </div>
</div>
<div class="pageAdminWithRightSidebar_main_rightSidebar_item buttonAction">
    <div class="btn btn-danger waves-effect waves-float waves-light" aria-label="Xóa trang" data-bs-toggle="modal" data-bs-target="#modalChooseLanguageBeforeDeletePage" style="width:100%;">Xóa ngôn ngữ</div>
</div>
<div class="pageAdminWithRightSidebar_main_rightSidebar_item">
    <div class="actionBox">
        @if($language=='vi')
            <div class="actionBox_item maxLine_1" data-bs-toggle="modal" data-bs-target="#modalChooseProduct" onclick="loadProduct(), loadThemeProductChoosed()">
                <i class="fa-solid fa-robot"></i>Viết bài Gợi ý
            </div>
        @else   
            <div class="actionBox_item maxLine_1" onClick="callAI('translate_content')">
                <i class="fa-solid fa-language"></i>Dịch toàn trang (trực quan)
            </div>
            <!-- Start:: trường hợp không có bản lưu auto job nào mới hiện ra nút nhấn -->
            @php
                $promptTranslateContent = [];
                foreach($prompts as $prompt){
                    if($prompt->reference_name=='content'&&$prompt->type=='translate_content'){
                        $promptTranslateContent = $prompt;
                        break;
                    }
                }
            @endphp
            @if(!empty($itemSeo->id)&&!empty($itemSeo->jobAutoTranslate)&&$itemSeo->jobAutoTranslate->count()==0)
                <div class="actionBox_item maxLine_1" onClick="createJobTranslateContent({{ $item->seo->id }}, '{{ $language }}')">
                    <i class="fa-solid fa-language"></i>Dịch nội dung (chạy ngầm)
                </div>
            @endif
            <!-- End:: trường hợp không có bản lưu auto job nào mới hiện ra nút nhấn -->
        @endif
        @if(!empty($itemSeo->link_canonical))
            <a href="{{ URL::current().'?id='.$item->id.'&language='.$language.'&id_seo_source='.$itemSeo->link_canonical }}" class="actionBox_item maxLine_1">
                <i class="fa-solid fa-file-import"></i>Copy từ trang Gốc
            </a>
        @else
            <div class="actionBox_item maxLine_1" data-bs-toggle="modal" data-bs-target="#modalViewProductCopied" onclick="searchProductCopied();">
                <i class="fa-solid fa-file-import"></i>Cập nhật trang Copy
            </div>
        @endif
    </div>
</div>