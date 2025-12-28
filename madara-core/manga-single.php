<?php

	/**
	 * The Template for Manga Detail page
	 *
	 * This template can be overridden by copying it to your-child-theme/madara-core/manga-single.php
	 *
	 * HOWEVER, on occasion Madara will need to update template files and you
	 * (the theme developer) will need to copy the new files to your theme to
	 * maintain compatibility. We try to do this as little as possible, but it does
	 * happen. When this occurs the version of the template file will be bumped and
	 * we will list any important changes on our theme release logs.
	 * @package Madara
	 * @version 1.7.2.2
	 */

	get_header();
	use App\Madara;
    
	$wp_manga           = madara_get_global_wp_manga();
	$wp_manga_functions = madara_get_global_wp_manga_functions();
    $thumb_size         = 'madara_novelhub_square';
	$post_id            = get_the_ID();
    
    $is_oneshot = is_manga_oneshot($post_id);
    if($is_oneshot){
        get_template_part( '/madara-core/manga', 'oneshot' );
        exit;
    }

	$madara_single_sidebar      = madara_get_theme_sidebar_setting();
	$madara_breadcrumb          = Madara::getOption( 'manga_single_breadcrumb', 'on' );
	$manga_profile_background   = madara_output_background_options( 'manga_profile_background' );
	$manga_single_summary       = Madara::getOption( 'manga_single_summary', 'on' );

	$wp_manga_settings = get_option( 'wp_manga_settings' );
	$related_manga     = isset( $wp_manga_settings['related_manga'] ) ? $wp_manga_settings['related_manga'] : null;
    
    $info_summary_layout = Madara::getOption('manga_profile_summary_layout', 1);
    $manga_reading_social_share = Madara::getOption( 'manga_reading_social_share', 'off' );

do_action( 'before_manga_single' );
 ?>
<div <?php post_class();?>>
    <div class="profile-manga summary-layout-<?php echo esc_attr($info_summary_layout);?>" style="<?php echo esc_attr( $manga_profile_background != '' ? $manga_profile_background : 'background-image: url(' . get_parent_theme_file_uri( '/images/bg-search.jpg' ) . ');' ); ?>">
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12">
                    <?php
                        if ( $madara_breadcrumb == 'on' ) {
                            get_template_part( 'madara-core/manga', 'breadcrumb' );
                        }
                    ?>
                    <div class="tab-summary <?php echo has_post_thumbnail() ? '' : esc_attr( 'no-thumb' ); ?>">
                        <?php 
                        
                        set_query_var('thumb_size', $thumb_size);
                        set_query_var('post_id', $post_id);
                        get_template_part( '/madara-core/single/info-summary', $info_summary_layout); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="c-page-content style-1">
    <div class="content-area">
        <div class="container">
            <div class="row <?php echo esc_attr( $madara_single_sidebar == 'left' ? 'sidebar-left' : '' ) ?>">
                <div class="main-col <?php echo esc_attr( $madara_single_sidebar !== 'full' && ( is_active_sidebar( 'manga_single_sidebar' ) || is_active_sidebar( 'main_sidebar' ) ) ? ' col-md-8 col-sm-8' : 'col-md-12 col-sm-12 sidebar-hidden' ) ?>">
                    <!-- container & no-sidebar-->
                    <div class="main-col-inner">
                        <div class="c-page">
                            <!-- <div class="c-page__inner"> -->
                            <div class="c-page__content">
                                <div class="manga-extra-info">
                                    <div class="manga-extra-info__tabs">
                                        <ul class="nav nav-tabs" id="manga-extra-info__tabs" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="manga-extra-info__tab-1" data-toggle="tab" href="#manga-extra-info__tab-content-1" role="tab" aria-controls="manga-extra-info__tab-content-1" aria-selected="true">
                                                    <?php echo esc_html__( 'About', 'madara-child' ); ?>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="manga-extra-info__tab-2" data-toggle="tab" href="#manga-extra-info__tab-content-2" role="tab" aria-controls="manga-extra-info__tab-content-2" aria-selected="false">
                                                    <?php echo esc_html__( 'Table of Contents', 'madara-child' ); ?>
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="manga-extra-info__tab-content">
                                            <div class="tab-pane fade show active" id="manga-extra-info__tab-content-1" role="tabpanel" aria-labelledby="manga-extra-info__tab-1">
                                                <div class="manga-extra-info__content">

                                                    <!-- exceprt-area -->
                                                    <div class="info-block manga-excerpt <?php echo $manga_single_summary == 'on' ? 'show-less' : ''?>">
                                                        <h4><?php echo esc_html__( 'Synopsis', 'madara-child' ); ?></h4>            
                                                        <div class="excerpt-content">
                                                            <?php 
                                                            global $post;
                                                            echo apply_filters('the_content',$post->post_content);
                                                            ?>
                                                        </div>                                                        

                                                        <?php if( !empty($post->post_content) && strlen(strip_tags($post->post_content)) > 500 && $manga_single_summary == 'on'){ ?>
                                                        <div id="read-more-btn">
                                                            <button><?php echo esc_html__( 'Read more', 'madara-child' ); ?></button>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                    <!-- END excerpt-area -->

                                                    <!--tags-area -->
                                                    <?php 
                                                    $madara = \App\Madara::getInstance();
                                                    $setting = $madara->getOption('manga_single_tags_post', 'info');
                                                    $tags = $wp_manga_functions->get_manga_tags( get_the_ID() );
                                                    if($tags != '' && ($setting == 'info' || $setting == 'both')) {?>
                                                    <div class="info-block manga-tags">
                                                        <h4>
                                                            <?php echo esc_html__( 'Tag', 'madara-child' ); ?>
                                                        </h4>
                                                        <div class="tags-content">
                                                            <?php 
                                                                $tags = str_replace(',','', $tags);
                                                                echo wp_kses_post( $tags );
                                                            ?>
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                    <!-- END tags-area -->
                                                    
                                                    <!-- related-area -->
                                                    <div class="info-block manga-related">
                                                        <?php
                                                        if ( $related_manga == 1 ) {
                                                                get_template_part( '/madara-core/manga', 'related' );
                                                        }
                                                        ?>
                                                    </div>
                                                    <!-- END related-area -->

                                                    <!-- comments-area -->
                                                    <div class="info-block manga-discussion">
                                                        <?php 
                                                            do_action( 'wp_manga_discussion' ); 
                                                        ?>
                                                        <!-- END comments-area -->
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="manga-extra-info__tab-content-2" role="tabpanel" aria-labelledby="manga-extra-info__tab-2">
                                                <div class="manga-extra-info__content">
                                                    <?php do_action('wp-manga-chapter-listing', get_the_ID()); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <!-- </div> -->
                        </div>
                    </div>
                </div>

				<?php
					if ( $madara_single_sidebar != 'full' && ( is_active_sidebar( 'main_sidebar' ) || is_active_sidebar( 'manga_single_sidebar' ) ) ) {
						?>
                        <div class="sidebar-col col-md-4 col-sm-4">
							<?php get_sidebar(); ?>
                        </div>
					<?php }
				?>

            </div>
        </div>
    </div>
</div>
<?php 
do_action( 'after_manga_single' ); ?>
<?php get_footer();