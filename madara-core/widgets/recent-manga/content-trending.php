<?php
	/**
	 * The Template for Manga Item layout 1 in WP Manga Recent Posts widget
	 *
	 * This template can be overridden by copying it to your-child-theme/madara-core/widgets/recent-manga/content-1.php
	 *
	 * HOWEVER, on occasion Madara will need to update template files and you
	 * (the theme developer) will need to copy the new files to your theme to
	 * maintain compatibility. We try to do this as little as possible, but it does
	 * happen. When this occurs the version of the template file will be bumped and
	 * we will list any important changes on our theme release logs.
	 * @package Madara
	 * @version 1.7.2.2
	 */

	$wp_manga_functions = madara_get_global_wp_manga_functions();
?>
<div class="trending-item-wrap">
	<?php if ( has_post_thumbnail() ) { ?>
		<div class="trending-img widget-thumbnail c-image-hover">
			<a title="<?php echo esc_attr( get_the_title() ); ?>" href="<?php echo esc_url( get_the_permalink() ); ?>">
				<?php
					echo madara_thumbnail( 'manga_wg_post_1' );
				?>
			</a>
		</div>
	<?php } ?>

	<div class="trending-content">
		<h5 class="widget-title">
			<a title="<?php echo esc_attr( get_the_title() ); ?>" href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
		</h5>

		<div class="trending-meta">
			<div class="genres">
				<?php 
					$genres = $wp_manga_functions->get_manga_genres( get_the_ID() );
					$genres_arr = explode( ', ', $genres );
						if ( $genres_arr ) {
							for ( $i = 0; $i < 3 ; $i++ ) {
							echo  '<span>' .$genres_arr[$i].' </span>';
							if ( $i !== 2 ) {
								echo '| ';
							}
						}
					}
				?>
			</div>
		</div>

	</div>
</div>