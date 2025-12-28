<?php 

/**
 * Trending manga
 */
class Trending_Manga_Widget extends WP_Widget {
    function __construct() {
        $widget_ops = array(
            'classname'   => 'c-popular manga-widget widget-manga-trending',
            'description' => esc_html__( 'Display Trending Manga', 'madara-child' )
        );
        parent::__construct( 'register_trending_manga_widget', esc_html__( 'WP Manga: Trending Manga', 'madara-child' ), $widget_ops );
        $this->alt_option_name = 'widget_manga_trending';
    }

    function widget( $args, $instance ) {
        global $wp_manga, $wp_manga_functions, $wp_manga_template;

        if ( ! isset( $args['widget_id'] ) ) {
            $args['widget_id'] = $this->id;
        }

        ob_start();
        extract( $args );

        $title          = ! empty( $instance['title'] ) ? $instance['title'] : '';
        $number_of_post = ! empty( $instance['number_of_post'] ) ? $instance['number_of_post'] : '3';

        $query_args = array(
            'posts_per_page' => $number_of_post,
            'order'          => 'DESC',
            'orderby'        => 'meta_value_num',
            'meta_key'     => '_wp_manga_day_views_value',
            'post_type'      => 'wp-manga',
            'post_status'    => 'publish',
        );

        $queried_posts = new WP_Query( $query_args );

        global $wp_manga_functions;

        echo $before_widget;

        ?>
        
        <div class="c-widget-content data-posts-per-page="<?php echo esc_attr( $number_of_post ); ?>">
            <?php
            if ( $title != '' ) {
                echo $before_title . $title . $after_title;
            }
            ?>

            <div class="time-range-tabs">
                <ul class="tabs">
                    <li class="tab-link active" data-range="day"><?php esc_html_e( 'Today', 'madara-child' ); ?></li>
                    <li class="tab-link" data-range="month"><?php esc_html_e( 'This Month', 'madara-child' ); ?></li>
                    <li class="tab-link" data-range="all"><?php esc_html_e( 'All Time', 'madara-child' ); ?></li>
                </ul>
            </div>

            <div id="trending-posts-content">
                <?php
                while ( $queried_posts->have_posts() ) {
                    $queried_posts->the_post();
                    ?>
                        <?php $wp_manga_template->load_template( 'widgets/recent-manga/content-trending', false ); ?>
                    <?php
                }
                wp_reset_postdata();
                ?>
            </div>
        </div>
        <?php

        echo $after_widget;
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['title']          = strip_tags( $new_instance['title'] );
        $instance['number_of_post'] = strip_tags( $new_instance['number_of_post'] );

        return $instance;
    }

    function form( $instance ) {
        $title          = ! empty( $instance['title'] ) ? $instance['title'] : '';
        $number_of_post = ! empty( $instance['number_of_post'] ) ? $instance['number_of_post'] : '3';

        $time_range = ! empty( $instance['time_range'] ) ? $instance['time_range'] : 'all';

        ?>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"> <?php echo esc_html__( 'Title', 'madara-child' ); ?>
                : </label>
            <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'number_of_post' ); ?>"><?php echo esc_html__( 'Number of posts', 'madara-child' ); ?>
                : </label>
            <input class="widefat" type="number" id="<?php echo $this->get_field_id( 'number_of_post' ); ?>" name="<?php echo $this->get_field_name( 'number_of_post' ); ?>" value="<?php echo esc_attr( $number_of_post ) ?>">
        </p>

        <?php
    }

}

function register_trending_manga_widget() {
    register_widget('Trending_Manga_Widget');
}

function enqueue_trending_manga_scripts() {
    wp_enqueue_script(
        'trending-manga-widget',
        get_stylesheet_directory_uri() . '/js/trending-manga-widget.js', // Adjust path
        array('jquery'),
        null,
        true
    );

    wp_localize_script('trending-manga-widget', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('trending_manga_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_trending_manga_scripts');

function get_trending_manga_ajax() {
    check_ajax_referer('trending_manga_nonce', 'nonce');

    $time_range = isset($_POST['time_range']) ? sanitize_text_field($_POST['time_range']) : 'day';
    $posts_per_page = isset($_POST['posts_per_page']) ? intval($_POST['posts_per_page']) : 5;

    $meta_key = '';
    switch ($time_range) {
        case 'day':
            $meta_key = '_wp_manga_day_views_value';
            break;
        case 'week':
            $meta_key = '_wp_manga_week_views_value';
            break;
        case 'month':
            $meta_key = '_wp_manga_month_views_value';
            break;
        case 'year':
            $meta_key = '_wp_manga_year_views_value';
            break;
    }

    $query_args = array(
        'posts_per_page' => $posts_per_page,
        'order'          => 'DESC',
        'post_type'      => 'wp-manga',
        'post_status'    => 'publish',
    );

    if ($meta_key) {
        $query_args['meta_key'] = $meta_key;
        $query_args['orderby']  = 'meta_value_num';
    }

    $query = new WP_Query($query_args);

    if ($query->have_posts()) {
        ob_start();
        while ($query->have_posts()) {
            $query->the_post();
            global $wp_manga_template;
            $wp_manga_template->load_template('widgets/recent-manga/content-trending', false);
        }
        wp_reset_postdata();
        $content = ob_get_clean();
        wp_send_json_success($content);
    } else {
        wp_send_json_error();
    }
}
add_action('wp_ajax_get_trending_manga', 'get_trending_manga_ajax');
add_action('wp_ajax_nopriv_get_trending_manga', 'get_trending_manga_ajax');
