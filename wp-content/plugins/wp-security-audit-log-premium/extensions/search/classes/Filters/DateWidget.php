<?php
/**
 * Class: Date Widget
 *
 * Date widget for search extension.
 *
 * @since 1.0.0
 * @package search-wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WSAL_AS_Filters_DateWidget
 *
 * @package search-wsal
 */
class WSAL_AS_Filters_DateWidget extends WSAL_AS_Filters_AbstractWidget {

	protected function RenderField() {
		$date_format = WSAL_SearchExtension::GetInstance()->GetDateFormat();
		?>
		<input type="text"
		   class="<?php echo esc_attr( $this->GetSafeName() ); ?>"
		   id="<?php echo esc_attr( $this->id ); ?>"
		   placeholder="<?php echo esc_attr( $date_format ); ?>"
		   data-prefix="<?php echo esc_attr( $this->prefix ); ?>"/>
		<?php
	}

	public function StaFooter() {
		?>
		<script type="text/javascript">
		window.WsalAs.Attach(function(){
			jQuery('input.<?php echo esc_attr( $this->GetSafeName() ); ?>').change(function(){
				if(this.value){
					WsalAs.AddFilter(jQuery(this).attr('data-prefix') + ':' + this.value);
					this.value = '';
				}
			});
		});
		</script>
		<?php
	}
}
