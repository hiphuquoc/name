<!-- === START:: Scripts Default === -->
<script src="{{ asset('sources/admin/app-assets/vendors/js/vendors.min.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/js/core/app-menu.js') }}"></script>
<!-- === END:: Scripts Default === -->
<!-- Include jQuery UI library -->
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
<!-- Include jQuery UI Sortable library -->
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
<!-- BEGIN: Page Vendor JS-->
<script src="{{ asset('sources/admin/app-assets/vendors/js/pickers/pickadate/picker.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/vendors/js/pickers/pickadate/picker.date.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/vendors/js/pickers/pickadate/picker.time.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/vendors/js/pickers/pickadate/legacy.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/js/scripts/forms/form-select2.min.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/vendors/js/forms/repeater/jquery.repeater.min.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/js/scripts/forms/form-repeater.min.js') }}"></script>
<!-- custom tag -->
<script src="{{ asset('sources/admin/app-assets/vendors/js/tagify/tagify.js') }}"></script>
<!-- BEGIN: SWEET ALERT -->
<script src="{{ asset('sources/admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/js/scripts/extensions/ext-component-sweet-alerts.js') }}"></script>
<!-- END: SWEET ALERT -->
<!-- BEGIN: SLICK -->
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<!-- END: SLICK -->
<!-- BEGIN: MENU -->
<script src="{{ asset('sources/admin/app-assets/js/core/app.min.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/js/scripts/customizer.min.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/js/core/app-menu.min.js') }}"></script>
<!-- END: MENU -->

<!-- END: Page Vendor JS-->
<script defer>
    $(window).on('load', function () {
        if (feather) {
            feather.replace({
                width: 14,
                height: 14
            });
        }
        $('[data-bs-toggle="tooltip"]').tooltip();
        loadImageFromGoogleCloud();
    })

    $(function () {
        $('[data-toggle="tooltip"]').tooltip({ html : true })
    })
    /* COUNT CHARACTOR */
    $('input, textarea').on('input', function(){
        const idElemt           = $(this).attr('id');
        if(idElemt){
            const lengthInput   = $(this).val().length;
            const elemtShow     = $(document).find("[data-charactor='" + idElemt + "']");
            elemtShow.html(lengthInput);
        }
    })
    /* Setting view */
    function settingView(name, valDefault){
        $.ajax({
            url         : '{{ route("admin.setting.view") }}',
            type        : 'get',
            dataType    : 'html',
            data        : {
                name,
                default : valDefault
            },
            success     : function(result){
                location.reload();
            }
        });
    }

    function submitForm(idForm){
        const elemt = $('#'+idForm);
        if(elemt.valid()) elemt.submit();
    }

    /* copy to clipboard */
    function copyToClipboard(idContent, callbackFunction=null) {
        // Get the text field
        var copyText = document.getElementById(idContent);

        // Select the text field
        copyText.select();
        // copyText.setSelectionRange(0, 99999); // For mobile devices - input không phải hidden

        // Copy the text inside the text field
        navigator.clipboard.writeText(copyText.value);
    
        // Alert the copied text
        callbackFunction;
    }
    /* loading trong khi chờ ajax */
    function addLoading(idWrite){
        const html = '{{ view("admin.template.loading") }}';
        $('.'+idWrite).append(html);
    }
    function removeLoading(){
        $('.js_loading_element').remove();
    }
    /* load image from goole cloud */
    function loadImageFromGoogleCloud(){
        $(document).find('img[data-google-cloud]').each(function(){
            var elementImg          = $(this);
            const urlGoogleCloud    = elementImg.attr('data-google-cloud');
            const size              = elementImg.attr('data-size');
            $.ajax({
                url         : '{{ route("ajax.loadImageFromGoogleCloud") }}',
                type        : 'get',
                dataType    : 'html',
                data        : {
                    url_google_cloud    : urlGoogleCloud,
                    size
                },
                success     : function(response){
                    elementImg.attr('src', response);
                }
            });
        });
    }
    /* function viết content */
    function callAI(action){
        console.log(action);
        $('[data-type="'+action+'"]').each(function() {
            
            const id                = $(this).data('id');
            const language          = $(this).data('language');
            const id_prompt         = $(this).data('id_prompt');
            chatGpt($(this), id, language, id_prompt);
        });
    }
    /* ai chatgpt */
    function chatGpt(input, id, language, id_prompt){
        addAndRemoveClass($(input), 'inputLoading', 'inputSuccess inputError');
        /* vô hiệu hóa box dùng tiny */ 
        const idBox = $(input).attr('id');
        var editor = tinymce.get(idBox);
        if (editor) editor.getBody().setAttribute('contenteditable', false);
        $.ajax({
            url         : '{{ route("main.chatGpt") }}',
            type        : 'get',
            dataType    : 'json',
            data        : {
                id, language, id_prompt
            }
        }).done(function(data){
            /* điền dữ liệu vào */
            if(data.error=='') {
                addAndRemoveClass($(input), 'inputSuccess', 'inputLoading inputError');
                $(input).val(data.content);
            }else {
                addAndRemoveClass($(input), 'inputError', 'inputLoading inputSuccess');
            }
            /* Cập nhật nội dung Tiny */
            if($(input).is('textarea')){
                const idBox = $(input).attr('id');
                if (editor) {
                    editor.setContent(data.content);
                    editor.getBody().setAttribute('contenteditable', true);
                }
            }
            /* đếm lại kí tụ nếu có */
            const idInput           = $(input).attr('id');
            if(idInput){
                const lengthInput   = $(input).val().length;
                const elemtShow     = $(document).find("[data-charactor='" + idInput + "']");
                elemtShow.html(lengthInput);
            }
        })
    }
    function addAndRemoveClass(input, add, remove){
        $(input).addClass(add).removeClass(remove);
        /* kiểm tra có phải input tiny */ 
        var inputTiny = $(input).next();
        if(inputTiny.hasClass('tox-tinymce')){
            inputTiny.addClass(add).removeClass(remove);
        }
        /* kiểm tra có phải input tag */
        var inputTag = $(input).prev();
        if(inputTag.is('tags')){
            inputTag.addClass(add).removeClass(remove);
        }
    }
</script>