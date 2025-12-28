<?php
    /**
	 * The Template for Manga Related section in Manga Detail page
	 *
	 * This template can be overridden by copying it to your-child-theme/madara-core/manga-related.php
	 *
	 * HOWEVER, on occasion Madara will need to update template files and you
	 * (the theme developer) will need to copy the new files to your theme to
	 * maintain compatibility. We try to do this as little as possible, but it does
	 * happen. When this occurs the version of the template file will be bumped and
	 * we will list any important changes on our theme release logs.
	 * @package Madara
	 * @version 1.7.2.2
	 */
	 
	 use App\Madara;

	$wp_manga_settings = get_option( 'wp_manga_settings' );
	$related_by        = $wp_manga_settings['related_by'];
    
    $manga_related_layout = Madara::getOption('manga_single_related_items_layout', 1);
    
    $mobile_col = (Madara::getOption('manga_single_related_item_mobile_width', '100') == 50 ? '6' : '12');
    
	$thumb_size        = array( 75, 106 ); // default
    if( $manga_related_layout == 2 ){
        $thumb_size         = 'madara_manga_big_thumb'; // big thumbnail
		if($mobile_col == 12){
			$thumb_size = 'madara_manga_big_thumb_full';
		}
    }
    
    $related_items_count = Madara::getOption('manga_single_related_items_count', 4);
	
	if ( $related_by ) {
		$post_id = get_the_ID();

		$related_args = array(
			'post_type'      => 'wp-manga',
			'post_status'    => 'publish',
			'orderby' => 'rand',
			'posts_per_page' => $related_items_count,
			'post__not_in'   => array( $post_id ),
		);

		switch ( $related_by ) {
			case 'related_author':
				$author_terms = wp_get_post_terms( $post_id, 'wp-manga-author' );

				$manga_author = array();

				if ( ! empty( $author_terms ) ) {
					foreach ( $author_terms as $author ) {
						$manga_author[] = $author->term_id;
					}
				}

				$related_args['tax_query'] = array(
					array(
						'taxonomy' => 'wp-manga-author',
						'field'    => 'term_id',
						'terms'    => $manga_author
					)
				);
				break;
			case 'related_year':
				$year_terms = wp_get_post_terms( $post_id, 'wp-manga-release' );

				$manga_year = array();

				if ( ! empty( $year_terms ) ) {
					foreach ( $year_terms as $year ) {
						$manga_year[] = $year->term_id;
					}
				}

				$related_args['tax_query'] = array(
					array(
						'taxonomy' => 'wp-manga-release',
						'field'    => 'term_id',
						'terms'    => $manga_year
					)
				);
				break;
			case 'related_artist' :
				$artists_terms = wp_get_post_terms( $post_id, 'wp-manga-artist' );

				$manga_artists = array();

				if ( ! empty( $artists_terms ) ) {
					foreach ( $artists_terms as $artists ) {
						$manga_artists[] = $artists->term_id;
					}
				}

				$related_args['tax_query'] = array(
					array(
						'taxonomy' => 'wp-manga-artist',
						'field'    => 'term_id',
						'terms'    => $manga_artists
					)
				);
				break;
			case 'related_genre' :

				$genre_terms = wp_get_post_terms( $post_id, 'wp-manga-genre' );

				$genre_id = array();

				if ( ! empty( $genre_terms ) ) {
					foreach ( $genre_terms as $term ) {
						$genre_id[] = $term->term_id;
					}
				}

				$related_args['tax_query'] = array(
					array(
						'taxonomy' => 'wp-manga-genre',
						'field'    => 'term_id',
						'terms'    => $genre_id
					)
				);
				break;
		}

		$related_query = new WP_Query( $related_args );

		if ( $related_query->have_posts() ) {
			?>

            <div class="related-manga">
				<h4>
					<?php esc_html_e( 'You may also like', 'madara-child' ) ?>
				</h4>

				<div class="related-content">
					<?php
						$col_width = 12 / $related_items_count;
						$index = 1;
						while ( $related_query->have_posts() ) {
							$related_query->the_post();
							$index ++;
							set_query_var( 'madara_loop_index', $index );
							set_query_var('manga_archives_item_layout', 'default');
							$date = get_post_meta( get_the_ID(), '_latest_update', true );
							get_template_part( 'madara-core/content/content', 'archive' );
						}

					?>
				</div>
            </div>

			<?php
		}

		wp_reset_postdata();
	}
