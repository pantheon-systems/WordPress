<?php

use Wpae\App\Service\VariationOptions\VariationOptionsFactory;

if ( ! class_exists('XmlExportFiltering') )
{
	class XmlExportFiltering
	{
		private $queryWhere = "";
		private $queryJoin = array();
		private $userWhere = "";
		private $userJoin = array();
		private $options;		
		private $tax_query = false;
		private $meta_query = false;		

		public function __construct($args = array())
		{
			$this->options = $args;						

			add_filter('wp_all_export_single_filter_rule', array(&$this, 'parse_rule_value'), 10, 1);
		}

		public function parseQuery()
		{
			// do not apply filters for child exports
			if ( ! empty(XmlExportEngine::$exportRecord->parent_id) )
			{
				$this->queryWhere = XmlExportEngine::$exportRecord->options['whereclause'];
				$this->queryJoin  = XmlExportEngine::$exportRecord->options['joinclause'];
				return;
			}

			global $wpdb;

			// disable exports for orphaned variations entirely
			if ( ! XmlExportEngine::$is_comment_export and ! XmlExportEngine::$is_user_export and ! empty(XmlExportEngine::$post_types) and @in_array("product", XmlExportEngine::$post_types) and class_exists('WooCommerce'))
			{					
				$tmp_queryWhere = $this->queryWhere;
				$tmp_queryJoin  = $this->queryJoin;							
				
				$this->queryJoin = array();

				$this->queryWhere = " $wpdb->posts.post_type = 'product' AND (($wpdb->posts.post_status <> 'trash' AND $wpdb->posts.post_status <> 'auto-draft'))";												
				
//				$where = $this->queryWhere;
//				$join  = implode( ' ', array_unique( $this->queryJoin ) );
//
//				$this->queryWhere = $tmp_queryWhere;
//				$this->queryJoin  = $tmp_queryJoin;
//
//				$this->queryWhere .= " AND $wpdb->posts.post_type = 'product' OR ($wpdb->posts.post_type = 'product_variation' AND $wpdb->posts.post_parent IN (
//					SELECT DISTINCT $wpdb->posts.ID
//					FROM $wpdb->posts $join
//					WHERE $where
//				)) GROUP BY $wpdb->posts.ID";

				$where = $this->queryWhere;
				$join  = implode( ' ', array_unique( $this->queryJoin ) );

				$this->queryWhere = $tmp_queryWhere;
				$this->queryJoin  = $tmp_queryJoin;

				$vatiationOptionsFactory = new VariationOptionsFactory();
				$variationOptions = $vatiationOptionsFactory->createVariationOptions(PMXE_EDITION);

				$this->queryWhere .= $variationOptions->getQueryWhere($wpdb, $where, $join, false);

			}

		}

		public static function render_filtering_block( $engine, $isWizard, $post, $is_on_template_screen = false )
		{
			?>
			<input type="hidden" class="hierarhy-output" name="filter_rules_hierarhy" value="<?php echo esc_html($post['filter_rules_hierarhy']);?>"/>
			<?php

			if ( $isWizard or $post['export_type'] != 'specific' ) return;
			
			?>
			<div class="wpallexport-collapsed wpallexport-section closed">
				<div class="wpallexport-content-section wpallexport-filtering-section" <?php if ($is_on_template_screen):?>style="margin-bottom: 10px;"<?php endif; ?>>
					<div class="wpallexport-collapsed-header" style="padding-left: 25px;">
						<h3><?php _e('Filtering Options','wp_all_export_plugin');?></h3>	
					</div>
					<div class="wpallexport-collapsed-content" style="padding: 0;">
						<div class="wpallexport-collapsed-content-inner">									
							<?php include_once PMXE_ROOT_DIR . '/views/admin/export/blocks/filters.php'; ?>
						</div>											
					</div>
				</div>
			</div>	
			<?php
		}

		/**
	     * __get function.
	     *
	     * @access public
	     * @param mixed $key
	     * @return mixed
	     */
	    public function __get( $key ) {
	        return $this->get( $key );
	    }	

	    /**
	     * Get a session variable
	     *
	     * @param string $key
	     * @param  mixed $default used if the session variable isn't set
	     * @return mixed value of session variable
	     */
	    public function get( $key, $default = null ) {        
	        return isset( $this->{$key} ) ? $this->{$key} : $default;
	    }
	}
}