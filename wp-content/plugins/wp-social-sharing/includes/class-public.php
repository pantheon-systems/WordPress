<?php
if( ! defined( "SS_VERSION" ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

class SS_Public {
	
	public function __construct() {
		add_filter( 'the_content', array( $this, 'add_links_after_content' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ), 99 );
		add_shortcode( 'wp_social_sharing',array($this,'social_sharing'));
	}
	
	public function add_links_after_content( $content ){
		$opts = ss_get_options();
		$show_buttons = false;
		
		if( ! empty( $opts['auto_add_post_types'] ) && in_array( get_post_type(), $opts['auto_add_post_types'] ) && is_singular( $opts['auto_add_post_types'] ) ) {
			$show_buttons = true;
		}
			
		$show_buttons = apply_filters( 'ss_display', $show_buttons );
		if( ! $show_buttons ) {
			return $content;
		}
		$opts['icon_order']=get_option('wss_wp_social_sharing');
		
		if($opts['social_icon_position'] == 'before' ){
			return $this->social_sharing($opts).$content;
		}
		else{
			return $content . $this->social_sharing($opts);			
		}
	}
	
	public function load_assets() 
	{
		$opts = ss_get_options();
		foreach ($opts['load_static'] as $static){
			if($static == 'load_css'){
				wp_enqueue_style( 'wp-social-sharing', SS_PLUGIN_URL . 'static/socialshare.css', array(), SS_VERSION );
			}	
			if($static == 'load_js'){
				wp_enqueue_script( 'wp-social-sharing', SS_PLUGIN_URL . 'static/socialshare.js', array(), SS_VERSION, true );				
			}		
		}
	}

	public function social_sharing( $atts=array() ) {
		extract(shortcode_atts(array(
				'social_options' => 'twitter, facebook, googleplus',
				'twitter_username' => '',
				'twitter_text' => __( 'Share on Twitter ', 'social-sharing' ),
				'facebook_text' => __( 'Share on Facebook', 'social-sharing' ),
				'googleplus_text' => __( 'Share on Google+', 'social-sharing' ),
				'linkedin_text' => __('Share on Linkedin', 'social-sharing' ),
				'pinterest_text'=>__('Share on Pinterest','social-sharing'),
				'xing_text'=>__('Share on Xing','social-sharing'),
				'reddit_text'	=>	__('Share on Reddit','social-sharing'),
				'icon_order'=>'f,t,g,l,p,x,r',
				'social_image'=> '', 
				'show_icons'=>'0',
				'before_button_text'=>'',
				'text_position'=> 'left'
		),$atts));

		if(!is_array($social_options))
			$social_options = array_filter( array_map( 'trim', explode( ',',$social_options ) ) );
		
		remove_filter('the_title','wptexturize');
		
		$title = urlencode(html_entity_decode(get_the_title()));
		add_filter('the_title','wptexturize');
		
		$url = urlencode( get_permalink() );
	
		$loadjs='';
		
		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'medium' );
		$thumb_url = $thumb['0'];
		if($thumb_url == ''){
			if(isset($atts['pinterest_image']) && $atts['pinterest_image'] == ''){
				$thumb_url = SS_PLUGIN_URL.'static/blank.jpg';								
			}
			else{
				$thumb_url = $atts['pinterest_image'];	
			}
		}
		if($social_image == ''){
			$social_image = $thumb_url;
		}
		$social_image = urlencode($social_image);
		
		$opts=ss_get_options();
		foreach ($opts['load_static'] as $static){
		    if($static == 'load_js'){
		       $loadjs='onclick="return ss_plugin_loadpopup_js(this);"';
		    }
		}
		
		$ssbutton_facebook		=	'button-facebook';
		$ssbutton_twitter		=	'button-twitter';
		$ssbutton_googleplus	=	'button-googleplus';
		$ssbutton_linkedin		=	'button-linkedin';
		$ssbutton_pinterest		=	'button-pinterest';
		$ssbutton_xing			=	'button-xing';
		$ssbutton_reddit		=	'button-reddit';	
		$sssocial_sharing='';
		if($show_icons){
			$sssocial_sharing		=	'ss-social-sharing';
			$ssbutton_facebook		=	'ss-button-facebook';
			$ssbutton_twitter		=	'ss-button-twitter';
			$ssbutton_googleplus	=	'ss-button-googleplus';
			$ssbutton_linkedin		=	'ss-button-linkedin';	
			$ssbutton_pinterest		=	'ss-button-pinterest';
			$ssbutton_xing			=	'ss-button-xing';
			$ssbutton_reddit		=	'ss-button-reddit';
		}
		$icon_order=explode(',',$icon_order);
		ob_start();
		?>
		<div class="social-sharing <?php echo $sssocial_sharing;?>">
			<?php if(!empty($before_button_text) && ($text_position == 'left' || $text_position == 'top')):?>
			<span class="<?php echo $text_position;?> before-sharebutton-text"><?php echo $before_button_text; ?></span>
	        <?php endif;?>
	        <?php 
	        foreach($icon_order as $o) {
	        	switch($o) {
	        		case 'f':
	        			if(in_array('facebook', $social_options)){
	        			?><a <?php echo $loadjs;?> rel="external nofollow" class="<?php echo $ssbutton_facebook;?>" href="http://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>" target="_blank" ><?php echo $facebook_text; ?></a><?php
	        			}
	        		break;
	        		case 't':
	        			if(in_array('twitter', $social_options)){
	        			?><a <?php echo $loadjs;?> rel="external nofollow" class="<?php echo $ssbutton_twitter;?>" href="http://twitter.com/intent/tweet/?text=<?php echo $title; ?>&url=<?php echo $url; ?><?php if(!empty($twitter_username)) {  echo '&via=' . $twitter_username; } ?>" target="_blank"><?php echo $twitter_text; ?></a><?php
	        			}
	        		break;
	        		case 'g':
	        			if(in_array('googleplus', $social_options)){
	        			?><a <?php echo $loadjs;?> rel="external nofollow" class="<?php echo $ssbutton_googleplus;?>" href="https://plus.google.com/share?url=<?php echo $url; ?>" target="_blank" ><?php echo $googleplus_text; ?></a><?php
	        			}
	        		break;
					case 'l':
						if(in_array('linkedin', $social_options)){
							?><a <?php echo $loadjs;?> rel="external nofollow" class="<?php echo $ssbutton_linkedin;?>" href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo substr($url,0,1024);?>&title=<?php echo substr($title,0,200);?>" target="_blank" ><?php echo $linkedin_text; ?></a><?php
						}
	        		break;
	        		case 'p':
	        			if(in_array('pinterest', $social_options)){
	        				?><a <?php echo $loadjs;?> rel="external nofollow" class="<?php echo $ssbutton_pinterest;?>" href="http://pinterest.com/pin/create/button/?url=<?php echo $url;?>&media=<?php echo $social_image;?>&description=<?php echo $title;?>" target="_blank" ><?php echo $pinterest_text; ?></a><?php
	        			}
					break;
                    case 'x':
                        if(in_array('xing', $social_options)){
                    	    ?><a <?php echo $loadjs;?> rel="external nofollow" class="<?php echo $ssbutton_xing;?>" href="https://www.xing.com/spi/shares/new?url=<?php echo $url;?>" target="_blank" ><?php echo $xing_text; ?></a><?php
                        }
	        		break;
					case 'r':
						if(in_array('reddit', $social_options)){
							?><a <?php echo $loadjs;?> rel="external nofollow"  class="<?php echo $ssbutton_reddit;?>" href="http://reddit.com/submit?url=<?php echo $url;?>&amp;title=<?php echo $title?>" target="_blank"><?php echo $reddit_text;?></a><?php
                        }
                    break;
          		}
	        } ?>
	        <?php if(!empty($before_button_text) && ($text_position == 'bottom' || $text_position == 'right')):?>
			<span class="<?php echo $text_position;?>  before-sharebutton-text"><?php echo $before_button_text; ?></span>
	        <?php endif;?>
	    </div>
	    <?php
	  	$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}