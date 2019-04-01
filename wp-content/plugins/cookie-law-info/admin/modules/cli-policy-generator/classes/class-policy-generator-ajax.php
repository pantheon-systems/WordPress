<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Cookie_Law_Info_Policy_Generator_Ajax extends Cookie_Law_Info_Cli_Policy_Generator
{
	
	public function __construct()
	{		
		add_action('wp_ajax_cli_policy_generator',array($this,'ajax_policy_generator'));
	}

	/*
	*
	* Main Ajax hook for processing requests
	*/
	public function ajax_policy_generator()
	{
		$out=array(
			'response'=>false,
			'message'=>__('Unable to handle your request.','cookie-law-info'),
		);
		$non_json_response=array();
		if(isset($_POST['cli_policy_generator_action']))
		{
			$cli_policy_generator_action=$_POST['cli_policy_generator_action'];
			$allowed_actions=array('autosave_contant_data','save_contentdata','get_policy_pageid');			
			if(in_array($cli_policy_generator_action,$allowed_actions) && method_exists($this,$cli_policy_generator_action))
			{
				$out=$this->{$cli_policy_generator_action}();
			}
		}
		if(in_array($cli_policy_generator_action,$non_json_response))
		{
			echo is_array($out) ? $out['message'] : $out;
		}else
		{
			echo json_encode($out);
		}		
		exit();
	}

	/*
	*	@since 1.7.4
	*	Get current policy page ID (Ajax-main)
	*	This is used to update the hidden field for policy page id. (In some case user press back button)
	*/
	public function get_policy_pageid()
	{
		$page_id=Cookie_Law_Info_Cli_Policy_Generator::get_cookie_policy_pageid();
		$policy_page_status=get_post_status($page_id);
		if($policy_page_status && $policy_page_status!='trash')
		{

		}else
		{
			$page_id=0;
		}
		return array(
			'response'=>true,
			'page_id'=>$page_id,
		);
	}

	/*
	*	@since 1.7.4
	*	Save current data (Ajax-main)
	*/
	public function save_contentdata()
	{
		$out=array(
			'response'=>true,
			'er'=>''
		);
		$content_data=isset($_POST['content_data']) ? $_POST['content_data'] : array();
		$page_id=(int) isset($_POST['page_id']) ? $_POST['page_id']*1 : 0;
		$enable_webtofee_powered_by=(int) isset($_POST['enable_webtofee_powered_by']) ? $_POST['enable_webtofee_powered_by']*1 : 0;
		$id=wp_insert_post(
			array(
				'ID'=>$page_id, //if ID is zero it will create new page otherwise update
				'post_title'=>'Cookie Policy',
				'post_type'=>'page',
				'post_content'=>Cookie_Law_Info_Cli_Policy_Generator::generate_page_content($enable_webtofee_powered_by,$content_data,0),
				'post_status' => 'draft', //default is draft
			)
		);
		if(is_wp_error($id))
		{
			$out=array(
				'response'=>false,
				'er'=>__('Error','cookie-law-info'),
				//'er'=>$id->get_error_message(),
			);
		}else
		{
			Cookie_Law_Info_Cli_Policy_Generator::set_cookie_policy_pageid($id);
			$out['url']=get_edit_post_link($id);
		}
		return $out;
	}

	

	/*
	*	@since 1.7.4
	*	Autosave Current content to session (Ajax-main)
	*/
	public function autosave_contant_data()
	{
		global $wpdb;
		$scan_table=$wpdb->prefix.$this->main_tb;
		$out=array(
			'response'=>true,
			'er'=>''
		);
		$content_data=isset($_POST['content_data']) ? $_POST['content_data'] : array();
		$page_id=isset($_POST['page_id']) ? $_POST['page_id'] : '';
		$enable_webtofee_powered_by=(int) isset($_POST['enable_webtofee_powered_by']) ? $_POST['enable_webtofee_powered_by']*1 : 0;
		if(is_array($content_data))
		{
			$content_html=Cookie_Law_Info_Cli_Policy_Generator::generate_page_content($enable_webtofee_powered_by,$content_data);
			update_option('cli_pg_content_data',$content_html);
		}else
		{
			$out=array(
				'response'=>false,
				'er'=>__('Error','cookie-law-info')
			);
		}
		return $out;
	}

}
new Cookie_Law_Info_Policy_Generator_Ajax();