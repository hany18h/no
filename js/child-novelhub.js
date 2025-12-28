jQuery(document).ready(function($){
    
    // slick slider for novelhub hero slider
    $('.novelhub-hero_slider__container').slick({
        arrows: true,
        dots: true,
        infinite: true,
        speed: 300,
        slidesToShow: 1,
        autoplay: true,
        customPaging: function(slider, i) {
            return '';
        },
        nextArrow: '<button type="button" class="slick-next"><i class="icon ion-ios-arrow-forward"></i></button>',
        prevArrow: '<button type="button" class="slick-prev"><i class="icon ion-ios-arrow-back"></i></button>',
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    arrows: false
                }                
            }
        ],
    });

    // slick slider for novelhub featured listing, only on mobile
    // check is mobile
    if (window.innerWidth < 1024) {
        $('.page-content-listing.item-big_thumbnail.item-one_featured .item-index-1-1').removeClass('item-index-1-1');
        // $('.page-content-listing.item-big_thumbnail.item-one_featured').slick({
        //     arrows: false,
        //     dots: true,
        //     infinite: true,
        //     speed: 300,
        //     slidesToShow: 1,
        //     rows: 3,
        //     slidesPerRow: 1,
        //     autoplay: true,
        //     customPaging: function(slider, i) {
        //         return '';
        //     },
        //     centerMode: true,
        //     centerPadding: '15px',
        // });
    }

    // canvas menu on mobile
    $('.mobile-navigation-wrap .header-btn-3').on('click', function(){
        $('.mobile-navigation-wrap .m-main-menu').toggleClass('active');
    });
    
    // View all excerpt
    $('#read-more-btn').on('click', function () {
        console.log('clicked');
        const $excerpt = $('.manga-extra-info .manga-excerpt');
        
        if ($excerpt.hasClass('show-less')) {
            $excerpt.removeClass('show-less').addClass('show-more');
            $(this).find(button).text('Read Less');
        } else if ($excerpt.hasClass('show-more')) {
            $excerpt.removeClass('show-more').addClass('show-less');
            $(this).find(button).text('Read More');
        }
    });

    // sidebar tools on reading page
    $('.sidebar-tools-item .btn-toggle-chapters').on('click', function(){
        $('.sidebar-tools-item button').removeClass('active');
        $(this).toggleClass('active');
        $('.reading-settings').removeClass('active');
        $('.side-col').toggleClass('show-chapters-list').removeClass('show-comments');
        $('.reading-manga .container .main-col').removeClass('active-comments').toggleClass('active-chapters-list')
    });

    $('.sidebar-tools-item .btn-toggle-comments').on('click', function(){
        $('.sidebar-tools-item button').removeClass('active');
        $(this).toggleClass('active');
        $('.reading-settings').removeClass('active');
        $('.side-col').toggleClass('show-comments').removeClass('show-chapters-list');
        $('.reading-manga .container .main-col').removeClass('active-chapters-list').toggleClass('active-comments');
    });

    $(document).on('click', '.side-col .close-btn button', function(){
        $('.sidebar-tools-item button').removeClass('active');
        $('.side-col').removeClass('show-chapters-list').removeClass('show-comments');
        $('.reading-manga .container .main-col').removeClass('active-chapters-list').removeClass('active-comments');
    });

    // extra settings for novel reading page

    $(document).on('click','.reading-settings .open-reader-settings',function (e){
        e.preventDefault();
        $('.sidebar-tools-item button').removeClass('active');
        $('.side-col').removeClass('show-chapters-list').removeClass('show-comments');
        $('.reading-manga .container .main-col').removeClass('active-chapters-list').removeClass('active-comments');
        $(this).toggleClass('active');
        $(this).parent().toggleClass('active');
        const selected_font = window.wpmanga.getCookie('wpmanga-reading-font');
        $('.theme-set-font input[value="'+selected_font+'"]').parent().addClass('active');
        const selected_fontsize = window.wpmanga.getCookie('wpmanga-reading-fontsize');
        $('#fontRange').val(selected_fontsize);
    });

    $('#wp-manga-reader-settings .close').on('click',function(){
        $('.reading-settings').removeClass('active');
        $('.open-reader-settings').removeClass('active');
    })

    $('#fontRange').on('change',function(){
        const fontSize = $(this).val();
        $('.read-container').attr('style', '--readingFontSize: '+fontSize+'px')
        // save cookie
        window.wpmanga.setCookie('wpmanga-reading-fontsize', fontSize, 30);
    })

    $('.theme-set-font input').on('click',function(){
        $('.theme-set-font li').removeClass('active');
        $(this).parent().addClass('active');
        $font = $(this).val();
        $('body').attr('data-font',$font);
        window.wpmanga.setCookie('wpmanga-reading-font', $font, 30);
    });

    $('.theme-set-color input').on('click',function(){
        $('.theme-set-color label').removeClass('active');
        $('.theme-set-color input').removeAttr('checked');
        $(this).attr('checked','checked');
        $(this).parent().addClass('active');
        $color = $(this).parent().data('schema');
        $('body').attr('data-schema',$color);
        window.wpmanga.setCookie('wpmanga-reading-schema', $color, 30);
    });

    $('#wp-manga-reader-settings #reset-reader-settings').on('click',function(){
        window.wpmanga.setCookie('wpmanga-reading-schema', '', 0);
        window.wpmanga.setCookie('wpmanga-reading-font', '', 0);
        window.wpmanga.setCookie('wpmanga-reading-fontsize', '', 0);
        $('.theme-set-color label input').removeAttr('checked');
        $('.theme-set-color label').removeClass('active');
        $('.theme-set-font label input').removeAttr('checked');
        $('.theme-set-font label').removeClass('active');
        $('body').attr('data-schema','');
        $('body').attr('data-font','');
        $('.read-container').attr('style', '--readingFontSize: 16px')
        $('#fontRange').val('');
    })

    if($('body').hasClass('reading-manga')){

        var $color = window.wpmanga.getCookie('wpmanga-reading-schema');
        if ($color != '') {
            $('.theme-set-color label input').removeAttr('checked');
            $('.theme-set-color').find('label._'+$color).addClass('active').find('input').attr('checked','checked');
    
            $('body').attr('data-schema',$color);
        }

        var $font = window.wpmanga.getCookie('wpmanga-reading-font');

        if ($font) {
            $('.theme-set-font label input').removeAttr('checked');
            $('.theme-set-font').find('label.'+$font).find('input').attr('checked','checked');
    
            if($('.c-page-content.chapter-type-text').length > 0){
                $('body').attr('data-font',$font);
            }
        }

        var $fontSize = window.wpmanga.getCookie('wpmanga-reading-fontsize');
        if ($fontSize) {
            $('#fontRange').val($fontSize);
            $('.read-container').attr('style', '--readingFontSize: '+$fontSize+'px')
        }
    }

    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 500) {
            $('.reading-sticky-menu').addClass('active');
        } else {
            $('.reading-sticky-menu').removeClass('active');
        }
    });

});