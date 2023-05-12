<div class="pageAdminWithRightSidebar_main_content_item width100" data-repeater-item>
    <div class="card">
        <div class="card-header border-bottom">
            <h4 class="card-title">
                Phiên bản của sản phẩm
                <i class="fa-solid fa-circle-xmark" data-repeater-delete=""></i>
            </h4>
        </div>
        <div class="card-body">
            <input type="hidden" name="id" value="{{ $price['id'] ?? null }}" />
            <div class="formBox">
                <div class="formBox_full flexBox">
                    <div class="flexBox_item">
                        <div class="formBox_full_item">
                            <label class="form-label inputRequired" for="name">Tiêu đề</label>
                            <input class="form-control" name="name" type="text" value="{{ $price['name'] ?? null }}" required />
                        </div>
                    </div>
                    <div class="flexBox_item">
                        <div class="formBox_full_item">
                            <label class="form-label inputRequired" for="en_name">Title (En)</label>
                            <input class="form-control" name="en_name" type="text" value="{{ $price['en_name'] ?? null }}" required />
                        </div>
                    </div>
                </div>
                <div class="formBox_full flexBox">
                    <div class="flexBox_item">
                        <div class="formBox_full_item">
                            <label class="form-label inputRequired" for="price">Giá bán</label>
                            <input class="form-control" name="price" type="number" value="{{ $price['price'] ?? null }}" required />
                        </div>
                    </div>
                    <div class="flexBox_item">
                        <div class="formBox_full_item">
                            <label class="form-label" for="price_before_promotion">Giá trước KM</label>
                            <input class="form-control" name="price_before_promotion" type="number" value="{{ $price['price_before_promotion'] ?? null }}" />
                        </div>
                    </div>
                </div>
                <!-- One Row -->
                @if(!empty($price['id'])&&$type!='copy')
                    <div class="formBox_full">
                        <label class="form-label" style="margin-right:0.5rem;">Ảnh phiên bản (hiển thị)</label>
                        <input type="file" name="image" onChange="uploadFileAjax(this, {{ $price['id'] ?? 0 }}, '{{ $item->seo->slug ?? null }}');" multiple />
                        <div class="uploadImageBox" style="margin-top:0.5rem;">
                            <div class="uploadImageBox_box js_readURLsCustom_idWrite" style="position:relative;">
                                @if(!empty($price->files)&&$price->files->isNotEmpty())
                                    @foreach($price->files as $file)
                                        <div id="js_removeGalleryProductPrice_{{ $file->id }}" class="uploadImageBox_box_item">
                                            <img src="{{ Storage::url($file->file_path) ?? null }}" />
                                            <div class="uploadImageBox_box_item_icon" onClick="removeGalleryProductPrice({{ $file->id }});"></div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
                <!-- One Row -->
                @if(!empty($price['id'])&&$type!='copy')
                    <div class="formBox_full">
                        <label class="form-label" style="margin-right:0.5rem;">Ảnh phiên bản (sản phẩm)</label>
                        <input type="file" name="imageSource" onChange="uploadSourceAjax(this, {{ $price['id'] ?? 0 }}, '{{ $item->seo->slug ?? null }}', '{{ $price['folder_drive'] }}');" multiple />
                        <div class="uploadImageBox" style="margin-top:0.5rem;">
                            <div class="uploadImageBox_box js_readURLsCustom_idWrite" style="position:relative;">
                                @if(!empty($price->sources)&&$price->sources->isNotEmpty())
                                    @foreach($price->sources as $source)
                                        <div id="js_removeSourceFile_{{ $source->id }}" class="uploadImageBox_box_item">
                                            <img src="{{ Storage::disk('google')->url($source->file_path) ?? null }}" loading="lazy" />
                                            <div class="uploadImageBox_box_item_icon" onClick="removeSourceFile({{ $source->id }});"></div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

@pushonce('scriptCustom')
<script type="text/javascript">

    function uploadFileAjax(input, idProductPrice, slug){
        if(idProductPrice!=0){
            addLoading('js_readURLsCustom_idWrite');
            /* lấy thông tin file */
            const files = $(input)[0].files;
            /* tạo đối tượng FormData */
            const formData = new FormData();
            /* thêm token vào */
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('product_price_id', idProductPrice);
            formData.append('slug', slug);
            for(let i=0;i<files.length;++i){
                /* thêm từng file vào */
                formData.append('files[]', files[i]);
            }
            $.ajax({
                url: '{{ route("admin.product.uploadImageProductPriceAjaxToFile") }}',
                type: 'post',
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                timeout: 600000,
                success: function (data) {
                    setTimeout(() => {
                        /* clear input file */ 
                        $(input).val('');
                        /* tắt loading trước để không nhảy hàng */
                        removeLoading();
                        /* tải bản xem trước */
                        const parent            = $(input).parent();
                        const elementWrite      = parent.find('.js_readURLsCustom_idWrite');
                        for(let i=0;i<data.length;++i){
                            const divDom        = document.createElement("div");
                            divDom.className    = 'uploadImageBox_box_item';
                            divDom.setAttribute('id', 'js_removeGalleryProductPrice_'+data[i].file_id);
                            divDom.innerHTML    = '<img src="'+data[i].file_url+'" /><div class="uploadImageBox_box_item_icon" onClick="removeGalleryProductPrice('+data[i].file_id+');"></div>';
                            elementWrite.append(divDom);
                        };
                    }, 500);
                }
            });
        }else {
            $(input).val('');
            alert('Vui lòng lưu Phiên bản sản phẩm trước khi upload ảnh!');
        }
    }

    function uploadSourceAjax(input, idProductPrice, slug, folderDrive){
        if(idProductPrice!=0){
            addLoading('js_readURLsCustom_idWrite');
            /* lấy thông tin file */
            const files = $(input)[0].files;
            /* tạo đối tượng FormData */
            const formData = new FormData();
            /* thêm token vào */
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('product_price_id', idProductPrice);
            formData.append('slug', slug);
            formData.append('folder_drive', folderDrive);
            for(let i=0;i<files.length;++i){
                /* thêm từng file vào */
                formData.append('files[]', files[i]);
            }
            $.ajax({
                url: '{{ route("admin.product.uploadImageProductPriceAjaxToSource") }}',
                type: 'post',
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                timeout: 600000,
                success: function (data) {
                    setTimeout(() => {
                        /* clear input file */ 
                        $(input).val('');
                        /* tắt loading trước để không nhảy hàng */
                        removeLoading();
                        /* tải bản xem trước */
                        const parent            = $(input).parent();
                        const elementWrite      = parent.find('.js_readURLsCustom_idWrite');
                        for(let i=0;i<data.length;++i){
                            const divDom        = document.createElement("div");
                            divDom.className    = 'uploadImageBox_box_item';
                            divDom.setAttribute('id', 'js_removeSourceFile_'+data[i].file_id);
                            divDom.innerHTML    = '<img src="'+data[i].file_url+'" /><div class="uploadImageBox_box_item_icon" onClick="removeSourceFile('+data[i].file_id+');"></div>';
                            elementWrite.append(divDom);
                        };
                    }, 500);
                }
            });
        }else {
            $(input).val('');
            alert('Vui lòng lưu Phiên bản sản phẩm trước khi upload ảnh!');
        }
        
    }


    function removeGalleryProductPrice(id){
        $.ajax({
            url         : "{{ route('admin.gallery.remove') }}",
            type        : "post",
            dataType    : "html",
            data        : { 
                '_token'    : '{{ csrf_token() }}',
                id : id 
            }
        }).done(function(data){
            if(data==true) $('#js_removeGalleryProductPrice_'+id).remove();
        });
    }

    function removeSourceFile(id){
        $.ajax({
            url         : "{{ route('admin.source.remove') }}",
            type        : "post",
            dataType    : "html",
            data        : { 
                '_token'    : '{{ csrf_token() }}',
                id : id 
            }
        }).done(function(data){
            if(data==true) $('#js_removeSourceFile_'+id).remove();
        });
    }

</script>
@endpushonce