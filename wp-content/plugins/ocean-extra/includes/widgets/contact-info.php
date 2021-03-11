<?php
/**
 * Contact Info Widget.
 *
 * @package OceanWP WordPress theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ocean_Extra_Contact_Info_Widget' ) ) {
	class Ocean_Extra_Contact_Info_Widget extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct(
				'ocean_contact_info',
				esc_html__( '&raquo; Contact Info', 'ocean-extra' ),
				array(
					'classname'   => 'widget-oceanwp-contact-info',
					'description' => esc_html__( 'Adds support for contact info.', 'ocean-extra' ),
					'customize_selective_refresh' => true,
				)
			);

			// Since 1.3.8
			add_action( 'admin_head-widgets.php', array( $this, 'social_widget_style' ) );
		}

		/**
		 * Custom widget style
		 *
		 * @since 1.3.8
		 *
		 * @param string $hook_suffix
		 */
		public function social_widget_style() { ?>
			<style>
				.oceanwp-infos { background: #fafafa; padding: 16px 10px; border: 1px solid #e5e5e5; margin-bottom: 10px; }
				.oceanwp-infos h2 { font-size: 16px; margin: 0 0 10px; }
				.oceanwp-infos p { margin: 0 0 8px; }
				.oceanwp-infos p:last-child { margin: 0; }
				.oceanwp-infos label { margin-bottom: 3px; display: block; color: #222; }
			</style>
		<?php
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

			$title      	= isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
			$style   		= isset( $instance['style'] ) ? $instance['style'] : 'default';
			$text   		= isset( $instance['text'] ) ? $instance['text'] : '';
			$target   		= isset( $instance['target'] ) ? $instance['target'] : 'self';
			$address_icon 	= isset( $instance['address_icon'] ) ? $instance['address_icon'] : '';
			$address_text 	= isset( $instance['address_text'] ) ? $instance['address_text'] : '';
			$address 		= isset( $instance['address'] ) ? $instance['address'] : '';
			$address_link 	= isset( $instance['address_link'] ) ? $instance['address_link'] : '';
			$phone_icon 	= isset( $instance['phone_icon'] ) ? $instance['phone_icon'] : '';
			$phone_text 	= isset( $instance['phone_text'] ) ? $instance['phone_text'] : '';
			$phone 			= isset( $instance['phone'] ) ? $instance['phone'] : '';
			$phone_link 	= isset( $instance['phone_link'] ) ? $instance['phone_link'] : '';
			$mobile_icon 	= isset( $instance['mobile_icon'] ) ? $instance['mobile_icon'] : '';
			$mobile_text 	= isset( $instance['mobile_text'] ) ? $instance['mobile_text'] : '';
			$mobile 		= isset( $instance['mobile'] ) ? $instance['mobile'] : '';
			$mobile_link 	= isset( $instance['mobile_link'] ) ? $instance['mobile_link'] : '';
			$fax_icon 		= isset( $instance['fax_icon'] ) ? $instance['fax_icon'] : '';
			$fax_text 		= isset( $instance['fax_text'] ) ? $instance['fax_text'] : '';
			$fax 			= isset( $instance['fax'] ) ? $instance['fax'] : '';
			$email_icon 	= isset( $instance['email_icon'] ) ? $instance['email_icon'] : '';
			$email_text 	= isset( $instance['email_text'] ) ? $instance['email_text'] : '';
			$email 			= isset( $instance['email'] ) ? $instance['email'] : '';
			$emailtxt 		= isset( $instance['emailtxt'] ) ? $instance['emailtxt'] : '';
			$web_icon 		= isset( $instance['web_icon'] ) ? $instance['web_icon'] : '';
			$web_text 		= isset( $instance['web_text'] ) ? $instance['web_text'] : '';
			$web 			= isset( $instance['web'] ) ? $instance['web'] : '';
			$webtxt 		= isset( $instance['webtxt'] ) ? $instance['webtxt'] : '';
			$skype 			= isset( $instance['skype'] ) ? $instance['skype'] : '';
			$skypetxt 		= isset( $instance['skypetxt'] ) ? $instance['skypetxt'] : '';

			// Before widget WP hook
			echo $args['before_widget'];

				// Show widget title
				if ( $title ) {
					echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
				}

				echo '<ul class="contact-info-widget '. esc_attr( $style ) .'">';
					if ( $text ) {
						echo '<li class="text">'. do_shortcode( $text ) .'</li>';
					}

					if ( $address ) {
						echo '<li class="address">';
							if ( 'no-icons' != $style ) {
								echo '<i class="'. esc_attr( $address_icon ) .'"></i>';
							}
							echo '<div class="oceanwp-info-wrap">';
								echo '<span class="oceanwp-contact-title">'. esc_html( $address_text ) .'</span>';
									if( ! empty($address_link) ) {
										echo '<a href="'. esc_url( $address_link ) .'" target="_'. esc_attr( $target ) .'">';
									}
									echo '<span class="oceanwp-contact-text">'. esc_html( $address ) .'</span>';
									if( ! empty($address_link) ) {
										echo '</a>';
									}
							echo '</div>';
						echo '</li>';
					}

					if ( $phone ) {
						echo '<li class="phone">';
							if ( 'no-icons' != $style ) {
								echo '<i class="'. esc_attr( $phone_icon ) .'"></i>';
							}
							echo '<div class="oceanwp-info-wrap">';
								echo '<span class="oceanwp-contact-title">'. esc_html( $phone_text ) .'</span>';
									if( ! empty($phone_link) ) {
										echo '<a href="tel:'. esc_attr( $phone_link ) .'">';
									}
									echo '<span class="oceanwp-contact-text">'. esc_html( $phone ) .'</span>';
									if( ! empty($phone_link) ) {
										echo '</a>';
									}
							echo '</div>';
						echo '</li>';
					}

					if ( $mobile ) {
						echo '<li class="mobile">';
							if ( 'no-icons' != $style ) {
								echo '<i class="'. esc_attr( $mobile_icon ) .'"></i>';
							}
							echo '<div class="oceanwp-info-wrap">';
								echo '<span class="oceanwp-contact-title">'. esc_html( $mobile_text ) .'</span>';
									if( ! empty($mobile_link) ) {
										echo '<a href="tel:'. esc_attr( $mobile_link ) .'">';
									}
									echo '<span class="oceanwp-contact-text">'. esc_html( $mobile ) .'</span>';
									if( ! empty($mobile_link) ) {
										echo '</a>';
									}
							echo '</div>';
						echo '</li>';
					}

					if ( $fax ) {
						echo '<li class="fax">';
							if ( 'no-icons' != $style ) {
								echo '<i class="'. esc_attr( $fax_icon ) .'"></i>';
							}
							echo '<div class="oceanwp-info-wrap">';
								echo '<span class="oceanwp-contact-title">'. esc_html( $fax_text ) .'</span>';
								echo '<span class="oceanwp-contact-text">'. esc_html( $fax ) .'</span>';
							echo '</div>';
						echo '</li>';
					}

					if ( $email ) {
						echo '<li class="email">';
							if ( 'no-icons' != $style ) {
								echo '<i class="'. esc_attr( $email_icon ) .'"></i>';
							}
							echo '<div class="oceanwp-info-wrap">';
								echo '<span class="oceanwp-contact-title">'. esc_html( $email_text ) .'</span>';
								echo '<span class="oceanwp-contact-text">';
									echo '<a href="mailto:'. esc_html( antispambot( $email ) ) .'">';
										if($emailtxt) {
											echo esc_html( $emailtxt );
										} else {
											echo antispambot( esc_attr( $email ) );
										}
									echo '</a>';
								echo '</span>';
							echo '</div>';
						echo '</li>';
					}

					if ( $web ) {
						echo '<li class="web">';
							if ( 'no-icons' != $style ) {
								echo '<i class="'. esc_attr( $web_icon ) .'"></i>';
							}
							echo '<div class="oceanwp-info-wrap">';
								echo '<span class="oceanwp-contact-title">'. esc_html( $web_text ) .'</span>';
								echo '<span class="oceanwp-contact-text">';
									echo '<a href="'. esc_url( $web ) .'" target="_'. esc_attr( $target ) .'">';
										if($webtxt) {
											echo esc_html( $webtxt );
										} else {
											echo esc_html( $web );
										}
									echo '</a>';
								echo '</span>';
							echo '</div>';
						echo '</li>';
					}

					if ( $skype ) {
						echo '<li class="skype">';
							echo '<a href="skype:'. esc_attr( $skype ) .'?call" target="_self" class="oceanwp-skype-button">';
								if($skypetxt) {
									echo esc_html( $skypetxt );
								} else {
									esc_html__('Skype', 'ocean-extra');
								}
							echo '</a>';
						echo '</li>';
					}
				echo '</ul>';

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
			$instance               	= $old_instance;
			$instance['title']      	= ! empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
			$instance['style']    		= ! empty( $new_instance['style'] ) ? $new_instance['style'] : '';
			$instance['text']    		= ! empty( $new_instance['text'] ) ? $new_instance['text'] : '';
			$instance['target']    		= ! empty( $new_instance['target'] ) ? $new_instance['target'] : '';
			$instance['address_icon']   = ! empty( $new_instance['address_icon'] ) ? strip_tags( $new_instance['address_icon'] ) : '';
			$instance['address_text']   = ! empty( $new_instance['address_text'] ) ? strip_tags( $new_instance['address_text'] ) : '';
			$instance['address']    	= ! empty( $new_instance['address'] ) ? strip_tags( $new_instance['address'] ) : '';
			$instance['address_link']   = ! empty( $new_instance['address_link'] ) ? esc_url( $new_instance['address_link'] ) : '';
			$instance['phone_icon']    	= ! empty( $new_instance['phone_icon'] ) ? strip_tags( $new_instance['phone_icon'] ) : '';
			$instance['phone_text']    	= ! empty( $new_instance['phone_text'] ) ? strip_tags( $new_instance['phone_text'] ) : '';
			$instance['phone']      	= ! empty( $new_instance['phone'] ) ? strip_tags( $new_instance['phone'] ) : '';
			$instance['phone_link']     = ! empty( $new_instance['phone_link'] ) ? strip_tags( $new_instance['phone_link'] ) : '';
			$instance['mobile_icon']    = ! empty( $new_instance['mobile_icon'] ) ? strip_tags( $new_instance['mobile_icon'] ) : '';
			$instance['mobile_text']    = ! empty( $new_instance['mobile_text'] ) ? strip_tags( $new_instance['mobile_text'] ) : '';
			$instance['mobile']     	= ! empty( $new_instance['mobile'] ) ? strip_tags( $new_instance['mobile'] ) : '';
			$instance['mobile_link']    = ! empty( $new_instance['mobile_link'] ) ? strip_tags( $new_instance['mobile_link'] ) : '';
			$instance['fax_icon']    	= ! empty( $new_instance['fax_icon'] ) ? strip_tags( $new_instance['fax_icon'] ) : '';
			$instance['fax_text']    	= ! empty( $new_instance['fax_text'] ) ? strip_tags( $new_instance['fax_text'] ) : '';
			$instance['fax']        	= ! empty( $new_instance['fax'] ) ? strip_tags( $new_instance['fax'] ) : '';
			$instance['email_icon']    	= ! empty( $new_instance['email_icon'] ) ? strip_tags( $new_instance['email_icon'] ) : '';
			$instance['email_text']    	= ! empty( $new_instance['email_text'] ) ? strip_tags( $new_instance['email_text'] ) : '';
			$instance['email']      	= ! empty( $new_instance['email'] ) ? strip_tags( $new_instance['email'] ) : '';
			$instance['emailtxt']   	= ! empty( $new_instance['emailtxt'] ) ? strip_tags( $new_instance['emailtxt'] ) : '';
			$instance['web_icon']    	= ! empty( $new_instance['web_icon'] ) ? strip_tags( $new_instance['web_icon'] ) : '';
			$instance['web_text']    	= ! empty( $new_instance['web_text'] ) ? strip_tags( $new_instance['web_text'] ) : '';
			$instance['web']        	= ! empty( $new_instance['web'] ) ? esc_url( $new_instance['web'] ) : '';
			$instance['webtxt']     	= ! empty( $new_instance['webtxt'] ) ? strip_tags( $new_instance['webtxt'] ) : '';
			$instance['skype']      	= ! empty( $new_instance['skype'] ) ? strip_tags( $new_instance['skype'] ) : '';
			$instance['skypetxt']   	= ! empty( $new_instance['skypetxt'] ) ? strip_tags( $new_instance['skypetxt'] ) : '';
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

			// Parse arguments
			$instance = wp_parse_args( (array) $instance, array(
				'title'         => esc_attr__( 'Contact Info', 'ocean-extra' ),
				'style' 		=> esc_attr__( 'Default', 'ocean-extra' ),
				'text' 			=> 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Pariatur, aspernatur, velit. Adipisci, animi, molestiae, neque voluptatum non voluptas atque aperiam.',
				'target'  		=> 'self',
				'address_icon'  => 'icon-location-pin',
				'address_text'  => esc_attr__( 'Address:', 'ocean-extra' ),
				'address'       => esc_attr__( 'Street Name, FL 54785', 'ocean-extra' ),
				'address_link'  => '',
				'phone_icon'  	=> 'icon-phone',
				'phone_text'  	=> esc_attr__( 'Phone:', 'ocean-extra' ),
				'phone' 		=> '621-254-2147',
				'phone_link' 	=> '',
				'mobile_icon'  	=> 'icon-screen-smartphone',
				'mobile_text'  	=> esc_attr__( 'Mobile:', 'ocean-extra' ),
				'mobile' 		=> '621-254-2147',
				'mobile_link'  	=> '',
				'fax_icon'  	=> 'icon-printer',
				'fax_text'  	=> esc_attr__( 'Fax:', 'ocean-extra' ),
				'fax' 			=> '621-254-2147',
				'email_icon'  	=> 'icon-envelope',
				'email_text'  	=> esc_attr__( 'Email:', 'ocean-extra' ),
				'email' 		=> 'contact@support.com',
				'emailtxt' 		=> 'contact@support.com',
				'web_icon'  	=> 'icon-link',
				'web_text'  	=> esc_attr__( 'Website:', 'ocean-extra' ),
				'web' 			=> '#',
				'webtxt' 		=> 'yourwebsite.com',
				'skype' 		=> 'YourUsername',
				'skypetxt' 		=> esc_html__( 'Skype Call Us', 'ocean-extra' ),
			) ); ?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'ocean-extra' ); ?>:</label>
				<input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>"><?php esc_html_e( 'Style', 'ocean-extra' ); ?></label>
				<select class='widefat' name="<?php echo $this->get_field_name( 'style' ); ?>" id="<?php echo $this->get_field_id( 'style' ); ?>">
					<option value="default" <?php if ( $instance['style'] == 'default') { ?>selected="selected"<?php } ?>><?php esc_html_e( 'Default', 'ocean-extra' ); ?></option>
					<option value="big-icons" <?php if ( $instance['style'] == 'big-icons') { ?>selected="selected"<?php } ?>><?php esc_html_e( 'Big Icons', 'ocean-extra' ); ?></option>
					<option value="no-icons" <?php if ( $instance['style'] == 'no-icons') { ?>selected="selected"<?php } ?>><?php esc_html_e( 'No Icons', 'ocean-extra' ); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php esc_html_e( 'Text', 'ocean-extra' ); ?></label>
				<textarea rows="15" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" class="widefat" style="height: 100px;"><?php if( !empty( $instance['text'] ) ) { echo esc_textarea( $instance['text'] ); } ?></textarea>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'target' ) ); ?>"><?php esc_html_e( 'Links Target', 'ocean-extra' ); ?></label>
				<select class='widefat' name="<?php echo $this->get_field_name( 'target' ); ?>" id="<?php echo $this->get_field_id( 'target' ); ?>">
					<option value="self" <?php if ( $instance['target'] == 'self') { ?>selected="selected"<?php } ?>><?php esc_html_e( 'Self', 'ocean-extra' ); ?></option>
					<option value="blank" <?php if ( $instance['target'] == 'blank') { ?>selected="selected"<?php } ?>><?php esc_html_e( 'Blank', 'ocean-extra' ); ?></option>
				</select>
			</p>

			<div class="oceanwp-infos">
				<h2><?php esc_html_e('Address:', 'ocean-extra'); ?></h2>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('address_icon') ); ?>"><?php esc_html_e('Icon Class', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('address_icon') ); ?>" name="<?php echo esc_attr( $this->get_field_name('address_icon') ); ?>" value="<?php echo esc_attr( $instance['address_icon'] ); ?>" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('address_text') ); ?>"><?php esc_html_e('Title', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('address_text') ); ?>" name="<?php echo esc_attr( $this->get_field_name('address_text') ); ?>" value="<?php echo esc_attr( $instance['address_text'] ); ?>" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('address') ); ?>"><?php esc_html_e('Content', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('address') ); ?>" name="<?php echo esc_attr( $this->get_field_name('address') ); ?>" value="<?php echo esc_attr( $instance['address'] ); ?>" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('address_link') ); ?>"><?php esc_html_e('Link (optional)', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('address_link') ); ?>" name="<?php echo esc_attr( $this->get_field_name('address_link') ); ?>" value="<?php echo esc_attr( $instance['address_link'] ); ?>" />
				</p>
			</div>

			<div class="oceanwp-infos">
				<h2><?php esc_html_e('Phone:', 'ocean-extra'); ?></h2>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('phone_icon') ); ?>"><?php esc_html_e('Icon Class', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('phone_icon') ); ?>" name="<?php echo esc_attr( $this->get_field_name('phone_icon') ); ?>" value="<?php echo esc_attr( $instance['phone_icon'] ); ?>" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('phone_text') ); ?>"><?php esc_html_e('Title', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('phone_text') ); ?>" name="<?php echo esc_attr( $this->get_field_name('phone_text') ); ?>" value="<?php echo esc_attr( $instance['phone_text'] ); ?>" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('phone') ); ?>"><?php esc_html_e('Content', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('phone') ); ?>" name="<?php echo esc_attr( $this->get_field_name('phone') ); ?>" value="<?php echo esc_attr( $instance['phone'] ); ?>" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('phone_link') ); ?>"><?php esc_html_e('Link (optional)', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('phone_link') ); ?>" name="<?php echo esc_attr( $this->get_field_name('phone_link') ); ?>" value="<?php echo esc_attr( $instance['phone_link'] ); ?>" />
				</p>
			</div>

			<div class="oceanwp-infos">
				<h2><?php esc_html_e('Mobile:', 'ocean-extra'); ?></h2>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('mobile_icon') ); ?>"><?php esc_html_e('Icon Class', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('mobile_icon') ); ?>" name="<?php echo esc_attr( $this->get_field_name('mobile_icon') ); ?>" value="<?php echo esc_attr( $instance['mobile_icon'] ); ?>" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('mobile_text') ); ?>"><?php esc_html_e('Title', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('mobile_text') ); ?>" name="<?php echo esc_attr( $this->get_field_name('mobile_text') ); ?>" value="<?php echo esc_attr( $instance['mobile_text'] ); ?>" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('mobile') ); ?>"><?php esc_html_e('Content', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('mobile') ); ?>" name="<?php echo esc_attr( $this->get_field_name('mobile') ); ?>" value="<?php echo esc_attr( $instance['mobile'] ); ?>" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('mobile_link') ); ?>"><?php esc_html_e('Link (optional)', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('mobile_link') ); ?>" name="<?php echo esc_attr( $this->get_field_name('mobile_link') ); ?>" value="<?php echo esc_attr( $instance['mobile_link'] ); ?>" />
				</p>
			</div>

			<div class="oceanwp-infos">
				<h2><?php esc_html_e('Fax:', 'ocean-extra'); ?></h2>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('fax_icon') ); ?>"><?php esc_html_e('Icon Class', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('fax_icon') ); ?>" name="<?php echo esc_attr( $this->get_field_name('fax_icon') ); ?>" value="<?php echo esc_attr( $instance['fax_icon'] ); ?>" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('fax_text') ); ?>"><?php esc_html_e('Title', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('fax_text') ); ?>" name="<?php echo esc_attr( $this->get_field_name('fax_text') ); ?>" value="<?php echo esc_attr( $instance['fax_text'] ); ?>" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('fax') ); ?>"><?php esc_html_e('Content', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('fax') ); ?>" name="<?php echo esc_attr( $this->get_field_name('fax') ); ?>" value="<?php echo esc_attr( $instance['fax'] ); ?>" />
				</p>
			</div>

			<div class="oceanwp-infos">
				<h2><?php esc_html_e('Email:', 'ocean-extra'); ?></h2>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('email_icon') ); ?>"><?php esc_html_e('Icon Class', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('email_icon') ); ?>" name="<?php echo esc_attr( $this->get_field_name('email_icon') ); ?>" value="<?php echo esc_attr( $instance['email_icon'] ); ?>" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('email_text') ); ?>"><?php esc_html_e('Title', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('email_text') ); ?>" name="<?php echo esc_attr( $this->get_field_name('email_text') ); ?>" value="<?php echo esc_attr( $instance['email_text'] ); ?>" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('email') ); ?>"><?php esc_html_e('URL', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('email') ); ?>" name="<?php echo esc_attr( $this->get_field_name('email') ); ?>" value="<?php echo esc_attr( $instance['email'] ); ?>" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('emailtxt') ); ?>"><?php esc_html_e('URL Text', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('emailtxt') ); ?>" name="<?php echo esc_attr( $this->get_field_name('emailtxt') ); ?>" value="<?php echo esc_attr( $instance['emailtxt'] ); ?>" />
				</p>
			</div>

			<div class="oceanwp-infos">
				<h2><?php esc_html_e('Website:', 'ocean-extra'); ?></h2>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('web_icon') ); ?>"><?php esc_html_e('Icon Class', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('web_icon') ); ?>" name="<?php echo esc_attr( $this->get_field_name('web_icon') ); ?>" value="<?php echo esc_attr( $instance['web_icon'] ); ?>" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('web_text') ); ?>"><?php esc_html_e('Title', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('web_text') ); ?>" name="<?php echo esc_attr( $this->get_field_name('web_text') ); ?>" value="<?php echo esc_attr( $instance['web_text'] ); ?>" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('web') ); ?>"><?php esc_html_e('URL', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('web') ); ?>" name="<?php echo esc_attr( $this->get_field_name('web') ); ?>" value="<?php echo esc_attr( $instance['web'] ); ?>" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('webtxt') ); ?>"><?php esc_html_e('URL Text', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('webtxt') ); ?>" name="<?php echo esc_attr( $this->get_field_name('webtxt') ); ?>" value="<?php echo esc_attr( $instance['webtxt'] ); ?>" />
				</p>
			</div>

			<div class="oceanwp-infos">
				<h2><?php esc_html_e('Skype:', 'ocean-extra'); ?></h2>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('skype') ); ?>"><?php esc_html_e('Username', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('skype') ); ?>" name="<?php echo esc_attr( $this->get_field_name('skype') ); ?>" value="<?php echo esc_attr( $instance['skype'] ); ?>" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('skypetxt') ); ?>"><?php esc_html_e('Text', 'ocean-extra'); ?></label>
					<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('skypetxt') ); ?>" name="<?php echo esc_attr( $this->get_field_name('skypetxt') ); ?>" value="<?php echo esc_attr( $instance['skypetxt'] ); ?>" />
				</p>
			</div>

		<?php

		}

	}
}
register_widget( 'Ocean_Extra_Contact_Info_Widget' );