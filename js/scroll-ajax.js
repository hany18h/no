jQuery(document).ready(function ($) {
    let loading = false;
    let noMoreNextChapters = false;
    let noMorePrevChapters = false;
    let lastScrollTop = 0;

    // Function to check if scrolled to the bottom of the last chapter
    function isScrolledToBottom(element) {
        return $(window).scrollTop() + $(window).height() >= element.offset().top + element.outerHeight() + 100;
    }

    // Function to check if scrolled to the top of the first chapter
    function isScrolledToTop(element) {
        return $(window).scrollTop() <= element.offset().top;
    }

    // Function to load chapters via AJAX
    function loadChapter(mangaId, chapterId, direction) {
        if($('#chapter-' + chapterId).length){
            return;
        }

        if (loading) return;
        loading = true;

        $.ajax({
            url: madara.ajaxurl,
            type: 'POST',
            data: {
                action: 'load_chapter', 
                manga_id: mangaId,
                chapter_id: chapterId
            },
            success: function (response) {
                var chapter_name = '';
                $('.page-content-listing .wp-manga-chapter').each((idx, obj) => {
                    if ($(obj).data('chapter-id') == chapterId) {
                        chapter_name = $(obj).find('a').text();
                    }
                });

                const newChapterBlock = `<div id="chapter-${chapterId}" class="reading-content" data-block-chapter-id="${chapterId}"><h3 class="chapter-name">${chapter_name}</h3>${response}</div>`;

                if (direction === 'next') {
                    $('.read-container').append(newChapterBlock);
                } else if (direction === 'prev') {
                    $('.read-container').prepend(newChapterBlock);

                    const newChapterHeight = $(`#chapter-${chapterId}`).outerHeight();
                    $(window).scrollTop($(window).scrollTop() + newChapterHeight); // Keep scroll position
                }

                if($('.read-container .premium-block').length){
                    $('.read-container .premium-block a').unbind('click', wp_manga_premium_block_click);
                    $('.read-container .premium-block a').on('click', wp_manga_premium_block_click);
                }
                    
            },
            complete: function () {
                loading = false;
            }
        });
    }

    // to support old version of Chapter Coin
    if(typeof wp_manga_premium_block_click === 'undefined'){
        window.wp_manga_premium_block_click = function (evt) {
            if (!window.wp_manga_chapter_coin_just_add_premium) {
                // update the chapter id
                $('#frm-wp-manga-buy-coin input[name=wp-manga-chapter]').val($(evt.target).closest('.reading-content').data('block-chapter-id'));

                var cl = $(evt.target).closest('.premium-block').attr('class');

                if (typeof cl !== 'undefined') {
                    var matches = cl.match(/coin-\d+/g);
                    if (matches) {
                        var coin = matches[0].replace('coin-', '');
                        $('#frm-wp-manga-buy-coin .message-sufficient .coin').html(coin);

                        var user_balance = $('#wp_manga_chapter_coin_user_balance').length > 0 ? $('#wp_manga_chapter_coin_user_balance').val() : 0;

                        if (parseInt(user_balance) < parseInt(coin)) {
                            $('#frm-wp-manga-buy-coin .message-lack-of-coin').removeClass('hidden');
                            $('#frm-wp-manga-buy-coin .message-sufficient').addClass('hidden');
                            $('#frm-wp-manga-buy-coin .btn-agree').hide();
                            $('#frm-wp-manga-buy-coin .btn-buycoin').show();
                        } else {

                            $('#frm-wp-manga-buy-coin .message-lack-of-coin').addClass('hidden');
                            $('#frm-wp-manga-buy-coin .message-sufficient').removeClass('hidden');
                            $('#frm-wp-manga-buy-coin .btn-agree').show();
                            $('#frm-wp-manga-buy-coin .btn-buycoin').hide();
                        }
                    }

                    matches = cl.match(/data-chapter-\d+/g);
                    if (matches) {
                        var chapter_id = matches[0].replace('data-chapter-', '');
                        $('#frm-wp-manga-buy-coin input[name="wp-manga-chapter"]').val(chapter_id);
                    }

                    $('#frm-wp-manga-buy-coin').modal();
                }
            } else {
                window.wp_manga_chapter_coin_just_add_premium = false;
            }

            evt.stopPropagation();
            evt.preventDefault();
            return false;
        };
    }

    // Scroll down event: Load next chapter when scrolled to the bottom
    function handleScrollDown() {
        if ($('.reading-content-wrap').hasClass('disabled-load-next-chapter')) {
            return;
        }

        const lastReadingContentBlock = $('.reading-content:last');
        const currChapter = $('.chapters-list').find('.wp-manga-chapter.reading');
        const nextChapter = $('.page-content-listing').hasClass('order-asc') ? currChapter.next('.wp-manga-chapter') : currChapter.prev('.wp-manga-chapter');
        const nextChapterId = nextChapter.length ? nextChapter.data('chapter-id') : null;
        const mangaId = $('.chapters-list .page-content-listing').data('manga-id');

        // Check if scrolled to the bottom of the current last chapter block
        if (!noMoreNextChapters && isScrolledToBottom(lastReadingContentBlock)) {
            // Ensure nextChapterId is valid and not undefined
            if (nextChapterId) {
                loadChapter(mangaId, nextChapterId, 'next');
            } else {
                noMoreNextChapters = true; // Mark as no more chapters if no valid nextChapterId
                $('.read-container').append('<h3 class="last-chap">' + madara_novelhub.msg_last_chap + '</h3>');
            }
        }
    }

    // Scroll up event: Load previous chapter when scrolled to the top
    function handleScrollUp() {
        if ($('.reading-content-wrap').hasClass('disabled-load-prev-chapter')) {
            return;
        }

        const firstReadingContentBlock = $('.reading-content:first');
        const currChapter = $('.chapters-list').find('.wp-manga-chapter.reading');
        const prevChapter = $('.page-content-listing').hasClass('order-asc') ? currChapter.prev('.wp-manga-chapter') : currChapter.next('.wp-manga-chapter');
        const prevChapterId = prevChapter.length ? prevChapter.data('chapter-id') : null;
        const mangaId = $('.chapters-list .page-content-listing').data('manga-id');

        if (!noMorePrevChapters && isScrolledToTop(firstReadingContentBlock)) {
            if (prevChapterId) {
                loadChapter(mangaId, prevChapterId, 'prev');
            } else {
                noMorePrevChapters = true;
                console.log('No more previous chapters.');
            }
        }
    }

    function checkVisibleChapterContent(){
        var chapters = $('.reading-content');
        for(var i = 0; i < chapters.length; i++){
            var chapter = chapters[i];
            if($(chapter).visible(true)){
                var id = $(chapter).data('block-chapter-id');
                $('.read-container').find('.reading-content.current').removeClass('current');
                $('.chapters-list').find('.wp-manga-chapter.reading').removeClass('reading');

                $('#chapter-' + id).addClass('current');
                $('.chapters-list').find('.wp-manga-chapter[data-chapter-id=' + id + ']').addClass('reading');
                
                var current_chapter_name = $('#chapter-' + id).find('.chapter-name').text();
                $('.reading-sticky-menu .current-chapter h3').text(current_chapter_name);
                
                break;
            }
        }
    }

    // Scroll event handler: separate scroll up and down detection
    $(window).on('scroll', function () {
        checkVisibleChapterContent();

        if ($(this).scrollTop() > lastScrollTop) {
            // Scrolling down
            handleScrollDown();
        } else {
            // Scrolling up
            handleScrollUp();
        }
        lastScrollTop = $(this).scrollTop();
    });

    // Initial setup
    lastScrollTop = $(window).scrollTop(); // Track scroll position
});