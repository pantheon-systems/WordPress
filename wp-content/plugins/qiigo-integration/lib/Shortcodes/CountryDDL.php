<?php

namespace Qiigo\Plugin\Integration\Shortcodes {
	class CountryDDL extends Base {
		protected static $count = 0;
		protected static $included = false;
		protected $ddl_id;
		
		public function __construct() {
			parent::__construct('country_ddl');
		}

		public function ExtractArgs($args) {
			if( isset($args['id']) )
				$this->ddl_id = $args['id'];
			else
				$this->ddl_id = 'country_ddl_'.static::$count++;
		}
		
		public function Render() {
			if( !static::$included ) {
				wp_enqueue_script('qiigo-country-ddl', plugins_url('country-ddl.js', __FILE__), array('jquery'), '1.0', true);
				static::$included = true;
			}
			
			$qry = new \WP_Query(array(
				'nopaging' => true,
				'post_type' => 'country',
				'order' => 'ASC',
				'orderby' => 'title'
			));
?>
		<select id="<?=$this->ddl_id?>" name="<?=$this->ddl_id?>">
			<option value="">Please Select</option>
<?php
			while( $qry->have_posts() ) {
				$qry->the_post();
				$title = get_the_title();
				$redir = get_field('redirect_url');
				$sel = '';
				if( $title == 'United States' )
					$sel = ' selected="selected"';
?>
			<option value="<?=$title?>"<?=((isset($redir) && trim($redir) != '') ? ' data-redirect="'.\htmlspecialchars($redir).'"' : '').$sel?>><?=$title?></option>
<?php
			}
?>
		</select>
		<script type="text/javascript">
			window.countryDDLs = window.countryDDLs || [];
			window.countryDDLs.push('<?=$this->ddl_id?>');
		</script>
<?php
			wp_reset_postdata();
		}
	}
}
