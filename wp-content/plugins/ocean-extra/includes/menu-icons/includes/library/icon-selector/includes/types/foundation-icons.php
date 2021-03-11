<?php
/**
 * Foundation Icons
 *
 */
class OE_Icon_Picker_Type_Foundation extends OE_Icon_Picker_Type_Font {

	/**
	 * Icon type ID
	 *
	 */
	protected $id = 'foundation-icons';

	/**
	 * Icon type name
	 *
	 */
	protected $name = 'Foundation';

	/**
	 * Icon type version
	 *
	 */
	protected $version = '3.0';

	/**
	 * Get icon groups
	 *
	 */
	public function get_groups() {
		$groups = array(
			array(
				'id'   => 'accessibility',
				'name' => __( 'Accessibility', 'ocean-extra' ),
			),
			array(
				'id'   => 'arrows',
				'name' => __( 'Arrows', 'ocean-extra' ),
			),
			array(
				'id'   => 'devices',
				'name' => __( 'Devices', 'ocean-extra' ),
			),
			array(
				'id'   => 'ecommerce',
				'name' => __( 'Ecommerce', 'ocean-extra' ),
			),
			array(
				'id'   => 'editor',
				'name' => __( 'Editor', 'ocean-extra' ),
			),
			array(
				'id'   => 'file-types',
				'name' => __( 'File Types', 'ocean-extra' ),
			),
			array(
				'id'   => 'general',
				'name' => __( 'General', 'ocean-extra' ),
			),
			array(
				'id'   => 'media-control',
				'name' => __( 'Media Controls', 'ocean-extra' ),
			),
			array(
				'id'   => 'misc',
				'name' => __( 'Miscellaneous', 'ocean-extra' ),
			),
			array(
				'id'   => 'people',
				'name' => __( 'People', 'ocean-extra' ),
			),
			array(
				'id'   => 'social',
				'name' => __( 'Social/Brand', 'ocean-extra' ),
			),
		);
		/**
		 * Filter genericon groups
		 *
		 */
		$groups = apply_filters( 'oe_icon_picker_foundations_groups', $groups );

		return $groups;
	}

	/**
	 * Get icon names
	 *
	 */
	public function get_items() {
		$items = array(
			array(
				'group' => 'accessibility',
				'id'    => 'fi-asl',
				'name'  => __( 'ASL', 'ocean-extra' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-blind',
				'name'  => __( 'Blind', 'ocean-extra' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-braille',
				'name'  => __( 'Braille', 'ocean-extra' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-closed-caption',
				'name'  => __( 'Closed Caption', 'ocean-extra' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-elevator',
				'name'  => __( 'Elevator', 'ocean-extra' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-guide-dog',
				'name'  => __( 'Guide Dog', 'ocean-extra' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-hearing-aid',
				'name'  => __( 'Hearing Aid', 'ocean-extra' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-universal-access',
				'name'  => __( 'Universal Access', 'ocean-extra' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-male',
				'name'  => __( 'Male', 'ocean-extra' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-female',
				'name'  => __( 'Female', 'ocean-extra' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-male-female',
				'name'  => __( 'Male & Female', 'ocean-extra' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-male-symbol',
				'name'  => __( 'Male Symbol', 'ocean-extra' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-female-symbol',
				'name'  => __( 'Female Symbol', 'ocean-extra' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-wheelchair',
				'name'  => __( 'Wheelchair', 'ocean-extra' ),
			),
			array(
				'group' => 'arrows',
				'id'    => 'fi-arrow-up',
				'name'  => __( 'Arrow: Up', 'ocean-extra' ),
			),
			array(
				'group' => 'arrows',
				'id'    => 'fi-arrow-down',
				'name'  => __( 'Arrow: Down', 'ocean-extra' ),
			),
			array(
				'group' => 'arrows',
				'id'    => 'fi-arrow-left',
				'name'  => __( 'Arrow: Left', 'ocean-extra' ),
			),
			array(
				'group' => 'arrows',
				'id'    => 'fi-arrow-right',
				'name'  => __( 'Arrow: Right', 'ocean-extra' ),
			),
			array(
				'group' => 'arrows',
				'id'    => 'fi-arrows-out',
				'name'  => __( 'Arrows: Out', 'ocean-extra' ),
			),
			array(
				'group' => 'arrows',
				'id'    => 'fi-arrows-in',
				'name'  => __( 'Arrows: In', 'ocean-extra' ),
			),
			array(
				'group' => 'arrows',
				'id'    => 'fi-arrows-expand',
				'name'  => __( 'Arrows: Expand', 'ocean-extra' ),
			),
			array(
				'group' => 'arrows',
				'id'    => 'fi-arrows-compress',
				'name'  => __( 'Arrows: Compress', 'ocean-extra' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-bluetooth',
				'name'  => __( 'Bluetooth', 'ocean-extra' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-camera',
				'name'  => __( 'Camera', 'ocean-extra' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-compass',
				'name'  => __( 'Compass', 'ocean-extra' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-laptop',
				'name'  => __( 'Laptop', 'ocean-extra' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-megaphone',
				'name'  => __( 'Megaphone', 'ocean-extra' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-microphone',
				'name'  => __( 'Microphone', 'ocean-extra' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-mobile',
				'name'  => __( 'Mobile', 'ocean-extra' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-mobile-signal',
				'name'  => __( 'Mobile Signal', 'ocean-extra' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-monitor',
				'name'  => __( 'Monitor', 'ocean-extra' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-tablet-portrait',
				'name'  => __( 'Tablet: Portrait', 'ocean-extra' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-tablet-landscape',
				'name'  => __( 'Tablet: Landscape', 'ocean-extra' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-telephone',
				'name'  => __( 'Telephone', 'ocean-extra' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-usb',
				'name'  => __( 'USB', 'ocean-extra' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-video',
				'name'  => __( 'Video', 'ocean-extra' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-bitcoin',
				'name'  => __( 'Bitcoin', 'ocean-extra' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-bitcoin-circle',
				'name'  => __( 'Bitcoin', 'ocean-extra' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-dollar',
				'name'  => __( 'Dollar', 'ocean-extra' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-euro',
				'name'  => __( 'EURO', 'ocean-extra' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-pound',
				'name'  => __( 'Pound', 'ocean-extra' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-yen',
				'name'  => __( 'Yen', 'ocean-extra' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-burst',
				'name'  => __( 'Burst', 'ocean-extra' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-burst-new',
				'name'  => __( 'Burst: New', 'ocean-extra' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-burst-sale',
				'name'  => __( 'Burst: Sale', 'ocean-extra' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-credit-card',
				'name'  => __( 'Credit Card', 'ocean-extra' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-dollar-bill',
				'name'  => __( 'Dollar Bill', 'ocean-extra' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-paypal',
				'name'  => 'PayPal',
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-price-tag',
				'name'  => __( 'Price Tag', 'ocean-extra' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-pricetag-multiple',
				'name'  => __( 'Price Tag: Multiple', 'ocean-extra' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-shopping-bag',
				'name'  => __( 'Shopping Bag', 'ocean-extra' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-shopping-cart',
				'name'  => __( 'Shopping Cart', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-bold',
				'name'  => __( 'Bold', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-italic',
				'name'  => __( 'Italic', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-underline',
				'name'  => __( 'Underline', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-strikethrough',
				'name'  => __( 'Strikethrough', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-text-color',
				'name'  => __( 'Text Color', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-background-color',
				'name'  => __( 'Background Color', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-superscript',
				'name'  => __( 'Superscript', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-subscript',
				'name'  => __( 'Subscript', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-align-left',
				'name'  => __( 'Align Left', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-align-center',
				'name'  => __( 'Align Center', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-align-right',
				'name'  => __( 'Align Right', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-align-justify',
				'name'  => __( 'Justify', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-list-number',
				'name'  => __( 'List: Number', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-list-bullet',
				'name'  => __( 'List: Bullet', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-indent-more',
				'name'  => __( 'Indent', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-indent-less',
				'name'  => __( 'Outdent', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-page-add',
				'name'  => __( 'Add Page', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-page-copy',
				'name'  => __( 'Copy Page', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-page-multiple',
				'name'  => __( 'Duplicate Page', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-page-delete',
				'name'  => __( 'Delete Page', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-page-remove',
				'name'  => __( 'Remove Page', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-page-edit',
				'name'  => __( 'Edit Page', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-page-export',
				'name'  => __( 'Export', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-page-export-csv',
				'name'  => __( 'Export to CSV', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-page-export-pdf',
				'name'  => __( 'Export to PDF', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-page-filled',
				'name'  => __( 'Fill Page', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-crop',
				'name'  => __( 'Crop', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-filter',
				'name'  => __( 'Filter', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-paint-bucket',
				'name'  => __( 'Paint Bucket', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-photo',
				'name'  => __( 'Photo', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-print',
				'name'  => __( 'Print', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-save',
				'name'  => __( 'Save', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-link',
				'name'  => __( 'Link', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-unlink',
				'name'  => __( 'Unlink', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-quote',
				'name'  => __( 'Quote', 'ocean-extra' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-page-search',
				'name'  => __( 'Search in Page', 'ocean-extra' ),
			),
			array(
				'group' => 'file-types',
				'id'    => 'fi-page',
				'name'  => __( 'File', 'ocean-extra' ),
			),
			array(
				'group' => 'file-types',
				'id'    => 'fi-page-csv',
				'name'  => __( 'CSV', 'ocean-extra' ),
			),
			array(
				'group' => 'file-types',
				'id'    => 'fi-page-doc',
				'name'  => __( 'Doc', 'ocean-extra' ),
			),
			array(
				'group' => 'file-types',
				'id'    => 'fi-page-pdf',
				'name'  => __( 'PDF', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-address-book',
				'name'  => __( 'Addressbook', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-alert',
				'name'  => __( 'Alert', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-annotate',
				'name'  => __( 'Annotate', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-archive',
				'name'  => __( 'Archive', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-bookmark',
				'name'  => __( 'Bookmark', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-calendar',
				'name'  => __( 'Calendar', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-clock',
				'name'  => __( 'Clock', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-cloud',
				'name'  => __( 'Cloud', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-comment',
				'name'  => __( 'Comment', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-comment-minus',
				'name'  => __( 'Comment: Minus', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-comment-quotes',
				'name'  => __( 'Comment: Quotes', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-comment-video',
				'name'  => __( 'Comment: Video', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-comments',
				'name'  => __( 'Comments', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-contrast',
				'name'  => __( 'Contrast', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-database',
				'name'  => __( 'Database', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-folder',
				'name'  => __( 'Folder', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-folder-add',
				'name'  => __( 'Folder: Add', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-folder-lock',
				'name'  => __( 'Folder: Lock', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-eye',
				'name'  => __( 'Eye', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-heart',
				'name'  => __( 'Heart', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-plus',
				'name'  => __( 'Plus', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-minus',
				'name'  => __( 'Minus', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-minus-circle',
				'name'  => __( 'Minus', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-x',
				'name'  => __( 'X', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-x-circle',
				'name'  => __( 'X', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-check',
				'name'  => __( 'Check', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-checkbox',
				'name'  => __( 'Check', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-download',
				'name'  => __( 'Download', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-upload',
				'name'  => __( 'Upload', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-upload-cloud',
				'name'  => __( 'Upload to Cloud', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-flag',
				'name'  => __( 'Flag', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-foundation',
				'name'  => __( 'Foundation', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-graph-bar',
				'name'  => __( 'Graph: Bar', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-graph-horizontal',
				'name'  => __( 'Graph: Horizontal', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-graph-pie',
				'name'  => __( 'Graph: Pie', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-graph-trend',
				'name'  => __( 'Graph: Trend', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-home',
				'name'  => __( 'Home', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-layout',
				'name'  => __( 'Layout', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-like',
				'name'  => __( 'Like', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-dislike',
				'name'  => __( 'Dislike', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-list',
				'name'  => __( 'List', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-list-thumbnails',
				'name'  => __( 'List: Thumbnails', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-lock',
				'name'  => __( 'Lock', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-unlock',
				'name'  => __( 'Unlock', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-marker',
				'name'  => __( 'Marker', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-magnifying-glass',
				'name'  => __( 'Magnifying Glass', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-refresh',
				'name'  => __( 'Refresh', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-paperclip',
				'name'  => __( 'Paperclip', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-pencil',
				'name'  => __( 'Pencil', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-play-video',
				'name'  => __( 'Play Video', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-results',
				'name'  => __( 'Results', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-results-demographics',
				'name'  => __( 'Results: Demographics', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-rss',
				'name'  => __( 'RSS', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-share',
				'name'  => __( 'Share', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-sound',
				'name'  => __( 'Sound', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-star',
				'name'  => __( 'Star', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-thumbnails',
				'name'  => __( 'Thumbnails', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-trash',
				'name'  => __( 'Trash', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-web',
				'name'  => __( 'Web', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-widget',
				'name'  => __( 'Widget', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-wrench',
				'name'  => __( 'Wrench', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-zoom-out',
				'name'  => __( 'Zoom Out', 'ocean-extra' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-zoom-in',
				'name'  => __( 'Zoom In', 'ocean-extra' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-record',
				'name'  => __( 'Record', 'ocean-extra' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-play-circle',
				'name'  => __( 'Play', 'ocean-extra' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-play',
				'name'  => __( 'Play', 'ocean-extra' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-pause',
				'name'  => __( 'Pause', 'ocean-extra' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-stop',
				'name'  => __( 'Stop', 'ocean-extra' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-previous',
				'name'  => __( 'Previous', 'ocean-extra' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-rewind',
				'name'  => __( 'Rewind', 'ocean-extra' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-fast-forward',
				'name'  => __( 'Fast Forward', 'ocean-extra' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-next',
				'name'  => __( 'Next', 'ocean-extra' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-volume',
				'name'  => __( 'Volume', 'ocean-extra' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-volume-none',
				'name'  => __( 'Volume: Low', 'ocean-extra' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-volume-strike',
				'name'  => __( 'Volume: Mute', 'ocean-extra' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-loop',
				'name'  => __( 'Loop', 'ocean-extra' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-shuffle',
				'name'  => __( 'Shuffle', 'ocean-extra' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-eject',
				'name'  => __( 'Eject', 'ocean-extra' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-rewind-ten',
				'name'  => __( 'Rewind 10', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-anchor',
				'name'  => __( 'Anchor', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-asterisk',
				'name'  => __( 'Asterisk', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-at-sign',
				'name'  => __( '@', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-battery-full',
				'name'  => __( 'Battery: Full', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-battery-half',
				'name'  => __( 'Battery: Half', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-battery-empty',
				'name'  => __( 'Battery: Empty', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-book',
				'name'  => __( 'Book', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-book-bookmark',
				'name'  => __( 'Bookmark', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-clipboard',
				'name'  => __( 'Clipboard', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-clipboard-pencil',
				'name'  => __( 'Clipboard: Pencil', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-clipboard-notes',
				'name'  => __( 'Clipboard: Notes', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-crown',
				'name'  => __( 'Crown', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-die-one',
				'name'  => __( 'Dice: 1', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-die-two',
				'name'  => __( 'Dice: 2', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-die-three',
				'name'  => __( 'Dice: 3', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-die-four',
				'name'  => __( 'Dice: 4', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-die-five',
				'name'  => __( 'Dice: 5', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-die-six',
				'name'  => __( 'Dice: 6', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-safety-cone',
				'name'  => __( 'Cone', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-first-aid',
				'name'  => __( 'Firs Aid', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-foot',
				'name'  => __( 'Foot', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-info',
				'name'  => __( 'Info', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-key',
				'name'  => __( 'Key', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-lightbulb',
				'name'  => __( 'Lightbulb', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-map',
				'name'  => __( 'Map', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-mountains',
				'name'  => __( 'Mountains', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-music',
				'name'  => __( 'Music', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-no-dogs',
				'name'  => __( 'No Dogs', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-no-smoking',
				'name'  => __( 'No Smoking', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-paw',
				'name'  => __( 'Paw', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-power',
				'name'  => __( 'Power', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-prohibited',
				'name'  => __( 'Prohibited', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-projection-screen',
				'name'  => __( 'Projection Screen', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-puzzle',
				'name'  => __( 'Puzzle', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-sheriff-badge',
				'name'  => __( 'Sheriff Badge', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-shield',
				'name'  => __( 'Shield', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-skull',
				'name'  => __( 'Skull', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-target',
				'name'  => __( 'Target', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-target-two',
				'name'  => __( 'Target', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-ticket',
				'name'  => __( 'Ticket', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-trees',
				'name'  => __( 'Trees', 'ocean-extra' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-trophy',
				'name'  => __( 'Trophy', 'ocean-extra' ),
			),
			array(
				'group' => 'people',
				'id'    => 'fi-torso',
				'name'  => __( 'Torso', 'ocean-extra' ),
			),
			array(
				'group' => 'people',
				'id'    => 'fi-torso-business',
				'name'  => __( 'Torso: Business', 'ocean-extra' ),
			),
			array(
				'group' => 'people',
				'id'    => 'fi-torso-female',
				'name'  => __( 'Torso: Female', 'ocean-extra' ),
			),
			array(
				'group' => 'people',
				'id'    => 'fi-torsos',
				'name'  => __( 'Torsos', 'ocean-extra' ),
			),
			array(
				'group' => 'people',
				'id'    => 'fi-torsos-all',
				'name'  => __( 'Torsos: All', 'ocean-extra' ),
			),
			array(
				'group' => 'people',
				'id'    => 'fi-torsos-all-female',
				'name'  => __( 'Torsos: All Female', 'ocean-extra' ),
			),
			array(
				'group' => 'people',
				'id'    => 'fi-torsos-male-female',
				'name'  => __( 'Torsos: Male & Female', 'ocean-extra' ),
			),
			array(
				'group' => 'people',
				'id'    => 'fi-torsos-female-male',
				'name'  => __( 'Torsos: Female & Male', 'ocean-extra' ),
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-500px',
				'name'  => '500px',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-adobe',
				'name'  => 'Adobe',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-amazon',
				'name'  => 'Amazon',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-android',
				'name'  => 'Android',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-apple',
				'name'  => 'Apple',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-behance',
				'name'  => 'Behance',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-bing',
				'name'  => 'bing',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-blogger',
				'name'  => 'Blogger',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-css3',
				'name'  => 'CSS3',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-delicious',
				'name'  => 'Delicious',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-designer-news',
				'name'  => 'Designer News',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-deviant-art',
				'name'  => 'deviantArt',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-digg',
				'name'  => 'Digg',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-dribbble',
				'name'  => 'dribbble',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-drive',
				'name'  => 'Drive',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-dropbox',
				'name'  => 'DropBox',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-evernote',
				'name'  => 'Evernote',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-facebook',
				'name'  => 'Facebook',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-flickr',
				'name'  => 'flickr',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-forrst',
				'name'  => 'forrst',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-foursquare',
				'name'  => 'Foursquare',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-game-center',
				'name'  => 'Game Center',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-github',
				'name'  => 'GitHub',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-google-plus',
				'name'  => 'Google+',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-hacker-news',
				'name'  => 'Hacker News',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-hi5',
				'name'  => 'hi5',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-html5',
				'name'  => 'HTML5',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-instagram',
				'name'  => 'Instagram',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-joomla',
				'name'  => 'Joomla!',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-lastfm',
				'name'  => 'last.fm',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-linkedin',
				'name'  => 'LinkedIn',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-medium',
				'name'  => 'Medium',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-myspace',
				'name'  => 'My Space',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-orkut',
				'name'  => 'Orkut',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-path',
				'name'  => 'path',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-picasa',
				'name'  => 'Picasa',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-pinterest',
				'name'  => 'Pinterest',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-rdio',
				'name'  => 'rdio',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-reddit',
				'name'  => 'reddit',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-skype',
				'name'  => 'Skype',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-skillshare',
				'name'  => 'SkillShare',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-smashing-mag',
				'name'  => 'Smashing Mag.',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-snapchat',
				'name'  => 'Snapchat',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-spotify',
				'name'  => 'Spotify',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-squidoo',
				'name'  => 'Squidoo',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-stack-overflow',
				'name'  => 'StackOverflow',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-steam',
				'name'  => 'Steam',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-stumbleupon',
				'name'  => 'StumbleUpon',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-treehouse',
				'name'  => 'TreeHouse',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-tumblr',
				'name'  => 'Tumblr',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-twitter',
				'name'  => 'Twitter',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-windows',
				'name'  => 'Windows',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-xbox',
				'name'  => 'XBox',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-yahoo',
				'name'  => 'Yahoo!',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-yelp',
				'name'  => 'Yelp',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-youtube',
				'name'  => 'YouTube',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-zerply',
				'name'  => 'Zerply',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-zurb',
				'name'  => 'Zurb',
			),
		);

		/**
		 * Filter genericon items
		 *
		 */
		$items = apply_filters( 'oe_icon_picker_foundations_items', $items );

		return $items;
	}
}
