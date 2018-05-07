<?php
/**
 * Class: Autocomplete Widget
 *
 * Autocomplete Widget for search extension.
 *
 * @since 1.0.0
 * @package search-wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WSAL_AS_Filters_AutoCompleteWidget
 *
 * @package search-wsal
 */
class WSAL_AS_Filters_AutoCompleteWidget extends WSAL_AS_Filters_AbstractWidget {

	public $user_query = '';

	protected $loaded_data = array();

	public function Add( $nice, $tokens ) {
		if ( is_string( $tokens ) ) {
			$tokens = array( $tokens );
		}
		$this->loaded_data[] = array(
			'value' => $nice,
			'tokens' => array_unique( $tokens ),
		);
	}

	public function HandleAjax() {
		$this->user_query = $_REQUEST['search'];
		$this->LoadData( true );
		header( 'Content-Type: application/json' );
		die( json_encode( $this->loaded_data ) );
	}

	protected function RenderField() {
		?>
		<input type="text" autocomplete="off"
			class="<?php echo esc_attr( $this->GetSafeName() ); ?>"
			id="<?php echo esc_attr( $this->id ); ?>"
			name="<?php echo esc_attr( $this->id ); ?>"
			data-prefix="<?php echo esc_attr( $this->prefix ); ?>"
			data-filter="<?php echo esc_attr( $this->filter->GetSafeName() ); ?>"/>
		<?php
	}

	public function StaFooter() {
		?>
		<script type="text/javascript">
		window.WsalAs.Attach(function(){
			jQuery("input.<?php echo $this->GetSafeName(); ?>").each(function(){
				var AsacCtrl = jQuery(this);
				if(!AsacCtrl.attr('data-asac-bound')){
					AsacCtrl.attr('data-asac-bound', '1');
					var filter = jQuery(this).attr('data-filter');
					var widget = <?php echo json_encode( $this->GetSafeName() ); ?>;
					var source = new Bloodhound({
						datumTokenizer: function (datum) {
							return Bloodhound.tokenizers.whitespace(datum.value);
						},
						queryTokenizer: Bloodhound.tokenizers.whitespace,
						limit: 5,
						prefetch: WsalAs.AjaxUrl
							+ '?action=' + WsalAs.AjaxAction
							+ '&filter=' + filter
							+ '&widget=' + widget
							+ '&search=' + '%QUERY'
					});

					source.initialize();

					AsacCtrl.typeahead(null, {
						hint: true,
						highlight: true,
						displayKey: 'value',
						source: source.ttAdapter()
					})
					.on('typeahead:selected', function(ev, sg, dn){
						var $this = jQuery(this);
						if($this.val()){
							WsalAs.AddFilter($this.attr('data-prefix') + ':' + $this.val());
							$this.val('');
						}
					});
				}
			});
		});
		</script>
		<?php
	}
}
