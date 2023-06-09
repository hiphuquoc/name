<!-- BEGIN: SLICK -->
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<!-- END: SLICK -->
<script type="text/javascript">
    $(window).ready(function(){
        /* check để xem có cookie csrf chưa (do lần đầu truy cập trang không có lỗi google login) */
        // checkToSetCsrfFirstTime();
        
        /* lazyload ảnh lần đầu */
        lazyLoad();
        /* lazyload ảnh khi scroll */
        $(window).on('scroll', function() {
            lazyLoad();
        });
        
        /* tải lại view sort cart */
        viewSortCart();

        // /* hiệu ứng */
        // $('.effectFadeIn').each(function(){
        //     $(this).css('opacity', 0);
        // });

        // buildTocContentMain('js_contentBox');

        // $('img').each(function() {
        //     if (!$(this).attr('alt') || !$(this).attr('title')) {
        //         console.log(this);
        //     }
        // });

        var Url             = document.URL;
        // var elementMenu     = null;
        $('.headerSide a').each(function(){
            const regex = new RegExp("^" + $(this).attr('href'));
            if(regex.test(Url)) {
                $(this).closest('ul').css({
                    height : 'auto',
                    opacity : 1
                });
                $(this).closest('ul').children().each(function(){
                    $(this).removeClass('selected');
                })
                $(this).closest('li').addClass('selected');
            }
        });

        /* check login để hiện thị button */
        checkLoginAndSetShow();
    });
    
    function lazyLoad(){
        /* đối với ảnh */
        $('img.lazyload').each(function() {
            if (!$(this).hasClass('loaded')) {
                var distance = $(window).scrollTop() - $(this).offset().top + 900;
                if (distance > 0) {
                    $(this).attr('src', $(this).attr('data-src'));
                    $(this).addClass('loaded').removeClass('loading_1').css('opacity', 1);
                }
            }
        });
        /* đối với div dùng background */
        $('div.lazyload').each(function() {
            if (!$(this).hasClass('loaded')) {
                var distance = $(window).scrollTop() - $(this).offset().top + 900;
                if (distance > 0) {
                    $(this).css({
                        background  : 'url("'+$(this).attr('data-src')+'") no-repeat center center / cover',
                        filter      : 'unset'
                    });
                    $(this).addClass('loaded');
                }
            }
        });
    }
    /* đang xây dựng */
    function noticeContrustion(){
        alert('Tính năng thanh toán online đang được xây dựng. Vui lòng quay lại sau hoặc liên hệ Zalo 0968617168');
    }
    // function toggleMenuMobile(){
    //     const elementMobileMenu = $('.layoutHeaderSide_header');
    //     elementMobileMenu.toggle();
    // }
    function showHideListMenuMobile(element, idMenu){
        let elementMenu     = $('#'+idMenu);
        let flag            = elementMenu.height();
        if(flag<=0){
            elementMenu.css({
                height: 'auto',
                opacity: '1'
            });
            $(element).addClass('open');
            $(element).removeClass('close');
        }else {
            elementMenu.css({
                height: '0',
                opacity: '0'
            });
            $(element).removeClass('open');
            $(element).addClass('close');
        }
    }
    function openCloseElemt(idElemt){
        let displayE    = $('#' + idElemt).css('display');
        if(displayE=='none'){
            $('#' + idElemt).css('display', 'block');
            $('body').css('overflow', 'hidden');
        }else {
            $('#' + idElemt).css('display', 'none');
            $('body').css('overflow', 'unset');
        }
    }
    // /* load image */
    // function loadImage(){
    //     $(document).find('img[data-src]').each(function(){
    //         setTimeout(() => {
    //             $(this).attr('src', $(this).attr('data-src'));
    //             if($(this).hasClass('loading_1')) $(this).removeClass('loading_1');
    //         }, 0);
    //     });
    // }
    /* fixed menu khi scroll đối với giao diện nhỏ hơn 990px */ 
    $(window).scroll(function(){
        // const heightMenu = $('.headerMain').outerHeight();
        if($(window).scrollTop()>300){
            $('.headerMain').addClass('fixed');
            $('.headerMain').css('opacity', '1');
        }
        if($(window).scrollTop()<=300){
            $('.headerMain').removeClass('fixed');
            $('.headerMain').css('opacity', '0');
        }
        if($(window).scrollTop()<55){
            $('.headerMain').removeClass('fixed');
            $('.headerMain').css('opacity', '1');
        }
    })
    const percentHeightScreenEffect = 1.3;
    /* hiệu ứng fade in */
    $(window).scroll(function(){
        $('.effectFadeIn').each(function(){
            const positionElement   = $(this).offset().top;
            const heightWindow      = $(window).height();
            const positionScroll    = $(window).scrollTop();
            if(parseInt(heightWindow/percentHeightScreenEffect + positionScroll)>=positionElement){
                $(this).animate({
                    opacity : 1,
                }, 800);
            }
        })
    });
    /* hiệu ứng rơi xuống => dùng cho phần tử có scrollTop thấp hơn ít nhất 1 màn hình */
    $(window).scroll(function(){
        $('.effectDropdown').each(function(){
            /* ẩn trước */
            if(!$(this).hasClass('alreadyEffectDropdown')) $(this).css('opacity', 0);
            /* thao tác */
            const positionElement   = $(this).offset().top;
            const heightWindow      = $(window).height();
            const positionScroll    = $(window).scrollTop();
            if(!$(this).hasClass('alreadyEffectDropdown')&&parseInt(heightWindow/percentHeightScreenEffect + positionScroll)>=positionElement){
                    const marginTopReal = parseInt($(this).css('margin-top'));
                    $(this).css({
                        'margin-top'    : (marginTopReal - 200)+'px'
                    });
                    $(this).animate({
                        'margin-top'    : marginTopReal+'px',
                        'opacity'       : 1
                    }, 800);
                    /* thực hiện rồi thì không thực hiện nữa */
                    $(this).addClass('alreadyEffectDropdown');
            }
        })
    });
    /* hiệu ứng xuất hiện từ trái qua phải => dùng cho phần tử có scrollTop thấp hơn ít nhất 1 màn hình */
    $(window).scroll(function(){
        $('.effectLeftToRight').each(function(){
            /* ẩn trước */
            if(!$(this).hasClass('alreadyEffectLeftToRight')) $(this).css('opacity', 0);
            /* thao tác */
            const positionElement           = $(this).offset().top;
            const heightWindow              = $(window).height();
            const positionScroll            = $(window).scrollTop();
            if(!$(this).hasClass('alreadyEffectLeftToRight')&&parseInt(heightWindow/percentHeightScreenEffect + positionScroll)>=positionElement){
                    const marginLeftReal    = parseInt($(this).css('margin-left'));
                    $(this).css({
                        'margin-left'   : (marginLeftReal - 200)+'px'
                    });
                    $(this).animate({
                        'margin-left'    : marginLeftReal+'px',
                        'opacity'       : 1
                    }, 800);
                    /* thực hiện rồi thì không thực hiện nữa */
                    $(this).addClass('alreadyEffectLeftToRight');
            }
        })
    });
    /* hiệu ứng xuất hiện từ dưới lên => dùng cho phần tử có scrollTop thấp hơn ít nhất 1 màn hình */
    $(window).scroll(function(){
        $('.effectBottomToTop').each(function(){
            /* ẩn trước */
            if(!$(this).hasClass('alreadyEffectBottomToTop')) $(this).css('opacity', 0);
            /* thao tác */
            const positionElement           = $(this).offset().top;
            const heightWindow              = $(window).height();
            const positionScroll            = $(window).scrollTop();
            if(!$(this).hasClass('alreadyEffectBottomToTop')&&parseInt(heightWindow/percentHeightScreenEffect + positionScroll)>=positionElement){
                    const marginTopReal     = parseInt($(this).css('margin-top'));
                    $(this).css({
                        'margin-top'    : (marginTopReal + 200)+'px'
                    });
                    $(this).animate({
                        'margin-top'    : marginTopReal+'px',
                        'opacity'       : 1
                    }, 800);
                    /* thực hiện rồi thì không thực hiện nữa */
                    $(this).addClass('alreadyEffectBottomToTop');
            }
        })
    });
    // /* tocContent */
    // function buildTocContentMain(idElement){
    //     var dataTocContent      = {};
    //     var i                   = 0;
    //     var indexToc            = 0;
    //     $('#'+idElement).find('h2').each(function(){
    //         if($(this).is(":visible")){
    //             let dataId        = $(this).attr('id');
    //             if(typeof dataId=='undefined'){
    //                 dataId          = 'thiet-ke-website-kien-giang_'+i;
    //                 $(this).attr('id', dataId);
    //                 ++indexToc;
    //             }
    //             const name          = $(this)[0].localName;
    //             const dataTitle     = $(this).html();
    //             dataTocContent[i]   = {
    //                 id      : dataId,
    //                 name    : name,
    //                 title   : dataTitle
    //             };
    //             ++i;
    //         }
    //     });
    //     $.ajax({
    //         url         : '{{ route("main.buildTocContentMain") }}',
    //         type        : 'get', 
    //         dataType    : 'html',
    //         data        : {
    //             dataSend    : dataTocContent
    //         },
    //         success     : function(data){
    //             $('#tocContentMain').html(data);
    //             fixedTocContentIcon();
    //             setHeightTocFixed();

    //             $(window).resize(function() {
    //                 fixedTocContentIcon();
    //                 setHeightTocFixed();
    //             });

    //             $('.tocFixedIcon, #callTocContentFromMenu, .tocFixed').click(function(){
    //                 let elementMenu = $('.tocContentMain.tocFixed');
    //                 let displayMenu = elementMenu.css('display');
    //                 if(displayMenu=='none'){
    //                     elementMenu.css('display', 'block');
    //                 }else {
    //                     elementMenu.css('display', 'none');
    //                 }
    //                 // fixedTocContentIcon();
    //             });

    //             $('.tocContentMain_title, .tocContentMain_close').click(function(){
    //                 let elemtMenu   = $('.tocContentMain .tocContentMain_list');
    //                 let displayMenu = elemtMenu.css('display');
    //                 if(displayMenu=='none'){
    //                     elemtMenu.css('display', 'block');
    //                     $('.tocContentMain_close').removeClass('hidden');
    //                 }else {
    //                     elemtMenu.css('display', 'none');
    //                     $('.tocContentMain_close').addClass('hidden');
    //                 }
    //             });

    //             function fixedTocContentIcon(){
    //                 let widthS      = $(window).width();
    //                 let widthC      = $('.container').outerWidth();
    //                 let leftE       = parseInt((widthS - widthC - 70) / 2);
    //                 if($(window).width() < 1200){
    //                     leftE       = parseInt((widthS - widthC + 20) / 2);
    //                 }
    //                 // $('.tocFixedIcon').css('left', leftE);
    //             }

    //             function setHeightTocFixed(){
    //                 let heightToc   = parseInt($(window).height() - 210);
    //                 $('.tocContentMain.tocFixed .tocContentMain_list').css('height', heightToc+'px');
    //             }

    //             let element         = $('#tocContentMain');
    //             if(element.length>0){
    //                 let positionE       = element.offset().top;
    //                 let heightE         = element.outerHeight();
    //                 let boxContent      = $('#'+idElement);
    //                 let positionB       = boxContent.offset().top;
    //                 let heightB         = boxContent.outerHeight();
    //                 let heightFooter    = $('.footer').outerHeight();
    //                 $(document).scroll(function(){
    //                     let scrollNow   = $(document).scrollTop();
    //                     // let minScroll   = parseInt(heightE + positionE);
    //                     // let maxScroll   = parseInt(heightB + positionB - heightFooter);
    //                     if(scrollNow > 500){ 
    //                         $('.tocFixedIcon').css('display', 'block');
    //                     }else {
    //                         $('.tocFixedIcon').css('display', 'none');
    //                     }
    //                 });
    //             }
    //         }
    //     });
    // }
    /* Go to top */
    mybutton 					    = document.getElementById("smoothScrollToTop");
    mybutton.style.display 	        = "none";
    window.onscroll                 = function() {scrollFunction()};
    function scrollFunction() {
        if (document.body.scrollTop > 500 || document.documentElement.scrollTop > 500) {
            mybutton.style.display 	= "block";
        } else {
            mybutton.style.display 	= "none";
        }
    }
    function smoothScrollToTop() {
        // const currentScroll = document.documentElement.scrollTop;
        // if (currentScroll > 0) {
        //     window.requestAnimationFrame(smoothScrollToTop);
        //     window.scrollTo(0, currentScroll - currentScroll / 8);
        // }
        document.documentElement.scrollTop          = 0;
    }
    /* link to a href #id smooth */
    document.querySelectorAll('a[href^="#"]').forEach(function(a) {
        a.addEventListener('click', function(event) {
            event.preventDefault();
            let id = this.getAttribute('href');
            let element = document.querySelector(id);
            if (!element) {
                console.error(`Element with ID ${id} not found`);
                return;
            }
            let offsetTop = element.offsetTop;
            window.scrollTo({
                top: offsetTop + 200,
                behavior: 'smooth'
            });
        });
    });
    /* toggle menu mobile */
    function toggleMenuMobile(idElement){
        const element   = $('#'+idElement);
        const displayE  = element.css('display');
        if(displayE=='none'){
            /* hiển thị */
            element.css('display', 'block');
            $('body').css('overflow', 'hidden');
            $('#js_blurBackground').addClass('blurBackground');
            $('.menuTopBackground').addClass('blurBackground');
            $('.backgroundBlurMobileMenu').css('display', 'block');
        }else {
            element.css('display', 'none');
            $('body').css('overflow', 'unset');
            $('#js_blurBackground').removeClass('blurBackground');
            $('.menuTopBackground').removeClass('blurBackground');
            $('.backgroundBlurMobileMenu').css('display', 'none');
        }
    }
    /* thay đổi option của product phần hiển thị ngoài */
    function changeOption(idShow){
        const elemtShow     = $('#'+idShow);
        const parent        = elemtShow.parent();
        /* ẩn tất cả phần tử con */
        parent.children().each(function(){
            $(this).removeClass('show').addClass('hide');
        })
        /* bật lại phần tử được chọn */
        elemtShow.removeClass('hide').addClass('show');
        /* lazy load cho ảnh trong phần tử */
        elemtShow.find('img').each(function(){
            $(this).addClass('lazyload');
            lazyLoad();
        })
    }
    /* hiện thông báo cho sản phẩm vào giỏ hàng thành công */
    function openCloseModal(idModal, action = null){
        const elementModal  = $('#'+idModal);
        const flag          = elementModal.css('display');
        /* tooggle */
        if(action==null){
            if(flag=='none'){
                elementModal.css('display', 'flex');
                $('#js_openCloseModal_blur').addClass('blurBackground');
                $('body').css('overflow', 'hidden');
            }else {
                elementModal.css('display', 'none');
                $('#js_openCloseModal_blur').removeClass('blurBackground');
                $('body').css('overflow', 'unset');
            }
        }
        /* đóng */
        if(action=='close'){
            elementModal.css('display', 'none');
            $('#js_openCloseModal_blur').removeClass('blurBackground');
            $('body').css('overflow', 'unset');
        }
        /* mở */
        if(action=='open'){
            elementModal.css('display', 'flex');
            $('#js_openCloseModal_blur').addClass('blurBackground');
            $('body').css('overflow', 'hidden');
        }
    }
    /* tăng giảm số lượng input quantity */
    function plusMinusQuantity(idInput, action){
        const elementInput  = $('#'+idInput);
        const valueInput    = elementInput.val();
        if(action=='minus'){
            if(valueInput>1) elementInput.val(parseInt(valueInput)-1);
        }else {
            elementInput.val(parseInt(valueInput)+1);
        }
    }
    /* thêm sản phẩm vào giỏ hàng */
    function addToCart(){
        let dataForm            = {};
        $('#js_addToCart_options').children().each(function(){
            if($(this).hasClass('selected')) {
                /* lấy ảnh của option được chọn */ 
                const key       = $(this).attr('id');
                $('*[data-option="'+key+'"]').each(function(){
                    /* lấy ảnh đầu tiên trong tất cả ảnh của option */
                    dataForm.image  = $(this).attr('src');
                    return false;
                });
                /* lấy giá của option được chọn */ 
                dataForm.price      = $(this).data('price');
                /* lấy option đang được chọn */
                dataForm.option_name = $(this).html();
                /* lấy url riêng của option được chọn */ 
                dataForm.product_price_id     = $(this).data('product_price_id');
            }
        });
        /* lấy id sản phẩm */ 
        dataForm.product_info_id    = $('#product_info_id').val();
        /* lấy số lượng */
        dataForm.quantity           = $('#js_addToCart_quantity').val();
        /* lấy title */
        dataForm.title              = $(document).find('h1').html();
        /* gửi dữ liệu để xử lý */
        dataForm.language = $('#language').val();
        $.ajax({
            url         : '{{ route("main.addToCart") }}',
            type        : 'get',
            dataType    : 'html',
            data        : dataForm
        }).done(function(data){
            /* reset lại value số lượng */
            $('#js_addToCart_quantity').val(1);
            /* hiện thông báo trong 5s */
            $('#js_addToCart_idWrite').html(data);
            openCloseModal('cartMessage');
            // setTimeout(() => {
            //     openCloseModal('cartMessage', 'close');
            // }, 5000);
            /* cập nhật lại thông tin giỏ hàng */ 
            viewSortCart();
        })
    }
    /* tải lại thông tin icon giỏ hàng */
    function viewSortCart(){
        const language = $('#language').val();
        $.ajax({
            url         : '{{ route("main.viewSortCart") }}',
            type        : 'get',
            dataType    : 'html',
            data        : { language },
            success     : function(response){
                $('#js_viewSortCart_idWrite').html(response);
            }
        });
    }
    /* tải lại thành tiền khi thay đổi số lượng */
    function updateCart(idRow, idTotal, idCount, idInput, theme = 'cartSort'){
        /* tải loading */ 
        loadLoading(idRow);
        /* lấy dữ liệu */
        const heightElementWrite    = $('#'+idRow).outerHeight();
        const elementInput          = $('#'+idInput);
        const valueInput            = elementInput.val();
        const idProduct             = elementInput.data('product_info_id');
        const idPrice               = elementInput.data('product_price_id');
        $.ajax({
            url         : '{{ route("main.updateCart") }}',
            type        : 'get',
            dataType    : 'json',
            data        : {
                product_info_id     : idProduct,
                product_price_id    : idPrice,
                quantity            : valueInput,
                theme
            },
            success     : function(response){
                setTimeout(function(){
                    $('#'+idRow).html(response.row);
                    $('#'+idTotal).html(response.total);
                    $('#'+idCount).html(response.count);
                }, 1000);
            }
        });
    }
    /* xóa sản phẩm khỏi cart */ 
    function removeProductCart(idProduct, idPrice, idRow, idTotal, idCount){
        /* tải loading */ 
        loadLoading(idRow);
        $.ajax({
            url         : '{{ route("main.removeProductCart") }}',
            type        : 'get',
            dataType    : 'json',
            data        : {
                product_info_id     : idProduct,
                product_price_id    : idPrice
            },
            success     : function(response){
                /* cart trống */
                if(response.empty_cart!='') $('#js_checkEmptyCart_idWrite').html(response.empty_cart);
                setTimeout(function(){
                    $('#'+idRow).hide();
                    $('#'+idTotal).html(response.total);
                    $('#'+idCount).html(response.count);
                }, 1000);
            }
        });
    }
    /* add loading icon */
    function loadLoading(idAppend, theme = 'loading_2'){
        $.ajax({
            url         : '{{ route("ajax.loadLoading") }}',
            type        : 'get',
            dataType    : 'html',
            data        : {
                theme       : theme
            },
            success     : function(response){
                $('#'+idAppend).append(response);
            }
        });
    }
    /* tính năng registry email ở footer */
    function submitFormRegistryEmail(idForm){
            event.preventDefault();
            const inputEmail = $('#'+idForm).find('[name*=registry_email]');
            const valueEmail = inputEmail.val();
            if(isValidEmail(valueEmail)){
                $.ajax({
                    url         : '{{ route("ajax.registryEmail") }}',
                    type        : 'get',
                    dataType    : 'json',
                    data        : {
                        registry_email : valueEmail
                    },
                    success     : function(response){
                        /* bật thông báo */
                        setMessageModal(response.title, response.content);
                    }
                });
            }else {
                inputEmail.val('');
                inputEmail.attr('placeholder', 'Email không hợp lệ!');
            }
        }
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    /* tìm kiếm sản phẩm ajax */
    function searchProductAjax(elementButton){
        const valueElement  = $(elementButton).val();
        const language      = $('#language').val();
        $.ajax({
            url         : '{{ route("ajax.searchProductAjax") }}',
            type        : 'get',
            dataType    : 'html',
            data        : {
                key_search  : valueElement,
                language
            },
            success     : function(response){
                if(response!='') {
                    $('#js_searchProductAjax_idWrite').html(response);
                }
                
            }
        });
    }
    /* toc content */
    function buildTocContentMain(idElement){
        var dataTocContent      = {};
        var i                   = 0;
        var indexToc            = 0;
        $('#'+idElement).find('h2').each(function(){
            let dataId        = $(this).attr('id');
            if(typeof dataId=='undefined'){
                dataId          = 'randomIdTocContent_'+i;
                $(this).attr('id', dataId);
                ++indexToc;
            }
            const name          = $(this)[0].localName;
            const dataTitle     = $(this).html();
            dataTocContent[i]   = {
                id      : dataId,
                name    : name,
                title   : dataTitle
            };
            ++i;
        });
        $.ajax({
            url         : '{{ route("main.buildTocContentMain") }}',
            type        : 'get', 
            dataType    : 'html',
            data        : {
                data    : dataTocContent
            },
            success     : function(data){
                $('#tocContentMain').html(data);
                fixedTocContentIcon();
                setHeightTocFixed();

                $(window).resize(function() {
                    fixedTocContentIcon();
                    setHeightTocFixed();
                });

                $('.tocFixedIcon, .tocContentMain.tocFixed .tocContentMain_close').click(function(){
                    let elementMenu = $('.tocContentMain.tocFixed');
                    let displayMenu = elementMenu.css('display');
                    if(displayMenu=='none'){
                        elementMenu.css('display', 'block');
                    }else {
                        elementMenu.css('display', 'none');
                    }
                    // fixedTocContentIcon();
                });

                $('.tocContentMain_title, .tocContentMain_close').click(function(){
                    let elemtMenu   = $('.tocContentMain .tocContentMain_list');
                    let displayMenu = elemtMenu.css('display');
                    if(displayMenu=='none'){
                        elemtMenu.css('display', 'block');
                        $('.tocContentMain_close').removeClass('hidden');
                    }else {
                        elemtMenu.css('display', 'none');
                        $('.tocContentMain_close').addClass('hidden');
                    }
                });

                function fixedTocContentIcon(){
                    let widthS      = $(window).width();
                    let widthC      = $('.container').outerWidth();
                    let leftE       = parseInt((widthS - widthC - 70) / 2);
                    if($(window).width() < 1200){
                        leftE       = parseInt((widthS - widthC + 20) / 2);
                    }
                    $('.tocFixedIcon').css('left', leftE);
                }

                function setHeightTocFixed(){
                    let heightToc   = parseInt($(window).height() - 210);
                    $('.tocContentMain.tocFixed .tocContentMain_list').css('height', heightToc+'px');
                }

                let element         = $('#tocContentMain');
                if(element.length>0){
                    let positionE       = element.offset().top;
                    let heightE         = element.outerHeight();
                    let boxContent      = $('#'+idElement);
                    let positionB       = boxContent.offset().top;
                    let heightB         = boxContent.outerHeight();
                    let heightFooter    = $('.footerBox').outerHeight();
                    $(document).scroll(function(){
                        let scrollNow   = $(document).scrollTop();
                        let minScroll   = parseInt(heightE + positionE);
                        let maxScroll   = parseInt(heightB + positionB - heightFooter);
                        if(scrollNow > minScroll && scrollNow < maxScroll){ 
                            $('.tocFixedIcon').css('display', 'block');
                        }else {
                            $('.tocFixedIcon').css('display', 'none');
                        }
                    });
                }
            }
        });
    }
    /* validate form khi nhập */
    function validateWhenType(elementInput, type = 'empty'){
        const idElement         = $(elementInput).attr('id');
        const parent            = $(document).find('[for*="'+idElement+'"]').parent();
        /* validate empty */
        if(type=='empty'){
            const valueElement  = $.trim($(elementInput).val());
            if(valueElement!=''&&valueElement!='0'){
                parent.removeClass('validateErrorEmpty');
                parent.addClass('validateSuccess');
            }else {
                parent.removeClass('validateSuccess');
                parent.addClass('validateErrorEmpty');
            }
        }
        /* validate phone */ 
        if(type=='phone'){
            const valueElement = $.trim($(elementInput).val());
            if(valueElement.length>=10&&/^\d+$/.test(valueElement)){
                parent.removeClass('validateErrorPhone');
                parent.addClass('validateSuccess');
            }else {
                parent.removeClass('validateSuccess');
                parent.addClass('validateErrorPhone');
            }
        }
        /* validate email */ 
        if(type=='email'){
            const valueElement = $.trim($(elementInput).val());
            /* check empty (nếu required) */
            if($(elementInput).prop('required')){
                if(valueElement==''){
                    parent.removeClass('validateSuccess');
                    parent.removeClass('validateErrorEmail');
                    parent.addClass('validateErrorEmpty');
                    return false;
                }
                /* check email hợp lệ */
                if(/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valueElement)){
                    parent.removeClass('validateErrorEmail');
                    parent.removeClass('validateErrorEmpty');
                    parent.addClass('validateSuccess');
                }else {
                    parent.removeClass('validateSuccess');
                    parent.removeClass('validateErrorEmpty');
                    parent.addClass('validateErrorEmail');
                }
            }else {
                /* check email hợp lệ */
                if(/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valueElement)){
                    parent.removeClass('validateErrorEmail');
                    parent.removeClass('validateErrorEmpty');
                    parent.addClass('validateSuccess');
                }
            }
        }
    }
    /* load quận/huyện */
    function loadDistrictByIdProvince(elementProvince, idWrite){
        const valueProvince = $(elementProvince).val();
        $.ajax({
            url         : '{{ route("ajax.loadDistrictByIdProvince") }}',
            type        : 'get',
            dataType    : 'html',
            data        : {
                province_info_id : valueProvince
            },
            success     : function(response){
                $('#'+idWrite).html(response);
            }
        });
    }
    /* validate form */
    function validateForm(idForm){
        let error       = [];
        /* input required không được bỏ trống */
        $('#'+idForm).find('input[required]').each(function(){
            /* đưa vào mảng */
            if($(this).val()==''){
                error.push($(this).attr('name'));
            }
        })
        /* select */
        $('#'+idForm).find('select[required]').each(function(){
            if($(this).val()==0) error.push($(this).attr('name'));
        })
        return error;
    }
    /* check csrf first time */
    function checkToSetCsrfFirstTime(){
        /* dùng cho trường hợp người dùng vào trang lần đầu chưa có cookie CSRF */
        const flag = '{{ $_COOKIE["XSRF-TOKEN"] ?? "" }}';
        if(flag==''){
            $.ajax({
                url: '{{ route("main.setCsrfFirstTime") }}',
                dataType: 'json',
                type: 'get',
                success: function(response) {
                    if(response==true) location.reload();
                }
            });
        }
    }
    /* check đăng nhập */
    function checkLoginAndSetShow(){
        const language = $('#language').val();
        $.ajax({
            url         : '{{ route("ajax.checkLoginAndSetShow") }}',
            type        : 'get',
            dataType    : 'json',
            data        : {
                '_token'            : '{{ csrf_token() }}',
                language
            },
            success     : function(response){
                /* button desktop */
                $('#js_checkLoginAndSetShow_button').html(response.button);
                $('#js_checkLoginAndSetShow_button').css('display', 'flex');
                /* button mobile */
                $('#js_checkLoginAndSetShow_buttonMobile').html(response.button_mobile);
                /* modal chung */
                $('#js_checkLoginAndSetShow_modal').html(response.modal);
            }
        });
    }
    /* loadmore wallpaper */
    function loadWallpaperMore(requestLoad = 5){
        var boxCategory       = $('#js_loadMore_box');
        if(boxCategory.length&&!boxCategory.hasClass('loading')){
            const distanceLoad  = boxCategory.outerHeight() + boxCategory.offset().top;
            if($(window).scrollTop() + 1200 > boxCategory.outerHeight() + boxCategory.offset().top) {
                /* thực thi */
                /* thêm class để đánh dấu đăng load => không load nữa */
                boxCategory.addClass('loading');
                /* lấy dữ liệu */
                const total         = parseInt($('#js_loadMore_total').val());
                const loaded        = parseInt($('#js_loadMore_loaded').val());
                const keyCategory   = $('#js_loadMore_keyCategory').val();
                const language      = $('#language').val();
                if(total>loaded){
                    $.ajax({
                        url         : '{{ route("main.category.loadMore") }}',
                        type        : 'get',
                        dataType    : 'json',
                        data        : {
                            total           : total,
                            loaded          : loaded,
                            key_category    : keyCategory,
                            language        : language,
                            request_load    : requestLoad
                        },
                        success     : function(response){
                            /* xóa bỏ class để thể hiện đã load xong */
                            boxCategory.removeClass('loading');
                            /* append dữ liệu */
                            if(response.content!=''){
                                $('#js_loadMore_loaded').val(response.loaded);
                                boxCategory.append(response.content);
                            }
                        }
                    });
                }

            }
        }
    }
    /* loadmore wallpaper promotion */
    function loadWallpaperPromotionMore(requestLoad = 5){
        var boxCategory       = $('#js_loadMore_box');
        if(boxCategory.length&&!boxCategory.hasClass('loading')){
            const distanceLoad  = boxCategory.outerHeight() + boxCategory.offset().top;
            if($(window).scrollTop() + 1200 > boxCategory.outerHeight() + boxCategory.offset().top) {
                /* thực thi */
                /* thêm class để đánh dấu đăng load => không load nữa */
                boxCategory.addClass('loading');
                /* lấy dữ liệu */
                const total         = parseInt($('#js_loadMore_total').val());
                const loaded        = parseInt($('#js_loadMore_loaded').val());
                const language      = $('#language').val();
                if(total>loaded){
                    $.ajax({
                        url         : '{{ route("main.category.loadMorePromotion") }}',
                        type        : 'get',
                        dataType    : 'json',
                        data        : {
                            total           : total,
                            loaded          : loaded,
                            language        : language,
                            request_load    : requestLoad
                        },
                        success     : function(response){
                            /* xóa bỏ class để thể hiện đã load xong */
                            boxCategory.removeClass('loading');
                            /* append dữ liệu */
                            if(response.content!=''){
                                $('#js_loadMore_loaded').val(response.loaded);
                                boxCategory.append(response.content);
                            }
                        }
                    });
                }

            }
        }
    }
    /* loadmore wallpaper promotion */
    function loadWallpaperSearchMore(requestLoad = 5){
        var boxCategory       = $('#js_loadMore_box');
        if(boxCategory.length&&!boxCategory.hasClass('loading')){
            const distanceLoad  = boxCategory.outerHeight() + boxCategory.offset().top;
            if($(window).scrollTop() + 1200 > boxCategory.outerHeight() + boxCategory.offset().top) {
                /* thực thi */
                /* thêm class để đánh dấu đăng load => không load nữa */
                boxCategory.addClass('loading');
                /* lấy dữ liệu */
                const total         = parseInt($('#js_loadMore_total').val());
                const loaded        = parseInt($('#js_loadMore_loaded').val());
                const language      = $('#language').val();
                if(total>loaded){
                    $.ajax({
                        url         : '{{ route("main.category.loadMoreSearch") }}',
                        type        : 'get',
                        dataType    : 'json',
                        data        : {
                            total           : total,
                            loaded          : loaded,
                            request_load    : requestLoad,
                            language        : language,
                            key_search      : '{{ request("key_search") }}'
                        },
                        success     : function(response){
                            /* xóa bỏ class để thể hiện đã load xong */
                            boxCategory.removeClass('loading');
                            /* append dữ liệu */
                            if(response.content!=''){
                                $('#js_loadMore_loaded').val(response.loaded);
                                boxCategory.append(response.content);
                            }
                        }
                    });
                }

            }
        }
    }
</script>