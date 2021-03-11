<?php
/**
 * Font Awesome
 *
 */

require_once dirname( __FILE__ ) . '/font.php';

/**
 * Icon type: Font Awesome
 *
 */
class OE_Icon_Picker_Type_Font_Awesome extends OE_Icon_Picker_Type_Font {

	/**
	 * Icon type ID
	 *
	 */
	protected $id = 'far';

	/**
	 * Icon type name
	 *
	 */
	protected $name = 'FontAwesome Regular';

	/**
	 * Icon type version
	 *
	 */
	protected $version = '5.11.2';

	/**
	 * Stylesheet ID
	 *
	 */
	protected $stylesheet_id = 'font-awesome';

	/**
	 * Get icon groups
	 *
	 */
	public function get_groups() {
		$groups = array(
			array(
				'id'   => 'regular',
				'name' => __( 'Regular', 'ocean-extra' ),
			),
		);

		/**
		 * Filter genericon groups
		 *
		 */
		$groups = apply_filters( 'oe_icon_picker_fa_groups', $groups );

		return $groups;
	}

	/**
	 * Get icon names
	 *
	 */
	public function get_items() {
		$items = array(
			array(
				'group' => 'regular',
				'id'    => 'fa-address-book',
				'name'  => __( 'Address Book', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-address-card',
				'name'  => __( 'Address Card', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-angry',
				'name'  => __( 'Angry', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-arrow-alt-circle-down',
				'name'  => __( 'Arrow Circle Down', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-arrow-alt-circle-left',
				'name'  => __( 'Arrow Circle Left', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-arrow-alt-circle-right',
				'name'  => __( 'Arrow Circle Right', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-arrow-alt-circle-up',
				'name'  => __( 'Arrow Circle Up', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-bell',
				'name'  => __( 'Bell', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-bell-slash',
				'name'  => __( 'Bell Slash', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-bookmark',
				'name'  => __( 'Bookmark', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-building',
				'name'  => __( 'Building', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-calendar',
				'name'  => __( 'Calendar', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-calendar-alt',
				'name'  => __( 'Calendar Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-calendar-check',
				'name'  => __( 'Calendar Check', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-calendar-minus',
				'name'  => __( 'Calendar Minus', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-calendar-plus',
				'name'  => __( 'Calendar Plus', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-calendar-times',
				'name'  => __( 'Calendar Times', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-caret-square-down',
				'name'  => __( 'Caret Square Down', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-caret-square-left',
				'name'  => __( 'Caret Square Left', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-caret-square-right',
				'name'  => __( 'Caret Square Right', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-caret-square-up',
				'name'  => __( 'Caret Square Up', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-chart-bar',
				'name'  => __( 'Chart Bar', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-check-circle',
				'name'  => __( 'Check Circle', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-check-square',
				'name'  => __( 'Check Square', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-circle',
				'name'  => __( 'Circle', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-clipboard',
				'name'  => __( 'Clipboard', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-clock',
				'name'  => __( 'Clock', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-clone',
				'name'  => __( 'Clone', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-closed-captioning',
				'name'  => __( 'Closed Captioning', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-comment',
				'name'  => __( 'Comment', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-comment-alt',
				'name'  => __( 'Comment Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-comment-dots',
				'name'  => __( 'Comment Dots', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-comments',
				'name'  => __( 'Comments', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-compass',
				'name'  => __( 'Compass', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-copy',
				'name'  => __( 'Copy', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-copyright',
				'name'  => __( 'Copyright', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-credit-card',
				'name'  => __( 'Credit Card', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-dizzy',
				'name'  => __( 'Dizzy', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-dot-circle',
				'name'  => __( 'Dot Circle', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-edit',
				'name'  => __( 'Edit', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-envelope',
				'name'  => __( 'Envelope', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-envelope-open',
				'name'  => __( 'Envelope Open', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-eye',
				'name'  => __( 'Eye', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-eye-slash',
				'name'  => __( 'Eye Slash', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-file',
				'name'  => __( 'File', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-file-alt',
				'name'  => __( 'File Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-file-archive',
				'name'  => __( 'File Archive', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-file-audio',
				'name'  => __( 'File Audio', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-file-code',
				'name'  => __( 'File Code', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-file-excel',
				'name'  => __( 'File Excel', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-file-image',
				'name'  => __( 'File Image', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-file-pdf',
				'name'  => __( 'File PDF', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-file-powerpoint',
				'name'  => __( 'File Powerpoint', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-file-video',
				'name'  => __( 'File Video', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-file-word',
				'name'  => __( 'File Word', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-flag',
				'name'  => __( 'Flag', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-flushed',
				'name'  => __( 'Flushed', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-folder',
				'name'  => __( 'Folder', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-folder-open',
				'name'  => __( 'Folder Open', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-frown',
				'name'  => __( 'Frown', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-frown-open',
				'name'  => __( 'Frown Open', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-futbol',
				'name'  => __( 'Futbol', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-gem',
				'name'  => __( 'Gem', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-grimace',
				'name'  => __( 'Grimace', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-grin',
				'name'  => __( 'Grin', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-grin-alt',
				'name'  => __( 'Grin Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-grin-beam',
				'name'  => __( 'Grin Beam', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-grin-beam-sweat',
				'name'  => __( 'Grin Beam Sweat', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-grin-hearts',
				'name'  => __( 'Grin Hearts', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-grin-squint',
				'name'  => __( 'Grin Squint', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-grin-squint-tears',
				'name'  => __( 'Grin Squint Tears', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-grin-stars',
				'name'  => __( 'Grin Stars', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-grin-tears',
				'name'  => __( 'Grin Tears', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-grin-tongue',
				'name'  => __( 'Grin Tongue', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-grin-tongue-squint',
				'name'  => __( 'Grin Tongue Squint', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-grin-tongue-wink',
				'name'  => __( 'Grin Tongue Wink', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-grin-wink',
				'name'  => __( 'Grin Wink', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-hand-lizard',
				'name'  => __( 'Hand Lizard', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-hand-paper',
				'name'  => __( 'Hand Paper', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-hand-peace',
				'name'  => __( 'Hand Peace', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-hand-point-down',
				'name'  => __( 'Hand Point Down', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-hand-point-left',
				'name'  => __( 'Hand Point Left', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-hand-point-right',
				'name'  => __( 'Hand Point Right', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-hand-point-up',
				'name'  => __( 'Hand Point Up', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-hand-pointer',
				'name'  => __( 'Hand Pointer', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-hand-rock',
				'name'  => __( 'Hand Rock', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-hand-scissors',
				'name'  => __( 'Hand Scissors', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-hand-spock',
				'name'  => __( 'Hand Spock', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-handshake',
				'name'  => __( 'Handshake', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-hdd',
				'name'  => __( 'HDD', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-heart',
				'name'  => __( 'Heart', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-hospital',
				'name'  => __( 'Hospital', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-hourglass',
				'name'  => __( 'Hourglass', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-id-badge',
				'name'  => __( 'Id Badge', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-id-card',
				'name'  => __( 'Id Card', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-image',
				'name'  => __( 'Image', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-images',
				'name'  => __( 'Images', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-keyboard',
				'name'  => __( 'Keyboard', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-kiss',
				'name'  => __( 'Kiss', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-kiss-beam',
				'name'  => __( 'Kiss Beam', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-kiss-wink-heart',
				'name'  => __( 'Kiss Wink Heart', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-laugh',
				'name'  => __( 'Laugh', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-laugh-beam',
				'name'  => __( 'Laugh Beam', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-laugh-squint',
				'name'  => __( 'Laugh Squint', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-laugh-wink',
				'name'  => __( 'Laugh Wink', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-lemon',
				'name'  => __( 'Lemon', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-life-ring',
				'name'  => __( 'Life Ring', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-lightbulb',
				'name'  => __( 'Lightbulb', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-list-alt',
				'name'  => __( 'List Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-map',
				'name'  => __( 'Map', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-meh',
				'name'  => __( 'Meh', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-meh-blank',
				'name'  => __( 'Me Blank', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-meh-rolling-eyes',
				'name'  => __( 'Meh Rolling Eyes', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-minus-square',
				'name'  => __( 'Minus Square', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-money-bill-alt',
				'name'  => __( 'Money Bill Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-moon',
				'name'  => __( 'Moon', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-newspaper',
				'name'  => __( 'Newspaper', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-object-group',
				'name'  => __( 'Object Group', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-object-ungroup',
				'name'  => __( 'Object Ungroup', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-paper-plane',
				'name'  => __( 'Paper Plane', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-pause-circle',
				'name'  => __( 'Pause Circle', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-play-circle',
				'name'  => __( 'Play Circle', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-plus-square',
				'name'  => __( 'Plus Square', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-question-circle',
				'name'  => __( 'Question Circle', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-registered',
				'name'  => __( 'Registered', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-save',
				'name'  => __( 'Save', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-share-square',
				'name'  => __( 'Share Square', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-smile',
				'name'  => __( 'Smile', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-smile-beam',
				'name'  => __( 'Smile Beam', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-smile-wink',
				'name'  => __( 'Smile Wink', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-snowflake',
				'name'  => __( 'Snowflake', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-square',
				'name'  => __( 'Square', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-snowflake',
				'name'  => __( 'Snowflake', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-star',
				'name'  => __( 'Star', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-star-half',
				'name'  => __( 'Star Half', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-sticky-note',
				'name'  => __( 'Sticky Note', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-stop-circle',
				'name'  => __( 'Stop Circle', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-sun',
				'name'  => __( 'Sun', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-surprise',
				'name'  => __( 'Surprise', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-thumbs-down',
				'name'  => __( 'Thumbs Down', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-thumbs-up',
				'name'  => __( 'Thumbs Up', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-times-circle',
				'name'  => __( 'Times Circle', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-tired',
				'name'  => __( 'Tired', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-trash-alt',
				'name'  => __( 'Trash Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-user',
				'name'  => __( 'User', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-user-circle',
				'name'  => __( 'User Circle', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-window-close',
				'name'  => __( 'Window Close', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-window-maximize',
				'name'  => __( 'Window Maximize', 'ocean-extra' ),
			),
			array(
				'group' => 'regular',
				'id'    => 'fa-window-restore',
				'name'  => __( 'Window Restore', 'ocean-extra' ),
			),
		);

		/**
		 * Filter FontAwesome items
		 *
		 */
		$items = apply_filters( 'oe_icon_picker_fa_items', $items );

		return $items;
	}
}
