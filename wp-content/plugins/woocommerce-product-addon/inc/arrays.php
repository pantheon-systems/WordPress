<?php
/**
 * Arrays contining settings/meta detail
 **/
 
ppom_direct_access_not_allowed();

function ppom_get_plugin_meta(){

	return array('name'	=> 'PPOM',
				'dir_name'		=> '',
				'shortname'		=> 'nm_personalizedproduct',
				'path'			=> PPOM_PATH,
				'url'			=> PPOM_URL,
				'db_version'	=> 3.12,
				'logo'			=> PPOM_URL . '/images/logo.png',
				'menu_position'	=> 90,
				'ppom_bulkquantity'	=> PPOM_WP_PLUGIN_DIR . '/ppom-addon-bulkquantity/classes/input.bulkquantity.php',
				'ppom_eventcalendar'	=> PPOM_WP_PLUGIN_DIR . '/ppom-addon-eventcalendar/classes/input.eventcalendar.php',
				'ppom_fixedprice'	=> PPOM_WP_PLUGIN_DIR . '/ppom-addon-fixedprice/classes/input.fixedprice.php',
	);
}

// Return cols for inputs
function ppom_get_input_cols() {
	
	$ppom_cols = array(2=>'2 Col',3=>'3 Col', 4=>'4 Col',5=>'5 Col',6=>'6 Col',
				7=>'7 Col',8=>'8 Col',9=>'9 Col',10=>'10 Col',11=>'11 Col',12=>'12 Col');
	
	return apply_filters('ppom_field_cols', $ppom_cols);
}

function ppom_field_visibility_options() {
	
	$visibility_options = array('everyone'	=> __('Everyone'),
								'guests'	=> __('Only Guests'),
								'members'	=> __('Only Members'),
								'roles'		=> __('By Role')
								);
								
	return apply_filters('ppom_field_visibility_options', $visibility_options);
}

function ppom_array_get_regions() {
	
	return array('AFRICA','AMERICA','ANTARCTICA','ASIA','ATLANTIC','AUSTRALIA',
				'EUROPE','INDIAN','PACIFIC');
}

// Get timezone list
function ppom_array_get_timezone_list($selected_regions, $show_time) 
{
	if( $selected_regions == 'All' ) {
	    $regions = array(
	        DateTimeZone::AFRICA,
	        DateTimeZone::AMERICA,
	        DateTimeZone::ANTARCTICA,
	        DateTimeZone::ASIA,
	        DateTimeZone::ATLANTIC,
	        DateTimeZone::AUSTRALIA,
	        DateTimeZone::EUROPE,
	        DateTimeZone::INDIAN,
	        DateTimeZone::PACIFIC,
	    );
	} else {
		$selected_regions = explode(",", $selected_regions);
		$tz_regions = array();
		
		foreach($selected_regions as $region) {
			// var_dump($region);
			switch($region) {
				case 'AFRICA':
					$tz_regions[] = DateTimeZone::AFRICA;
				break;
				case 'AMERICA':
					$tz_regions[] = DateTimeZone::AMERICA;
				break;
				case 'ANTARCTICA':
					$tz_regions[] = DateTimeZone::ANTARCTICA;
				break;
				case 'ASIA':
					$tz_regions[] = DateTimeZone::ASIA;
				break;
				case 'ATLANTIC':
					$tz_regions[] = DateTimeZone::ATLANTIC;
				break;
				case 'AUSTRALIA':
					$tz_regions[] = DateTimeZone::AUSTRALIA;
				break;
				case 'EUROPE':
					$tz_regions[] = DateTimeZone::EUROPE;
				break;
				case 'INDIAN':
					$tz_regions[] = DateTimeZone::INDIAN;
				break;
				case 'PACIFIC':
					$tz_regions[] = DateTimeZone::PACIFIC;
				break;
			}
			
		}
		
		$regions = $tz_regions;
	}
	
	// ppom_pa($regions);

    $timezones = array();
    foreach( $regions as $region )
    {
        $timezones = array_merge( $timezones, DateTimeZone::listIdentifiers( $region ) );
    }

    $timezone_offsets = array();
    foreach( $timezones as $timezone )
    {
        $tz = new DateTimeZone($timezone);
        $timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
    }

    // sort timezone by timezone name
    ksort($timezone_offsets);

    $timezone_list = array();
    foreach( $timezone_offsets as $timezone => $offset )
    {
        $offset_prefix = $offset < 0 ? '-' : '+';
        $offset_formatted = gmdate( 'H:i', abs($offset) );

        $pretty_offset = "UTC${offset_prefix}${offset_formatted}";
        
        $t = new DateTimeZone($timezone);
        $c = new DateTime(null, $t);
        $current_time = $c->format('g:i A');

		if( $show_time == 'on' ) {
        	$timezone_list[$timezone] = "(${pretty_offset}) $timezone - $current_time";
		} else {
			$timezone_list[$timezone] = "(${pretty_offset}) $timezone";
		}
    }

    return $timezone_list;
}
// Return Plupload languages
function ppom_get_plupload_languages() {
	
	return array (
								'' => 'Default',
								'ar' => 'AR',
								'az' => 'AZ',
								'bs' => 'BS',
								'cs' => 'CS',
								'cy' => 'CY',
								'da' => 'DA',
								'de' => 'DE',
								'el' => 'EL',
								'en' => 'EN',
								'es' => 'ES',
								'et' => 'ET',
								'fa' => 'FA',
								'fi' => 'FI',
								'fr' => 'FR',
								'he' => 'HE',
								'hr' => 'HR',
								'hu' => 'HU',
								'hy' => 'HY',
								'id' => 'ID',
								'it' => 'IT',
								'ja' => 'JA',
								'ka' => 'KA',
								'kk' => 'KK',
								'km' => 'KM',
								'ko' => 'KO',
								'lt' => 'LT',
								'lv' => 'LV',
								'mn' => 'MN',
								'ms' => 'MS',
								'nl' => 'NL',
								'pl' => 'PL',
								'pt_BR' => 'PT_BR',
								'ro' => 'RO',
								'ru' => 'RU',
								'sk' => 'SK',
								'sq' => 'SQ',
								'sr' => 'SR',
								'sr_RS' => 'SR_RS',
								'sv' => 'SV',
								'th_TH' => 'TH_TH',
								'tr' => 'TR',
								'uk_UA' => 'UK_UA',
								'zh_CN' => 'ZH_CN',
								'zh_TW' => 'ZH_TW',
						);
}

// PPOM Settings
function ppom_array_settings() {
	
	$ppom_settings = array(
       
		array(
			'title' => 'PPOM Labels',
			'type'  => 'title',
			'desc'	=> __('You can add your own Labels for PPOM Like Price Table etc'),
			'id'    => 'ppom_labels_settings',
		),
		
		array(
            'title'		=> __( 'Option Total', 'ppom' ),
            'type'		=> 'text',
            'desc'		=> __( 'Label For Price Table', 'ppom' ),
            'default'	=> __('Option Total', 'ppom'),
            'id'		=> 'ppom_label_option_total',
            'css'   	=> 'min-width:300px;',
			'desc_tip'	=> true,
        ),
        array(
            'title'		=> __( 'Product Price', 'ppom' ),
            'type'		=> 'text',
            'desc'		=> __( 'Label For Price Table', 'ppom' ),
            'default'	=> __('Product Price', 'ppom'),
            'id'		=> 'ppom_label_product_price',
            'css'   	=> 'min-width:300px;',
			'desc_tip'	=> true,
        ),
        array(
            'title'		=> __( 'Total', 'ppom' ),
            'type'		=> 'text',
            'desc'		=> __( 'Label For Price Table', 'ppom' ),
            'default'	=> __('Total', 'ppom'),
            'id'		=> 'ppom_label_total',
            'css'   	=> 'min-width:300px;',
			'desc_tip'	=> true,
        ),
        array(
            'title'		=> __( 'Fixed Fee', 'ppom' ),
            'type'		=> 'text',
            'desc'		=> __( 'Label For Price Table', 'ppom' ),
            'default'	=> __('Fixed Fee', 'ppom'),
            'id'		=> 'ppom_label_fixed_fee',
            'css'   	=> 'min-width:300px;',
			'desc_tip'	=> true,
        ),
        array(
            'title'		=> __( 'Discount Price', 'ppom' ),
            'type'		=> 'text',
            'desc'		=> __( 'Label For Price Table', 'ppom' ),
            'default'	=> __('Discount Price', 'ppom'),
            'id'		=> 'ppom_label_discount_price',
            'css'   	=> 'min-width:300px;',
			'desc_tip'	=> true,
        ),
        array(
            'title'		=> __( 'Total Discount', 'ppom' ),
            'type'		=> 'text',
            'desc'		=> __( 'Label For Price Table', 'ppom' ),
            'default'	=> __('Total Discount', 'ppom'),
            'id'		=> 'ppom_label_total_discount',
            'css'   	=> 'min-width:300px;',
			'desc_tip'	=> true,
        ),
        
        array(
				'title'          => __( 'Disable Bootstrap', 'ppom' ),
				'type'          => 'checkbox',
				'label'         => __( 'Yes', 'ppom' ),
				'default'       => 'no',
				'id'            => 'ppom_disable_bootstrap',
				'desc'          => __( 'Bootstrap JS is being loaded from CDN, it will disable if your site already loading it.', 'ppom' ),
			),
			
		array(
				'title'          => __( 'Disable FontAwesome', 'ppom' ),
				'type'          => 'checkbox',
				'label'         => __( 'Yes', 'ppom' ),
				'default'       => 'no',
				'id'            => 'ppom_disable_fontawesome',
				'desc'          => __( 'FontAwesome are being loaded from CDN, it will disable if your site already loading it.', 'ppom' ),
			),
        
	    array(
			'type' => 'sectionend',
			'id'   => 'ppom_labels_settings',
		),
			
		array(
            'name'     => __( 'Advance Features (PRO)', 'ppom' ),
            'type'     => 'title',
            'desc'     => __('These options will work when PRO version is installed', 'ppom'),
            'id'       => 'ppom_pro_features'
        ),
        
        array(
				'title'          => __( 'Hide Options Price?', 'ppom' ),
				'type'          => 'checkbox',
				'label'         => __( 'Yes', 'ppom' ),
				'default'       => 'no',
				'id'            => 'ppom_hide_option_price',
				'desc'          => __( 'Hides options price in Selec/Radio/Checkbox/Image display prices with label', 'ppom' ),
				
			),
			
		array(
            'title'		=> __( 'Select Option Label', 'ppom' ),
            'type'		=> 'text',
            'desc'		=> __( 'Label For Price Table', 'ppom' ),
            'default'	=> __('Select Options', 'ppom'),
            'id'		=> 'ppom_label_select_option',
            'css'   	=> 'min-width:300px;',
			'desc_tip'	=> true,
        ),
			
		array(
				'title'          => __( 'Enable PPOM REST API?', 'ppom' ),
				'type'          => 'checkbox',
				'label'         => __( 'Yes', 'ppom' ),
				'default'       => 'no',
				'id'            => 'ppom_api_enable',
				'desc'          => __( 'Check this option to enable PPOM REST API', 'ppom' ),
			),
			
		array(
            'title' => __( 'PPOM API Secret Key', 'ppom' ),
            'type' => 'text',
            'desc' => __( 'Enter any characters to create a secret key. This key must be set while requesting to API', 'ppom' ),
            'id'   => 'ppom_rest_secret_key',
            'css'   		=> 'min-width:300px;',
			'desc_tip'		=> true,
        ),
        
        array(
			'type' => 'sectionend',
			'id'   => 'ppom_pro_features',
		),
        
		);
		
	return apply_filters('ppom_settings_data', $ppom_settings);
}