<?php
	/**
	 * The Template for Manga Chapter Reading page
	 *
	 * This template can be overridden by copying it to your-child-theme/madara-core/manga-single-reading.php
	 *
	 * HOWEVER, on occasion Madara will need to update template files and you
	 * (the theme developer) will need to copy the new files to your theme to
	 * maintain compatibility. We try to do this as little as possible, but it does
	 * happen. When this occurs the version of the template file will be bumped and
	 * we will list any important changes on our theme release logs.
	 * @package Madara
	 * @version 1.7.3.10
	 */
	 
	 use App\Madara;
	global $wp_manga_functions;
	$manga_id  = get_the_ID();
	$reading_chapter = function_exists('madara_permalink_reading_chapter') ? madara_permalink_reading_chapter() : false;

	$thumb_size = 'madara_novelhub_square';
	if(!$reading_chapter){
		 // support Madara Core before 1.6
		 if($chapter_slug = get_query_var('chapter')){
			global $wp_manga_functions;
			$reading_chapter = $wp_manga_functions->get_chapter_by_slug( $manga_id, $chapter_slug );
		 }
		 
		 if(!$reading_chapter){
			global $wp_query;
			$wp_query->set_404();
			status_header( 404 );
			get_template_part( 404 ); exit();
		 }
	}

	$cur_chap = $reading_chapter['chapter_slug'];

	$wp_manga           = madara_get_global_wp_manga();
	$wp_manga_functions = madara_get_global_wp_manga_functions();
	
	$style    = isset( $_GET['style'] ) ? $_GET['style'] : $wp_manga_functions->get_reading_style();

	$wp_manga_settings = get_option( 'wp_manga_settings' );
	$related_manga     = isset( $wp_manga_settings['related_manga'] ) ? $wp_manga_settings['related_manga'] : null;
	if($related_manga == 1){
		$related_manga = Madara::getOption( 'manga_reading_related', 'on' ) == 'on' ? 1 : 0;
	}
	$madara_single_sidebar      = madara_get_theme_sidebar_setting();
	$madara_breadcrumb          = Madara::getOption( 'manga_single_breadcrumb', 'on' );
	$manga_reading_discussion   = Madara::getOption( 'manga_reading_discussion', 'on' );
	$manga_reading_social_share = Madara::getOption( 'manga_reading_social_share', 'off' );
	$manga_reading_load_prev = Madara::getOption('manga_reading_load_prev', 'off');
	$manga_reading_load_next = Madara::getOption('manga_reading_load_next', 'on');
	
	$manga_reading_sticky_menu = Madara::getOption('manga_reading_sticky_menu', 'on');

	$chapter_type = get_post_meta( $manga_id, '_wp_manga_chapter_type', true );
	$is_text_chapter_right_sidebar = ($madara_single_sidebar != 'full' && $chapter_type == 'text' && Madara::getOption( 'manga_reading_text_sidebar', 'on' ) == 'on') ? true : false;
	
	if ( $madara_single_sidebar == 'full' || $is_text_chapter_right_sidebar ) {
		$main_col_class = 'sidebar-hidden col-12 col-sm-12 col-md-12 col-lg-12';
	} else {
		$main_col_class = 'main-col col-12 col-sm-8 col-md-8 col-lg-8';
	}

	$bg_style = madara_output_background_options('chapter_reading_background');
	
	get_header();
?>
	<?php
		if ($manga_reading_sticky_menu !== 'off') {
			?>
				<div class="reading-sticky-menu">
					<div class="container">
						<div class="reading-sticky-menu-inner">
							<div class="back-to-novel-detail">
								<h4>
									<i class="fa fa-book"></i>
									<a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title($manga_id); ?></a>
								</h4>
							</div>
							<span class="sticky-menu-separator">/</span>
							<div class="current-chapter">
								<?php 
									if ($reading_chapter) {
										echo '<h3>'.esc_html($reading_chapter['chapter_name']).'</h3>';
									}
								?>
							</div>
						</div>
					</div>
				</div>
			<?php
		}
	?>
    <div class="c-page-content style-1 reading-content-wrap <?php echo $manga_reading_load_prev == 'off' ? 'disabled-load-prev-chapter' : ''?> <?php echo $manga_reading_load_next == 'off' ? 'disabled-load-next-chapter' : ''?> chapter-type-<?php echo esc_attr($chapter_type == '' ? 'manga' : $chapter_type);?>" data-site-url="<?php echo home_url( '/' ); ?>">
        <div class="content-area">
            <div class="container">
                <div class="container-inner">
                    <div class="main-col sidebar-hidden">
						
						<div class="manga-info">
							<?php 
								if(!$bg_style) {
									?>
									<div class="manga-background" style="background:  linear-gradient(rgba(67, 67, 67, 0.57), rgba(34, 34, 34, 0.4)), url(<?php echo get_the_post_thumbnail_url($manga_id, 'thumbnail');?>) center / cover no-repeat">
									</div>
									<?php
								}
							?>
							<?php if ( has_post_thumbnail() ) { ?>
								<div class="summary_image">
									<a href="<?php echo get_the_permalink(); ?>">
										<?php echo madara_thumbnail( $thumb_size ); ?>
									</a>
								</div>
							<?php } ?>

							<div class="manga-title">
								<h4><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title($manga_id); ?></a></h4>
							</div>
							<?php 
								
							$authors = $wp_manga_functions->get_manga_authors($manga_id );
							if ( $authors ) { ?>
							<div class="manga-author site">
								<div class="txt">
									<span><?php echo esc_html__('Author: ', 'madara-child');?></span>
									<span><?php echo wp_kses_post( $authors );?></span>
								</div>
							</div>
							<?php } ?>
						</div>
						
                        <!-- container & no-sidebar-->
                        <div class="main-col-inner">
                            <div class="c-blog-post">
                                <div class="entry-content">
                                    <div class="entry-content_wrap">

                                        <div class="read-container">

											<?php echo apply_filters( 'madara_ads_before_content', madara_ads_position( 'ads_before_content', 'body-top-ads' ) ); ?>

                                            <div id="chapter-<?php echo esc_attr($reading_chapter['chapter_id']);?>" class="reading-content current" data-block-chapter-id="<?php echo esc_attr($reading_chapter['chapter_id']);?>">
												<input type="hidden" id="wp-manga-current-chap" data-id="<?php echo esc_attr($reading_chapter['chapter_id']);?>" value="<?php echo esc_attr($cur_chap);?>"/>
												<?php 
													if ($reading_chapter) {
														echo '<h3 class="chapter-name">'.esc_html($reading_chapter['chapter_name']).'</h3>';
													}
                                                
                                                global $post;
                                                
                                                if( !$post->post_password || ($post->post_password && !post_password_required()) ){
												
                                                    /**
                                                     * If alternative_content is empty, show default content
                                                     **/
                                                    $alternative_content = apply_filters('wp_manga_chapter_content_alternative', '');
                                                    
                                                    if(!$alternative_content){
                                                        do_action('wp_manga_before_chapter_content', $cur_chap, $manga_id);
                                                        
                                                        do_action('wp_manga_chapter_content', $cur_chap, $manga_id);
                                                        
                                                        do_action('wp_manga_after_chapter_content', $cur_chap, $manga_id);
                                                    } else {
                                                        echo madara_filter_content($alternative_content);
                                                    }
                                                
                                                } else {
                                                    // show the password form
                                                    the_content();
                                                }
												
												?>

                                            </div>
										
											<?php echo apply_filters( 'madara_ads_after_content', madara_ads_position( 'ads_after_content', 'body-bottom-ads' ) ); ?>

                                        </div>


                                    </div>
                                </div>
								<?php if($manga_reading_load_next == 'off'){?>
								<div class="entry-header footer" id="manga-reading-nav-foot" data-position="footer" data-id="<?php echo esc_attr(get_the_ID());?>"><?php $wp_manga->manga_nav( 'footer' ); ?></div>
								<?php } ?>
                            </div>

                        </div>
                    </div>

					<div class="side-col">
						<h4>
							<span class="chapters-heading"><?php echo _x('Chapters', 'madara-child', 'Side Col Chapters List heading'); ?></span>
							<span class="comments-heading"><?php echo _x('Comments', 'madara-child', 'Side Col Comments heading'); ?></span>
							<div class="close-btn">							
								<button><i class="fa fa-times"></i></button>
							</div>
						</h4>
						<div class="side-col-inner">
							<div class="chapters-list">
								
								<?php 
									global $wp_manga_functions, $wp_manga_database;
			
									$sort_option = $wp_manga_database->get_sort_setting();
									
									$manga = $wp_manga_functions->get_all_chapters( $manga_id, $sort_option['sort'] );
									
									$current_read_chapter = 0;
									if ( is_user_logged_in() ) {
										$user_id = get_current_user_id();
										$history = madara_get_current_reading_chapter($user_id, $manga_id);
										if($history){
											$current_read_chapter = $history['c'];
										}
									}
									
									global $wp_manga_template;
									include $wp_manga_template->load_template('single/info','chapters', false);
								?>
							</div>

							<div class="comments">
								<!-- comments-area -->
								<?php 
								if($manga_reading_discussion == 'on') 
									do_action( 'wp_manga_discussion' ); ?>
								<!-- END comments-area -->
							</div>
						</div>
					</div>

                </div>
				<div class="sidebar-tools">
					<div class="sidebar-tools-inner">
						<div class="sidebar-tools-item">
							<button class="btn-toggle-chapters">
								<i class="fa fa-bars"></i>
							</button>
							<button class="btn-toggle-comments">
								<i class="fa fa-comments"></i>
							</button>
							<?php if($chapter_type == 'text'){ ?>
								<div class="reading-settings">
									<button type="button" class="adjust-fz open-reader-settings"><i class="fas fa-cog"></i></button>
									<div id="wp-manga-reader-settings">
										<div class="box-content">
											<div class="box-header">
												<button type="button" class="close" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="box-body">
												<section class="adj-fs">
													<?php $list_font = ['Times New Roman','Helvetica']; ?>
													<h6><?php echo esc_html__('Fonts', 'madara-child');?></h6>
													<ul class="theme-set-font">
														<?php foreach ($list_font as $i => $font){
																$font_family_style = ($font == 'Times New Roman' ? 'Times New Roman", Times, serif' : 'Helvetica, sans-serif')
																?>
																<li>
																	<input type="radio" value="<?=$font?>" id="<?=$font?>" name="<?=$font?>" />
																	<label for="<?=$font?>"><strong style="font-family: <?php echo $font_family_style ?> ">Aa</strong></label>
																</li>
														<?php }?>
													</ul>
												</section>

												<section class="adj-fz">
													<h6><?php echo esc_html__('Text size', 'madara-child');?></h6>
													<div class="slidecontainer">
														<span style="font-size:13px">A</span><input type="range" min="10" max="30" value="14" class="slider" id="fontRange"><span style="font-size:20px">A</span>
													</div>
												</section>

												<section class="adj-bg">
													<h6><?php echo esc_html__('Background', 'madara-child');?></h6>
													<span class="theme-set-color">

														<label class="_default" data-schema="default">
														<input name="tc" type="radio" value="_color1" id="col3" checked="checked">
														<strong>T</strong>
														</label>
														
														<label class="_yellow" data-schema="yellow">
														<input name="tc" type="radio" value="_color2" id="col2">
														<strong>T</strong>
														</label>

														<label class="_dark" data-schema="dark">
														<input name="tc" type="radio" value="_color3" id="col1">
														<strong>T</strong>
														</label>

													</span>
												</section>
											</div>
											<div class="box-footer">
												<button type="button" id="reset-reader-settings" class="reset-reader-settings"><i class="fas fa-undo"></i> Reset</button>
											</div>
										</div>
									</div>
								</div>
							<?php } ?>
							<?php if($manga_reading_social_share == 'on'){ ?>
								<!-- <button class="btn-toggle-share">
									<i class="fa fa-share-alt"></i>
								</button> -->
							<?php } ?>
						</div>
					</div>
				</div>
            </div>
        </div>
    </div>
<?php do_action( 'after_manga_single' ); ?>
<?php

	get_footer();
	echo '<style>
		.site-footer {
			display: none;
		}
	</style>';
