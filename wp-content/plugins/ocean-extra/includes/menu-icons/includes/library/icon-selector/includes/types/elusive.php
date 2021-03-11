<?php
/**
 * Elusive Icons
 *
 */
class OE_Icon_Picker_Type_Elusive extends OE_Icon_Picker_Type_Font {

	/**
	 * Icon type ID
	 *
	 */
	protected $id = 'elusive';

	/**
	 * Icon type name
	 *
	 */
	protected $name = 'Elusive';

	/**
	 * Icon type version
	 *
	 */
	protected $version = '2.0';

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
				'id'   => 'currency',
				'name' => __( 'Currency', 'ocean-extra' ),
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
				'id'   => 'places',
				'name' => __( 'Places', 'ocean-extra' ),
			),
			array(
				'id'   => 'social',
				'name' => __( 'Social', 'ocean-extra' ),
			),
		);

		/**
		 * Filter genericon groups
		 *
		 */
		$groups = apply_filters( 'oe_icon_picker_genericon_groups', $groups );

		return $groups;
	}


	/**
	 * Get icon names
	 *
	 */
	public function get_items() {
		$items = array(
			array(
				'group' => 'actions',
				'id'    => 'el-icon-adjust',
				'name'  => __( 'Adjust', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-adjust-alt',
				'name'  => __( 'Adjust', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-align-left',
				'name'  => __( 'Align Left', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-align-center',
				'name'  => __( 'Align Center', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-align-right',
				'name'  => __( 'Align Right', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-align-justify',
				'name'  => __( 'Justify', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-arrow-up',
				'name'  => __( 'Arrow Up', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-arrow-down',
				'name'  => __( 'Arrow Down', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-arrow-left',
				'name'  => __( 'Arrow Left', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-arrow-right',
				'name'  => __( 'Arrow Right', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-fast-backward',
				'name'  => __( 'Fast Backward', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-step-backward',
				'name'  => __( 'Step Backward', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-backward',
				'name'  => __( 'Backward', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-forward',
				'name'  => __( 'Forward', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-forward-alt',
				'name'  => __( 'Forward', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-step-forward',
				'name'  => __( 'Step Forward', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-fast-forward',
				'name'  => __( 'Fast Forward', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-bold',
				'name'  => __( 'Bold', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-italic',
				'name'  => __( 'Italic', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-link',
				'name'  => __( 'Link', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-caret-up',
				'name'  => __( 'Caret Up', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-caret-down',
				'name'  => __( 'Caret Down', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-caret-left',
				'name'  => __( 'Caret Left', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-caret-right',
				'name'  => __( 'Caret Right', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-check',
				'name'  => __( 'Check', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-check-empty',
				'name'  => __( 'Check Empty', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-chevron-up',
				'name'  => __( 'Chevron Up', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-chevron-down',
				'name'  => __( 'Chevron Down', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-chevron-left',
				'name'  => __( 'Chevron Left', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-chevron-right',
				'name'  => __( 'Chevron Right', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-circle-arrow-up',
				'name'  => __( 'Circle Arrow Up', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-circle-arrow-down',
				'name'  => __( 'Circle Arrow Down', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-circle-arrow-left',
				'name'  => __( 'Circle Arrow Left', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-circle-arrow-right',
				'name'  => __( 'Circle Arrow Right', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-download',
				'name'  => __( 'Download', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-download-alt',
				'name'  => __( 'Download', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-edit',
				'name'  => __( 'Edit', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-eject',
				'name'  => __( 'Eject', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-file-new',
				'name'  => __( 'File New', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-file-new-alt',
				'name'  => __( 'File New', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-file-edit',
				'name'  => __( 'File Edit', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-file-edit-alt',
				'name'  => __( 'File Edit', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-fork',
				'name'  => __( 'Fork', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-fullscreen',
				'name'  => __( 'Fullscreen', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-indent-left',
				'name'  => __( 'Indent Left', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-indent-right',
				'name'  => __( 'Indent Right', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-list',
				'name'  => __( 'List', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-list-alt',
				'name'  => __( 'List', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-lock',
				'name'  => __( 'Lock', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-lock-alt',
				'name'  => __( 'Lock', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-unlock',
				'name'  => __( 'Unlock', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-unlock-alt',
				'name'  => __( 'Unlock', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-map-marker',
				'name'  => __( 'Map Marker', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-map-marker-alt',
				'name'  => __( 'Map Marker', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-minus',
				'name'  => __( 'Minus', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-minus-sign',
				'name'  => __( 'Minus Sign', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-move',
				'name'  => __( 'Move', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-off',
				'name'  => __( 'Off', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-ok',
				'name'  => __( 'OK', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-ok-circle',
				'name'  => __( 'OK Circle', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-ok-sign',
				'name'  => __( 'OK Sign', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-play',
				'name'  => __( 'Play', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-play-alt',
				'name'  => __( 'Play', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-pause',
				'name'  => __( 'Pause', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-pause-alt',
				'name'  => __( 'Pause', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-stop',
				'name'  => __( 'Stop', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-stop-alt',
				'name'  => __( 'Stop', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-plus',
				'name'  => __( 'Plus', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-plus-sign',
				'name'  => __( 'Plus Sign', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-print',
				'name'  => __( 'Print', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-question',
				'name'  => __( 'Question', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-question-sign',
				'name'  => __( 'Question Sign', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-record',
				'name'  => __( 'Record', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-refresh',
				'name'  => __( 'Refresh', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-remove',
				'name'  => __( 'Remove', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-repeat',
				'name'  => __( 'Repeat', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-repeat-alt',
				'name'  => __( 'Repeat', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-resize-vertical',
				'name'  => __( 'Resize Vertical', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-resize-horizontal',
				'name'  => __( 'Resize Horizontal', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-resize-full',
				'name'  => __( 'Resize Full', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-resize-small',
				'name'  => __( 'Resize Small', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-return-key',
				'name'  => __( 'Return', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-retweet',
				'name'  => __( 'Retweet', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-reverse-alt',
				'name'  => __( 'Reverse', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-search',
				'name'  => __( 'Search', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-search-alt',
				'name'  => __( 'Search', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-share',
				'name'  => __( 'Share', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-share-alt',
				'name'  => __( 'Share', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-tag',
				'name'  => __( 'Tag', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-tasks',
				'name'  => __( 'Tasks', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-text-height',
				'name'  => __( 'Text Height', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-text-width',
				'name'  => __( 'Text Width', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-thumbs-up',
				'name'  => __( 'Thumbs Up', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-thumbs-down',
				'name'  => __( 'Thumbs Down', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-tint',
				'name'  => __( 'Tint', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-trash',
				'name'  => __( 'Trash', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-trash-alt',
				'name'  => __( 'Trash', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-upload',
				'name'  => __( 'Upload', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-view-mode',
				'name'  => __( 'View Mode', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-volume-up',
				'name'  => __( 'Volume Up', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-volume-down',
				'name'  => __( 'Volume Down', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-volume-off',
				'name'  => __( 'Mute', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-warning-sign',
				'name'  => __( 'Warning Sign', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-zoom-in',
				'name'  => __( 'Zoom In', 'ocean-extra' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-zoom-out',
				'name'  => __( 'Zoom Out', 'ocean-extra' ),
			),
			array(
				'group' => 'currency',
				'id'    => 'el-icon-eur',
				'name'  => 'EUR',
			),
			array(
				'group' => 'currency',
				'id'    => 'el-icon-gbp',
				'name'  => 'GBP',
			),
			array(
				'group' => 'currency',
				'id'    => 'el-icon-usd',
				'name'  => 'USD',
			),
			array(
				'group' => 'media',
				'id'    => 'el-icon-video',
				'name'  => __( 'Video', 'ocean-extra' ),
			),
			array(
				'group' => 'media',
				'id'    => 'el-icon-video-alt',
				'name'  => __( 'Video', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-adult',
				'name'  => __( 'Adult', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-address-book',
				'name'  => __( 'Address Book', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-address-book-alt',
				'name'  => __( 'Address Book', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-asl',
				'name'  => __( 'ASL', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-asterisk',
				'name'  => __( 'Asterisk', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-ban-circle',
				'name'  => __( 'Ban Circle', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-barcode',
				'name'  => __( 'Barcode', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-bell',
				'name'  => __( 'Bell', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-blind',
				'name'  => __( 'Blind', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-book',
				'name'  => __( 'Book', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-braille',
				'name'  => __( 'Braille', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-briefcase',
				'name'  => __( 'Briefcase', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-broom',
				'name'  => __( 'Broom', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-brush',
				'name'  => __( 'Brush', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-bulb',
				'name'  => __( 'Bulb', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-bullhorn',
				'name'  => __( 'Bullhorn', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-calendar',
				'name'  => __( 'Calendar', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-calendar-sign',
				'name'  => __( 'Calendar Sign', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-camera',
				'name'  => __( 'Camera', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-car',
				'name'  => __( 'Car', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-cc',
				'name'  => __( 'CC', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-certificate',
				'name'  => __( 'Certificate', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-child',
				'name'  => __( 'Child', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-cog',
				'name'  => __( 'Cog', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-cog-alt',
				'name'  => __( 'Cog', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-cogs',
				'name'  => __( 'Cogs', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-comment',
				'name'  => __( 'Comment', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-comment-alt',
				'name'  => __( 'Comment', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-compass',
				'name'  => __( 'Compass', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-compass-alt',
				'name'  => __( 'Compass', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-credit-card',
				'name'  => __( 'Credit Card', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-css',
				'name'  => 'CSS',
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-envelope',
				'name'  => __( 'Envelope', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-envelope-alt',
				'name'  => __( 'Envelope', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-error',
				'name'  => __( 'Error', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-error-alt',
				'name'  => __( 'Error', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-exclamation-sign',
				'name'  => __( 'Exclamation Sign', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-eye-close',
				'name'  => __( 'Eye Close', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-eye-open',
				'name'  => __( 'Eye Open', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-male',
				'name'  => __( 'Male', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-female',
				'name'  => __( 'Female', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-file',
				'name'  => __( 'File', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-file-alt',
				'name'  => __( 'File', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-film',
				'name'  => __( 'Film', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-filter',
				'name'  => __( 'Filter', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-fire',
				'name'  => __( 'Fire', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-flag',
				'name'  => __( 'Flag', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-flag-alt',
				'name'  => __( 'Flag', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-folder',
				'name'  => __( 'Folder', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-folder-open',
				'name'  => __( 'Folder Open', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-folder-close',
				'name'  => __( 'Folder Close', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-folder-sign',
				'name'  => __( 'Folder Sign', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-font',
				'name'  => __( 'Font', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-fontsize',
				'name'  => __( 'Font Size', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-gift',
				'name'  => __( 'Gift', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-glass',
				'name'  => __( 'Glass', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-glasses',
				'name'  => __( 'Glasses', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-globe',
				'name'  => __( 'Globe', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-globe-alt',
				'name'  => __( 'Globe', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-graph',
				'name'  => __( 'Graph', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-graph-alt',
				'name'  => __( 'Graph', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-group',
				'name'  => __( 'Group', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-group-alt',
				'name'  => __( 'Group', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-guidedog',
				'name'  => __( 'Guide Dog', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-hand-up',
				'name'  => __( 'Hand Up', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-hand-down',
				'name'  => __( 'Hand Down', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-hand-left',
				'name'  => __( 'Hand Left', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-hand-right',
				'name'  => __( 'Hand Right', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-hdd',
				'name'  => __( 'HDD', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-headphones',
				'name'  => __( 'Headphones', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-hearing-impaired',
				'name'  => __( 'Hearing Impaired', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-heart',
				'name'  => __( 'Heart', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-heart-alt',
				'name'  => __( 'Heart', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-heart-empty',
				'name'  => __( 'Heart Empty', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-hourglass',
				'name'  => __( 'Hourglass', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-idea',
				'name'  => __( 'Idea', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-idea-alt',
				'name'  => __( 'Idea', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-inbox',
				'name'  => __( 'Inbox', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-inbox-alt',
				'name'  => __( 'Inbox', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-inbox-box',
				'name'  => __( 'Inbox', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-info-sign',
				'name'  => __( 'Info', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-key',
				'name'  => __( 'Key', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-laptop',
				'name'  => __( 'Laptop', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-laptop-alt',
				'name'  => __( 'Laptop', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-leaf',
				'name'  => __( 'Leaf', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-lines',
				'name'  => __( 'Lines', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-magic',
				'name'  => __( 'Magic', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-magnet',
				'name'  => __( 'Magnet', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-mic',
				'name'  => __( 'Mic', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-music',
				'name'  => __( 'Music', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-paper-clip',
				'name'  => __( 'Paper Clip', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-paper-clip-alt',
				'name'  => __( 'Paper Clip', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-pencil',
				'name'  => __( 'Pencil', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-pencil-alt',
				'name'  => __( 'Pencil', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-person',
				'name'  => __( 'Person', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-phone',
				'name'  => __( 'Phone', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-phone-alt',
				'name'  => __( 'Phone', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-photo',
				'name'  => __( 'Photo', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-photo-alt',
				'name'  => __( 'Photo', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-picture',
				'name'  => __( 'Picture', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-plane',
				'name'  => __( 'Plane', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-podcast',
				'name'  => __( 'Podcast', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-puzzle',
				'name'  => __( 'Puzzle', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-qrcode',
				'name'  => __( 'QR Code', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-quotes',
				'name'  => __( 'Quotes', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-quotes-alt',
				'name'  => __( 'Quotes', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-random',
				'name'  => __( 'Random', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-scissors',
				'name'  => __( 'Scissors', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-screen',
				'name'  => __( 'Screen', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-screen-alt',
				'name'  => __( 'Screen', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-screenshot',
				'name'  => __( 'Screenshot', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-shopping-cart',
				'name'  => __( 'Shopping Cart', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-shopping-cart-sign',
				'name'  => __( 'Shopping Cart Sign', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-signal',
				'name'  => __( 'Signal', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-smiley',
				'name'  => __( 'Smiley', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-smiley-alt',
				'name'  => __( 'Smiley', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-speaker',
				'name'  => __( 'Speaker', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-user',
				'name'  => __( 'User', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-th',
				'name'  => __( 'Thumbnails', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-th-large',
				'name'  => __( 'Thumbnails (Large)', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-th-list',
				'name'  => __( 'Thumbnails (List)', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-time',
				'name'  => __( 'Time', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-time-alt',
				'name'  => __( 'Time', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-torso',
				'name'  => __( 'Torso', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-wheelchair',
				'name'  => __( 'Wheelchair', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-wrench',
				'name'  => __( 'Wrench', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-wrench-alt',
				'name'  => __( 'Wrench', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-universal-access',
				'name'  => __( 'Universal Access', 'ocean-extra' ),
			),
			array(
				'group' => 'places',
				'id'    => 'el-icon-bookmark',
				'name'  => __( 'Bookmark', 'ocean-extra' ),
			),
			array(
				'group' => 'places',
				'id'    => 'el-icon-bookmark-empty',
				'name'  => __( 'Bookmark Empty', 'ocean-extra' ),
			),
			array(
				'group' => 'places',
				'id'    => 'el-icon-dashboard',
				'name'  => __( 'Dashboard', 'ocean-extra' ),
			),
			array(
				'group' => 'places',
				'id'    => 'el-icon-home',
				'name'  => __( 'Home', 'ocean-extra' ),
			),
			array(
				'group' => 'places',
				'id'    => 'el-icon-home-alt',
				'name'  => __( 'Home', 'ocean-extra' ),
			),
			array(
				'group' => 'places',
				'id'    => 'el-icon-iphone-home',
				'name'  => __( 'Home (iPhone)', 'ocean-extra' ),
			),
			array(
				'group' => 'places',
				'id'    => 'el-icon-network',
				'name'  => __( 'Network', 'ocean-extra' ),
			),
			array(
				'group' => 'places',
				'id'    => 'el-icon-tags',
				'name'  => __( 'Tags', 'ocean-extra' ),
			),
			array(
				'group' => 'places',
				'id'    => 'el-icon-website',
				'name'  => __( 'Website', 'ocean-extra' ),
			),
			array(
				'group' => 'places',
				'id'    => 'el-icon-website-alt',
				'name'  => __( 'Website', 'ocean-extra' ),
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-behance',
				'name'  => 'Behance',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-blogger',
				'name'  => 'Blogger',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-cloud',
				'name'  => __( 'Cloud', 'ocean-extra' ),
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-cloud-alt',
				'name'  => __( 'Cloud', 'ocean-extra' ),
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-delicious',
				'name'  => 'Delicious',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-deviantart',
				'name'  => 'DeviantArt',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-digg',
				'name'  => 'Digg',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-dribbble',
				'name'  => 'Dribbble',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-facebook',
				'name'  => 'Facebook',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-facetime-video',
				'name'  => 'Facetime Video',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-flickr',
				'name'  => 'Flickr',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-foursquare',
				'name'  => 'Foursquare',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-friendfeed',
				'name'  => 'FriendFeed',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-friendfeed-rect',
				'name'  => 'FriendFeed',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-github',
				'name'  => 'GitHub',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-github-text',
				'name'  => 'GitHub',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-googleplus',
				'name'  => 'Google+',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-instagram',
				'name'  => 'Instagram',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-lastfm',
				'name'  => 'Last.fm',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-linkedin',
				'name'  => 'LinkedIn',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-livejournal',
				'name'  => 'LiveJournal',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-myspace',
				'name'  => 'MySpace',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-opensource',
				'name'  => __( 'Open Source', 'ocean-extra' ),
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-path',
				'name'  => 'path',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-picasa',
				'name'  => 'Picasa',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-pinterest',
				'name'  => 'Pinterest',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-rss',
				'name'  => 'RSS',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-reddit',
				'name'  => 'Reddit',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-skype',
				'name'  => 'Skype',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-slideshare',
				'name'  => 'Slideshare',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-soundcloud',
				'name'  => 'SoundCloud',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-spotify',
				'name'  => 'Spotify',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-stackoverflow',
				'name'  => 'Stack Overflow',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-stumbleupon',
				'name'  => 'StumbleUpon',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-twitter',
				'name'  => 'Twitter',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-tumblr',
				'name'  => 'Tumblr',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-viadeo',
				'name'  => 'Viadeo',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-vimeo',
				'name'  => 'Vimeo',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-vkontakte',
				'name'  => 'VKontakte',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-w3c',
				'name'  => 'W3C',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-wordpress',
				'name'  => 'WordPress',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-youtube',
				'name'  => 'YouTube',
			),
		);

		/**
		 * Filter genericon items
		 *
		 */
		$items = apply_filters( 'oe_icon_picker_genericon_items', $items );

		return $items;
	}
}
