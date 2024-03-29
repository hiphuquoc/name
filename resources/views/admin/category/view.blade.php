@extends('layouts.admin')
@section('content')

    @php
        $titlePage      = 'Thêm Category mới';
        $submit         = 'admin.category.createAndUpdate';
        if(!empty($type)&&$type=='edit'){
            $titlePage  = 'Chỉnh sửa Category';
        }
    @endphp

    <form id="formAction" class="needs-validation invalid" action="{{ route($submit) }}" method="POST" novalidate enctype="multipart/form-data">
    @csrf
    <input type="hidden" id="seo_id" name="seo_id" value="{{ $itemSeo->id ?? 0 }}" />
    <input type="hidden" id="category_info_id" name="category_info_id" value="{{ !empty($item->id)&&$type!='copy' ? $item->id : 0 }}" />
    <input type="hidden" id="language" name="language" value="{{ $language ?? 'vi' }}" />
    <input type="hidden" id="type" name="type" value="{{ $type }}" />
        <div class="pageAdminWithRightSidebar withRightSidebar">
            <div class="pageAdminWithRightSidebar_header">
                <div style="display:flex;align-items:flex-end;">
                    <div style="width:100%;">{{ $titlePage }}</div>
                    <div class="languageBox">
                        @foreach(config('language') as $lang)
                            @php
                                /* trang đang sửa có ngôn ngữ ? */
                                $selected = null;
                                if($language==$lang['key']) $selected = 'selected';
                                /* các trang đã tồn tại bảng ngôn ngữ này trong CSDL */
                                $disable        = 'disable';
                                $languageLink   = route("admin.category.view", [
                                    "language"  => $lang['key'], 
                                    "id"        => $item->id
                                ]);
                                foreach($item->seos as $s){
                                    if(!empty($s->infoSeo->language)&&$s->infoSeo->language==$lang['key']){
                                        $disable = null;
                                        break;
                                    }
                                }
                            @endphp
                            <a href="{{ $languageLink }}" class="languageBox_item {{ $selected }} {{ $disable }}">
                                <img src="/storage/images/svg/icon_flag_{{ $lang['key'] }}.png" />
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Error -->
            @if ($errors->any())
                <ul class="errorList">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <!-- MESSAGE -->
            @include('admin.template.messageAction')
            
            <div class="pageAdminWithRightSidebar_main">
                <!-- START:: Main content -->
                <div class="pageAdminWithRightSidebar_main_content">
                    <div class="pageAdminWithRightSidebar_main_content_item">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">Thông tin trang</h4>
                            </div>
                            <div class="card-body">

                                @include('admin.category.formPage')

                            </div>
                        </div>
                    </div>
                    <div class="pageAdminWithRightSidebar_main_content_item">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">Thông tin SEO</h4>
                            </div>
                            <div class="card-body">

                                @include('admin.form.formSeo')
                                
                            </div>
                        </div>
                    </div>
                    <!-- nội dung -->
                    @php
                        $i = 0;
                    @endphp
                    @foreach($prompts as $prompt)
                        <!-- tiếng việt -> form viết content (đối với bản viết có nhiều box theo layout prompt viết bài) -->
                        @if($language=='vi') 
                            @if($prompt->type=='auto_content'&&$prompt->reference_name=='content')
                                <div class="pageAdminWithRightSidebar_main_content_item width100">
                                    <div class="card">
                                        <div class="card-body">
                                        
                                            @include('admin.form.formContent', [
                                                'prompt'    => $prompt,
                                                'content' => $itemSeo->contents[$i]->content ?? null, 
                                                'idBox' => 'content_'.$i
                                            ])
                                                
                                        </div>
                                    </div>
                                </div>
                                @php
                                    ++$i;
                                @endphp
                            @endif
                        @else 
                            <!-- tiếng khác -> form dịch (đối với bản dịch chỉ có duy nhất 1 box content - gom dữ liệu lại) -->
                            @if($prompt->type=='translate_content'&&$prompt->reference_name=='content')
                                <div class="pageAdminWithRightSidebar_main_content_item width100">
                                    <div class="card">
                                        <div class="card-body">
                                            @php
                                                $xhtmlContent = '';
                                                if(!empty($itemSeo->contents)) foreach($itemSeo->contents as $c) $xhtmlContent .= $c->content;
                                            @endphp
                                            @include('admin.form.formContent', [
                                                'prompt'    => $prompt,
                                                'content'   => $xhtmlContent, 
                                                'idBox'     => 'content_'.$i
                                            ])
                                                
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endforeach
                </div>
                <!-- END:: Main content -->

                <!-- START:: Sidebar content -->
                <div class="pageAdminWithRightSidebar_main_rightSidebar">
                    <!-- Button Save -->
                    <div class="pageAdminWithRightSidebar_main_rightSidebar_item buttonAction" style="padding-bottom:1rem;">
                        @if(!empty($itemSeo->slug_full))
                            <a href="/{{ $itemSeo->slug_full }}" target="_blank" style="font-size:1.4rem;"><i class="fa-regular fa-eye"></i></a>
                        @endif
                        <a href="{{ route('admin.category.list') }}" type="button" class="btn btn-secondary waves-effect waves-float waves-light">Quay lại</a>
                        <button type="submit" class="btn btn-success waves-effect waves-float waves-light" onClick="javascript:submitForm('formAction');" aria-label="Lưu">Lưu</button>
                    </div>
                    <div class="pageAdminWithRightSidebar_main_rightSidebar_item">
                        <div class="actionBox">
                            @if($language=='vi')
                                <div class="actionBox_item maxLine_1" onClick="callAI('auto_content')">
                                    <i class="fa-solid fa-robot"></i>Auto Content
                                </div>
                            @else   
                                <div class="actionBox_item maxLine_1" onClick="callAI('translate_content')">
                                    <i class="fa-solid fa-language"></i>Dịch Content
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="customScrollBar-y" style="height: calc(100% - 70px);border-top: 1px dashed #adb5bd;">
                        <!-- Form Upload -->
                        <div class="pageAdminWithRightSidebar_main_rightSidebar_item">
                            @include('admin.form.formImage')
                        </div>
                        {{-- <!-- Form Slider -->
                        <div class="pageAdminWithRightSidebar_main_rightSidebar_item">
                            @include('admin.form.formSlider')
                        </div>
                        <!-- Form Gallery -->
                        <div class="pageAdminWithRightSidebar_main_rightSidebar_item">
                            @include('admin.form.formIcon')
                        </div> --}}
                    </div>
                </div>
                <!-- END:: Sidebar content -->
            </div>
        </div>
    </form>    
@endsection
@push('scriptCustom')
    <script type="text/javascript">

        $('.repeater').repeater();

    </script>
@endpush