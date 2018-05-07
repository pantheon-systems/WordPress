<?php
/**
 * Class: Abstract Widget
 *
 * Abstract widget class.
 *
 * @since 1.0.0
 * @package search-wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WSAL_AS_Filters_AbstractWidget
 *
 * @package search-wsal
 */
abstract class WSAL_AS_Filters_AbstractWidget {

	/**
	 * Widget ID.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * The filter for this class.
	 *
	 * @var WSAL_AS_Filters_AbstractFilter
	 */
	public $filter;

	/**
	 * Widget title/label.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Value prefix for the filter.
	 *
	 * @var string
	 */
	public $prefix;

	/**
	 * Data loader callback.
	 *
	 * @var callable|null
	 */
	protected $data_loader_func = null;

	protected $data_loader_data = null;

	protected static $counter = 0;

	public function __construct( WSAL_AS_Filters_AbstractFilter $filter, $prefix, $title = '' ) {
		$this->filter = $filter;
		$this->prefix = $prefix;
		$this->id = 'wsal_as_widget_' . $this->prefix;
		$this->title = $title;
	}

	/**
	 * Set data loading callback.
	 *
	 * @param callable $ldr A callback that will receive this widget as first parameter and is supposed to populate this widget.
	 * @param mixed    $usr Some data to be passed to callback as 2nd parameter.
	 */
	public function SetDataLoader( $ldr, $usr = null ) {
		$this->data_loader_func = $ldr;
		$this->data_loader_data = $usr;
	}

	protected $data_loaded = false;

	/**
	 * Called when widget needs to be populated.
	 *
	 * @param type $forceLoad Force (re)loading data.
	 */
	public function LoadData( $forceLoad = false ) {
		if ( ( ! $this->data_loaded || $forceLoad ) && $this->data_loader_func ) { // Avoid loading data multiple times.
			call_user_func( $this->data_loader_func, $this, $this->data_loader_data );
			$this->data_loaded = true;
		}
	}

	/**
	 * Handle ajax calls here.
	 */
	public function HandleAjax(){ }

	/**
	 * Renders widget HTML directly.
	 */
	public function Render() {
		$this->LoadData();
		$this->RenderLabel();
		$this->RenderField();
	}

	/**
	 * Renders widget label (left).
	 */
	protected function RenderLabel() {
		?>
		<label for="<?php echo esc_attr( $this->id ); ?>">
			<?php echo esc_html( $this->title ); ?>
		</label>
		<?php
	}

	/**
	 * Renders widget field (right).
	 */
	protected function RenderField() {
		?>
		<input type="text" id="<?php echo esc_attr( $this->id ); ?>"
				data-prefix="<?php echo esc_attr( $this->prefix ); ?>"/>
		<?php
	}

	/**
	 * Called only once per class.
	 */
	public function StaHeader(){ }

	/**
	 * Called only once per class.
	 */
	public function StaFooter(){ }

	/**
	 * Called only once per instance.
	 */
	public function DynHeader(){ }

	/**
	 * Called only once per instance.
	 */
	public function DynFooter(){ }

	/**
	 * Generates a widget name.
	 *
	 * @return string
	 */
	public function GetSafeName() {
		return strtolower( get_class( $this ) );
	}
}
