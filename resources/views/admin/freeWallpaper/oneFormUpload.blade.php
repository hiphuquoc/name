<div class="formFreeWallpaperBox_item js_uploadWallpaper_{{ $idBox }}">
    <div id="js_addFormUpload_wallpaper_{{ $idBox }}" class="formFreeWallpaperBox_item_gallery">
        @if(!empty($wallpaper))
            <img src="{{ \App\Helpers\Image::getUrlImageCloud($wallpaper->file_cloud) }}" style="width:100%;" />
        @endif
    </div>
    <div class="formFreeWallpaperBox_item_form">
        <div class="formBox">
            <div class="formBox_full">
                <div class="formBox_full_item">
                    <label class="form-label inputRequired" for="name">Alt ảnh</label>
                    <textarea class="form-control" name="name[{{ $idBox }}]" rows="1" required>{{ $wallpaper->name ?? config('main.auto_fill.alt.vi') }}</textarea>
                </div>
                {{-- <div class="formBox_full_item">
                    <label class="form-label inputRequired" for="name">Alt ảnh EN</label>
                    <textarea class="form-control" name="en_name[{{ $idBox }}]" rows="1" required>{{ $wallpaper->en_name ?? null }}</textarea>
                </div> --}}
                @foreach(config('main.category_type') as $type)
                    <div class="formBox_full_item">
                        <label class="form-label" for="{{ $type['key'] }}">{{ $type['name'] }}</label>
                        <select class="select2 form-select select2-hidden-accessible" name="{{ $type['key'] }}[{{ $idBox }}]" multiple="true" onchange="autoFillNameAndEnName({{ $idBox }});">
                            <option value="">- Lựa chọn -</option>
                            @if(!empty($categories))
                                @foreach($categories as $category)
                                    @if(!empty($category->seo->type)&&$category->seo->type==$type['key'])
                                        @php
                                            $selected   = null;
                                            if(!empty($wallpaper)&&!empty($wallpaper->categories)){
                                                foreach($wallpaper->categories as $relationCategory){
                                                    if($relationCategory->category_info_id==$category->id) {
                                                        $selected = ' selected';
                                                        break;
                                                    }
                                                }
                                            }
                                        @endphp
                                        <option value="{{ $category->id }}" data-name="{{ $category->name }}" data-en-name="{{ $category->en_name }}" {{ $selected }}>{{ $category->name }}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                @endforeach
                <div class="formBox_full_item">
                    @php
                        $arrayTagName           = [];
                        if(!empty($wallpaper->tags)){
                            foreach($wallpaper->tags as $tag){
                                if(!empty($tag->infoTag->name)) $arrayTagName[] = $tag->infoTag->name;
                            }
                        }
                        $strTagName             = implode(',', $arrayTagName);
                        $tagName                = 'tagName_'.$idBox;
                    @endphp
                    <label for="{{ $tagName }}" class="form-label">Tag name</label>
                    <input id="{{ $tagName }}" name="tags[{{ $idBox }}]" class="form-control" placeholder="Nhập tag name" value="{{ $strTagName }}" onchange="autoFillNameAndEnName({{ $idBox }});" />
                    <!-- script custom tag -->
                    <script type="text/javascript">
                        var strTag = {!! json_encode($arrayTag) !!};
                        new Tagify(document.querySelector("#{{ $tagName }}"), {
                            whitelist: strTag,
                            maxTags: Infinity, // allows to select max items
                            dropdown: {
                                maxItems: Infinity, // display max items
                                classname: "tags-inline", // Custom inline class
                                enabled: 0,
                                closeOnSelect: false
                            }
                        });
                    </script>
                </div>
                <div class="formBox_full_item">
                    <label class="form-label" for="description">Prompt Midjourney</label>
                    <textarea class="form-control" name="description[{{ $idBox }}]" rows="2"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>