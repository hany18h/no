<?php

add_shortcode('madara_novelhub_hero_slider', 'madara_novelhub_hero_slider_render', 10, 2);
function madara_novelhub_hero_slider_render( $atts, $content ) {
    global $wp_manga_functions;

    $id        = 'c-post-slider-' . rand( 1, 999 );
    $cats      = isset( $atts['cats'] ) && $atts['cats'] != '' ? $atts['cats'] : '';
    $tags      = isset( $atts['tags'] ) && $atts['tags'] != '' ? $atts['tags'] : '';
    $orderby   = isset( $atts['orderby'] ) && $atts['orderby'] != '' ? $atts['orderby'] : 'latest';
    $count     = isset( $atts['count'] ) && $atts['count'] != '' ? $atts['count'] : '5';
    $order     = isset( $atts['order'] ) && $atts['order'] != '' ? $atts['order'] : 'DESC';
    $ids       = isset( $atts['ids'] ) && $atts['ids'] != '' ? $atts['ids'] : '';
    $post_type = isset( $atts['post_type'] ) && $atts['post_type'] != '' ? $atts['post_type'] : 'wp-manga';
    $manga_type = isset( $atts['manga_type'] ) && $atts['manga_type'] != '' ? $atts['manga_type'] : '';
    $time      = isset( $atts['time'] ) && $atts['time'] != '' ? $atts['time'] : 'all';
    $autoplay      = isset( $atts['autoplay'] ) && $atts['autoplay'] != '' ? $atts['autoplay'] : false;

    if ( $orderby == 'view' ) {
        $orderby = 'most_viewed';
    } else if ( $orderby == 'random' ) {
        $orderby = 'rand';
    } else if ( $orderby == 'comment' ) {
        $orderby = 'most_commented';
    } else if ( $orderby == 'title' ) {
        $orderby = 'title';
    } else if ( $orderby == 'input' ) {
        $orderby = 'post__in';
    } else {
        $orderby = 'date';
    }
    
    $args = array(
        'categories' => $cats,
        'tags'       => $tags,
        'ids'        => $ids,
        'post_type'  => $post_type,
        'timerange'  => $time
    );
    
    if($post_type == 'wp-manga'){
        if($manga_type != ''){
            $args['meta_query_value'] = $manga_type;
            $args['key'] = '_wp_manga_chapter_type';
        }
    }

    if($orderby == 'most_viewed'){
        switch($time){
            case 'day':
                $args['viewed_meta_key'] = '_wp_manga_day_views_value';
                break;
            case 'week':
                $args['viewed_meta_key'] = '_wp_manga_week_views_value';
                break;
            case 'month':
                $args['viewed_meta_key'] = '_wp_manga_month_views_value';
                break;
            case 'year':
                $args['viewed_meta_key'] = '_wp_manga_year_views_value';
                break;
            case 'all':
            default:
                $args['viewed_meta_key'] = '_wp_manga_views';
                break;
        }
    }
    $shortcode_query = App\Models\Database::getPosts( $count, $order, 1, $orderby, $args );

    $wrap_class  = 'manga-novelhub-hero-slider';
    $inner_class = 'novelhub-hero_slider__container';
    
    ob_start();
    if ( $shortcode_query->have_posts() ) {

?>

        <div id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $wrap_class );?>" data-count="<?php echo esc_attr( $count ); ?>" <?php if($autoplay && $autoplay == '1'){?>data-autoplay="1"<?php }?>>

        <div class="<?php echo esc_attr( $inner_class ); ?>" role="toolbar">

            <?php
                while ( $shortcode_query->have_posts() ) {

                    $shortcode_query->the_post();
                    $banner = get_post_meta(get_the_ID(),'manga_banner',true) ? get_post_meta(get_the_ID(),'manga_banner',true) :  get_stylesheet_directory_uri() . '/assets/images/1920x480.png';
                    ?>
                    <a href="<?php the_permalink(); ?>" class="novelhub-hero_slider__item">
                        <img src="<?php echo esc_url($banner); ?>" alt="<?php the_title(); ?>">
                    </a>
                <?php }//end while
                wp_reset_postdata();
            ?>

            </div>

        </div>

        <?php
    }
    $output = ob_get_contents();
    ob_end_clean();

    return $output;

}


add_shortcode('madara_novelhub_listing', 'madara_novelhub_listing_render', 10, 2);
function madara_novelhub_listing_render( $atts, $content ) {

    $title         = !empty( $atts['heading'] ) ? $atts['heading'] : '';
    $title_url     = !empty( $atts['heading_url'] ) ? $atts['heading_url'] : '';
    $heading_icon  = !empty( $atts['heading_icon'] ) ? $atts['heading_icon'] : (function_exists( 'madara_default_heading_icon' ) ? madara_default_heading_icon( false ) : '');
    $view_all_label = !empty( $atts['view_all_label'] ) ? $atts['view_all_label'] : '';
    $view_all_url  = !empty( $atts['view_all_url'] ) ? $atts['view_all_url'] : '';
    $item_layout    = isset($atts['item_layout']) ? $atts['item_layout'] : 'default';
    $style          = isset($atts['style']) ? $atts['style'] : ''; // '' or 'one_featured'
    $orderby       = !empty( $atts['orderby'] ) ? $atts['orderby'] : 'latest';
    $count         = !empty( $atts['count'] ) ? $atts['count'] : '';
    $order         = !empty( $atts['order'] ) ? $atts['order'] : 'DESC';
    $genres        = !empty( $atts['genres'] ) ? $atts['genres'] : null;
    $tags          = !empty( $atts['tags'] ) ? $atts['tags'] : null;
    $ids           = !empty( $atts['ids'] ) ? $atts['ids'] : '';
    $items_per_row = !empty( $atts['items_per_row'] ) ? $atts['items_per_row'] : 0;
    $chapter_type  = !empty( $atts['chapter_type'] ) ? $atts['chapter_type'] : 'all';
    $sidebar = !empty( $atts['sidebar'] ) ? $atts['sidebar'] : 0;
    // use 0 to get current user ID, leave empty if not filtered by author
    $author_id = isset($atts['author']) ? $atts['author'] : '';
    
    $shortcode_args = array();
    if($author_id != ''){
        $shortcode_args['author'] = $author_id ? $author_id : get_current_user_id();
    }
    
    // if only list mangas by Following 
    $following = isset($atts['following']) ? 1 : 0;
    
    if($following){
        $current_user_id = get_current_user_id();
        if(!$current_user_id){
            return;
        }
        
        $bookmarks     = get_user_meta( $current_user_id, '_wp_manga_bookmark', true );
        if(! empty($bookmarks)){
            $ids = implode(',',array_column($bookmarks, 'id'));
        } else {
            return;
        }
    }

    if( empty( $ids ) ){
        $shortcode_args = array_merge($shortcode_args, array(
            'post_type'      => 'wp-manga',
            'posts_per_page' => $count,
            'order'          => $order,
            'orderby'        => $orderby,
        ));
        
        $meta_query = array('relation' => 'AND');

        if ( $chapter_type == 'manga' ) {
            $meta_query = array(
                'relation' => 'AND',
                array(
                    'relation' => 'OR',
                    array(
                        'key'     => '_wp_manga_chapter_type',
                        'value'   => '',
                        'compare' => 'NOT EXISTS'
                    ),
                    array(
                        'key'   => '_wp_manga_chapter_type',
                        'value' => 'manga',
                    )
                )
            );
        } elseif ( $chapter_type == 'text' || $chapter_type == 'video' ) {
            $meta_query = array(
                'relation' => 'AND',
                array(
                    'key'     => '_wp_manga_chapter_type',
                    'value'   => $chapter_type,
                    'compare' => '='
                )
            );
        }
        
        $type = isset($atts['type']) ? $atts['type'] : '';
        $status = isset($atts['status']) ? $atts['status'] : '';
        
        if ( ! empty( $status ) ) {
            array_push( $meta_query, array(
                                                        'key'     => '_wp_manga_status',
                                                        'value'   => $status,
                                                        ));
        }
        
        if ( ! empty( $type ) ) {
            array_push( $meta_query, array(
                                                        'key'     => '_wp_manga_type',
                                                        'value'   => $type,
                                                        ));
        }
        
        $shortcode_args['meta_query'] = $meta_query;

        if( !empty( $tags ) || !empty( $genres ) ){
            $shortcode_args['tax_query'] = array(
                'relation' => 'OR'
            );

            if( !empty( $tags ) ){
                $tags = explode( ',', $tags );

                if( !empty( $tags ) ){
                    $shortcode_args['tax_query'][] = array(
                        'taxonomy' => 'wp-manga-tag',
                        'field'    => 'slug',
                        'terms'    => $tags
                    );
                }
            }

            if( !empty( $genres ) ){
                $genres = explode( ',', $genres );

                if( !empty( $genres ) ){
                    $shortcode_args['tax_query'][] = array(
                        'taxonomy' => 'wp-manga-genre',
                        'field'    => 'slug',
                        'terms'    => $genres
                    );
                }
            }
        }

        if( !function_exists( 'madara_manga_query' ) ){
            return;
        }

        $shortcode_query = madara_manga_query( $shortcode_args );

    } else{
        $shortcode_args = array_merge($shortcode_args, array(
            'post__in'       => explode( ',', $ids ),
            'post_type'      => 'wp-manga',
        ));

        $shortcode_query = new WP_Query( $shortcode_args );
    }
    

    ob_start();

    if ( $shortcode_query->have_posts() ) {
        ?>
        <div class="c-page">
            <div class="c-page__content">

                <?php if ( ! empty( $title ) ) { ?>
                    <div class="tab-wrap">
                        <div class="c-blog__heading style-2 font-heading">
                            <?php 
                                if ( ! empty( $title_url ) ) {
                                    echo '<h4><a href="' . esc_url( $title_url ) . '">'.esc_html($title).'</a></h4>';
                                } else {
                                    echo '<h4>'.esc_html( $title ).'</h4>';
                                }
                            ?>

                            <?php if ( ! empty( $view_all_label ) ) { ?>
                                <a href="<?php echo esc_url( $view_all_url ); ?>" class="heading-view-all"><?php echo esc_html( $view_all_label ); ?></a>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <!-- Tab panes -->
                <div class="tab-content-wrap">
                    <div role="tabpanel" class="c-tabs-item">
                        <div class="page-content-listing item-<?php echo esc_attr($item_layout);?> item-<?php echo esc_attr($style);?>">
                            <?php
                                if ( $shortcode_query->have_posts() ) {

                                    global $wp_query;
                                    $index = 1;
                                    $wp_query->set( 'madara_post_count', madara_get_post_count( $shortcode_query ) );

                                    if ( !$sidebar ) {
                                        $wp_query->set( 'sidebar', 'full' );
                                    }
                                    
                                    $wp_query->set('manga_archives_item_layout', $item_layout);
                                    $wp_query->set('manga_archives_item_style', $style);
                                    
                                    if($items_per_row){
                                        $wp_query->set('manga_archives_item_columns', $items_per_row);
                                    }
                                    
                                    if($item_layout == 'chapters'){
                                        $html = '<table class="manga-shortcodes manga-chapters-listing">
                                        <thead>
                                        <th class="genre">' . esc_html__('Genre','madara-shortcode') . '</th>
                                        <th class="title">' . esc_html__('Title','madara-shortcode') . '</th>
                                        <th class="release">' . esc_html__('Release','madara-shortcode') . '</th>
                                        <th class="author">' . esc_html__('Author','madara-shortcode') . '</th>
                                        <th class="time">' . esc_html__('Time','madara-shortcode') . '</th>
                                        </thead><tbody>';
                                        echo apply_filters('wp-manga-shortcode-manga-listing-layout-chapters-header', $html);
                                    }

                                    while ( $shortcode_query->have_posts() ) {

                                        $wp_query->set( 'madara_loop_index', $index );
                                        $index ++;

                                        $shortcode_query->the_post();
                                        
                                        if($item_layout == 'chapters'){
                                            include dirname(__FILE__) .  '/html/manga-listing/chapter-item.php';
                                        } else {
                                            get_template_part( 'madara-core/content/content', 'archive' );
                                        }
                                    }
                                    
                                    if($item_layout == 'chapters'){
                                        $html = '</tbody></table>';
                                        if ($view_all_label != '') {
                                            $html .= '<a href="' . esc_url($view_all_url) . '" class="view-all">' . esc_html($view_all_label) . '</a>';
                                        }
                                        echo apply_filters('wp-manga-shortcode-manga-listing-layout-chapters-footer', $html);
                                    }

                                } else {
                                    get_template_part( 'madara-core/content/content-none' );
                                }

                                wp_reset_postdata();

                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    $output = ob_get_contents();
    ob_end_clean();

    return $output;
}