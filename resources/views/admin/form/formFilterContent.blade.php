@foreach($prompts as $prompt)
    <!-- tiếng việt -> form viết content (đối với bản viết có nhiều box theo layout prompt viết bài) -->
    @if($language=='vi') 
        @if($prompt->reference_name=='content'&&($prompt->type=='auto_content'||$prompt->type=='auto_content_for_image'))
            <div class="pageAdminWithRightSidebar_main_content_item width100">
                <div class="card">
                    <div class="card-body">
                        @php
                            $key                    = $prompt->ordering;
                            $contentsByLanguageUse  = $itemSeoSourceToCopy->contents ?? $itemSeo->contents ?? [];
                            /* lấy content theo ordering */
                            $xhtmlContent           = '';
                            if(!empty($contentsByLanguageUse)&&$contentsByLanguageUse->count()>0){
                                foreach($contentsByLanguageUse as $c){
                                    if($c->ordering==$key) {
                                        $xhtmlContent = $c->content;
                                        break;
                                    }
                                }
                            }
                        @endphp
                        @include('admin.form.formContent', [
                            'prompt'            => $prompt,
                            'content'           => $xhtmlContent, 
                            'flagCopySource'    => !empty($itemSeoSourceToCopy) ? true : false,
                            'idBox'             => 'content_'.$key,
                            'ordering'          => $key,
                        ])
                            
                    </div>
                </div>
            </div>
        @endif
    @else
        <!-- tiếng khác -> form dịch -->
        @if($prompt->type=='translate_content'&&$prompt->reference_name=='content')
            @php
                $contentsByLanguageUse   = $itemSeoSourceToCopy->contents ?? $itemSeo->contents ?? [];
                $contentsViUse           = $itemSourceToCopy->seo->contents ?? $item->seo->contents ?? [];
            @endphp
            @if(!empty($contentsViUse))
                @foreach($contentsViUse as $content)
                    @php
                        $key                = $content->ordering;
                        /* lấy content theo ordering */
                        $xhtmlContent       = '';
                        foreach($contentsByLanguageUse as $c){
                            if($c->ordering==$key) {
                                $xhtmlContent = $c->content;
                                break;
                            }
                        }
                    @endphp
                    <div class="pageAdminWithRightSidebar_main_content_item width100">
                        <div class="card">
                            <div class="card-body">
                                @include('admin.form.formContent', [
                                    'prompt'            => $prompt,
                                    'content'           => $xhtmlContent, 
                                    'flagCopySource'    => !empty($itemSourceToCopy) ? true : false,
                                    'idBox'             => 'content_'.$key,
                                    'idContent'         => $content->id ?? 0, /* truyền id của content tiếng viết (để dịch) */
                                    'ordering'          => $key,
                                ]) 
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        @endif
    @endif
@endforeach