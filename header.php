<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

	<?php
		/**
		 * The Header for our theme.
		 *
		 * Displays all of the <head> section and everything up till <div id="content">
		 *
		 * @package madara
		 */

		use App\Madara;

		$madara_header_style = apply_filters( 'madara_header_style', Madara::getOption( 'header_style', 1 ) );

	?>


	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php

if ( function_exists( 'wp_body_open' ) ) {
    wp_body_open();
} else {
    do_action( 'wp_body_open' );
}
?>

<?php if ( ! is_404() ) { ?>

<?php

	/**
	 * madara_before_body hook
	 *
	 * @hooked madara_before_body - 10
	 *
	 * @author
	 * @since 1.0
	 * @code     Madara
	 */
	do_action( 'madara_before_body' );
	
	$minimal_reading_page = Madara::getOption( 'minimal_reading_page', 'off' );
	$madara_ajax_search = Madara::getOption('madara_ajax_search', 'on');
?>

<div class="wrap">
    <div class="body-wrap">
		<?php if(!(function_exists('is_manga_reading_page') && is_manga_reading_page()) || $minimal_reading_page == 'off') {?>
        <header class="site-header">
            <div class="c-header__top">
                <div class="main-navigation <?php echo esc_attr( $madara_header_style == 3 ? 'style-2' : 'style-1'); ?> ">
                    <div class="container-fluid <?php echo esc_attr( $madara_header_style == '2' ? 'custom-width' : '' ); ?>">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="main-navigation_wrap">
                                    <div class="wrap-left">
                                        <div class="wrap_branding">
                                            <a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
                                                <?php $logo = Madara::getOption( 'logo_image', '' ) == '' ? esc_url( get_parent_theme_file_uri() ) . '/images/logo.png' : Madara::getOption( 'logo_image', '' );
                                                
                                                $size_str = "";
                                                $logo_image_size = Madara::getOption( 'logo_image_size', '' );
                                                if($logo_image_size){
                                                    $sizes = explode("x", $logo_image_size);
                                                    if(count($sizes) == 2){
                                                        $size_str = " width={$sizes[0]} height={$sizes[0]} ";
                                                    }
                                                }
                                                ?>
                                                <img class="img-responsive" src="<?php echo esc_url( $logo ); ?>" <?php echo esc_html($size_str);?> alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"/>
                                            </a>
                                        </div>
                                                
                                        <div class="main-menu">
                                            <?php get_template_part( 'html/header/main-nav' ); ?>
                                        </div>
                                        
                                    </div>

                                    <div class="wrap-right">
                                            <a href="<?php echo esc_url( home_url( '/?s=&post_type=wp-manga&post_type=wp-manga' ));?>" class="open-search-main-menu"> 
                                                <i class="icon ion-ios-search"></i>
                                                <span><?php esc_html_e('Search...', 'madara-child');?></span>
                                            </a>
                                            <?php
                                            $header_button_text = Madara::getOption('header_button_text', '');
                                            $header_button_url = Madara::getOption('header_button_url', '');
                                            if($header_button_text && $header_button_url){
                                            ?>
                                                <a href="<?php echo esc_url($header_button_url);?>" class="header-btn-1"><?php echo esc_html($header_button_text);?></a>
                                            <?php
                                            }
                                            $header_login_buttons = Madara::getOption('header_disable_login_buttons', 'on');
                                            $user_enabled = ($header_login_buttons == 'on') && ! is_user_logged_in() && get_option( 'users_can_register' );
                                            $user_manga_logged = is_user_logged_in() && class_exists( 'WP_MANGA' );

                                            if ( $user_enabled ) { ?>
                                                <div class="c-modal_item">
                                                    <!-- Button trigger modal -->
                                                    <a href="javascript:void(0)" data-toggle="modal" data-target="#form-login" class="btn-active-modal"><?php echo esc_html__( 'Sign in', 'madara-child' ); ?></a>
                                                    <a href="javascript:void(0)" data-toggle="modal" data-target="#form-sign-up" class="btn-active-modal"><?php echo esc_html__( 'Sign up', 'madara-child' ); ?></a>
                                                </div>
                                            <?php } elseif ( $user_manga_logged ) {
                                                global $wp_manga_user_actions;
                                                ?>
                                                <div class="c-modal_item">
                                                    <?php
                                                        if(defined('WP_MANGA_VER') && WP_MANGA_VER >= 1.6){
                                                            $wp_manga_user_actions->get_user_section( 50, true);
                                                        } else {
                                                            echo wp_kses_post($wp_manga_user_actions->get_user_section());
                                                        }
                                                    ?>
                                                </div>
                                            <?php }?>
                                    </div>

                                </div>

                                <div class="mobile-navigation-wrap">
                                    <div class="m-nav-top">
                                        <div class="wrap_branding">
                                            <a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
                                                <?php $logo = Madara::getOption( 'logo_image', '' ) == '' ? esc_url( get_parent_theme_file_uri() ) . '/images/logo.png' : Madara::getOption( 'logo_image', '' );
                                                
                                                $size_str = "";
                                                $logo_image_size = Madara::getOption( 'logo_image_size', '' );
                                                if($logo_image_size){
                                                    $sizes = explode("x", $logo_image_size);
                                                    if(count($sizes) == 2){
                                                        $size_str = " width={$sizes[0]} height={$sizes[0]} ";
                                                    }
                                                }
                                                ?>
                                                <img class="img-responsive" src="<?php echo esc_url( $logo ); ?>" <?php echo esc_html($size_str);?> alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"/>
                                            </a>
                                        </div>
                                        <div class="m-nav-top-right">
                                            <div class="m-nav-search">
                                                <a href="<?php echo esc_url( home_url( '/?s=&post_type=wp-manga' ));?>" class="open-search-main-menu"> 
                                                    <i class="icon ion-ios-search"></i>
                                                </a>
                                            </div>
                                            <div class="c-togle__menu">
                                                <button type="button" class="menu_icon__open">
                                                    <span></span> <span></span> <span></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

			<?php get_template_part( 'html/header/mobile-navigation' ); ?>

        </header>

		<?php get_template_part( 'html/main-top' ); ?>
		<?php } ?>
        <div class="site-content">
        <?php do_action('madara_before_body_content');?>
<?php }
