<?php

	/**
	 * The Template for printing out a manga property (Summary/Synopsis) in Manga Detail page
	 *
	 * This template can be overridden by copying it to your-child-theme/madara-core/single/info-summary.php
	 *
	 * HOWEVER, on occasion Madara will need to update template files and you
	 * (the theme developer) will need to copy the new files to your theme to
	 * maintain compatibility. We try to do this as little as possible, but it does
	 * happen. When this occurs the version of the template file will be bumped and
	 * we will list any important changes on our theme release logs.
	 * @package Madara
	 * @version 1.7.2.2
	 */

    global $wp_manga_functions;
    global $wp_manga_setting;
	 ?>
<?php if ( has_post_thumbnail() ) { ?>
    <div class="summary_image">
        <a href="<?php echo get_the_permalink(); ?>">
            <?php echo madara_thumbnail( $thumb_size ); ?>
        </a>
    </div>
<?php } ?>
<div class="summary_content">
    <div class="manga-type">
        <?php 
            $manga_type = get_post_meta( $post_id, '_wp_manga_type', true );
            if ( $manga_type ) {
                echo '<span class="manga-type-label">' . esc_html( $manga_type ) . '</span>';
            }
        ?>
    </div>

    <div class="manga-title">
        <h2><?php the_title(); ?></h2>
    </div>
    
    <div class="manga-data row-1">
        <div class="genres-content">
			<?php 
                $genres     = $wp_manga_functions->get_manga_genres( get_the_ID() );
                $genres_arr = explode( ', ', $genres );
                if ( $genres_arr ) {
                    echo  '<span><i class="icon ion-ios-book"></i></span>' . '<span>' .$genres_arr[0].' </span>';
                }
            ?>
		</div>

        <div class="released-chapters">
            <?php 
                $chapters = $wp_manga_functions->get_chapters_count(get_the_ID());
                if ( $chapters ) {
                    echo '<span><i class="icon ion-ios-list"></i></span>' . '<span>' . sprintf(esc_html__( "%s Chapters", 'madara-child'), $chapters ) . '</span>';
                }
            ?>
        </div>

        <div class="manga-status">
            <?php 
                $status = $wp_manga_functions->get_manga_status( get_the_ID() );
                if ( $status ) {
                    echo '<span><i class="icon ion-ios-clock"></i></span>' . '<span>' . esc_html( $status ) . '</span>';
                }
            ?>
        </div>

        <div class="manga-views">
            <?php
                $views = get_post_meta( get_the_ID(), '_wp_manga_views', true );
                if ( $views == false ) {
                    $views = 0;
                }
                echo '<span><i class="icon ion-ios-eye"></i></span>' . '<span>' . sprintf(esc_html__( "%s Views", 'madara-child'), $views ) . '</span>';
            ?>
        </div>
    </div>

    <div class="manga-data row-2">
        <div class="manga-author">
            <?php 
                
                $authors = $wp_manga_functions->get_manga_authors( get_the_ID() );
                if ( $authors ) {
                    echo '<span>'.esc_html__('Author: ', 'madara-child').'</span>' . '<span>' . wp_kses_post( $authors ) . '</span>';
                }
            ?>
        </div>
    </div>

    <div class="manga-rating">
        <div class="post-rating">
            <?php
                $wp_manga_functions->manga_rating_display( get_the_ID(), true );
            ?>
        </div>
    </div>

    <div class="manga-actions">
        <div class="quick-read">
            <?php 
                $is_oneshot = is_manga_oneshot(get_the_ID());
                if(!$is_oneshot){
                    set_query_var( 'manga_id', get_the_ID() );
                    get_template_part('madara-core/single/quick-buttons');
                }
            ?>
        </div>
        <div class="bookmark-btn">
        <?php
            $user_bookmark = $wp_manga_setting->get_manga_option('user_bookmark', 1);
            if($user_bookmark){?>
            <div class="add-bookmark">
                <?php
                    $wp_manga_functions->bookmark_link_e(); 
                ?>
            </div>
            <?php } ?>
        </div>

        <?php do_action( 'madara_single_manga_action' ); ?>
    </div>