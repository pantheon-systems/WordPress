<?php
/**
 * Policy generator
 *
 * @link       http://cookielawinfo.com/
 * @since      1.7.4
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
include( plugin_dir_path( __FILE__ ).'classes/class-policy-generator-ajax.php');
include( plugin_dir_path( __FILE__ ).'classes/class-preview-page.php');
class Cookie_Law_Info_Cli_Policy_Generator
{
	public static $policy_pageid='cli_pg_policy_page_id';
	public function __construct()
	{
		add_action('admin_menu',array($this,'add_admin_pages'));
		add_filter('display_post_states',array($this,'add_display_post_states'),10,2);
	}

	/**
	 * Add a post display state for Cookie Policy page in the page list table.
	 *
	 * @since 1.7.4
	 **/
	public function add_display_post_states($post_states,$post) 
	{
		if($post->ID==$this->get_cookie_policy_pageid())
		{
			$post_states['page_for_cookie_policy']='Cookie Policy Page';
		}	
        return $post_states;
	}

	private static function gen_page_html($content_data_arr=array(),$render_shortcode=1)
	{
		$html='';
		foreach($content_data_arr as $key=>$value) 
		{
			$html.='<h3>'.$value['hd'].'</h3>';
			$html.='<div>'.($render_shortcode==1 ? do_shortcode(stripslashes($value['content'])) : stripslashes($value['content'])).'</div>';
		}
		return $html;
	}

	public static function get_page_content()
	{
		$contant_val=get_option('cli_pg_content_data');
		$html=isset($contant_val) ? $contant_val : '';
		return $html;
	}

	/**
    * Generating content for page from session/post
    * @since 1.7.4
    * @return string
    */
    public static function generate_page_content($powered_by,$content_data_post_arr=array(),$render_shortcode=1)
    {
    	$html='<div class="cli_pg_page_contaner">';
    	$html.=self::gen_page_html($content_data_post_arr,$render_shortcode);
        $html.=($powered_by==1 ? '[webtoffee_powered_by]' : '').'</div>';
		return $html;
    }

    /**
    * Get policy page id from option table
    * @since 1.7.4
    * @return int
    */
	public static function get_cookie_policy_pageid()
	{
		$pageid=get_option(self::$policy_pageid);
		if($pageid===false)
		{
			return 0;
		}else
		{
			return $pageid;
		}
	}

	/**
    * Update policy page id
    * @since 1.7.4
    * 
    */
	public static function set_cookie_policy_pageid($pageid)
	{
		update_option(self::$policy_pageid,$pageid);
	}


	/**
	 * Add administration menus
	 *
	 * @since 1.7.4
	 **/
	public function add_admin_pages() 
	{
        add_submenu_page(
			'edit.php?post_type='.CLI_POST_TYPE,
			__('Policy generator','cookie-law-info'),
			__('Policy generator','cookie-law-info'),
			'manage_options',
			'cookie-law-info-policy-generator',
			array($this,'policy_generator_page')
		);		
	}

	/*
	*
	* Policy generator page (Admin page)
	*
	* @since 1.7.4
	**/
	public function policy_generator_page()
	{
		add_editor_style(plugin_dir_url( __FILE__ ).'assets/css/editor-style.css');
		wp_enqueue_script('cli_policy_generator',plugin_dir_url( __FILE__ ).'assets/js/cli-policy-generator-admin.js', array( 'jquery'),CLI_VERSION,false);
		wp_enqueue_script($this->plugin_name);
		$params = array(
	        'nonces' => array(
	            'cli_policy_generator' => wp_create_nonce('cli_policy_generator'),
	        ),
	        'ajax_url' => admin_url('admin-ajax.php'),
	        'page_id' =>'',
	        'labels'=>array(
	        	'error'=>__('Error','cookie-law-info'),
	        	'success'=>__('Success','cookie-law-info'),
		    )
	    );
		wp_localize_script('cli_policy_generator','cli_policy_generator',$params);
		$default_data=$this->process_default_data();
		$preview_url=home_url().'/cli-policy-preview/';
		include plugin_dir_path( __FILE__ ).'views/policy-generator.php';
	}

	/*
	*
	* Process default data, read template file
	* @since 1.7.4
	**/
	private function process_default_data()
	{
		$data_path=plugin_dir_path( __FILE__ ).'data/';
		include $data_path.'data.policy-generator.php';
		$out=array();
		if($cli_pg_default_data)
		{
			foreach($cli_pg_default_data as $data)
			{
				$temp=$data;
				//reading template file
				if($data['body']=="" && $data['body_file']!="" && file_exists($data_path.$data['body_file'])) 
				{
					ob_start();
					include $data_path.$data['body_file'];
					$temp['body']=$this->process_data(ob_get_clean());
				}
				$out[]=$temp;
			}
		}
		return $out;
	}

	/*
	*
	* Process default data, read template file
	* @since 1.7.4
	**/
	private function process_data($txt)
	{
		$home_url=home_url();
		$home_domain=parse_url($home_url,PHP_URL_HOST);
		$txt=str_replace(array(
			'{{site_url}}'
		),
		array(
			'<a href="'.$home_url.'">'.$home_domain.'</a>'
		),$txt);
		//$txt=do_shortcode($txt);
		return $txt;
	}
}
$cli_pg=new Cookie_Law_Info_Cli_Policy_Generator();
$cli_pg->plugin_name=$this->plugin_name;
