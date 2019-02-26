<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class CrellySliderTables {

	// Update the current Crelly Slider version in the database
	public static function setVersion() {
		update_option('cs_version', CS_VERSION);
	}

	public static function removeVersion() {
		delete_option('cs_version');
	}

	// Creates or updates all the tables
	public static function setTables() {
		self::setSlidersTable();
		self::setSlidesTable();
		self::setElementsTable();
	}

	public static function setSlidersTable() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'crellyslider_sliders';

		$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		name TEXT CHARACTER SET utf8,
		alias TEXT CHARACTER SET utf8,
		layout TEXT CHARACTER SET utf8,
		responsive INT,
		startWidth INT,
		startHeight INT,
		automaticSlide INT,
		showControls INT,
		showNavigation INT,
		enableSwipe INT DEFAULT 1,
		showProgressBar INT,
		pauseOnHover INT,
		randomOrder INT DEFAULT 0,
		startFromSlide INT DEFAULT 0,
		callbacks TEXT CHARACTER SET utf8,
		fromDate DATETIME DEFAULT '1000-01-01 00:00:00',
		toDate DATETIME DEFAULT '9999-12-31 23:59:59',
		UNIQUE KEY id (id)
		);";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	// Warning: the time variable is a string because it could contain the 'all' word
	public static function setSlidesTable() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'crellyslider_slides';

		$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		slider_parent mediumint(9),
		position INT,
		draft INT DEFAULT 0,
		background_type_image TEXT CHARACTER SET utf8,
		background_type_color TEXT CHARACTER SET utf8,
		background_type_color_input INT DEFAULT -1,
		background_propriety_position_x TEXT CHARACTER SET utf8,
		background_propriety_position_y TEXT CHARACTER SET utf8,
		background_repeat TEXT CHARACTER SET utf8,
		background_propriety_size TEXT CHARACTER SET utf8,
		data_in TEXT CHARACTER SET utf8,
		data_out TEXT CHARACTER SET utf8,
		data_time INT,
		data_easeIn INT,
		data_easeOut INT,
		link TEXT CHARACTER SET utf8,
		link_new_tab INT DEFAULT 0,
		custom_css TEXT CHARACTER SET utf8,
		UNIQUE KEY id (id)
		);";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	public static function setElementsTable() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'crellyslider_elements';

		$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		slider_parent mediumint(9),
		slide_parent mediumint(9),
		position INT,
		type TEXT CHARACTER SET utf8,
		data_easeIn INT,
		data_easeOut INT,
		data_ignoreEaseOut INT DEFAULT 0,
		data_delay INT,
		data_time TEXT CHARACTER SET utf8,
		data_top FLOAT,
		data_left FLOAT,
		z_index INT,
		data_in TEXT CHARACTER SET utf8,
		data_out TEXT CHARACTER SET utf8,
		custom_css TEXT CHARACTER SET utf8,
		custom_css_classes TEXT CHARACTER SET utf8,
		inner_html TEXT CHARACTER SET utf8,
		image_src TEXT CHARACTER SET utf8,
		image_alt TEXT CHARACTER SET utf8,
		link TEXT CHARACTER SET utf8,
		link_new_tab INT DEFAULT 0,
		video_id TEXT CHARACTER SET utf8,
		video_loop INT,
		video_autoplay INT,
		UNIQUE KEY id (id)
		);";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	// Drops all the slider tables
	public static function dropTables() {
		global $wpdb;

		self::dropTable($wpdb->prefix . 'crellyslider_sliders');
		self::dropTable($wpdb->prefix . 'crellyslider_slides');
		self::dropTable($wpdb->prefix . 'crellyslider_elements');
	}

	public static function dropTable($table_name) {
		global $wpdb;

		$sql = 'DROP TABLE ' . $table_name . ';';
		$wpdb->query($sql);
	}

	// Removes everything related to Crelly Slider from the database
	public static function clearDatabase() {
		self::dropTables();
		self::removeVersion();
	}
}

?>
