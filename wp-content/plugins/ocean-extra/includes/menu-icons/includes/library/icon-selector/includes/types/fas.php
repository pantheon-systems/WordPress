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
class OE_Icon_Picker_Type_Font_Awesome_Solid extends OE_Icon_Picker_Type_Font {

	/**
	 * Icon type ID
	 *
	 */
	protected $id = 'fas';

	/**
	 * Icon type name
	 *
	 */
	protected $name = 'FontAwesome Solid';

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
				'id'   => 'solid',
				'name' => __( 'Solid', 'ocean-extra' ),
			),
		);

		/**
		 * Filter genericon groups
		 *
		 */
		$groups = apply_filters( 'oe_icon_picker_fas_groups', $groups );

		return $groups;
	}

	/**
	 * Get icon names
	 *
	 */
	public function get_items() {
		$items = array(
			array(
				'group' => 'solid',
				'id'    => 'fa-ad',
				'name'  => __( 'AD', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-address-book',
				'name'  => __( 'Address Book', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-address-card',
				'name'  => __( 'Address Card', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-adjust',
				'name'  => __( 'Adjust', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-air-freshener',
				'name'  => __( 'Air Freshener', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-align-center',
				'name'  => __( 'Align Center', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-align-justify',
				'name'  => __( 'Align Justify', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-align-left',
				'name'  => __( 'Align Left', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-align-right',
				'name'  => __( 'Align Right', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-ambulance',
				'name'  => __( 'Ambulance', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-american-sign-language-interpreting',
				'name'  => __( 'American Sign Language', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-anchor',
				'name'  => __( 'Anchor', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-angle-double-down',
				'name'  => __( 'Angle Double Down', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-angle-double-left',
				'name'  => __( 'Angle Double Left', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-angle-double-right',
				'name'  => __( 'Angle Double Right', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-angle-double-up',
				'name'  => __( 'Angle Double Up', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-angle-down',
				'name'  => __( 'Angle Down', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-angle-left',
				'name'  => __( 'Angle Left', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-angle-right',
				'name'  => __( 'Angle Right', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-angle-up',
				'name'  => __( 'Angle Up', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-ankh',
				'name'  => __( 'Ankh', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-apple-alt',
				'name'  => __( 'Apple Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-archive',
				'name'  => __( 'Archive', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-archway',
				'name'  => __( 'Archway', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-arrow-alt-circle-down',
				'name'  => __( 'Arrow Alt Circle Down', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-arrow-alt-circle-left',
				'name'  => __( 'Arrow Alt Circle Left', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-arrow-alt-circle-right',
				'name'  => __( 'Arrow Alt Circle Right', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-arrow-alt-circle-up',
				'name'  => __( 'Arrow Alt Circle up', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-arrow-circle-down',
				'name'  => __( 'Arrow Circle Down', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-arrow-circle-left',
				'name'  => __( 'Arrow Circle Left', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-arrow-circle-right',
				'name'  => __( 'Arrow Circle Right', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-arrow-circle-up',
				'name'  => __( 'Arrow Circle up', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-arrow-down',
				'name'  => __( 'Arrow Down', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-arrow-left',
				'name'  => __( 'Arrow Left', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-arrow-right',
				'name'  => __( 'Arrow Right', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-arrow-up',
				'name'  => __( 'Arrow up', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-arrows-alt',
				'name'  => __( 'Arrow Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-assistive-listening-systems',
				'name'  => __( 'Assistive Listening Systems', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-asterisk',
				'name'  => __( 'Asterisk', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-asterisk',
				'name'  => __( 'Asterisk', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-at',
				'name'  => __( 'At', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-atlas',
				'name'  => __( 'Atlas', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-atom',
				'name'  => __( 'Atom', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-audio-description',
				'name'  => __( 'Audio Description', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-award',
				'name'  => __( 'Award', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-baby',
				'name'  => __( 'Baby', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-baby-carriage',
				'name'  => __( 'Baby Carriage', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-backspace',
				'name'  => __( 'Backspace', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-backward',
				'name'  => __( 'Backward', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-balance-scale',
				'name'  => __( 'Balance Scale', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-ban',
				'name'  => __( 'Ban', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-barcode',
				'name'  => __( 'Barcode', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-bars',
				'name'  => __( 'Bars', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-baseball-ball',
				'name'  => __( 'Baseball Ball', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-basketball-ball',
				'name'  => __( 'Basketball Ball', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-bath',
				'name'  => __( 'Bath', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-battery-empty',
				'name'  => __( 'Battery Empty', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-battery-full',
				'name'  => __( 'Battery Full', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-bed',
				'name'  => __( 'Bed', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-beer',
				'name'  => __( 'Beer', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-bell',
				'name'  => __( 'Bell', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-bell-slash',
				'name'  => __( 'Bell Slash', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-bezier-curve',
				'name'  => __( 'Bezier Curve', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-bicycle',
				'name'  => __( 'Bicycle', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-biking',
				'name'  => __( 'Biking', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-binoculars',
				'name'  => __( 'Binoculars', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-biohazard',
				'name'  => __( 'Biohazard', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-birthday-cake',
				'name'  => __( 'Birthday Cake', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-blender',
				'name'  => __( 'Blender', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-blind',
				'name'  => __( 'Blind', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-blog',
				'name'  => __( 'Blog', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-bold',
				'name'  => __( 'Bold', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-bolt',
				'name'  => __( 'Bolt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-bomb',
				'name'  => __( 'Bomb', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-bong',
				'name'  => __( 'Bong', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-book',
				'name'  => __( 'Book', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-bookmark',
				'name'  => __( 'Bookmark', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-border-all',
				'name'  => __( 'Border All', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-border-none',
				'name'  => __( 'Border None', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-bowling-ball',
				'name'  => __( 'Bowling Ball', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-box',
				'name'  => __( 'Box', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-box-open',
				'name'  => __( 'Box Open', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-boxes',
				'name'  => __( 'Boxes', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-braille',
				'name'  => __( 'Braille', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-brain',
				'name'  => __( 'Brain', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-bread-slice',
				'name'  => __( 'Bread Slice', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-briefcase',
				'name'  => __( 'Briefcase', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-briefcase-medical',
				'name'  => __( 'Briefcase Medical', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-broadcast-tower',
				'name'  => __( 'Broadcast Tower', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-broom',
				'name'  => __( 'Broom', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-brush',
				'name'  => __( 'Brush', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-bug',
				'name'  => __( 'Bug', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-building',
				'name'  => __( 'Building', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-bullhorn',
				'name'  => __( 'Bullhorn', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-bullseye',
				'name'  => __( 'Bullseye', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-burn',
				'name'  => __( 'Burn', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-bus-alt',
				'name'  => __( 'Bus Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-business-time',
				'name'  => __( 'Business Time', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-calculator',
				'name'  => __( 'Calculator', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-calendar',
				'name'  => __( 'Calendar', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-calendar-alt',
				'name'  => __( 'Calendar Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-calendar-check',
				'name'  => __( 'Calendar Check', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-calendar-day',
				'name'  => __( 'Calendar Day', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-calendar-minus',
				'name'  => __( 'Calendar Minus', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-calendar-plus',
				'name'  => __( 'Calendar Plus', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-calendar-times',
				'name'  => __( 'Calendar Times', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-camera',
				'name'  => __( 'Camera', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-camera-retro',
				'name'  => __( 'Camera Retro', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-campground',
				'name'  => __( 'Campground', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-cannabis',
				'name'  => __( 'Cannabis', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-capsules',
				'name'  => __( 'Capsules', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-car',
				'name'  => __( 'Car', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-caret-down',
				'name'  => __( 'Caret Down', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-caret-left',
				'name'  => __( 'Caret Left', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-caret-right',
				'name'  => __( 'Caret Right', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-caret-square-down',
				'name'  => __( 'Caret Square Down', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-caret-square-left',
				'name'  => __( 'Caret Square Left', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-caret-square-right',
				'name'  => __( 'Caret Square Right', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-caret-square-up',
				'name'  => __( 'Caret Square Up', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-caret-up',
				'name'  => __( 'Caret Up', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-carrot',
				'name'  => __( 'Carrot', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-cart-plus',
				'name'  => __( 'Cart Plus', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-certificate',
				'name'  => __( 'Certificate', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-chart-area',
				'name'  => __( 'Chart Area', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-chart-bar',
				'name'  => __( 'Chart Bar', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-chart-line',
				'name'  => __( 'Chart Line', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-chart-pie',
				'name'  => __( 'Chart Pie', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-check',
				'name'  => __( 'Check', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-check-circle',
				'name'  => __( 'Check Circle', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-check-double',
				'name'  => __( 'Check Double', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-check-square',
				'name'  => __( 'Check Square', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-cheese',
				'name'  => __( 'Cheese', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-chess',
				'name'  => __( 'Chess', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-chess-king',
				'name'  => __( 'Chess King', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-chess-queen',
				'name'  => __( 'Chess Queen', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-chevron-circle-down',
				'name'  => __( 'Chevron Circle Down', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-chevron-circle-left',
				'name'  => __( 'Chevron Circle Left', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-chevron-circle-right',
				'name'  => __( 'Chevron Circle Right', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-chevron-circle-up',
				'name'  => __( 'Chevron Circle Up', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-chevron-down',
				'name'  => __( 'Chevron Down', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-chevron-left',
				'name'  => __( 'Chevron Left', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-chevron-right',
				'name'  => __( 'Chevron Right', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-chevron-up',
				'name'  => __( 'Chevron Up', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-child',
				'name'  => __( 'Child', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-circle',
				'name'  => __( 'Circle', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-circle-notch',
				'name'  => __( 'Circle Notch', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-clinic-medical',
				'name'  => __( 'Clinic Medical', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-clipboard',
				'name'  => __( 'Clipboard', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-clipboard-check',
				'name'  => __( 'Clipboard Check', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-clipboard-list',
				'name'  => __( 'Clipboard List', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-clock',
				'name'  => __( 'Clock', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-clone',
				'name'  => __( 'Clone', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-closed-captioning',
				'name'  => __( 'Closed Captioning', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-cloud',
				'name'  => __( 'Cloud', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-cloud-download-alt',
				'name'  => __( 'Cloud Download Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-cloud-upload-alt',
				'name'  => __( 'Cloud Upload Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-cocktail',
				'name'  => __( 'Cocktail', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-code',
				'name'  => __( 'Code', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-code-branch',
				'name'  => __( 'Code Branch', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-coffee',
				'name'  => __( 'Coffee', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-cog',
				'name'  => __( 'Cog', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-cogs',
				'name'  => __( 'Cogs', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-coins',
				'name'  => __( 'Coins', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-columns',
				'name'  => __( 'Columns', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-comment',
				'name'  => __( 'Comment', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-comment-alt',
				'name'  => __( 'Comment Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-comment-dots',
				'name'  => __( 'Comment Dots', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-comment-slash',
				'name'  => __( 'Comment Slash', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-comments',
				'name'  => __( 'comments', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-compact-disc',
				'name'  => __( 'Compact Disc', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-compass',
				'name'  => __( 'Compass', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-compress',
				'name'  => __( 'Compress', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-compress-arrows-alt',
				'name'  => __( 'Compress Arrows Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-concierge-bell',
				'name'  => __( 'Concierge Bell', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-cookie',
				'name'  => __( 'Cookie', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-cookie-bite',
				'name'  => __( 'Cookie Bite', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-copy',
				'name'  => __( 'Copy', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-copyright',
				'name'  => __( 'Copyright', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-credit-card',
				'name'  => __( 'Credit Card', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-crop',
				'name'  => __( 'Crop', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-crosshairs',
				'name'  => __( 'Crosshairs', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-crown',
				'name'  => __( 'Crown', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-cube',
				'name'  => __( 'Cube', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-cubes',
				'name'  => __( 'Cubes', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-cut',
				'name'  => __( 'Cut', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-database',
				'name'  => __( 'Database', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-deaf',
				'name'  => __( 'Deaf', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-desktop',
				'name'  => __( 'Desktop', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-dice',
				'name'  => __( 'Dice', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-dice-d6',
				'name'  => __( 'Dice D6', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-directions',
				'name'  => __( 'Directions', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-dog',
				'name'  => __( 'Dog', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-dollar-sign',
				'name'  => __( 'Dollar Sign', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-dolly',
				'name'  => __( 'Dolly', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-donate',
				'name'  => __( 'Donate', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-download',
				'name'  => __( 'Download', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-drafting-compass',
				'name'  => __( 'Drafting Compass', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-drum',
				'name'  => __( 'Drum', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-drum-steelpan',
				'name'  => __( 'Drum Steelpan', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-dumbbell',
				'name'  => __( 'Dumbbell', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-dumpster',
				'name'  => __( 'Dumpster', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-edit',
				'name'  => __( 'Edit', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-egg',
				'name'  => __( 'Egg', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-eject',
				'name'  => __( 'Eject', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-ellipsis-v',
				'name'  => __( 'Ellipsis V', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-envelope',
				'name'  => __( 'Envelope', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-envelope-open',
				'name'  => __( 'Envelope Open', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-envelope-open-text',
				'name'  => __( 'Envelope Open Text', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-envelope-square',
				'name'  => __( 'Envelope Square', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-equals',
				'name'  => __( 'Equals', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-eraser',
				'name'  => __( 'Eraser', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-euro-sign',
				'name'  => __( 'Euro Sign', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-exclamation',
				'name'  => __( 'Exclamation', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-exchange-alt',
				'name'  => __( 'Exchange Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-exclamation-circle',
				'name'  => __( 'Exclamation Circle', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-exclamation-triangle',
				'name'  => __( 'Exclamation Triangle', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-expand',
				'name'  => __( 'Expand', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-expand-arrows-alt',
				'name'  => __( 'Expand Arrows Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-external-link-alt',
				'name'  => __( 'External Link Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-external-link-square-alt',
				'name'  => __( 'External Link Square Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-eye',
				'name'  => __( 'Eye', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-eye-dropper',
				'name'  => __( 'Eye Dropper', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-eye-slash',
				'name'  => __( 'Eye Slash', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-fan',
				'name'  => __( 'Fan', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-fast-backward',
				'name'  => __( 'Fast Backward', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-fast-forward',
				'name'  => __( 'Fast Forward', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-fax',
				'name'  => __( 'Fax', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-feather',
				'name'  => __( 'Feather', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-feather-alt',
				'name'  => __( 'Feather Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-female',
				'name'  => __( 'Female', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-fighter-jet',
				'name'  => __( 'Fighter Jet', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-file',
				'name'  => __( 'File', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-file-alt',
				'name'  => __( 'File Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-file-archive',
				'name'  => __( 'File Archive', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-file-audio',
				'name'  => __( 'File Audio', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-file-code',
				'name'  => __( 'File Code', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-file-contract',
				'name'  => __( 'File Contract', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-file-csv',
				'name'  => __( 'File CSV', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-file-download',
				'name'  => __( 'File Download', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-file-excel',
				'name'  => __( 'File Excel', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-file-export',
				'name'  => __( 'File Export', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-file-image',
				'name'  => __( 'File Image', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-file-import',
				'name'  => __( 'File Import', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-file-invoice',
				'name'  => __( 'File Invoice', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-file-invoice-dollar',
				'name'  => __( 'File Invoice Dollar', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-file-pdf',
				'name'  => __( 'File Pdf', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-file-powerpoint',
				'name'  => __( 'File Powerpoint', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-file-prescription',
				'name'  => __( 'File Prescription', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-file-signature',
				'name'  => __( 'File Signature', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-file-upload',
				'name'  => __( 'File Upload', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-file-video',
				'name'  => __( 'File Video', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-file-word',
				'name'  => __( 'File Word', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-fill',
				'name'  => __( 'Fill', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-fill-drip',
				'name'  => __( 'Fill Drip', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-film',
				'name'  => __( 'Film', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-filter',
				'name'  => __( 'Filter', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-fingerprint',
				'name'  => __( 'Fingerprint', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-fire',
				'name'  => __( 'Fire', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-fire-alt',
				'name'  => __( 'Fire Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-flag',
				'name'  => __( 'Flag', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-flag-usa',
				'name'  => __( 'Flag USA', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-flask',
				'name'  => __( 'Flask', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-folder',
				'name'  => __( 'Folder', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-folder-open',
				'name'  => __( 'Folder Open', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-font',
				'name'  => __( 'Font', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-football-ball',
				'name'  => __( 'Football Ball', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-forward',
				'name'  => __( 'Forward', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-futbol',
				'name'  => __( 'Futbol', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-gamepad',
				'name'  => __( 'Gamepad', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-gem',
				'name'  => __( 'Gem', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-genderless',
				'name'  => __( 'Genderless', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-ghost',
				'name'  => __( 'Ghost', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-gift',
				'name'  => __( 'Gift', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-gifts',
				'name'  => __( 'Gifts', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-glass-cheers',
				'name'  => __( 'Glass Cheers', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-glass-martini',
				'name'  => __( 'Glass Martini', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-glass-martini-alt',
				'name'  => __( 'Glass Martini Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-glasses',
				'name'  => __( 'Glasses', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-globe',
				'name'  => __( 'Globe', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-globe-africa',
				'name'  => __( 'Globe Africa', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-globe-americas',
				'name'  => __( 'Globe Americas', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-globe-asia',
				'name'  => __( 'Globe Asia', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-globe-europe',
				'name'  => __( 'Globe Europe', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-graduation-cap',
				'name'  => __( 'Graduation Cap', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-grin',
				'name'  => __( 'Grin', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-grin-alt',
				'name'  => __( 'Grin Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-grin-beam',
				'name'  => __( 'Grin Beam', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-grin-beam-sweat',
				'name'  => __( 'Grin Beam Sweat', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-grin-hearts',
				'name'  => __( 'Grin Hearts', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-grip-horizontal',
				'name'  => __( 'Grip Horizontal', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-guitar',
				'name'  => __( 'Guitar', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-h-square',
				'name'  => __( 'H-Square', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hamburger',
				'name'  => __( 'Hamburger', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hand-holding',
				'name'  => __( 'Hand Holding', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hand-holding-heart',
				'name'  => __( 'Hand Holding Heart', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hand-holding-usd',
				'name'  => __( 'Hand Holding USD', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hand-lizard',
				'name'  => __( 'Hand-Lizard', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hand-middle-finger',
				'name'  => __( 'Hand Middle Finger', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hand-paper',
				'name'  => __( 'Hand Paper', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hand-peace',
				'name'  => __( 'Hand Peace', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hand-point-down',
				'name'  => __( 'Hand Point Down', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hand-point-left',
				'name'  => __( 'Hand Point Left', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hand-point-right',
				'name'  => __( 'Hand Point Right', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hand-point-up',
				'name'  => __( 'Hand Point Up', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hand-pointer',
				'name'  => __( 'Hand Pointer', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hands',
				'name'  => __( 'Hands', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hands-helping',
				'name'  => __( 'Hands Helping', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-handshake',
				'name'  => __( 'Handshake', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hashtag',
				'name'  => __( 'Hashtag', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hat-cowboy',
				'name'  => __( 'Hat Cowboy', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hdd',
				'name'  => __( 'HDD', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-heading',
				'name'  => __( 'Heading', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-headphones',
				'name'  => __( 'Headphones', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-headphones-alt',
				'name'  => __( 'Headphones Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-headset',
				'name'  => __( 'Headset', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-heart',
				'name'  => __( 'Heart', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-heartbeat',
				'name'  => __( 'Heartbeat', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hiking',
				'name'  => __( 'Hiking', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-history',
				'name'  => __( 'History', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-home',
				'name'  => __( 'Home', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hospital',
				'name'  => __( 'Hospital', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hotel',
				'name'  => __( 'Hotel', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hourglass',
				'name'  => __( 'Hourglass', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hourglass-end',
				'name'  => __( 'Hourglass End', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hourglass-half',
				'name'  => __( 'Hourglass Half', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-hourglass-start',
				'name'  => __( 'Hourglass Start', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-ice-cream',
				'name'  => __( 'Ice Cream', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-icons',
				'name'  => __( 'Icons', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-id-badge',
				'name'  => __( 'Id Badge', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-id-card',
				'name'  => __( 'Id Card', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-id-card-alt',
				'name'  => __( 'Id Card Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-image',
				'name'  => __( 'Image', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-images',
				'name'  => __( 'Images', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-inbox',
				'name'  => __( 'Inbox', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-indent',
				'name'  => __( 'Indent', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-industry',
				'name'  => __( 'Industry', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-infinity',
				'name'  => __( 'Infinity', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-info',
				'name'  => __( 'Info', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-info-circle',
				'name'  => __( 'Info Circle', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-italic',
				'name'  => __( 'Italic', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-key',
				'name'  => __( 'Key', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-keyboard',
				'name'  => __( 'Keyboard', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-landmark',
				'name'  => __( 'Landmark', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-language',
				'name'  => __( 'Language', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-laptop',
				'name'  => __( 'Laptop', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-laptop-code',
				'name'  => __( 'Laptop Code', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-layer-group',
				'name'  => __( 'Layer Group', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-leaf',
				'name'  => __( 'Leaf', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-level-down-alt',
				'name'  => __( 'Level Down Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-level-up-alt',
				'name'  => __( 'Level Up Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-life-ring',
				'name'  => __( 'Life Ring', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-lightbulb',
				'name'  => __( 'Lightbulb', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-link',
				'name'  => __( 'Link', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-list',
				'name'  => __( 'List', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-location-arrow',
				'name'  => __( 'Location Arrow', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-lock',
				'name'  => __( 'Lock', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-lock-open',
				'name'  => __( 'Lock Open', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-long-arrow-alt-down',
				'name'  => __( 'Long Arrow Alt Down', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-long-arrow-alt-left',
				'name'  => __( 'Long Arrow Alt Left', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-long-arrow-alt-right',
				'name'  => __( 'Long Arrow Alt Right', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-long-arrow-alt-up',
				'name'  => __( 'Long Arrow Alt Up', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-magic',
				'name'  => __( 'Magic', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-magnet',
				'name'  => __( 'Magnet', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-male',
				'name'  => __( 'Male', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-map',
				'name'  => __( 'Map', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-map-marked-alt',
				'name'  => __( 'Map Marked Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-map-marker-alt',
				'name'  => __( 'Map Marker Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-mars',
				'name'  => __( 'Mars', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-mars-double',
				'name'  => __( 'Mars Double', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-mars-stroke',
				'name'  => __( 'Mars Stroke', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-medal',
				'name'  => __( 'Medal', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-medkit',
				'name'  => __( 'Medkit', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-mercury',
				'name'  => __( 'Mercury', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-microchip',
				'name'  => __( 'Microchip', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-microphone',
				'name'  => __( 'Microphone', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-microphone-alt',
				'name'  => __( 'Microphone Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-microphone-alt-slash',
				'name'  => __( 'Microphone Alt Slash', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-microphone-slash',
				'name'  => __( 'Microphone Slash', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-microscope',
				'name'  => __( 'Microscope', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-mobile-alt',
				'name'  => __( 'Mobile Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-money-bill',
				'name'  => __( 'Money Bill', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-money-check-alt',
				'name'  => __( 'Money Check Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-motorcycle',
				'name'  => __( 'Motorcycle', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-mountain',
				'name'  => __( 'Mountain', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-mug-hot',
				'name'  => __( 'Mug Hot', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-music',
				'name'  => __( 'Music', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-network-wired',
				'name'  => __( 'Network Wired', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-notes-medical',
				'name'  => __( 'Notes Medical', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-om',
				'name'  => __( 'Om', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-outdent',
				'name'  => __( 'Outdent', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-paint-brush',
				'name'  => __( 'Paint Brush', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-palette',
				'name'  => __( 'Palette', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-paper-plane',
				'name'  => __( 'Paper Plane', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-paperclip',
				'name'  => __( 'Paperclip', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-paragraph',
				'name'  => __( 'Paragraph', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-passport',
				'name'  => __( 'Passport', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-paste',
				'name'  => __( 'Paste', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-pause',
				'name'  => __( 'Pause', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-paw',
				'name'  => __( 'Paw', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-pen-nib',
				'name'  => __( 'Pen Nib', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-pen-square',
				'name'  => __( 'Pen Square', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-pencil-ruler',
				'name'  => __( 'Pencil Ruler', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-phone-alt',
				'name'  => __( 'Phone Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-phone-slash',
				'name'  => __( 'Phone Slash', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-phone-square-alt',
				'name'  => __( 'Phone Square Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-phone-volume',
				'name'  => __( 'Phone Volume', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-piggy-bank',
				'name'  => __( 'Piggy Bank', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-pizza-slice',
				'name'  => __( 'Pizza Slice', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-plane-departure',
				'name'  => __( 'Plane Departure', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-play',
				'name'  => __( 'Play', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-play-circle',
				'name'  => __( 'Play Circle', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-plug',
				'name'  => __( 'Plug', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-plus-circle',
				'name'  => __( 'Plus Circle', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-plus-square',
				'name'  => __( 'Plus Square', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-podcast',
				'name'  => __( 'Podcast', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-poll',
				'name'  => __( 'Poll', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-poll-h',
				'name'  => __( 'Poll-H', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-poo',
				'name'  => __( 'Poo', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-portrait',
				'name'  => __( 'Portrait', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-pound-sign',
				'name'  => __( 'Pound Sign', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-power-off',
				'name'  => __( 'Power Off', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-print',
				'name'  => __( 'Print', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-project-diagram',
				'name'  => __( 'Project Diagram', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-qrcode',
				'name'  => __( 'Qrcode', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-question-circle',
				'name'  => __( 'Question Circle', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-quote-left',
				'name'  => __( 'Quote Left', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-quote-right',
				'name'  => __( 'Quote Right', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-radiation',
				'name'  => __( 'Radiation', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-radiation-alt',
				'name'  => __( 'Radiation Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-random',
				'name'  => __( 'Random', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-receipt',
				'name'  => __( 'Receipt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-redo',
				'name'  => __( 'Redo', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-redo-alt',
				'name'  => __( 'Redo Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-registered',
				'name'  => __( 'Registered', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-reply',
				'name'  => __( 'Reply', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-reply-all',
				'name'  => __( 'Reply All', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-retweet',
				'name'  => __( 'Retweet', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-rocket',
				'name'  => __( 'Rocket', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-route',
				'name'  => __( 'Route', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-rss',
				'name'  => __( 'RSS', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-ruble-sign',
				'name'  => __( 'Ruble Sign', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-running',
				'name'  => __( 'Running', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-rupee-sign',
				'name'  => __( 'Rupee Sign', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-satellite',
				'name'  => __( 'Satellite', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-save',
				'name'  => __( 'Save', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-sd-card',
				'name'  => __( 'SD Card', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-search',
				'name'  => __( 'Search', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-search-location',
				'name'  => __( 'Search Location', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-server',
				'name'  => __( 'Server', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-share',
				'name'  => __( 'Share', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-share-alt',
				'name'  => __( 'Share Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-share-alt-square',
				'name'  => __( 'Share Alt Square', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-share-square',
				'name'  => __( 'Share Square', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-shield-alt',
				'name'  => __( 'Shield Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-shipping-fast',
				'name'  => __( 'Shipping Fast', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-shopping-bag',
				'name'  => __( 'Shopping Bag', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-shopping-basket',
				'name'  => __( 'Shopping Basket', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-shopping-cart',
				'name'  => __( 'Shopping Cart', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-sign-in-alt',
				'name'  => __( 'Sign In Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-sign-language',
				'name'  => __( 'Sign Language', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-sign-out-alt',
				'name'  => __( 'Sign Out Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-signal',
				'name'  => __( 'Signal', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-sitemap',
				'name'  => __( 'Sitemap', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-skating',
				'name'  => __( 'Skating', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-skiing',
				'name'  => __( 'Skiing', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-skiing-nordic',
				'name'  => __( 'Skiing Nordic', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-sliders-h',
				'name'  => __( 'Sliders-H', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-smile',
				'name'  => __( 'Smile', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-smoking-ban',
				'name'  => __( 'Smoking Ban', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-snowboarding',
				'name'  => __( 'Snowboarding', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-snowflake',
				'name'  => __( 'Snowflake', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-snowman',
				'name'  => __( 'Snowman', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-socks',
				'name'  => __( 'Socks', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-sort',
				'name'  => __( 'Sort', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-sort-alpha-down',
				'name'  => __( 'Sort Alpha Down', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-sort-alpha-down-alt',
				'name'  => __( 'Sort Alpha Down Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-sort-alpha-up',
				'name'  => __( 'Sort Alpha Up', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-sort-alpha-up-alt',
				'name'  => __( 'Sort Alpha Up Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-spell-check',
				'name'  => __( 'Spell Check', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-spider',
				'name'  => __( 'Spider', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-spinner',
				'name'  => __( 'Spinner', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-star',
				'name'  => __( 'Star', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-star-half',
				'name'  => __( 'Star Half', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-star-half-alt',
				'name'  => __( 'Star Half Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-stethoscope',
				'name'  => __( 'Stethoscope', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-sticky-note',
				'name'  => __( 'Sticky Note', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-store',
				'name'  => __( 'Store', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-store-alt',
				'name'  => __( 'Store Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-street-view',
				'name'  => __( 'Street View', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-subway',
				'name'  => __( 'Subway', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-suitcase',
				'name'  => __( 'Suitcase', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-suitcase-rolling',
				'name'  => __( 'Suitcase Rolling', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-sync',
				'name'  => __( 'Sync', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-sync-alt',
				'name'  => __( 'Sync Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-table',
				'name'  => __( 'Table', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-tag',
				'name'  => __( 'Tag', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-tags',
				'name'  => __( 'Tags', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-tasks',
				'name'  => __( 'Tasks', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-taxi',
				'name'  => __( 'Taxi', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-th',
				'name'  => __( 'Th', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-th-large',
				'name'  => __( 'Th Large', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-th-list',
				'name'  => __( 'Th List', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-thumbs-down',
				'name'  => __( 'Thumbs Down', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-thumbs-up',
				'name'  => __( 'Thumbs Up', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-thumbtack',
				'name'  => __( 'Thumbtack', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-ticket-alt',
				'name'  => __( 'Ticket Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-tint',
				'name'  => __( 'Tint', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-tools',
				'name'  => __( 'Tools', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-traffic-light',
				'name'  => __( 'Traffic Light', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-tooth',
				'name'  => __( 'Tooth', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-train',
				'name'  => __( 'Train', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-tram',
				'name'  => __( 'Tram', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-train',
				'name'  => __( 'Train', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-transgender',
				'name'  => __( 'Transgender', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-transgender-alt',
				'name'  => __( 'Transgender Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-trash',
				'name'  => __( 'Trash', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-trash-alt',
				'name'  => __( 'Trash Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-trophy',
				'name'  => __( 'Trophy', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-truck',
				'name'  => __( 'Truck', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-tshirt',
				'name'  => __( 'Tshirt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-umbrella',
				'name'  => __( 'Umbrella', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-universal-access',
				'name'  => __( 'Universal Access', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-university',
				'name'  => __( 'University', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-unlink',
				'name'  => __( 'Unlink', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-unlock',
				'name'  => __( 'Unlock', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-unlock-alt',
				'name'  => __( 'Unlock Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-upload',
				'name'  => __( 'Upload', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-user',
				'name'  => __( 'User', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-user-circle',
				'name'  => __( 'User Circle', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-user-graduate',
				'name'  => __( 'User Graduate', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-user-lock',
				'name'  => __( 'User Lock', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-user-md',
				'name'  => __( 'User Md', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-user-tie',
				'name'  => __( 'User Tie', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-users',
				'name'  => __( 'Users', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-utensils',
				'name'  => __( 'Utensils', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-vector-square',
				'name'  => __( 'Vector Square', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-venus',
				'name'  => __( 'Venus', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-venus-double',
				'name'  => __( 'Venus Double', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-venus-mars',
				'name'  => __( 'Venus Mars', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-video',
				'name'  => __( 'Video', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-voicemail',
				'name'  => __( 'Voicemail', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-walking',
				'name'  => __( 'Walking', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-wallet',
				'name'  => __( 'Wallet', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-wheelchair',
				'name'  => __( 'Wheelchair', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-wifi',
				'name'  => __( 'Wifi', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-wine-glass',
				'name'  => __( 'Wine Glass', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-wine-glass-alt',
				'name'  => __( 'Wine Glass Alt', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-wrench',
				'name'  => __( 'Wrench', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-yen-sign',
				'name'  => __( 'Yen Sign', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-yin-yang',
				'name'  => __( 'Yin Yang', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-window-restore',
				'name'  => __( 'Window Restore', 'ocean-extra' ),
			),
			array(
				'group' => 'solid',
				'id'    => 'fa-school',
				'name'  => __( 'School', 'ocean-extra' ),
			),
		);

		/**
		 * Filter FontAwesome items
		 *
		 */
		$items = apply_filters( 'oe_icon_picker_fas_items', $items );

		return $items;
	}
}
