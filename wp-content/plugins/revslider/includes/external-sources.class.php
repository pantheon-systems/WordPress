<?php
/**
 * External Sources Input Classes for Back and Front End
 * @since: 5.0
 **/

if( !defined( 'ABSPATH') ) die();

/**
 * Facebook
 *
 * with help of the API this class delivers album images from Facebook
 *
 * @package    socialstreams
 * @subpackage socialstreams/facebook
 * @author     ThemePunch <info@themepunch.com>
 */

class RevSliderFacebook {
	/**
	 * Stream Array
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $stream    Stream Data Array
	 */
	private $stream;

	/**
	* Transient seconds
	*
	* @since    1.0.0
	* @access   private
	* @var      number    $transient Transient time in seconds
	*/
	private $transient_sec;

	public function __construct($transient_sec = 1200) {
	    $this->transient_sec = 	$transient_sec;
	} 

	/**
	 * Get User ID from its URL
	 *
	 * @since    1.0.0
	 * @param    string    $user_url URL of the Page
	 */
	public function get_user_from_url($user_url){
		$theid = str_replace("https", "", $user_url);
		$theid = str_replace("http", "", $theid);
		$theid = str_replace("://", "", $theid);
		$theid = str_replace("www.", "", $theid);
		$theid = str_replace("facebook", "", $theid);
		$theid = str_replace(".com", "", $theid);
		$theid = str_replace("/", "", $theid);
		$theid = explode("?", $theid);
		return trim($theid[0]);
	}

	/**
	 * Get Photosets List from User
	 *
	 * @since    1.0.0
	 * @param    string    $user_id 	Facebook User id (not name)
	 * @param    int       $item_count 	number of photos to pull
	 */
	public function get_photo_sets($user_id,$item_count=10,$app_id,$app_secret){
		//photoset params
		$oauth = wp_remote_fopen("https://graph.facebook.com/oauth/access_token?type=client_cred&client_id=".$app_id."&client_secret=".$app_secret);
    $oauth = json_decode($oauth);
		$url = "https://graph.facebook.com/$user_id/albums?access_token=".$oauth->access_token;
		$photo_sets_list = json_decode(wp_remote_fopen($url));
		//echo '<pre>';
		//print_r($photo_sets_list);
		//echo '</pre>';
		
		if(isset($photo_sets_list->data))
			return $photo_sets_list->data;
		else return '';
	}

	/**
	 * Get Photoset Photos
	 *
	 * @since    5.1.1 
	 * @param    string    $photo_set_id 	Photoset ID
	 * @param    int       $item_count 	number of photos to pull
	 */
	public function get_photo_set_photos($photo_set_id,$item_count=10,$app_id,$app_secret){
    $oauth = wp_remote_fopen("https://graph.facebook.com/oauth/access_token?type=client_cred&client_id=".$app_id."&client_secret=".$app_secret);
    $oauth = json_decode($oauth);
    $url = "https://graph.facebook.com/$photo_set_id/photos?fields=photos&access_token=".$oauth->access_token."&fields=id,from,message,picture,link,name,icon,privacy,type,status_type,object_id,application,created_time,updated_time,is_hidden,is_expired,comments.limit(1).summary(true),likes.limit(1).summary(true)";

		$transient_name = 'revslider_' . md5($url);

		if ($this->transient_sec > 0 && false !== ($data = get_transient( $transient_name)))
			return ($data);

		$photo_set_photos = json_decode(wp_remote_fopen($url));

    /* 
    echo '<pre>';
    print_r($photo_set_photos);
    echo '</pre>';
    */
		if(isset($photo_set_photos->data)){
			set_transient( $transient_name, $photo_set_photos->data, $this->transient_sec );
			return $photo_set_photos->data;
		}
		else return '';
	}

	/**
	 * Get Photosets List from User as Options for Selectbox
	 *
	 * @since    1.0.0
	 * @param    string    $user_url 	Facebook User id (not name)
	 * @param    int       $item_count 	number of photos to pull
	 */
	public function get_photo_set_photos_options($user_url,$current_album,$app_id,$app_secret,$item_count=99){
		$user_id = $this->get_user_from_url($user_url);
		$photo_sets = $this->get_photo_sets($user_id,999,$app_id,$app_secret);
    if(empty($current_album)) $current_album = "";
		$return = array();
		if(is_array($photo_sets)){
			foreach($photo_sets as $photo_set){
				$return[] = '<option title="'.$photo_set->name.'" '.selected( $photo_set->id , $current_album , false ).' value="'.$photo_set->id.'">'.$photo_set->name.'</option>"';
			}
		}
		return $return;
	}
	

	/**
	 * Get Feed
	 *
	 * @since    1.0.0
	 * @param    string    $user 	User ID
	 * @param    int       $item_count 	number of itmes to pull
	 */
	public function get_photo_feed($user,$app_id,$app_secret,$item_count=10){
		$oauth = wp_remote_fopen("https://graph.facebook.com/oauth/access_token?type=client_cred&client_id=".$app_id."&client_secret=".$app_secret);
    $oauth = json_decode($oauth);
    $url = "https://graph.facebook.com/$user/feed?access_token=".$oauth->access_token."&fields=id,from,message,picture,link,name,icon,privacy,type,status_type,object_id,application,created_time,updated_time,is_hidden,is_expired,comments.limit(1).summary(true),likes.limit(1).summary(true)";

		$transient_name = 'revslider_' . md5($url);
		if ($this->transient_sec > 0 && false !== ($data = get_transient( $transient_name)))
			return ($data);

		$feed = json_decode(wp_remote_fopen($url));

		if(isset($feed->data)){
			set_transient( $transient_name, $feed->data, $this->transient_sec );
			return $feed->data;
		}
		else return '';
	}

	/**
	 * Decode URL from feed
	 *
	 * @since    1.0.0
	 * @param    string    $url 	facebook Output Data
	 */
	private function decode_facebook_url($url) {
		$url = str_replace('u00253A',':',$url);
		$url = str_replace('\u00255C\u00252F','/',$url);
		$url = str_replace('u00252F','/',$url);
		$url = str_replace('u00253F','?',$url);
		$url = str_replace('u00253D','=',$url);
		$url = str_replace('u002526','&',$url);
		return $url;
	}
}  // End Class

/**
 * Twitter
 *
 * with help of the API this class delivers all kind of tweeted images from twitter
 *
 * @package    socialstreams
 * @subpackage socialstreams/twitter
 * @author     ThemePunch <info@themepunch.com>
 */

class RevSliderTwitter {

  /**
   * Consumer Key
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $consumer_key    Consumer Key
   */
  private $consumer_key;

  /**
   * Consumer Secret
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $consumer_secret    Consumer Secret
   */
  private $consumer_secret;

  /**
   * Access Token
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $access_token    Access Token
   */
  private $access_token;

  /**
   * Access Token Secret
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $access_token_secret    Access Token Secret
   */
  private $access_token_secret;

  /**
   * Twitter Account
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $twitter_account    Account User Name
   */
  private $twitter_account;

  /**
   * Transient seconds
   *
   * @since    1.0.0
   * @access   private
   * @var      number    $transient Transient time in seconds
   */
  private $transient_sec;

  /**
   * Stream Array
   *
   * @since    1.0.0
   * @access   private
   * @var      array    $stream    Stream Data Array
   */
  private $stream;

  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param      string    $consumer_key Twitter App Registration Consomer Key
   * @param      string    $consumer_secret Twitter App Registration Consomer Secret
   * @param      string    $access_token Twitter App Registration Access Token
   * @param      string    $access_token_secret Twitter App Registration Access Token Secret
   */
  public function __construct($consumer_key,$consumer_secret,$access_token,$access_token_secret,$transient_sec = 1200) {
    $this->consumer_key         =   $consumer_key;
    $this->consumer_secret      =   $consumer_secret;
    $this->access_token         =   $access_token;
    $this->access_token_secret  =   $access_token_secret;
    $this->transient_sec		= 	$transient_sec;
  } 

  /**
   * Get Tweets
   *
   * @since    1.0.0
   * @param    string    $twitter_account   Twitter account without trailing @ char
   */
  public function get_public_photos($twitter_account,$include_rts,$exclude_replies,$count,$imageonly){
    
    //require_once( 'class-wp-twitter-api.php' );
	// Set your personal data retrieved at https://dev.twitter.com/apps
	$credentials = array(
	  'consumer_key'    =>  $this->consumer_key,
	  'consumer_secret' =>    $this->consumer_secret 
	);
	// Let's instantiate our class with our credentials
	$twitter_api = new RevSliderTwitterApi( $credentials , $this->transient_sec);
	
    $include_rts = $include_rts=="on" ? "true" : "false"; 
    $exclude_replies = $include_rts=="on" ? "false" : "true"; 

	$query = '&tweet_mode=extended&count=500&include_entities=true&include_rts='.$include_rts.'&exclude_replies='.$exclude_replies.'&screen_name='.$twitter_account;
	
	$tweets = $twitter_api->query( $query );

    if(!empty($tweets)){
    	return $tweets;
    }
	else return '';
  }


  /**
   * Find Key in array and return value (multidim array possible)
   *
   * @since    1.0.0
   * @param    string    $key   Needle
   * @param    array     $form  Haystack
   */
  public function array_find_element_by_key($key, $form) {
	  if (is_array($form) &&  array_key_exists($key, $form)) {
	    $ret = $form[$key];
	    return $ret;
	  }
	  if(is_array($form))
	      foreach ($form as $k => $v) {
	        if (is_array($v)) {
	          $ret = $this->array_find_element_by_key($key, $form[$k]);
	          if ($ret) {
	            return $ret;
	          }
	        }
	      }
	  return FALSE;
  }
} // End Class

/**
* Class WordPress Twitter API
*
* https://github.com/micc83/Twitter-API-1.1-Client-for-Wordpress/blob/master/class-wp-twitter-api.php
* @version 1.0.0
*/
class RevSliderTwitterApi {
  
        var $bearer_token,
                
        // Default credentials
        $args = array(
                'consumer_key'       =>        'default_consumer_key',
                'consumer_secret'    =>        'default_consumer_secret'
        ),
        
        // Default type of the resource and cache duration
        $query_args = array(
                'type'               =>        'statuses/user_timeline',
                'cache'              =>        1800
        ),

        $has_error = false;
        
        /**
         * WordPress Twitter API Constructor
         *
         * @param array $args
         */
        public function __construct( $args = array() , $transient_sec = 1200 ) {
                
                if ( is_array( $args ) && !empty( $args ) )
                        $this->args = array_merge( $this->args, $args );
                
                if ( ! $this->bearer_token = get_option( 'twitter_bearer_token' ) )
                        $this->bearer_token = $this->get_bearer_token();
                
               $this->query_args['cache'] = $transient_sec;
        }
        
        /**
         * Get the token from oauth Twitter API
         *
         * @return string Oauth Token
         */
        private function get_bearer_token() {
                
                $bearer_token_credentials = $this->args['consumer_key'] . ':' . $this->args['consumer_secret'];
                $bearer_token_credentials_64 = base64_encode( $bearer_token_credentials );
                
                $args = array(
                        'method'                =>         	'POST',
                        'timeout'               =>         	5,
                        'redirection'        	=>         	5,
                        'httpversion'        	=>         	'1.0',
                        'blocking'              =>         	true,
                        'headers'               =>         	array(
                                'Authorization'       =>        'Basic ' . $bearer_token_credentials_64,
                                'Content-Type'        =>        'application/x-www-form-urlencoded;charset=UTF-8',
                                'Accept-Encoding'     =>        'gzip'
                        ),
                        'body'                  => 			array( 'grant_type'      =>        'client_credentials' ),
                        'cookies'               =>    		array()
                );
                
                $response = wp_remote_post( 'https://api.twitter.com/oauth2/token', $args );
                
                if ( is_wp_error( $response ) || 200 != $response['response']['code'] )
                        return $this->bail( __( 'Can\'t get the bearer token, check your credentials', 'wp_twitter_api' ), $response );
                
                $result = json_decode( $response['body'] );
                
                update_option( 'twitter_bearer_token', $result->access_token );
                
                return $result->access_token;
                
        }
        
        /**
         * Query twitter's API
         *
         * @uses $this->get_bearer_token() to retrieve token if not working
         *
         * @param string $query Insert the query in the format "count=1&include_entities=true&include_rts=true&screen_name=micc1983!
         * @param array $query_args Array of arguments: Resource type (string) and cache duration (int)
         * @param bool $stop Stop the query to avoid infinite loop
         *
         * @return bool|object Return an object containing the result
         */
        public function query( $query, $query_args = array(), $stop = false ) {
                
                if ( $this->has_error )
                        return false;
                
                if ( is_array( $query_args ) && !empty( $query_args ) )
                        $this->query_args = array_merge( $this->query_args, $query_args );
                
                $transient_name = 'wta_' . md5( $query );

                if ($this->query_args['cache'] > 0 && false !== ( $data = get_transient( $transient_name ) ) )
                	return json_decode( $data );
                
                $args = array(
                        'method'             =>         'GET',
                        'timeout'            =>         5,
                        'redirection'        =>         5,
                        'httpversion'        =>         '1.0',
                        'blocking'           =>         true,
                        'headers'            =>         array(
                                'Authorization'		=>        'Bearer ' . $this->bearer_token,
                                'Accept-Encoding'   =>        'gzip'
                        ),
                        'body'               =>         null,
                        'cookies'            =>         array()
                );
                
                $response = wp_remote_get( 'https://api.twitter.com/1.1/' . $this->query_args['type'] . '.json?' . $query, $args );
                if ( is_wp_error( $response ) || 200 != $response['response']['code'] ){
                
                        if ( !$stop ){
                                $this->bearer_token = $this->get_bearer_token();
                                return $this->query( $query, $this->query_args, true );
                        } else {
                                return $this->bail( __( 'Bearer Token is good, check your query', 'wp_twitter_api' ), $response );
                        }
                        
                }
                set_transient( $transient_name, $response['body'], $this->query_args['cache'] );
                return json_decode( $response['body'] );
                
        }
        
        /**
         * Let's manage errors
         *
         * WP_DEBUG has to be set to true to show errors
         *
         * @param string $error_text Error message
         * @param string $error_object Server response or wp_error
         */
        private function bail( $error_text, $error_object = '' ) {
                
                $this->has_error = true;
                
                if ( is_wp_error( $error_object ) ){
                        $error_text .= ' - Wp Error: ' . $error_object->get_error_message();
                } elseif ( !empty( $error_object ) && isset( $error_object['response']['message'] ) ) {
                        $error_text .= ' ( Response: ' . $error_object['response']['message'] . ' )';
                }
                
                trigger_error( $error_text , E_USER_NOTICE );
                
        }
        
}


/**
 * Instagram
 *
 * with help of the API this class delivers all kind of Images from instagram
 *
 * @package    socialstreams
 * @subpackage socialstreams/instagram
 * @author     ThemePunch <info@themepunch.com>
 */

class RevSliderInstagram {

	/**
	 * API key
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $api_key    Instagram API key
	 */
	private $api_key;

	/**
	 * Stream Array
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $stream    Stream Data Array
	 */
	private $stream;

	/**
   * Transient seconds
   *
   * @since    1.0.0
   * @access   private
   * @var      number    $transient Transient time in seconds
   */
  private $transient_sec;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $api_key	Instagram API key.
	 */
	public function __construct($transient_sec=1200) {
		$this->transient_sec = $transient_sec;
	}

	/**
	 * Get Instagram Pictures Public by User
	 *
	 * @since    1.0.0
	 * @param    string    $user_id 	Instagram User id (not name)
	 */
	public function get_public_photos($search_user_id,$count){
    if(!empty($search_user_id)){
        $url = 'https://www.instagram.com/'.$search_user_id.'/?__a=1';
        $transient_name = 'revslider_' . md5($url);
        if ($this->transient_sec > 0 && false !== ($data = get_transient( $transient_name)))
          return ($data);

        $rsp = json_decode(json_encode($this->getFallbackImages($search_user_id)));
          
      for($i=0;$i<$count;$i++) {
              if(isset($rsp->edge_owner_to_timeline_media->edges[$i])){
                $return[] = $rsp->edge_owner_to_timeline_media->edges[$i];
              }
        }

        if(isset($return)){
          $rsp->edge_owner_to_timeline_media->edges = $return;
          set_transient( $transient_name, $return, $this->transient_sec );
          return $return;
        }
        else return '';
    }
    else return '';
  }

  /**
  * Get user ID if necessary
  * @since 5.4.6.3
  */
  public function get_user_id($search_user_id) {
   // $url = 'https://api.instagram.com/v1/users/search?q='.$search_user_id.'&access_token='.$this->api_key;
    $url = 'https://www.instagram.com/'.$search_user_id.'/?__a=1';

    // check for transient
    $transient_name = 'revslider_' . md5($url);
    if ($this->transient_sec > 0 && false !== ($data = get_transient( $transient_name)))
      return ($data);

    // contact API
    $rsp = json_decode(wp_remote_fopen($url));

    // set new transient
    if(isset($rsp->user->id))
      set_transient( $transient_name, $rsp->user->id, 604800 );

    // return user id
    if(isset($rsp->user->id))
      return $rsp->user->id;
    else 
      return false;
  }

  /**
   * Fallback method to get 12 latest photos
   * @param String $search_user_id (name of instagram user)
   */
  private function getFallbackImages($search_user_id) {
    //FALLBACK 12 ELEMENTS
    $page_res = $this->client_request('get', '/' . $search_user_id . '/');
    switch ($page_res['http_code']) {
      default:
        break;
    
      case 404:
        break;
    
      case 200:
        $page_data_matches = array();
    
        if (!preg_match('#window\._sharedData\s*=\s*(.*?)\s*;\s*</script>#', $page_res['body'], $page_data_matches)) {
          _e('Instagram reports: Parse script error',EG_TEXTDOMAIN);
    
        } else {
          $page_data = json_decode($page_data_matches[1], true);
    
          if (!$page_data || empty($page_data['entry_data']['ProfilePage'][0]['graphql']['user'])) {
            _e('Instagram reports: Content did not match expected',EG_TEXTDOMAIN);
    
          } else {
            $user_data = $page_data['entry_data']['ProfilePage'][0]['graphql']['user'];
    
            if ($user_data['is_private']) {
              _e('Instagram reports: Content is private',EG_TEXTDOMAIN);
    
            }
          }
        }
    
        break;
    }
    $user_data = $page_data['entry_data']['ProfilePage'][0]['graphql']['user'];
    return $user_data;
  }
  
  /**
   * Cliente request to get 12 instagram photos fallback
   * @param unknown $type
   * @param unknown $url
   * @param unknown $options
   * @return number[]|string[]|NULL|number[]|string[]|number[]|unknown[]|string[]|number[]|unknown[]|unknown[][]|string[][]|number[][]|NULL[][]
   */
  private function client_request($type, $url, $options = null) {

    $this->index('client', array(
        'base_url' => 'https://www.instagram.com/',
        'cookie_jar' => array(),
        'headers' => array(
            // 'Accept-Encoding' => supports_gz () ? 'gzip' : null,
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.87 Safari/537.36',
            'Origin' => 'https://www.instagram.com',
            'Referer' => 'https://www.instagram.com',
            'Connection' => 'close'
        )
    ));
    $client = $this->index('client');
    $type = strtoupper($type);
    $options = is_array($options) ? $options : array();

    $url = (!empty($client['base_url']) ? rtrim($client['base_url'], '/') : '') . $url;
    $url_info = parse_url($url);

    $scheme = !empty($url_info['scheme']) ? $url_info['scheme'] : '';
    $host = !empty($url_info['host']) ? $url_info['host'] : '';
    $port = !empty($url_info['port']) ? $url_info['port'] : '';
    $path = !empty($url_info['path']) ? $url_info['path'] : '';
    $query_str = !empty($url_info['query']) ? $url_info['query'] : '';

    if (!empty($options['query'])) {
      $query_str = http_build_query($options['query']);
    }

    $headers = !empty($client['headers']) ? $client['headers'] : array();

    if (!empty($options['headers'])) {
      $headers = $this->array_merge_assoc($headers, $options['headers']);
    }

    $headers['Host'] = $host;

    $client_cookies = $this->client_get_cookies_list($host);
    $cookies = $client_cookies;

    if (!empty($options['cookies'])) {
      $cookies = $this->array_merge_assoc($cookies, $options['cookies']);
    }

    if ($cookies) {
      $request_cookies_raw = array();

      foreach ($cookies as $cookie_name => $cookie_value) {
        $request_cookies_raw[] = $cookie_name . '=' . $cookie_value;
      }
      unset($cookie_name, $cookie_data);

      $headers['Cookie'] = implode('; ', $request_cookies_raw);
    }

    if ($type === 'POST' && !empty($options['data'])) {
      $data_str = http_build_query($options['data']);
      $headers['Content-Type'] = 'application/x-www-form-urlencoded';
      $headers['Content-Length'] = strlen($data_str);

    } else {
      $data_str = '';
    }

    $headers_raw_list = array();

    foreach ($headers as $header_key => $header_value) {
      $headers_raw_list[] = $header_key . ': ' . $header_value;
    }
    unset($header_key, $header_value);

    $transport_error = null;
    $curl_support = function_exists('curl_init');
    $sockets_support = function_exists('fsockopen');

    if (!$curl_support && !$sockets_support) {
      log_error('Curl and sockets are not supported on this server');

      return array(
          'status' => 0,
          'transport_error' => 'php on web-server does not support curl and sockets'
      );
    }

    if ($curl_support) {


      $curl = curl_init();
      $curl_options = array(
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_HEADER => true,
          CURLOPT_URL => $scheme . '://' . $host . $path . (!empty($query_str) ? '?' . $query_str : ''),
          CURLOPT_HTTPHEADER => $headers_raw_list,
          CURLOPT_SSL_VERIFYPEER => false,
          CURLOPT_CONNECTTIMEOUT => 15,
          CURLOPT_TIMEOUT => 60,
      );
      if ($type === 'POST') {
        $curl_options[CURLOPT_POST] = true;
        $curl_options[CURLOPT_POSTFIELDS] = $data_str;
      }

      curl_setopt_array($curl, $curl_options);

      $response_str = curl_exec($curl);
      $curl_info = curl_getinfo($curl);
      $curl_error = curl_error($curl);

      curl_close($curl);


      if ($curl_info['http_code'] === 0) {
        log_error('An error occurred while loading data. curl_error: ' . $curl_error);

        $transport_error = array('status' => 0, 'transport_error' => 'curl');

        if (!$sockets_support) {
          return $transport_error;

        }

      }
    }

    if (!$curl_support || $transport_error) {
      log_error('Trying to load data using sockets');

      $headers_str = implode("\r\n", $headers_raw_list);

      $out = sprintf("%s %s HTTP/1.1\r\n%s\r\n\r\n%s", $type, $path . (!empty($query_str) ? '?' . $query_str : ''), $headers_str, $data_str);

      if ($scheme === 'https') {
        $scheme = 'ssl';
        $port = !empty($port) ? $port : 443;
      }

      $scheme = !empty($scheme) ? $scheme . '://' : '';
      $port = !empty($port) ? $port : 80;

      $sock = @fsockopen($scheme . $host, $port, $err_num, $err_str, 15);

      if (!$sock) {
        log_error('An error occurred while loading data error_number: ' . $err_num . ', error_number: ' . $err_str);

        return array(
            'status' => 0,
            'error_number' => $err_num,
            'error_message' => $err_str,
            'transport_error' => $transport_error ? 'curl and sockets' : 'sockets'
        );
      }

      fwrite($sock, $out);

      $response_str = '';

      while ($line = fgets($sock, 128)) {
        $response_str .= $line;
      }

      fclose($sock);
    }


    @list ($response_headers_str, $response_body_encoded, $alt_body_encoded) = explode("\r\n\r\n", $response_str);

    if ($alt_body_encoded) {
      $response_headers_str = $response_body_encoded;
      $response_body_encoded = $alt_body_encoded;
    }


    $response_body = $response_body_encoded;
    $response_headers_raw_list = explode("\r\n", $response_headers_str);
    $response_http = array_shift($response_headers_raw_list);

    preg_match('#^([^\s]+)\s(\d+)\s([^$]+)$#', $response_http, $response_http_matches);
    array_shift($response_http_matches);
    list ($response_http_protocol, $response_http_code, $response_http_message) = $response_http_matches;

    $response_headers = array();
    $response_cookies = array();
    foreach ($response_headers_raw_list as $header_row) {
      list ($header_key, $header_value) = explode(': ', $header_row, 2);

      if (strtolower($header_key) === 'set-cookie') {
        $cookie_params = explode('; ', $header_value);

        if (empty($cookie_params[0])) {
          continue;
        }

        list ($cookie_name, $cookie_value) = explode('=', $cookie_params[0]);
        $response_cookies[$cookie_name] = $cookie_value;

      } else {
        $response_headers[$header_key] = $header_value;
      }
    }
    unset($header_row, $header_key, $header_value, $cookie_name, $cookie_value);

    if ($response_cookies) {
      $response_cookies['ig_or'] = 'landscape-primary';
      $response_cookies['ig_pr'] = '1';
      $response_cookies['ig_vh'] = rand(500, 1000);
      $response_cookies['ig_vw'] = rand(1100, 2000);

      $client['cookie_jar'][$host] = $this->array_merge_assoc($client_cookies, $response_cookies);
      $this->index('client', $client);
    }
    return array(
        'status' => 1,
        'http_protocol' => $response_http_protocol,
        'http_code' => $response_http_code,
        'http_message' => $response_http_message,
        'headers' => $response_headers,
        'cookies' => $response_cookies,
        'body' => $response_body
    );
  }
  /**
   * Helper function for fallback photos function
   * @param unknown $domain
   * @return unknown
   */
  private function client_get_cookies_list($domain) {
    $client = $this->index('client');
    $cookie_jar = $client['cookie_jar'];

    return !empty($cookie_jar[$domain]) ? $cookie_jar[$domain] : array();
  }
  /**
   * Helper function for fallback photos function
   * @param unknown $key
   * @param unknown $value
   * @param string $f
   * @return NULL|string
   */
  private function index($key, $value = null, $f = false) {
    static $index = array();

    if ($value || $f) {
      $index[$key] = $value;
    }

    return !empty($index[$key]) ? $index[$key] : null;
  }
  /**
   * Helper function for fallback photos function
   * @return NULL
   */
  private function array_merge_assoc() {
    $mixed = null;
    $arrays = func_get_args();
  
    foreach ($arrays as $k => $arr) {
      if ($k === 0) {
        $mixed = $arr;
        continue;
      }
  
      $mixed = array_combine(
          array_merge(array_keys($mixed), array_keys($arr)),
          array_merge(array_values($mixed), array_values($arr))
          );
    }
  
    return $mixed;
  }

	/**
	 * Get Instagram Pictures Public by Tag
	 *
	 * @since    1.0.0
	 * @param    string    $user_id 	Instagram User id (not name)
	 */
	public function get_tag_photos($search_tag,$count){
		//call the API and decode the response
		$url = "https://www.instagram.com/explore/tags/".$search_tag."/?__a=1";
    
    $transient_name = 'revslider_' . md5($url);
		/*if ($this->transient_sec > 0 && false !== ($data = get_transient( $transient_name)))
			return ($data);
*/
		$rsp = json_decode(wp_remote_fopen($url));

    
      for($i=0;$i<$count;$i++) {
          $return[] = $rsp->tag->media->nodes[$i];
      }
    
    if(isset($rsp->tag->media->nodes)){
      $rsp->tag->media->nodes = $return;
      set_transient( $transient_name, $rsp->tag->media->nodes, $this->transient_sec );
      return $rsp->tag->media->nodes;
    }
    else return '';
	}
}	// End Class

/**
 * Flickr
 *
 * with help of the API this class delivers all kind of Images from flickr
 *
 * @package    socialstreams
 * @subpackage socialstreams/flickr
 * @author     ThemePunch <info@themepunch.com>
 */

class RevSliderFlickr {

	/**
	 * API key
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $api_key    flickr API key
	 */
	private $api_key;

	/**
	 * API params
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $api_param_defaults    Basic params to call with API
	 */
	private $api_param_defaults;

	/**
	 * Stream Array
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $stream    Stream Data Array
	 */
	private $stream;

	/**
	 * Basic URL
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $url    Url to fetch user from
	 */
	private $flickr_url;

	/**
	* Transient seconds
	*
	* @since    1.0.0
	* @access   private
	* @var      number    $transient Transient time in seconds
	*/
	private $transient_sec;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $api_key	flickr API key.
	 */
	public function __construct($api_key,$transient_sec=1200) {
		$this->api_key = $api_key;
		$this->api_param_defaults = array(
		  'api_key' => $this->api_key,
		  'format' => 'json',
		  'nojsoncallback' => 1,
		);

    $this->transient_sec = $transient_sec;

	}

	/**
	 * Calls Flicker API with set of params, returns json
	 *
	 * @since    1.0.0
	 * @param    array    $params 	Parameter build for API request
	 */
	private function call_flickr_api($params){
		//build url
		$encoded_params = array();
		foreach ($params as $k => $v){
		  $encoded_params[] = urlencode($k).'='.urlencode($v);
		}

		//call the API and decode the response
		$url = "https://api.flickr.com/services/rest/?".implode('&', $encoded_params);
		$transient_name = 'revslider_' . md5($url);

		if ($this->transient_sec > 0 && false !== ($data = get_transient( $transient_name)))
			return ($data);

		$rsp = json_decode(file_get_contents($url));
		if(isset($rsp)){
			set_transient( $transient_name, $rsp, $this->transient_sec );
			return $rsp;
		}
		else return '';
	}

	/**
	 * Get User ID from its URL
	 *
	 * @since    1.0.0
	 * @param    string    $user_url URL of the Gallery
	 */
	public function get_user_from_url($user_url){
		//gallery params
		$user_params = $this->api_param_defaults + array(
			'method'  => 'flickr.urls.lookupUser',
  			'url' => $user_url,
		);
		
		//set User Url
		$this->flickr_url = $user_url;

		//get gallery info
		$user_info = $this->call_flickr_api($user_params);
		if(isset($user_info->user->id))
			return $user_info->user->id;
		else return '';
	}

	/**
	 * Get Group ID from its URL
	 *
	 * @since    1.0.0
	 * @param    string    $group_url URL of the Gallery
	 */
	public function get_group_from_url($group_url){
		//gallery params
		$group_params = $this->api_param_defaults + array(
			'method'  => 'flickr.urls.lookupGroup',
  			'url' => $group_url,
		);
		
		//set User Url
		$this->flickr_url = $group_url;

		//get gallery info
		$group_info = $this->call_flickr_api($group_params);
		if(isset($group_info->group->id))
			return $group_info->group->id;
		else return '';
	}

	/**
	 * Get Public Photos
	 *
	 * @since    1.0.0
	 * @param    string    $user_id 	flicker User id (not name)
	 * @param    int       $item_count 	number of photos to pull
	 */
	public function get_public_photos($user_id,$item_count=10){
		//public photos params
		$public_photo_params = $this->api_param_defaults + array(
			'method'  => 'flickr.people.getPublicPhotos',
  			'user_id' => $user_id,
  			'extras'  => 'description, license, date_upload, date_taken, owner_name, icon_server, original_format, last_update, geo, tags, machine_tags, o_dims, views, media, path_alias, url_sq, url_t, url_s, url_q, url_m, url_n, url_z, url_c, url_l, url_o',
  			'per_page'=> $item_count,
  			'page' => 1
		);
		
		//get photo list
		$public_photos_list = $this->call_flickr_api($public_photo_params);
    //var_dump($public_photos);
		if(isset($public_photos_list->photos->photo))      
			return $public_photos_list->photos->photo;
		else return '';
	}

	/**
	 * Get Photosets List from User
	 *
	 * @since    1.0.0
	 * @param    string    $user_id 	flicker User id (not name)
	 * @param    int       $item_count 	number of photos to pull
	 */
	public function get_photo_sets($user_id,$item_count=10,$current_photoset){
		//photoset params
		$photo_set_params = $this->api_param_defaults + array(
			'method'  => 'flickr.photosets.getList',
  			'user_id' => $user_id,
  			'per_page'=> $item_count,
  			'page'    => 1
		);
		
		//get photoset list
		$photo_sets_list = $this->call_flickr_api($photo_set_params);
		
		$return = array();
		foreach($photo_sets_list->photosets->photoset as $photo_set){
			if(empty($photo_set->title->_content)) $photo_set->title->_content = "";
			if(empty($photo_set->photos))  $photo_set->photos = 0;
			$return[] = '<option title="'.$photo_set->description->_content.'" '.selected( $photo_set->id , $current_photoset , false ).' value="'.$photo_set->id.'">'.$photo_set->title->_content.' ('.$photo_set->photos.' photos)</option>"';
		}
		return $return;
	}

	/**
	 * Get Photoset Photos
	 *
	 * @since    1.0.0
	 * @param    string    $photo_set_id 	Photoset ID
	 * @param    int       $item_count 	number of photos to pull
	 */
	public function get_photo_set_photos($photo_set_id,$item_count=10){
		//photoset photos params
		$this->stream = array();
		$photo_set_params = $this->api_param_defaults + array(
			'method'  		=> 'flickr.photosets.getPhotos',
  			'photoset_id' 	=> $photo_set_id,
  			'per_page'		=> $item_count,
  			'page'    		=> 1,
  			'extras'		=> 'license, date_upload, date_taken, owner_name, icon_server, original_format, last_update, geo, tags, machine_tags, o_dims, views, media, path_alias, url_sq, url_t, url_s, url_q, url_m, url_n, url_z, url_c, url_l, url_o'
		);
		
		//get photo list
		$photo_set_photos = $this->call_flickr_api($photo_set_params);
		if(isset($photo_set_photos->photoset->photo))
			return $photo_set_photos->photoset->photo;
		else return '';
	}

	/**
	 * Get Groop Pool Photos
	 *
	 * @since    1.0.0
	 * @param    string    $group_id 	Photoset ID
	 * @param    int       $item_count 	number of photos to pull
	 */
	public function get_group_photos($group_id,$item_count=10){
		//photoset photos params
		$group_pool_params = $this->api_param_defaults + array(
			'method'  		=> 'flickr.groups.pools.getPhotos',
  			'group_id' 	=> $group_id,
  			'per_page'		=> $item_count,
  			'page'    		=> 1,
  			'extras'		=> 'license, date_upload, date_taken, owner_name, icon_server, original_format, last_update, geo, tags, machine_tags, o_dims, views, media, path_alias, url_sq, url_t, url_s, url_q, url_m, url_n, url_z, url_c, url_l, url_o'
		);
		
		//get photo list
		$group_pool_photos = $this->call_flickr_api($group_pool_params);
		if(isset($group_pool_photos->photos->photo))
			return $group_pool_photos->photos->photo;
		else
			return '';
	}

	/**
	 * Get Gallery ID from its URL
	 *
	 * @since    1.0.0
	 * @param    string    $gallery_url URL of the Gallery
	 * @param    int       $item_count 	number of photos to pull
	 */
	public function get_gallery_from_url($gallery_url){
		//gallery params
		$gallery_params = $this->api_param_defaults + array(
			'method'  => 'flickr.urls.lookupGallery',
  			'url' => $gallery_url,
		);
		
		//get gallery info
		$gallery_info = $this->call_flickr_api($gallery_params);
		if(isset($gallery_info->gallery->id))
			return $gallery_info->gallery->id;
		else return '';
	}

	/**
	 * Get Gallery Photos
	 *
	 * @since    1.0.0
	 * @param    string    $gallery_id 	flicker Gallery id (not name)
	 * @param    int       $item_count 	number of photos to pull
	 */
	public function get_gallery_photos($gallery_id,$item_count=10){
		//gallery photos params
		$gallery_photo_params = $this->api_param_defaults + array(
			'method'  => 'flickr.galleries.getPhotos',
  			'gallery_id' => $gallery_id,
  			'extras'  => 'description, license, date_upload, date_taken, owner_name, icon_server, original_format, last_update, geo, tags, machine_tags, o_dims, views, media, path_alias, url_sq, url_t, url_s, url_q, url_m, url_n, url_z, url_c, url_l, url_o',
  			'per_page'=> $item_count,
  			'page' => 1
		);
		
		//get photo list
		$gallery_photos_list = $this->call_flickr_api($gallery_photo_params);
		if(isset($gallery_photos_list->photos->photo))
			return $gallery_photos_list->photos->photo;
		else return '';
	}

	/**
	 * Encode the flickr ID for URL (base58)
	 *
	 * @since    1.0.0
	 * @param    string    $num 	flickr photo id
	 */
	private function base_encode($num, $alphabet='123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ') {
		$base_count = strlen($alphabet);
		$encoded = '';
		while ($num >= $base_count) {
			$div = $num/$base_count;
			$mod = ($num-($base_count*intval($div)));
			$encoded = $alphabet[$mod] . $encoded;
			$num = intval($div);
		}
		if ($num) $encoded = $alphabet[$num] . $encoded;
		return $encoded;
	}
}	// End Class


/**
 * Youtube
 *
 * with help of the API this class delivers all kind of Images/Videos from youtube
 *
 * @package    socialstreams
 * @subpackage socialstreams/youtube
 * @author     ThemePunch <info@themepunch.com>
 */

class RevSliderYoutube {

	/**
	 * API key
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $api_key    Youtube API key
	 */
	private $api_key;

	/**
	 * Channel ID
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $channel_id    Youtube Channel ID
	 */
	private $channel_id;

	/**
	 * Stream Array
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $stream    Stream Data Array
	 */
	private $stream;

	/**
	* Transient seconds
	*
	* @since    1.0.0
	* @access   private
	* @var      number    $transient Transient time in seconds
	*/
	private $transient_sec;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $api_key	Youtube API key.
	 */
	public function __construct($api_key,$channel_id,$transient_sec=1200) {
		$this->api_key = $api_key;
		$this->channel_id = $channel_id;
		$this->transient_sec = $transient_sec;
	}


	/**
	 * Get Youtube Playlists
	 *
	 * @since    1.0.0
	 */
	public function get_playlists(){
		//call the API and decode the response
		$url = "https://www.googleapis.com/youtube/v3/playlists?part=snippet&maxResults=50&channelId=".$this->channel_id."&key=".$this->api_key;
		$rsp = json_decode(wp_remote_fopen($url));
		if(isset($rsp->items)){
			return $rsp->items;
		}else{
			return false;
		}
	}

	/**
	 * Get Youtube Playlist Items
	 *
	 * @since    1.0.0
	 * @param    string    $playlist_id 	Youtube Playlist ID
	 * @param    integer    $count 	Max videos count
	 */
	public function show_playlist_videos($playlist_id,$count=50){
		//call the API and decode the response
    if(empty($count)) $count = 50;
		$url = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=".$playlist_id."&maxResults=".$count."&fields=items%2Fsnippet&key=".$this->api_key;
		
		$transient_name = 'revslider_' . md5($url);

		if ($this->transient_sec > 0 && false !== ($data = get_transient( $transient_name)))
			return ($data);

		$rsp = json_decode(wp_remote_fopen($url));

		set_transient( $transient_name, $rsp->items, $this->transient_sec );
		return $rsp->items;
	}

	/**
	 * Get Youtube Channel Items
	 *
	 * @since    1.0.0
	 * @param    integer    $count 	Max videos count
	 */
	public function show_channel_videos($count=50){
    if(empty($count)) $count = 50;
		//call the API and decode the response
		$url = "https://www.googleapis.com/youtube/v3/search?part=snippet&channelId=".$this->channel_id."&maxResults=".$count."&key=".$this->api_key."&order=date";
		
		$transient_name = 'revslider_' . md5($url);
		
		if ($this->transient_sec > 0 && false !== ($data = get_transient( $transient_name)))
			return ($data);

		$rsp = json_decode(wp_remote_fopen($url));

		set_transient( $transient_name, $rsp->items, $this->transient_sec );
		return $rsp->items;
	}

	/**
	 * Get Playlists from Channel as Options for Selectbox
	 *
	 * @since    1.0.0
	 */
	public function get_playlist_options($current_playlist){
		$return = array();
		$playlists = $this->get_playlists();
		if(!empty($playlists)){
			foreach($playlists as $playlist){
				$return[] = '<option title="'.$playlist->snippet->description.'" '.selected( $playlist->id , $current_playlist , false ).' value="'.$playlist->id.'">'.$playlist->snippet->title.'</option>"';
			}
		}
		return $return;
	}
}	// End Class

/**
 * Vimeo
 *
 * with help of the API this class delivers all kind of Images/Videos from Vimeo
 *
 * @package    socialstreams
 * @subpackage socialstreams/vimeo
 * @author     ThemePunch <info@themepunch.com>
 */

class RevSliderVimeo {
	/**
	 * Stream Array
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $stream    Stream Data Array
	 */
	private $stream;

	/**
	* Transient seconds
	*
	* @since    1.0.0
	* @access   private
	* @var      number    $transient Transient time in seconds
	*/
	private $transient_sec;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $api_key	Youtube API key.
	 */
	public function __construct($transient_sec=1200) {
		$this->transient_sec = $transient_sec;
	}

	/**
	 * Get Vimeo User Videos
	 *
	 * @since    1.0.0
	 */
	public function get_vimeo_videos($type,$value){
		//call the API and decode the response
		if($type=="user"){
			$url = "https://vimeo.com/api/v2/".$value."/videos.json";
		}
		else{
			$url = "https://vimeo.com/api/v2/".$type."/".$value."/videos.json";
		}
		
		$transient_name = 'revslider_' . md5($url);
		
		if ($this->transient_sec > 0 && false !== ($data = get_transient( $transient_name)))
			return ($data);

		$rsp = json_decode(wp_remote_fopen($url));
		set_transient( $transient_name, $rsp, $this->transient_sec );
		return $rsp;
	}
}	// End Class
?>