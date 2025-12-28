jQuery(document).ready(function ($) {
    let cacheData = {};
    $('.tab-link').on('click', function () {
        // Remove 'active' class from all tabs and add to the clicked tab
        $('.tab-link').removeClass('active');
        $(this).addClass('active');

        // Get the selected time range
        let timeRange = $(this).data('range');
        let postsPerPage = $(this).closest('.c-widget-content').data('posts-per-page');

        if (cacheData[timeRange]) {
            $('#trending-posts-content').html(cacheData[timeRange]);
            return;
        }
        
        // Make AJAX request
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'get_trending_manga',
                time_range: timeRange,
                posts_per_page: postsPerPage,
                nonce: ajax_object.nonce,
            },
            beforeSend: function () {
                $('#trending-posts-content').html('<div class="trending-spinner"><i class="fas fa-circle-notch fa-spin"></i></div>');
            },
            success: function (response) {
                if (response.success) {
                    cacheData[timeRange] = response.data;
                    $('#trending-posts-content').html(response.data);
                } else {
                    $('#trending-posts-content').html('<p>No posts found.</p>');
                }
            },
            error: function () {
                $('#trending-posts-content').html('<p>error</p>');
            },
        });
    });
});