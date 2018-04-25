<?php
/**
 * Class Strong_Testimonials_About
 *
 * @since 2.27.0
 */
class Strong_Testimonials_About {

	/**
	 * Strong_Testimonials_About constructor.
	 */
	public function __construct() {
        $this->add_actions();
    }

	/**
	 * Initialize.
	 */
	public function init() {
	}

	/**
	 * Add actions and filters.
	 */
	public function add_actions() {
		add_filter( 'wpmtst_submenu_pages', array( $this, 'add_submenu' ) );
	}

	/**
     * Add submenu page.
     *
	 * @param $pages
	 *
	 * @return mixed
	 */
	public function add_submenu( $pages ) {
		$pages[90] = $this->get_submenu();
		return $pages;
	}

	/**
	 * Return submenu page parameters.
	 *
	 * @return array
	 */
	public function get_submenu() {
		return array(
			'page_title' => __( 'About' ),
	        'menu_title' => __( 'About' ),
		    'capability' => 'strong_testimonials_about',
			'menu_slug'  => 'about-strong-testimonials',
			'function'   => array( $this, 'about_page' ),
		);
	}

	/**
	 * Print the About page.
	 */
	public function about_page() {
		$major_minor = strtok( WPMTST_VERSION, '.' ) . '.' . strtok( '.' );
		$active_tab  = isset( $_GET['tab'] ) ? $_GET['tab'] : 'about';
		$url         = admin_url( 'edit.php?post_type=wpm-testimonial&page=about-strong-testimonials' );
		?>
		<div class="wrap about-wrap">

			<h1><?php printf( __( 'Welcome to Strong Testimonials %s', 'strong-testimonials' ), $major_minor ); ?></h1>

			<p class="about-text">
                <?php _e( 'Thank you for updating to the latest version!' ); ?>
                <?php printf( 'Strong Testimonials %s offers improved view options.', $major_minor ); ?>
            </p>

			<div class="wp-badge strong-testimonials"><?php printf( __( 'Version %s' ), $major_minor ); ?></div>

			<h2 class="nav-tab-wrapper wp-clearfix">

				<a href="<?php echo $url; ?>" class="nav-tab <?php echo $active_tab == 'about' ? 'nav-tab-active' : ''; ?>"><?php _e( 'About' ); ?></a>

				<a href="<?php echo add_query_arg( 'tab', 'whats-new', $url ); ?>" class="nav-tab <?php echo $active_tab == 'whats-new' ? 'nav-tab-active' : ''; ?>"><?php _e( 'What&#8217;s New' ); ?></a>

				<a href="<?php echo add_query_arg( 'tab', 'how-to', $url ); ?>" class="nav-tab <?php echo $active_tab == 'how-to' ? 'nav-tab-active' : ''; ?>"><?php _e( 'How To', 'strong-testimonials' ); ?></a>

			</h2>

			<!--
			<div class="changelog point-releases">
			</div>
			-->

			<?php
			switch( $active_tab ) {
				case 'how-to':
					include WPMTST_ADMIN . 'about/how-to.php';
					break;
				case 'whats-new':
					include WPMTST_ADMIN . 'about/whats-new.php';
					break;
				default:
					include WPMTST_ADMIN. 'about/about.php';
			}

			include WPMTST_ADMIN. 'about/links.php';
			include WPMTST_ADMIN. 'about/addons.php';
			?>

		</div>
		<?php
	}


}

new Strong_Testimonials_About();
