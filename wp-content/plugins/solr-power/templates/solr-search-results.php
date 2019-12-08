<?php
/**
 * Default search results template
 *
 * @package Solr_Power
 */

?>
<div id="solr-search-results">
	<header class="page-header">
		<h1 class="page-title">
			<?php esc_html_e( 'Search Results', 'solr-for-wordpress-on-pantheon' ); ?>
		</h1>
	</header><!-- .page-header -->
	<?php
	if ( $query->have_posts() ) :
		while ( $query->have_posts() ) :
			$query->the_post();
			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">
					<?php
					the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h1>' );
					if ( in_array( 'category', get_object_taxonomies( get_post_type() ) ) ) :
						?>
						<div class="entry-meta">
							<span class="cat-links"><?php echo get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'solr-for-wordpress-on-pantheon' ) ); ?></span>
						</div>
						<?php
					endif;
					?>
				</header><!-- .entry-header -->

				<div class="entry-summary">
					<?php the_excerpt(); ?>
				</div><!-- .entry-summary -->
			</article><!-- #post-## -->
		<?php endwhile; ?>
		<nav class="navigation paging-navigation" role="navigation">
			<h1 class="screen-reader-text"><?php esc_html_e( 'Posts navigation', 'solr-for-wordpress-on-pantheon' ); ?></h1>
			<div class="pagination loop-pagination">
				<?php
				$big = 999999999; // Need an unlikely integer.

				echo paginate_links(
					array(
						'base'               => str_replace( $big, '%#%', get_pagenum_link( $big, false ) ),
						'format'             => '?paged=%#%',
						'current'            => max( 1, $query->get( 'paged' ) ),
						'total'              => $query->max_num_pages,
						'prev_text'          => __( 'Previous page', 'solr-for-wordpress-on-pantheon' ),
						'next_text'          => __( 'Next page', 'solr-for-wordpress-on-pantheon' ),
						'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'solr-for-wordpress-on-pantheon' ) . ' </span>',

					)
				);
				wp_reset_postdata();
				?>
			</div>
		</nav>
	<?php else : ?>
		<article>
			<p><?php esc_html_e( 'Sorry, no posts matched your criteria.', 'solr-for-wordpress-on-pantheon' ); ?></p>
		</article>
	<?php endif; ?>
</div>
