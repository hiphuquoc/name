<div class="actionBox">
    @if($language=='vi')
        <div class="actionBox_item maxLine_1" onClick="callAI('auto_content')">
            <i class="fa-solid fa-robot"></i>Viết toàn trang
        </div>
        <!-- dịch content (những trang chưa có) -->
        <div class="actionBox_item maxLine_1" onclick="createJobTranslateAndCreatePageAjax('{{ $item->seo->slug ?? '' }}');">
            <i class="fa-solid fa-plus"></i>Tạo trang ngôn ngữ còn thiếu (chạy ngầm)
        </div>
        <!-- dịch content (những trang chưa có) -->
        <div class="actionBox_item maxLine_1" onclick="createMultiJobTranslateContent('{{ $item->seo->slug ?? '' }}');">
            <i class="fa-solid fa-language"></i>Dịch content tất cả ngôn ngữ (chạy ngầm)
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
    <!-- copy trang gốc -->
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