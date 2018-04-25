<?php

/**
 * Class Strong_Testimonials_Settings_Compat
 *
 * @since 2.28.0
 */
class Strong_Testimonials_Settings_Compat {

	const TAB_NAME = 'compat';

	const OPTION_NAME = 'wpmtst_compat_options';

	const GROUP_NAME = 'wpmtst-compat-group';

	var $options;

	/**
	 * Strong_Testimonials_Settings_Compat constructor.
	 */
	public function __construct() {
		$this->options = get_option( self::OPTION_NAME );
		$this->add_actions();
	}

	/**
	 * Add actions and filters.
	 */
	public function add_actions() {
		add_action( 'wpmtst_register_settings', array( $this, 'register_settings' ) );
		add_action( 'wpmtst_settings_tabs', array( $this, 'register_tab' ), 3, 2 );
		add_filter( 'wpmtst_settings_callbacks', array( $this, 'register_settings_page' ) );
	}

	/**
	 * Register settings tab.
	 *
	 * @param $active_tab
	 * @param $url
	 */
	public function register_tab( $active_tab, $url ) {
		printf( '<a href="%s" class="nav-tab %s">%s</a>',
		        esc_url( add_query_arg( 'tab', self::TAB_NAME, $url ) ),
		        esc_attr( $active_tab == self::TAB_NAME ? 'nav-tab-active' : '' ),
		        __( 'Compatibility', 'strong-testimonials' )
		);
	}

	/**
	 * Register settings.
	 */
	public function register_settings() {
		register_setting( self::GROUP_NAME, self::OPTION_NAME, array( $this, 'sanitize_options' ) );
	}

	/**
	 * Register settings page.
	 *
	 * @param $pages
	 *
	 * @return mixed
	 */
	public function register_settings_page( $pages ) {
		$pages[ self::TAB_NAME ] = array( $this, 'settings_page' );

		return $pages;
	}

	/**
	 * Sanitize settings.
	 *
	 * @param $input
	 *
	 * @return array
	 */
	public function sanitize_options( $input ) {
		$input['page_loading']            = sanitize_text_field( $input['page_loading'] );
		if ( 'general' == $input['page_loading'] ) {
			$input['prerender']      = 'all';
			$input['ajax']['method'] = 'universal';
		} else {
			$input['prerender']      = sanitize_text_field( $input['prerender'] );
			$input['ajax']['method'] = sanitize_text_field( $input['ajax']['method'] );
		}
		$input['ajax']['universal_timer'] = floatval( sanitize_text_field( $input['ajax']['universal_timer'] ) );
		$input['ajax']['observer_timer']  = floatval( sanitize_text_field( $input['ajax']['observer_timer'] ) );
		$input['ajax']['container_id']    = sanitize_text_field( $input['ajax']['container_id'] );
		$input['ajax']['addednode_id']    = sanitize_text_field( $input['ajax']['addednode_id'] );
		$input['ajax']['event']           = sanitize_text_field( $input['ajax']['event'] );
		$input['ajax']['script']          = sanitize_text_field( $input['ajax']['script'] );

		return $input;
	}

	/**
	 * Print settings page.
	 */
	public function settings_page() {
		settings_fields( self::GROUP_NAME );
		$this->settings_top();
	}

	/**
	 * Compatibility settings
	 */
	public function settings_top() {
		$this->settings_intro();
		$this->settings_page_loading();
		$this->settings_prerender();
		$this->settings_monitor();
	}

	/**
	 * Settings intro
	 */
	public function settings_intro() {
		?>
        <h2><?php _e( 'Common Scenarios' ); ?></h2>
        <table class="form-table" cellpadding="0" cellspacing="0">
            <tr valign="top">
                <td>

                    <div class="scenarios">
                        <div class="row header">
                            <div>
								<?php _e( 'Views Not Working', 'strong-testimonials' ); ?>
                            </div>
                            <div>
								<?php _e( 'Possible Cause', 'strong-testimonials' ); ?>
                            </div>
                            <div>
								<?php _e( 'Solution', 'strong-testimonials' ); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div>
                                <p><?php _e( 'A testimonial view does not look right the first time you view the page.', 'strong-testimonials' ); ?></p>
                                <p><?php _e( 'For example, it does not seem to have any style, the slideshow has not started, or the pagination is missing.', 'strong-testimonials' ); ?></p>
                                <p><?php _e( 'When you refresh the page, the view does appear correctly.', 'strong-testimonials' ); ?></p>
                            </div>
                            <div>
                                <p><?php _e( 'Your site is using <strong>Ajax page loading</strong> &ndash; also known as page animations, transition effects or Pjax (pushState Ajax) &ndash; provided by your theme or another plugin.', 'strong-testimonials' ); ?></p>
                                <p><?php _e( 'Instead of loading the entire page, this technique fetches only the new content.', 'strong-testimonials' ); ?></p>
                            </div>
                            <div>
                                <p><strong><?php _e( 'Ajax Page Loading', 'strong-testimonials' ); ?>
                                        :</strong> <?php _e( 'General', 'strong-testimonials' ); ?></p>
                                <p><a href="#"
                                      id="set-scenario-1"><?php _ex( 'Set this now', 'link text on Settings > Compatibility tab', 'strong-testimonials' ); ?></a>
                                </p>
                            </div>
                        </div>
                    </div>

                </td>
            </tr>
        </table>
		<?php
	}

	/**
	 * Page Loading
	 */
	public function settings_page_loading() {
		?>
        <h2><?php _e( 'Ajax Page Loading' ); ?></h2>

        <table class="form-table" cellpadding="0" cellspacing="0">
            <tr valign="top">
                <th scope="row">
					<?php _e( 'Type', 'strong-testimonials' ); ?>
                </th>
                <td>
                    <div class="row header">
                        <p>
							<?php _e( 'This does not perform Ajax page loading.', 'strong-testimonials' ); ?>
							<?php _e( 'It provides compatibility with themes and plugins that use Ajax to load pages, also known as page animation or transition effects.', 'strong-testimonials' ); ?>
                        </p>
                    </div>
                    <fieldset data-radio-group="prerender">
						<?php $this->settings_page_loading_none(); ?>
						<?php $this->settings_page_loading_general(); ?>
						<?php $this->settings_page_loading_advanced(); ?>
                    </fieldset>
                </td>
            </tr>
        </table>
		<?php
	}

	/**
	 * None (default)
	 */
	public function settings_page_loading_none() {
		?>
        <div class="row">
            <div>
                <label for="page-loading-none">
                    <input type="radio" id="page-loading-none" name="wpmtst_compat_options[page_loading]"
                           value="" <?php checked( $this->options['page_loading'], '' ); ?>/>
					<?php _e( 'None', 'strong-testimonials' ); ?>
                    <em><?php _e( '(default)', 'strong-testimonials' ); ?></em>
                </label>
            </div>
            <div>
                <p class="about"><?php _e( 'No compatibility needed.', 'strong-testimonials' ); ?></p>
                <p class="about"><?php _e( 'This works well for most themes.', 'strong-testimonials' ); ?></p>
            </div>
        </div>
		<?php
	}

	/**
	 * General
	 */
	public function settings_page_loading_general() {
		?>
        <div class="row">
            <div>
                <label for="page-loading-general">
                    <input type="radio" id="page-loading-general" name="wpmtst_compat_options[page_loading]"
                           value="general" <?php checked( $this->options['page_loading'], 'general' ); ?>/>
					<?php _e( 'General', 'strong-testimonials' ); ?>
                </label>
            </div>
            <div>
                <p class="about"><?php _e( 'Be ready to render any view at any time.', 'strong-testimonials' ); ?></p>
                <p class="about"><?php _e( 'This works well with common Ajax methods.', 'strong-testimonials' ); ?></p>
            </div>
        </div>
		<?php
	}

	/**
	 * Advanced
	 */
	public function settings_page_loading_advanced() {
		?>
        <div class="row">
            <div>
                <label for="page-loading-advanced">
                    <input type="radio" id="page-loading-advanced" name="wpmtst_compat_options[page_loading]"
                           value="advanced" <?php checked( $this->options['page_loading'], 'advanced' ); ?>
                           data-group="advanced"/>
					<?php _e( 'Advanced', 'strong-testimonials' ); ?>
                </label>
            </div>
            <div>
                <p class="about"><?php _e( 'For specific configurations.', 'strong-testimonials' ); ?></p>
            </div>
        </div>
		<?php
	}

	/**
	 * Prerender
	 */
	public function settings_prerender() {
		?>
        <table class="form-table" cellpadding="0" cellspacing="0" data-sub="advanced">
            <tr valign="top">
                <th scope="row">
					<?php _e( 'Prerender', 'strong-testimonials' ); ?>
                </th>
                <td>
                    <div class="row header">
                        <p><?php _e( 'Load stylesheets and populate script variables up front.', 'strong-testimonials' ); ?>
                            <a class="open-help-tab" href="#tab-panel-wpmtst-help-prerender"><?php _e( 'Help' ); ?></a>
                        </p>
                    </div>
                    <fieldset data-radio-group="prerender">
						<?php $this->settings_prerender_current(); ?>
						<?php $this->settings_prerender_all(); ?>
						<?php $this->settings_prerender_none(); ?>
                    </fieldset>
                </td>
            </tr>
        </table>
		<?php
	}

	/**
	 * Current (default)
	 */
	public function settings_prerender_current() {
		?>
        <div class="row">
            <div>
                <label for="prerender-current">
                    <input type="radio" id="prerender-current" name="wpmtst_compat_options[prerender]"
                           value="current" <?php checked( $this->options['prerender'], 'current' ); ?>/>
					<?php _e( 'Current page', 'strong-testimonials' ); ?>
                    <em><?php _e( '(default)', 'strong-testimonials' ); ?></em>
                </label>
            </div>
            <div>
                <p class="about"><?php _e( 'For the current page only.', 'strong-testimonials' ); ?></p>
                <p class="about"><?php _e( 'This works well for most themes.', 'strong-testimonials' ); ?></p>
            </div>
        </div>
		<?php
	}

	/**
	 * All
	 */
	public function settings_prerender_all() {
		?>
        <div class="row">
            <div>
                <label for="prerender-all">
                    <input type="radio" id="prerender-all" name="wpmtst_compat_options[prerender]"
                           value="all" <?php checked( $this->options['prerender'], 'all' ); ?>/>
					<?php _e( 'All views', 'strong-testimonials' ); ?>
                </label>
            </div>
            <div>
                <p class="about"><?php _e( 'For all views. Required for Ajax page loading.', 'strong-testimonials' ); ?></p>
                <p class="about"><?php _e( 'Then select an option for <strong>Monitor</strong> below.', 'strong-testimonials' ); ?></p>
            </div>
        </div>
		<?php
	}

	/**
	 * None
	 */
	public function settings_prerender_none() {
		?>
        <div class="row">
            <div>
                <label for="prerender-none">
                    <input type="radio" id="prerender-none" name="wpmtst_compat_options[prerender]"
                           value="none" <?php checked( $this->options['prerender'], 'none' ); ?>/>
					<?php _e( 'None', 'strong-testimonials' ); ?>
                </label>
            </div>
            <div>
                <p class="about"><?php _e( 'When the shortcode is rendered. May result in a flash of unstyled content.', 'strong-testimonials' ); ?></p>
            </div>
        </div>
		<?php
	}

	/**
	 * Monitor
	 */
	public function settings_monitor() {
		?>
        <table class="form-table" cellpadding="0" cellspacing="0" data-sub="advanced">
            <tr valign="top">
                <th scope="row">
					<?php _e( 'Monitor', 'strong-testimonials' ); ?>
                </th>
                <td>
                    <div class="row header">
                        <p><?php _e( 'Initialize slideshows, pagination and form validation as pages change.', 'strong-testimonials' ); ?></p>
                    </div>
                    <fieldset data-radio-group="method">
						<?php $this->settings_monitor_none(); ?>
						<?php $this->settings_monitor_universal(); ?>
						<?php $this->settings_monitor_observer(); ?>
						<?php $this->settings_monitor_event(); ?>
						<?php $this->settings_monitor_script(); ?>
                    </fieldset>
                </td>
            </tr>
        </table>
		<?php
	}

	/**
	 * None
	 */
	public function settings_monitor_none() {
		?>
        <div class="row">
            <div>
                <label for="method-none">
                    <input type="radio" id="method-none" name="wpmtst_compat_options[ajax][method]" value=""
						<?php checked( $this->options['ajax']['method'], '' ); ?> />
					<?php _e( 'None', 'strong-testimonials' ); ?>
                    <em><?php _e( '(default)', 'strong-testimonials' ); ?></em>
                </label>
            </div>
            <div>
                <p class="about"><?php _e( 'No compatibility needed.', 'strong-testimonials' ); ?></p>
            </div>
        </div>
		<?php
	}

	/**
	 * Universal (timer)
	 */
	public function settings_monitor_universal() {
		?>
        <div class="row">
            <div>
                <label for="method-universal">
                    <input type="radio"
                           id="method-universal"
                           name="wpmtst_compat_options[ajax][method]"
                           value="universal"
						<?php checked( $this->options['ajax']['method'], 'universal' ); ?>
                           data-group="universal"/>
					<?php _e( 'Universal', 'strong-testimonials' ); ?>
                </label>
            </div>
            <div>
                <p class="about"><?php _e( 'Watch for page changes on a timer.', 'strong-testimonials' ); ?></p>
            </div>
        </div>

        <div class="row" data-sub="universal">
            <div class="radio-sub">
                <label for="universal-timer">
					<?php _ex( 'Check every', 'timer setting', 'strong-testimonials' ); ?>
                </label>
            </div>
            <div>
                <input type="number" id="universal-timer" name="wpmtst_compat_options[ajax][universal_timer]"
                       min=".1" max="5" step=".1" size="3"
                       value="<?php echo $this->options['ajax']['universal_timer']; ?>"/>
				<?php _ex( 'seconds', 'timer setting', 'strong-testimonials' ); ?>
            </div>
        </div>
		<?php
	}

	/**
	 * Observer
	 */
	public function settings_monitor_observer() {
		?>
        <div class="row">
            <div>
                <label for="method-observer">
                    <input type="radio" id="method-observer" name="wpmtst_compat_options[ajax][method]" value="observer"
						<?php checked( $this->options['ajax']['method'], 'observer' ); ?>
                           data-group="observer"/>
					<?php _e( 'Observer', 'strong-testimonials' ); ?>
                </label>
            </div>
            <div>
                <p class="about"><?php _e( 'React to changes in specific page elements.', 'strong-testimonials' ); ?></p>
                <p class="description"><?php _e( 'For advanced users.', 'strong-testimonials' ); ?></p>
            </div>
        </div>

		<?php
		/*
		 * Timer
		 */
		?>
        <div class="row" data-sub="observer">
            <div class="radio-sub">
                <label for="observer-timer">
					<?php _ex( 'Check once after', 'timer setting', 'strong-testimonials' ); ?>
                </label>
            </div>
            <div>
                <input type="number" id="observer-timer"
                       name="wpmtst_compat_options[ajax][observer_timer]"
                       min=".1" max="5" step=".1" size="3"
                       value="<?php echo $this->options['ajax']['observer_timer']; ?>"/>
				<?php _ex( 'seconds', 'timer setting', 'strong-testimonials' ); ?>
            </div>
        </div>

		<?php
		/*
		 * Container element ID
		 */
		?>
        <div class="row" data-sub="observer">
            <div class="radio-sub">
                <label for="container-id">
					<?php _e( 'Container ID', 'strong-testimonials' ); ?>
                </label>
            </div>
            <div>
                <span class="code input-before">#</span>
                <input type="text" id="container-id" class="code element"
                       name="wpmtst_compat_options[ajax][container_id]"
                       value="<?php echo $this->options['ajax']['container_id']; ?>"/>
                <p class="about adjacent"><?php _e( 'the element to observe', 'strong-testimonials' ); ?></p>
            </div>
        </div>

		<?php
		/*
		 * Added node ID
		 */
		?>
        <div class="row" data-sub="observer">
            <div class="radio-sub">
                <label for="addednode-id">
					<?php _e( 'Added node ID', 'strong-testimonials' ); ?>
                </label>
            </div>
            <div>
                <span class="code input-before">#</span>
                <input type="text" id="addednode-id" class="code element"
                       name="wpmtst_compat_options[ajax][addednode_id]"
                       value="<?php echo $this->options['ajax']['addednode_id']; ?>"/>
                <p class="about adjacent"><?php _e( 'the element being added', 'strong-testimonials' ); ?></p>
            </div>
        </div>
		<?php
	}

	/**
	 * Custom event
	 */
	public function settings_monitor_event() {
		?>
        <div class="row">
            <div>
                <label for="method-event">
                    <input type="radio" id="method-event" name="wpmtst_compat_options[ajax][method]" value="event"
						<?php checked( $this->options['ajax']['method'], 'event' ); ?>
                           data-group="event"/>
					<?php _e( 'Custom event', 'strong-testimonials' ); ?>
                </label>
            </div>
            <div>
                <p class="about"><?php _e( 'Listen for specific events.', 'strong-testimonials' ); ?></p>
                <p class="description"><?php _e( 'For advanced users.', 'strong-testimonials' ); ?></p>
            </div>
        </div>

        <div class="row" data-sub="event">
            <div class="radio-sub">
                <label for="event-name">
					<?php _e( 'Event name', 'strong-testimonials' ); ?>
                </label>
            </div>
            <div>
                <input type="text" id="event-name" class="code"
                       name="wpmtst_compat_options[ajax][event]"
                       value="<?php echo $this->options['ajax']['event']; ?>" size="30"/>
            </div>
        </div>
		<?php
	}

	/**
	 * Specific script
	 */
	public function settings_monitor_script() {
		?>
        <div class="row">
            <div>
                <label for="method-script">
                    <input type="radio" id="method-script" name="wpmtst_compat_options[ajax][method]" value="script"
						<?php checked( $this->options['ajax']['method'], 'script' ); ?>
                           data-group="script"/>
					<?php _e( 'Specific script', 'strong-testimonials' ); ?>
                </label>
            </div>
            <div>
                <p class="about"><?php _e( 'Register a callback for a specific Ajax script.', 'strong-testimonials' ); ?></p>
                <p class="description"><?php _e( 'For advanced users.', 'strong-testimonials' ); ?></p>
            </div>
        </div>

        <div class="row" data-sub="script">
            <div class="radio-sub">
                <label for="script-name">
					<?php _e( 'Script name', 'strong-testimonials' ); ?>
                </label>
            </div>
            <div>
                <select id="script-name" name="wpmtst_compat_options[ajax][script]">
                    <option value="" <?php selected( $this->options['ajax']['script'], '' ); ?>>
						<?php _e( '&mdash; Select &mdash;' ); ?>
                    </option>
                    <option value="barba" <?php selected( $this->options['ajax']['script'], 'barba' ); ?>>Barba.js
                    </option>
                </select>
            </div>
        </div>
		<?php
	}

}

new Strong_Testimonials_Settings_Compat();
