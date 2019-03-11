<?php
/**
 * Compatibility with PixelYourSite
 * @link http://www.pixelyoursite.com/
 * @since 1.7.2
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
class Cookie_Law_Info_PixelYourSite
{
	public function __construct()
    {
    	if($this->is_plugin_active())
    	{   
            $this->add_main_filter();
    	}
    }

    /*
    *
    * Add main filter based on GDPR main cookie (accept/reject)
    * @since 1.7.2
    */
    private function add_main_filter()
    {
    	$viewed_cookie="viewed_cookie_policy";
    	$out_fn='__return_true'; //block it
    	$out=true;
    	if(isset($_COOKIE[$viewed_cookie]))
    	{
    		if($_COOKIE[$viewed_cookie]=='yes')
    		{
    			$out_fn='__return_false'; //remove blocking
    			$out=false;
    		}
    	}
    	add_filter('pys_disable_by_gdpr',$out_fn,10,2);
    	return $out;
    }


    /*
    *
    * Checks PixelYourSite plugin is active
    * @since 1.7.2
    */
    private function is_plugin_active()
    {
    	if(!function_exists('is_plugin_active')) 
    	{
			include_once(ABSPATH.'wp-admin/includes/plugin.php');
		}		
		return is_plugin_active('pixelyoursite-pro/pixelyoursite-pro.php') || is_plugin_active('pixelyoursite/facebook-pixel-master.php');
    }
}
new Cookie_Law_Info_PixelYourSite();