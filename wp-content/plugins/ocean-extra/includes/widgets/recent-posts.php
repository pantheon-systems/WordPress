<?php
/**
 * Recent Posts Widget.
 *
 * @package OceanWP WordPress theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ocean_Extra_Recent_Posts_Thumbnails_Widget' ) ) {
	class Ocean_Extra_Recent_Posts_Thumbnails_Widget extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct(
				'ocean_recent_posts',
				esc_html__( '&raquo; Recent Posts', 'ocean-extra' ),
				array(
					'classname'   => 'widget-oceanwp-recent-posts recent-posts-widget',
					'description' => esc_html__( 'Shows a listing of your recent or random posts.', 'ocean-extra' ),
					'customize_selective_refresh' => true,
				)
			);

			$this->defaults = array(
				'title'      => esc_html__( 'Recent Posts', 'ocean-extra' ),
				'number'     => '3',
				'post_type'  => 'post',
				'taxonomy'   => '',
				'terms'      => '',
				'order'      => 'DESC',
				'orderby'    => 'date',
			);
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 * @since 1.0.0
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {

			// Parse instance
			extract( wp_parse_args( $instance, $this->defaults ) );

			// Apply filters to the title
			$title = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';

			// Before widget WP hook
			echo $args['before_widget'];

				// Show widget title
				if ( $title ) {
					echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
				} ?>

				<ul class="oceanwp-recent-posts clr">

					<?php
					// Query args
					$query_args = array(
						'post_type'      => $post_type,
						'posts_per_page' => $number,
						'no_found_rows'  => true,
					);

					// Order params - needs FALLBACK don't ever edit!
					if ( ! empty( $orderby ) ) {
						$query_args['order']   = $order;
						$query_args['orderby'] = $orderby;
					} else {
						$query_args['orderby'] = $order; // THIS IS THE FALLBACK
					}

					// Taxonomy args
					if ( ! empty( $taxonomy ) && ! empty( $terms ) ) {

						// Sanitize terms and convert to array
						$terms = str_replace( ', ', ',', $terms );
						$terms = explode( ',', $terms );

						// Add to query arg
						$query_args['tax_query']  = array(
							array(
								'taxonomy' => $taxonomy,
								'field'    => 'slug',
								'terms'    => $terms,
							),
						);

					}

					// Exclude current post
					if ( is_singular() ) {
						$query_args['post__not_in'] = array( get_the_ID() );
					}

					// Query posts
					$oceanwp_query = new WP_Query( $query_args );

					if ( $oceanwp_query->have_posts() ) :

						while ( $oceanwp_query->have_posts() ) : $oceanwp_query->the_post(); ?>

							<li class="clr">

								<?php if ( has_post_thumbnail() ) { ?>
									<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="recent-posts-thumbnail">
										<?php
										// Display post thumbnail
										the_post_thumbnail( 'thumbnail', array(
											'alt'		=> get_the_title(),
											'itemprop' 	=> 'image',
										) ); ?>

										<span class="overlay"></span>
									</a>
								<?php } ?>

								<div class="recent-posts-details clr">

									<div class="recent-posts-details-inner clr">

										<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="recent-posts-title"><?php the_title(); ?></a>

										<div class="recent-posts-info clr">
											<div class="recent-posts-date"><?php echo get_the_date(); ?><span class="sep">/</span></div>
											<div class="recent-posts-comments"><a href="<?php comments_link(); ?>"><?php comments_number( esc_html__( '0 Comments', 'ocean-extra' ), esc_html__( '1 Comment',  'ocean-extra' ), esc_html__( '% Comments', 'ocean-extra' ) ); ?></a></div>
										</div>

									</div>

								</div>

							</li>

						<?php endwhile; ?>

					<?php else : ?>

						<p class="not-found">
							<?php esc_html_e('No posts found.', 'ocean-extra'); ?>
						</p>

					<?php endif; ?>

				</ul>

				<?php wp_reset_postdata(); ?>

			<?php
			// After widget WP hook
			echo $args['after_widget'];

		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 * @since 1.0.0
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance 				= $old_instance;
			$instance['title']      = ! empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
			$instance['post_type']  = ! empty( $new_instance['post_type'] ) ? strip_tags( $new_instance['post_type'] ) : '';
			$instance['taxonomy']   = ! empty( $new_instance['taxonomy'] ) ? strip_tags( $new_instance['taxonomy'] ) : '';
			$instance['terms']      = ! empty( $new_instance['terms'] ) ? strip_tags( $new_instance['terms'] ) : '';
			$instance['number']     = ! empty( $new_instance['number'] ) ? strip_tags( $new_instance['number'] ) : '';
			$instance['order']      = ! empty( $new_instance['order'] ) ? strip_tags( $new_instance['order'] ) : '';
			$instance['orderby']    = ! empty( $new_instance['orderby'] ) ? strip_tags( $new_instance['orderby'] ) : '';
			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 * @since 1.0.0
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form( $instance ) {

			extract( wp_parse_args( ( array ) $instance, $this->defaults ) ); ?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'ocean-extra' ); ?></label>
				<input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number', 'ocean-extra' ); ?></label>
				<input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" min="0" step="1" value="<?php echo esc_attr( $number ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'post_type' ) ); ?>"><?php esc_html_e( 'Post Type', 'ocean-extra' ); ?></label>
				<br />
				<select class='oceanwp-select' name="<?php echo esc_attr( $this->get_field_name( 'post_type' ) ); ?>" style="width:100%;">
					<option value="post" <?php selected( $post_type, 'post' ); ?>><?php esc_html_e( 'Post', 'ocean-extra' ); ?></option>
					<?php
					// Get Post Types
					$get_post_types = get_post_types( array(
						'public'   => true,
						'_builtin' => false,
					), 'objects', 'and' );
					foreach ( $get_post_types as $key => $val ) : ?>
						<?php if ( $key != 'post' ) { ?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $post_type, $key ); ?>><?php echo esc_html( $val->labels->name ); ?></option>
						<?php } ?>
					<?php endforeach; ?>
				</select>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'taxonomy' ) ); ?>"><?php esc_html_e( 'Query By Taxonomy', 'ocean-extra' ); ?></label>
				<br />
				<select class='oceanwp-select' name="<?php echo esc_attr( $this->get_field_name( 'taxonomy' ) ); ?>" style="width:100%;">
					<option value="" <?php if ( ! $taxonomy ) { ?>selected="selected"<?php } ?>><?php esc_html_e( 'No', 'ocean-extra' ); ?></option>
					<?php
					// Get Taxonomies
					$get_taxonomies = get_taxonomies( array(
						'public' => true,
					), 'objects' ); ?>
					<?php foreach ( $get_taxonomies as $get_taxonomy ) : ?>
						<option value="<?php echo esc_attr( $get_taxonomy->name ); ?>" <?php selected( $taxonomy, $get_taxonomy->name ); ?>><?php echo esc_html( ucfirst( $get_taxonomy->labels->singular_name ) ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'terms' ) ); ?>"><?php esc_html_e( 'Terms', 'ocean-extra' ); ?></label>
				<br />
				<input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'terms' ) ); ?>" type="text" value="<?php echo esc_attr( $terms ); ?>" />
				<small><?php esc_html_e( 'Enter the term slugs to query by seperated by a "comma"', 'ocean-extra' ); ?></small>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"><?php esc_html_e( 'Order', 'ocean-extra' ); ?></label>
				<br />
				<select class='oceanwp-select' name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>" style="width:100%;">
					<option value="DESC" <?php selected( $order, 'DESC', true ); ?>><?php esc_html_e( 'Descending', 'ocean-extra' ); ?></option>
					<option value="ASC" <?php selected( $order, 'ASC', true ); ?>><?php esc_html_e( 'Ascending', 'ocean-extra' ); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"><?php esc_html_e( 'Order By', 'ocean-extra' ); ?>:</label>
				<br />
				<select class='oceanwp-select' name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" style="width:100%;">
				<?php
				// Orderby options
				$orderby_array = array (
					'date'          => esc_html__( 'Date', 'ocean-extra' ),
					'title'         => esc_html__( 'Title', 'ocean-extra' ),
					'modified'      => esc_html__( 'Modified', 'ocean-extra' ),
					'author'        => esc_html__( 'Author', 'ocean-extra' ),
					'rand'          => esc_html__( 'Random', 'ocean-extra' ),
					'comment_count' => esc_html__( 'Comment Count', 'ocean-extra' ),
				);
				foreach ( $orderby_array as $key => $value ) { ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $orderby, $key ); ?>>
						<?php echo esc_attr( strip_tags( $value ) ); ?>
					</option>
				<?php } ?>
				</select>
			</p>

		<?php

		}

	}
}
register_widget( 'Ocean_Extra_Recent_Posts_Thumbnails_Widget' );