<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2017 ThemePunch
 */
 
if( !defined( 'ABSPATH') ) exit();

class RevSliderLoadBalancer {
	
	public $servers = array();
	
	/**
	 * set the server list on construct
	 **/
	public function __construct(){
		$this->servers = get_option('revslider_servers', array());
		$this->servers = (empty($this->servers)) ? array('themepunch.tools') : $this->servers; //, 'themepunch-ext-a.tools'
	}
	
	/**
	 * get the url depending on the purpose, here with key, you can switch do a different server
	 **/
	public function get_url($purpose, $key = 0){
		$url = 'https://';
		
		$use_url = (!isset($this->servers[$key])) ? reset($this->servers) : $this->servers[$key];
		
		//$use_url = 'themepunch.tools';
		switch($purpose){
			case 'updates':
				$url .= 'updates.';
				break;
			case 'templates':
				$url .= 'templates.';
				break;
			case 'library':
				$url .= 'library.';
				break;
			default:
				return false;
		}
		
		$url .= $use_url;
		
		return $url;
	}
	
	/**
	 * refresh the server list to be used, will be done once in a month
	 **/
	public function refresh_server_list($force = false){
		global $wp_version;
		
		$last_check = get_option('revslider_server_refresh', false);
		
		if($force === true || $last_check === false || time() - $last_check > 60 * 60 * 24 * 14){
			//$url = $this->get_url('updates');
			$url		= 'https://updates.themepunch.tools';
			$count		= 0;
			/*$response	= false;
			do{*/
				$request	= wp_remote_post($url.'/get_server_list.php', array(
					'user-agent' => 'WordPress/'.$wp_version.'; '.get_bloginfo('url'),
					'body' => array(
						'item' => urlencode(RS_PLUGIN_SLUG),
						'version' => urlencode(RevSliderGlobals::SLIDER_REVISION)
					),
					'timeout' => 45
				));
				if(!is_wp_error($request)){
					if($response = maybe_unserialize($request['body'])){
						$list = json_decode($response, true);
						update_option('revslider_servers', $list);
					}
				}/*else{
					$url = $this->get_url('updates');
				}
				$count++;
			}while($response === false && $count < 5);*/
			
			update_option('revslider_server_refresh', time());
		}
	}
	
	/**
	 * move the server list, to take the next server as the one currently seems unavailable
	 **/
	public function move_server_list(){
		
		$servers = $this->servers;
		
		$a = array_shift($servers);
		$servers[] = $a;
		
		$this->servers = $servers;
		update_option('revslider_servers', $servers);
	}
}

?>