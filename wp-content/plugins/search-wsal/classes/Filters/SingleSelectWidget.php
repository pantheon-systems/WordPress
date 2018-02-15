<?php

class WSAL_AS_Filters_SingleSelectWidget extends WSAL_AS_Filters_AbstractWidget {
	
	protected $items = array();
	
	protected function RenderField(){
		?><select class="<?php echo $this->GetSafeName(); ?>"
				id="<?php echo esc_attr($this->id); ?>"
				data-prefix="<?php echo esc_attr($this->prefix); ?>">
			<option value=""></option><?php
			foreach($this->items as $value => $text){
				if(is_object($text)){
					// render group (and items)
					echo '<optgroup label="' . esc_attr($value) . '">';
					foreach($text->items as $s_value => $s_text)
						echo '<option value="' . esc_attr($s_value) . '">' . esc_html($s_text) . '</option>';
					echo '</optgroup>';
				}else{
					// render item
					echo '<option value="' . esc_attr($value) . '">' . esc_html($text) . '</option>';
				}
			}
		?></select><?php
	}
	
	public function Add($text, $value){
		$this->items[$value] = $text;
	}
	
	public function AddGroup($name){
		$this->items[$name] = new WSAL_AS_Filters_SingleSelectWidgetGroup();
		return $this->items[$name];
	}
	
	public function StaFooter(){
		?><script type="text/javascript">
			window.WsalAs.Attach(function(){
				jQuery('select.<?php echo $this->GetSafeName(); ?>').change(function(){
					if(this.value){
						WsalAs.AddFilter(jQuery(this).attr('data-prefix') + ':' + this.value);
						this.value = '';
					}
				});
			});
		</script><?php
	}
	
}

class WSAL_AS_Filters_SingleSelectWidgetGroup {
	public $items = array();
	
	public function Add($text, $value){
		$this->items[$value] = $text;
	}
}