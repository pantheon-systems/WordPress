<?php
/**
 * Simple Line Icons
 *
 */

require_once dirname( __FILE__ ) . '/font.php';

/**
 * Icon type: Simple Line Icons
 *
 */

class OE_Icon_Picker_Type_Simple_Line_Icons extends OE_Icon_Picker_Type_Font {

	/**
	 * Icon type ID
	 *
	 */
	protected $id = 'line-icon';

	/**
	 * Icon type name
	 *
	 */
	protected $name = 'Simple Line Icons';

	/**
	 * Icon type version
	 *
	 */
	protected $version = '2.4.0';

	/**
	 * Stylesheet ID
	 *
	 */
	protected $stylesheet_id = 'simple-line-icons';

	/**
	 * Get icon groups
	 *
	 */
	public function get_groups() {
		$groups = array(
			array(
				'id'   => 'actions',
				'name' => __( 'Actions', 'ocean-extra' ),
			),
			array(
				'id'   => 'media',
				'name' => __( 'Media', 'ocean-extra' ),
			),
			array(
				'id'   => 'misc',
				'name' => __( 'Misc.', 'ocean-extra' ),
			),
			array(
				'id'   => 'social',
				'name' => __( 'Social', 'ocean-extra' ),
			),
		);

		/**
		 * Filter simple line icons groups
		 *
		 */
		$groups = apply_filters( 'oe_icon_picker_simple_line_icons_groups', $groups );

		return $groups;
	}

	/**
	 * Get icon names
	 *
	 */
	public function get_items() {
		$items = array(
			array(
				'group' => 'misc',
				'id'    => 'icon-user',
				'name'  => __( 'User', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-people',
				'name'  => __( 'People', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-user-female',
				'name'  => __( 'User Female', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-user-follow',
				'name'  => __( 'User Follow', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-user-following',
				'name'  => __( 'User Following', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-user-unfollow',
				'name'  => __( 'User Unfollow', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-login',
				'name'  => __( 'Login', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-logout',
				'name'  => __( 'Logout', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-emotsmile',
				'name'  => __( 'Emotsmile', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-phone',
				'name'  => __( 'Phone', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-call-end',
				'name'  => __( 'Call End', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-call-in',
				'name'  => __( 'Call In', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-call-out',
				'name'  => __( 'Call Out', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-map',
				'name'  => __( 'Map', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-location-pin',
				'name'  => __( 'Location Pin', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-direction',
				'name'  => __( 'Direction', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-directions',
				'name'  => __( 'Directions', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-compass',
				'name'  => __( 'Compass', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-layers',
				'name'  => __( 'Layers', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-menu',
				'name'  => __( 'Menu', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-list',
				'name'  => __( 'List', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-options-vertical',
				'name'  => __( 'Options Vertical', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-options',
				'name'  => __( 'Options', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-arrow-down',
				'name'  => __( 'Arrow Down', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-arrow-left',
				'name'  => __( 'Arrow Left', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-arrow-right',
				'name'  => __( 'Arrow Right', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-arrow-up',
				'name'  => __( 'Arrow Up', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-arrow-up-circle',
				'name'  => __( 'Arrow Up Circle', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-arrow-left-circle',
				'name'  => __( 'Arrow Left Circle', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-arrow-right-circle',
				'name'  => __( 'Arrow Right Circle', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-arrow-down-circle',
				'name'  => __( 'Arrow Down Circle', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-check',
				'name'  => __( 'Check', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-clock',
				'name'  => __( 'Clock', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-plus',
				'name'  => __( 'Plus', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-minus',
				'name'  => __( 'Minus', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-close',
				'name'  => __( 'Close', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-exclamation',
				'name'  => __( 'Exclamation', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-organization',
				'name'  => __( 'Organization', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-trophy',
				'name'  => __( 'Trophy', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-screen-smartphone',
				'name'  => __( 'Screen Smartphone', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-screen-desktop',
				'name'  => __( 'Screen Desktop', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-plane',
				'name'  => __( 'Plane', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-notebook',
				'name'  => __( 'Notebook', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-mustache',
				'name'  => __( 'Mustache', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-mouse',
				'name'  => __( 'Mouse', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-magnet',
				'name'  => __( 'Magnet', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-energy',
				'name'  => __( 'Energy', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-disc',
				'name'  => __( 'Disc', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-cursor',
				'name'  => __( 'Cursor', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-cursor-move',
				'name'  => __( 'Cursor Move', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-crop',
				'name'  => __( 'Crop', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-chemistry',
				'name'  => __( 'Chemistry', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-speedometer',
				'name'  => __( 'Speedometer', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-shield',
				'name'  => __( 'Shield', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-screen-tablet',
				'name'  => __( 'Screen Tablet', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-magic-wand',
				'name'  => __( 'Magic Wand', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-hourglass',
				'name'  => __( 'Hourglass', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-graduation',
				'name'  => __( 'Graduation', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-ghost',
				'name'  => __( 'Ghost', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-game-controller',
				'name'  => __( 'Game Controller', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-fire',
				'name'  => __( 'Fire', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-eyeglass',
				'name'  => __( 'Eyeglass', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-envelope-open',
				'name'  => __( 'Envelope Open', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-envelope-letter',
				'name'  => __( 'Envelope Letter', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-bell',
				'name'  => __( 'Bell', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-badge',
				'name'  => __( 'Badge', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-anchor',
				'name'  => __( 'Anchor', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-wallet',
				'name'  => __( 'Wallet', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-vector',
				'name'  => __( 'Vector', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-speech',
				'name'  => __( 'Speech', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-puzzle',
				'name'  => __( 'Puzzle', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-printer',
				'name'  => __( 'Printer', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-present',
				'name'  => __( 'Present', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-playlist',
				'name'  => __( 'Playlist', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-pin',
				'name'  => __( 'Pin', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-picture',
				'name'  => __( 'Picture', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-handbag',
				'name'  => __( 'Handbag', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-globe-alt',
				'name'  => __( 'Globe Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-globe',
				'name'  => __( 'Globe', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-folder-alt',
				'name'  => __( 'Folder Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-folder',
				'name'  => __( 'Folder', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-film',
				'name'  => __( 'Film', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-feed',
				'name'  => __( 'Feed', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-drop',
				'name'  => __( 'Drop', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-drawer',
				'name'  => __( 'Drawer', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-docs',
				'name'  => __( 'Docs', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-doc',
				'name'  => __( 'Doc', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-diamond',
				'name'  => __( 'Diamond', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-cup',
				'name'  => __( 'Cup', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-calculator',
				'name'  => __( 'Calculator', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-bubbles',
				'name'  => __( 'Bubbles', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-briefcase',
				'name'  => __( 'Briefcase', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-book-open',
				'name'  => __( 'Book Open', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-basket-loaded',
				'name'  => __( 'Basket Loaded', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-basket',
				'name'  => __( 'Basket', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-bag',
				'name'  => __( 'Bag', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-action-undo',
				'name'  => __( 'Action Undo', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'icon-action-redo',
				'name'  => __( 'Action Redo', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-wrench',
				'name'  => __( 'Wrench', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-umbrella',
				'name'  => __( 'Umbrella', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-trash',
				'name'  => __( 'Trash', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-tag',
				'name'  => __( 'Tag', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-support',
				'name'  => __( 'Support', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-frame',
				'name'  => __( 'Frame', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-size-fullscreen',
				'name'  => __( 'Size Fullscreen', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-size-actual',
				'name'  => __( 'Size Actual', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-shuffle',
				'name'  => __( 'Shuffle', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-share-alt',
				'name'  => __( 'Share Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-share',
				'name'  => __( 'Share', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-rocket',
				'name'  => __( 'Rocket', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-question',
				'name'  => __( 'Question', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-pie-chart',
				'name'  => __( 'Pie Chart', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-pencil',
				'name'  => __( 'Pencil', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-note',
				'name'  => __( 'Note', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-loop',
				'name'  => __( 'Loop', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-home',
				'name'  => __( 'Home', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-grid',
				'name'  => __( 'Grid', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-graph',
				'name'  => __( 'Graph', 'ocean-extra' ),
			),
			array(
				'group' => 'media',
				'id'    => 'icon-microphone',
				'name'  => __( 'Microphone', 'ocean-extra' ),
			),
			array(
				'group' => 'media',
				'id'    => 'icon-music-tone-alt',
				'name'  => __( 'Music Tone Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'media',
				'id'    => 'icon-music-tone',
				'name'  => __( 'Music Tone', 'ocean-extra' ),
			),
			array(
				'group' => 'media',
				'id'    => 'icon-earphones-alt',
				'name'  => __( 'Earphones Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'media',
				'id'    => 'icon-earphones',
				'name'  => __( 'Earphones', 'ocean-extra' ),
			),
			array(
				'group' => 'media',
				'id'    => 'icon-equalizer',
				'name'  => __( 'Equalizer', 'ocean-extra' ),
			),
			array(
				'group' => 'media',
				'id'    => 'icon-like',
				'name'  => __( 'Like', 'ocean-extra' ),
			),
			array(
				'group' => 'media',
				'id'    => 'icon-dislike',
				'name'  => __( 'Dislike', 'ocean-extra' ),
			),
			array(
				'group' => 'media',
				'id'    => 'icon-control-start',
				'name'  => __( 'Control Start', 'ocean-extra' ),
			),
			array(
				'group' => 'media',
				'id'    => 'icon-control-rewind',
				'name'  => __( 'Control Rewind', 'ocean-extra' ),
			),
			array(
				'group' => 'media',
				'id'    => 'icon-control-play',
				'name'  => __( 'Control Play', 'ocean-extra' ),
			),
			array(
				'group' => 'media',
				'id'    => 'icon-control-pause',
				'name'  => __( 'Control Pause', 'ocean-extra' ),
			),
			array(
				'group' => 'media',
				'id'    => 'icon-control-forward',
				'name'  => __( 'Control Forward', 'ocean-extra' ),
			),
			array(
				'group' => 'media',
				'id'    => 'icon-control-end',
				'name'  => __( 'Control End', 'ocean-extra' ),
			),
			array(
				'group' => 'media',
				'id'    => 'icon-volume-1',
				'name'  => __( 'Volume 1', 'ocean-extra' ),
			),
			array(
				'group' => 'media',
				'id'    => 'icon-volume-2',
				'name'  => __( 'Volume 2', 'ocean-extra' ),
			),
			array(
				'group' => 'media',
				'id'    => 'icon-volume-off',
				'name'  => __( 'Volume Off', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-calendar',
				'name'  => __( 'Calendar', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-bulb',
				'name'  => __( 'Bulb', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-chart',
				'name'  => __( 'Chart', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-ban',
				'name'  => __( 'Ban', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-bubble',
				'name'  => __( 'Bubble', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-camrecorder',
				'name'  => __( 'Camrecorder', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-camera',
				'name'  => __( 'Camera', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-cloud-download',
				'name'  => __( 'Cloud Download', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-cloud-upload',
				'name'  => __( 'Cloud Upload', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-envelope',
				'name'  => __( 'Envelope', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-eye',
				'name'  => __( 'Eye', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-flag',
				'name'  => __( 'Flag', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-heart',
				'name'  => __( 'Heart', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-info',
				'name'  => __( 'Info', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-key',
				'name'  => __( 'Key', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-link',
				'name'  => __( 'Link', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-lock',
				'name'  => __( 'Lock', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-lock-open',
				'name'  => __( 'Lock Open', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-magnifier',
				'name'  => __( 'Magnifier', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-magnifier-add',
				'name'  => __( 'Magnifier Add', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-magnifier-remove',
				'name'  => __( 'Magnifier Remove', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-paper-clip',
				'name'  => __( 'Paper Clip', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-paper-plane',
				'name'  => __( 'Paper Plane', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-power',
				'name'  => __( 'Power', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-refresh',
				'name'  => __( 'Refresh', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-reload',
				'name'  => __( 'Reload', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-settings',
				'name'  => __( 'Settings', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-star',
				'name'  => __( 'Star', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-symbol-female',
				'name'  => __( 'Symbol Female', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-symbol-male',
				'name'  => __( 'Symbol Male', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-target',
				'name'  => __( 'Target', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-credit-card',
				'name'  => __( 'Credit Card', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'icon-paypal',
				'name'  => __( 'Paypal', 'ocean-extra' ),
			),
			array(
				'group' => 'social',
				'id'    => 'icon-social-tumblr',
				'name'  => __( 'Social Tumblr', 'ocean-extra' ),
			),
			array(
				'group' => 'social',
				'id'    => 'icon-social-twitter',
				'name'  => __( 'Social Twitter', 'ocean-extra' ),
			),
			array(
				'group' => 'social',
				'id'    => 'icon-social-facebook',
				'name'  => __( 'Social Facebook', 'ocean-extra' ),
			),
			array(
				'group' => 'social',
				'id'    => 'icon-social-instagram',
				'name'  => __( 'Social Instagram', 'ocean-extra' ),
			),
			array(
				'group' => 'social',
				'id'    => 'icon-social-linkedin',
				'name'  => __( 'Social Linkedin', 'ocean-extra' ),
			),
			array(
				'group' => 'social',
				'id'    => 'icon-social-pinterest',
				'name'  => __( 'Social Pinterest', 'ocean-extra' ),
			),
			array(
				'group' => 'social',
				'id'    => 'icon-social-github',
				'name'  => __( 'Social Github', 'ocean-extra' ),
			),
			array(
				'group' => 'social',
				'id'    => 'icon-social-google',
				'name'  => __( 'Social Google', 'ocean-extra' ),
			),
			array(
				'group' => 'social',
				'id'    => 'icon-social-reddit',
				'name'  => __( 'Social Reddit', 'ocean-extra' ),
			),
			array(
				'group' => 'social',
				'id'    => 'icon-social-skype',
				'name'  => __( 'Social Skype', 'ocean-extra' ),
			),
			array(
				'group' => 'social',
				'id'    => 'icon-social-dribbble',
				'name'  => __( 'Social Dribbble', 'ocean-extra' ),
			),
			array(
				'group' => 'social',
				'id'    => 'icon-social-behance',
				'name'  => __( 'Social Behance', 'ocean-extra' ),
			),
			array(
				'group' => 'social',
				'id'    => 'icon-social-foursqare',
				'name'  => __( 'Social Foursqare', 'ocean-extra' ),
			),
			array(
				'group' => 'social',
				'id'    => 'icon-social-soundcloud',
				'name'  => __( 'Social Soundcloud', 'ocean-extra' ),
			),
			array(
				'group' => 'social',
				'id'    => 'icon-social-spotify',
				'name'  => __( 'Social Spotify', 'ocean-extra' ),
			),
			array(
				'group' => 'social',
				'id'    => 'icon-social-stumbleupon',
				'name'  => __( 'Social Stumbleupon', 'ocean-extra' ),
			),
			array(
				'group' => 'social',
				'id'    => 'icon-social-youtube',
				'name'  => __( 'Social Youtube', 'ocean-extra' ),
			),
			array(
				'group' => 'social',
				'id'    => 'icon-social-dropbox',
				'name'  => __( 'Social Dropbox', 'ocean-extra' ),
			),
		);

		/**
		 * Filter simple line icons items
		 *
		 */
		$items = apply_filters( 'oe_icon_picker_simple_line_icons_items', $items );

		return $items;
	}
}
