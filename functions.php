<?php
use \App\Madara;

	add_action( 'wp_enqueue_scripts', 'madara_scripts_styles_child_theme' );
	function madara_scripts_styles_child_theme() {
		$theme = wp_get_theme();
		wp_enqueue_style( 'madara-css', get_template_directory_uri() . '/style.css', 
		array(
			'bootstrap',
			'slick',
			'slick-theme'
		), 
			$theme->parent()->get('Version')
		);
		
		wp_enqueue_style( 'madara-css-child', get_stylesheet_directory_uri() . '/style.css', array(
			'madara-css'
		), '1.2' );

		wp_enqueue_script( 'jquery-visible', get_stylesheet_directory_uri() . '/js/jquery.visible.min.js', array( 'jquery' ), '1.0', true );

		wp_enqueue_script( 'madara-js-child-novelhub', get_stylesheet_directory_uri() . '/js/child-novelhub.js', array( 'jquery' ), '1.1.1', true );
		if( class_exists('WP_MANGA') && is_manga_reading_page() ){
			wp_enqueue_script('scroll-ajax', get_stylesheet_directory_uri() . '/js/scroll-ajax.js', array('jquery'), '1.2', true);
			wp_localize_script( 'scroll-ajax', 'madara_novelhub', array(
				'msg_last_chap' => esc_html__('You have reached the last chapter', 'madara-child')
			) );
		}
	}
	
	/* Disable VC auto-update */
	add_action( 'admin_init', 'madara_vc_disable_update', 9 );
	function madara_vc_disable_update() {
		if ( function_exists( 'vc_license' ) && function_exists( 'vc_updater' ) && ! vc_license()->isActivated() ) {

			remove_filter( 'upgrader_pre_download', array( vc_updater(), 'preUpgradeFilter' ), 10 );
			remove_filter( 'pre_set_site_transient_update_plugins', array(
				vc_updater()->updateManager(),
				'check_update'
			) );

		}
	}
    
	//add new thumbnail size
	add_filter( 'madara_thumb_config', 'add_new_thumb_sizes' );
	function add_new_thumb_sizes($availabe_sizes) {
		$availabe_sizes['madara_novelhub_square'] = [300, 300, true, 'Thumb 300x300px', esc_html__('This square thumb is used for NovelHub child theme', 'madara-child')];
		return $availabe_sizes;
	}

    /**
     * does not support Widgets Block Editor yet
     **/
    function madara_child_theme_support() {
        remove_theme_support( 'widgets-block-editor' );
		load_theme_textdomain( 'madara-child', get_stylesheet_directory_uri() . '/languages' );
    }
    add_action( 'after_setup_theme', 'madara_child_theme_support' );

	// Extra Shortcodes
	require 'shortcodes.php';

	function madara_novelhub_load_chapter_via_ajax() {
		$manga_id = $_POST['manga_id'];
		$chapter_id = $_POST['chapter_id'];
		global $wp_manga_functions, $wp_manga_chapter, $wp_manga;
		$chapter  = $wp_manga_chapter->get_chapter_by_id( $manga_id, $chapter_id );
		$chapter_type = get_post_meta( $manga_id, '_wp_manga_chapter_type', true );

		
		if ( !$chapter ) {
			wp_die();
		}

		$chapter_slug = $chapter['chapter_slug'];
	
		ob_start();

		if($chapter['storage_in_use']){
			global $__CURRENT_CHAPTER;
			$__CURRENT_CHAPTER = $chapter;
			
			if( !$post->post_password || ($post->post_password && !post_password_required()) ){
											
				/**
				 * If alternative_content is empty, show default content
				 **/
				$alternative_content = apply_filters('wp_manga_chapter_content_alternative', '');

				if(!$alternative_content){
					// Images Chapter
					$in_use = $chapter['storage']['inUse'];
				
					$alt_host = isset( $_GET['host'] ) ? $_GET['host'] : null;
					if ( $alt_host ) {
						$in_use = $alt_host;
					}
				
					$storage = $chapter['storage'];
					if ( ! isset( $storage[ $in_use ] ) || ! is_array( $storage[ $in_use ]['page'] ) ) {
						wp_die();
					}
				
					$madara_reading_list_total_item = 0;
					$need_button_fullsize = false;
				
					$lazyload_dfimg = apply_filters( 'madara_image_placeholder_url', get_parent_theme_file_uri( '/images/dflazy.jpg' ), 0 );
				
					foreach ( $chapter['storage'][ $in_use ]['page'] as $page => $link ) {
				
						$madara_reading_list_total_item = count( $chapter['storage'][ $in_use ]['page'] );
				
						$host = $chapter['storage'][ $in_use ]['host'];
						$src  = apply_filters( 'wp_manga_chapter_image_url', $host . $link['src'], $host, $link['src'], $manga_id, $chapter_slug );
						if ( $src != '' ) {
				
							do_action( 'madara_before_chapter_image_wrapper', $page, $madara_reading_list_total_item, $src ); ?>
				
							<div class="page-break">
								<?php do_action( 'madara_before_chapter_image', $page, $madara_reading_list_total_item ); ?>
								
								<img id="image-<?php echo esc_attr( $page ); ?>" src="<?php echo esc_url( $src ); ?>">
								
								<?php do_action( 'madara_after_chapter_image', $page, $madara_reading_list_total_item ); ?>
							</div>
				
							<?php do_action( 'madara_after_chapter_image_wrapper', $page, $madara_reading_list_total_item );
						}
					}
				
					$chapter_images = ob_get_clean();
					
					// Output the chapter images allowing HTML
					echo wp_kses_post( $chapter_images );
				} else {
					echo madara_filter_content($alternative_content);
				}
			}
		} else {
			// Get Chapter (text) Content
			$chapter_content = new WP_Query( array(
				'post_parent' => $chapter_id,
				'post_type'   => 'chapter_text_content'
			) );

			if ( $chapter_content->have_posts() ) {
				$posts = $chapter_content->posts;
		
				$post = $posts[0];
				
				global $__CURRENT_CHAPTER;
				$__CURRENT_CHAPTER = $chapter;
				
				if( !$post->post_password || ($post->post_password && !post_password_required()) ){
												
					/**
					 * If alternative_content is empty, show default content
					 **/
					$alternative_content = apply_filters('wp_manga_chapter_content_alternative', '');

					if(!$alternative_content){
						if ( $chapter_type == 'text' ) { ?>
		
							<?php do_action( 'madara_before_text_chapter_content' ); ?>
				
							<div class="text-left">
								<?php echo apply_filters('the_content', $post->post_content);
								?>
							</div>
				
							<?php do_action( 'madara_after_text_chapter_content' ); ?>
				
						<?php } elseif ( $chapter_type == 'video' ) { ?>
				
							<?php do_action( 'madara_before_video_chapter_content' ); ?>
				
							<div class="chapter-video-frame">
								<?php 
								$wp_manga->chapter_video_content($post, ''); ?>
							</div>
				
							<?php do_action( 'madara_after_video_chapter_content' ); ?>
				
						<?php }
					} else {
						echo madara_filter_content($alternative_content);
					}
				
				} else {
					// show the password form
					the_content();
				}
		
			}
		}
	
		wp_die();
	}
	
	// Hook the AJAX action
	add_action( 'wp_ajax_load_chapter', 'madara_novelhub_load_chapter_via_ajax' );
	add_action( 'wp_ajax_nopriv_load_chapter', 'madara_novelhub_load_chapter_via_ajax' );

	// register new options
	add_action( 'customize_register', 'madara_novelhub_customize_register' );
	function madara_novelhub_customize_register($wp_customize){
		$wp_customize->add_section( 'madara_novelhub' , array(
			'title'      => esc_html__( 'Child Theme Settings', 'madara-child' ),
			'priority'   => 50,
		) );

		$settings = array(
			'manga_reading_sticky_menu' => 'on',
			'manga_reading_load_prev' => 'off',
			'manga_reading_load_next' => 'on',
			'header_button_text' => '', 
			'header_button_url' => '',
			'manga_detail_bg_type' => 'color',
			'manga_detail_bg_color' => '',
		);

		$settings = array_merge($settings, madara_background_control_properties('chapter_reading_background'));

		$settings = array_merge($settings, array('chapter_reading_bg_color' => ''));

		foreach($settings as $setting => $def){
			$wp_customize->add_setting( $setting,
				array(
					'default' => $def,
					'transport' => 'refresh',
				)
			);
		}

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'manga_reading_load_prev',
				array(
					'label'          => esc_html__( 'Reading Page - Scroll Up To Load Previous Chapter', 'madara-child' ),
					'section'        => 'madara_novelhub',
					'settings'       => 'manga_reading_load_prev',
					'type'           => 'select',
					'choices'	=> array(
						'on' => esc_html__( 'On', 'madara-child' ),
						'off' => esc_html__( 'Off', 'madara-child' )
					),
				)
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'manga_reading_load_next',
				array(
					'label'          => esc_html__( 'Reading Page - Scroll Down To Load Next Chapter', 'madara-child' ),
					'section'        => 'madara_novelhub',
					'settings'       => 'manga_reading_load_next',
					'type'           => 'select',
					'choices'	=> array(
						'on' => esc_html__( 'On', 'madara-child' ),
						'off' => esc_html__( 'Off', 'madara-child' )
					),
				)
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'manga_reading_sticky_menu',
				array(
					'label'          => esc_html__( 'Reading Page - Sticky Reading Menu', 'madara-child' ),
					'section'        => 'madara_novelhub',
					'settings'       => 'manga_reading_sticky_menu',
					'type'           => 'select',
					'choices'	=> array(
						'on' => esc_html__( 'On', 'madara-child' ),
						'off' => esc_html__( 'Off', 'madara-child' )
					),
				)
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'header_button_text',
				array(
					'label'          => esc_html__( 'Header Button - Label', 'madara-child' ),
					'section'        => 'madara_novelhub',
					'settings'       => 'header_button_text',
					'type'           => 'text',
					'description' 	=> esc_html__( 'Label of the Header Button', 'madara-child' ),
				)
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'header_button_url',
				array(
					'label'          => esc_html__( 'Header Button - URL', 'madara-child' ),
					'section'        => 'madara_novelhub',
					'settings'       => 'header_button_url',
					'type'           => 'text',
					'description' => esc_html__( 'URL of the Header Button', 'madara-child' ),
				)
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'manga_detail_bg_type',
				array(
					'label'          => esc_html__( 'Manga Detail - Header Background Type', 'madara-child' ),
					'section'        => 'madara_novelhub',
					'settings'       => 'manga_detail_bg_type',
					'type'           => 'select',
					'choices'	=> array(
						'background_image' => esc_html__( 'Manga Single - Background Image', 'madara-child' ),
						'color' => esc_html__( 'Color', 'madara-child' )
					),
					'description' => esc_html__( 'Type of background in the Manga Detail page', 'madara-child' ),
				)
			)
		);

		$wp_customize->add_control( new Skyrocket_Customize_Alpha_Color_Control( $wp_customize, 'manga_detail_bg_color',
			array(
				'label' => __( 'Manga Detail - Header Background', 'madara-child' ),
				'description' => esc_html__( 'Main Color for Manga Detail - Header Background. Used when "Manga Detail - Header Background Type" is "color"', 'madara-child' ),
				'section' => 'madara_novelhub',
				'show_opacity' => true,
				'palette' => array(
					'#000',
					'#fff',
					'#df312c',
					'#df9a23',
					'#eef000',
					'#7ed934',
					'#1571c1',
					'#8309e7'
				)
			)
		) );


		madara_customizer_register_background_controls($wp_customize, 'chapter_reading_background', esc_html__('Chapter Reader - Header Background', 'madara-child'), 'madara_novelhub');
	}

add_action('wp_head', 'madara_novehub_custom_css');

function madara_novehub_custom_css(){
	$custom_css = '';

	$manga_detail_background_type = Madara::getOption('manga_detail_bg_type', 'color');
	
	if($manga_detail_background_type == 'color'){
		$color = Madara::getOption('manga_detail_bg_color', '');
		if(!$color) $color = 'rgba(70, 41, 51, .9)';

		$custom_css .= 'body.manga-page:not(.reading-manga) .profile-manga {
			background: linear-gradient(180deg, ' . $color . ' 0, #000 100%) !important;
		}';
	}

	$style = madara_output_background_options('chapter_reading_background');
	if($style){
		$custom_css .= '.text-ui-light.reading-manga .manga-info, .text-ui-dark.reading-manga .manga-info{' . $style . '}';
	}

	echo '<style type="text/css">' . $custom_css . '</style>';
}

require get_stylesheet_directory() . '/sample-data/sampledata.php';

require_once get_stylesheet_directory() . '/widgets/trending-manga-widget.php';

add_action('widgets_init', 'register_trending_manga_widget');

require 'admin/settings-page.php';
add_action( 'admin_enqueue_scripts', 'madara_child_novelhub_admin_init');
function madara_child_novelhub_admin_init(){
	$license_key = get_option(MADARA_CHILD_NOVELHUB_LICENSE_KEY);
	if ($license_key) {
		
	} else {
		$screen = get_current_screen();            
		if($screen && $screen->id != 'toplevel_page_madara-welcome' && $screen->id != 'themes' && $screen->id != 'settings_page_madara-child-novelhub'){
			// Redirect
			wp_redirect( admin_url( 'options-general.php?page=madara-child-novelhub' ));
		}
	}
}