<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/*
    ===============================================================================

    Copyright 2018 @ WebToffee

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/
class Cookie_Law_Info_Shortcode {

    public $version;

    public $parent_obj; //instance of the class that includes this class

    public $plugin_obj;

    public $plugin_name;

	public function __construct($parent_obj)
	{
		$this->version=$parent_obj->version;
        $this->parent_obj=$parent_obj;
        $this->plugin_obj=$parent_obj->plugin_obj;
        $this->plugin_name=$parent_obj->plugin_name;

        // Shortcodes:
        add_shortcode( 'delete_cookies',array($this,'cookielawinfo_delete_cookies_shortcode')); // a shortcode [delete_cookies (text="Delete Cookies")]
        add_shortcode( 'cookie_audit',array($this,'cookielawinfo_table_shortcode'));           // a shortcode [cookie_audit style="winter"]
        add_shortcode( 'cookie_accept',array($this,'cookielawinfo_shortcode_accept_button'));      // a shortcode [cookie_accept (colour="red")]
        add_shortcode( 'cookie_reject',array($this,'cookielawinfo_shortcode_reject_button'));      // a shortcode [cookie_reject (colour="red")]
        add_shortcode( 'cookie_link',array($this,'cookielawinfo_shortcode_more_link'));            // a shortcode [cookie_link]
        add_shortcode( 'cookie_button',array($this,'cookielawinfo_shortcode_main_button'));        // a shortcode [cookie_button]
        add_shortcode('cookie_after_accept',array($this,'cookie_after_accept_shortcode'));
	}

    /*
    *   Add content after accepting the cookie notice.
    *   Usage : 
                Inside post editor
                [cookie_after_accept] ...Your content goes here...  [/cookie_after_accept]
                Inside template
                <?php echo do_shortcode('...shortcode goes here...'); ?>
    */
    public function cookie_after_accept_shortcode($atts=array(),$content='')
    {
        if(isset($_COOKIE["viewed_cookie_policy"]) && $_COOKIE["viewed_cookie_policy"] == 'yes')
        {
            return $content;
        }else
        {
            return '';
        }
    }


    /**
     A shortcode that outputs a link which will delete the cookie used to track
     whether or not a vistor has dismissed the header message (i.e. so it doesn't
     keep on showing on all pages)

     Usage: [delete_cookies]
            [delete_cookies linktext="delete cookies"]
     
     N.B. This shortcut does not block cookies, or delete any other cookies!
    */
    public function cookielawinfo_delete_cookies_shortcode($atts) 
    {
        extract(shortcode_atts( array(
            'text' => __('Delete Cookies', 'cookie-law-info'),
        ), $atts ) );
        return "<a href='' class='cookielawinfo-cookie-delete'>{$text}</a>";
    }


    /**
     A nice shortcode to output a table of cookies you have saved, output in ascending
     alphabetical order. If there are no cookie records found a single empty row is shown.
     You can customise the 'not shown' message (see commented code below)

     N.B. This only shows the information you entered on the "cookie" admin page, it
     does not necessarily mean you comply with the cookie law. It is up to you, or
     the website owner, to make sure you have conducted an appropriate cookie audit
     and are informing website visitors of the actual cookies that are being stored.

     Usage:                 [cookie_audit]
                            [cookie_audit style="winter"]
                            [cookie_audit not_shown_message="No records found"]
                            [cookie_audit style="winter" not_shown_message="Not found"]

     Styles included:       simple, classic, modern, rounded, elegant, winter.
                            Default style applied: classic.

     Additional styles:     You can customise the CSS by editing the CSS file itself,
                            included with plugin.
    */
    public function cookielawinfo_table_shortcode( $atts )
    {
        
        /** RICHARDASHBY EDIT: only add CSS if table is being used */
        wp_enqueue_style($this->plugin_name.'-table');
        /** END EDIT */
        
        extract( shortcode_atts( array(
            'style' => 'classic',
            'not_shown_message' => ''
        ), $atts ) );
        
        $args = array(
            'post_type' => CLI_POST_TYPE,
            /** 28/05/2013: Changing from 10 to 50 to allow longer tables of cookie data */
            'posts_per_page' => 50,
            'order' => 'ASC',
            'orderby' => 'title'
        );
        $posts = get_posts($args);
        $ret = '<table class="cookielawinfo-' . $style . '"><thead><tr>';
        $ret .= '<th class="cookielawinfo-column-1">'.__('Cookie', 'cookie-law-info').'</th>';
        $ret .= '<th class="cookielawinfo-column-2">'.__('Type', 'cookie-law-info').'</th>';
        $ret .= '<th class="cookielawinfo-column-3">'.__('Duration', 'cookie-law-info').'</th>';
        $ret .= '<th class="cookielawinfo-column-4">'.__('Description', 'cookie-law-info').'</th>';
        $ret = apply_filters('cli_new_columns_to_audit_table',$ret);
        $ret .= '</tr>';
        $ret .= '</thead><tbody>';
        
        if(!$posts) 
        {
            $ret .= '<tr class="cookielawinfo-row"><td colspan="4" class="cookielawinfo-column-empty">' . $not_shown_message . '</td></tr>';
        }

        // Get custom fields:
        if($posts)
        {
            foreach( $posts as $post )
            {
                $custom = get_post_custom( $post->ID );
                $cookie_type = ( isset ( $custom["_cli_cookie_type"][0] ) ) ? $custom["_cli_cookie_type"][0] : '';
                $cookie_duration = ( isset ( $custom["_cli_cookie_duration"][0] ) ) ? $custom["_cli_cookie_duration"][0] : '';
                // Output HTML:
                $ret .= '<tr class="cookielawinfo-row"><td class="cookielawinfo-column-1">' . $post->post_title . '</td>';
                $ret .= '<td class="cookielawinfo-column-2">' . $cookie_type .'</td>';
                $ret .= '<td class="cookielawinfo-column-3">' . $cookie_duration .'</td>';
                $ret .= '<td class="cookielawinfo-column-4">' . $post->post_content .'</td>';
                $ret = apply_filters('cli_new_column_values_to_audit_table',$ret, $custom);
                $ret .= '</tr>';
            }
        }
        $ret .= '</tbody></table>';
        return $ret;
    }

    public function render_cookie_raw_table($cookie_post = array(), $ret)
    {
        
        // Get custom fields:
        $custom = get_post_custom( $cookie_post->ID );
        $post = get_post($cookie_post->ID);
        $cookie_type = ( isset ( $custom["_cli_cookie_type"][0] ) ) ? $custom["_cli_cookie_type"][0] : '';
        $cookie_duration = ( isset ( $custom["_cli_cookie_duration"][0] ) ) ? $custom["_cli_cookie_duration"][0] : '';
        // Output HTML:
        $ret .= '<tr class="cookielawinfo-row"><td class="cookielawinfo-column-1">' . $post->post_title . '</td>';
        $ret .= '<td class="cookielawinfo-column-2">' . $cookie_type .'</td>';
        $ret .= '<td class="cookielawinfo-column-3">' . $cookie_duration .'</td>';
        $ret .= '<td class="cookielawinfo-column-4">' . $post->post_content .'</td>';
        $ret .= '</tr>';
        return $ret;       
    }




    /**  
    *   Returns HTML for a standard (green, medium sized) 'Accept' button
    */
    public function cookielawinfo_shortcode_accept_button( $atts ) 
    {
        extract(shortcode_atts(array(
            'colour' => 'green'
        ), $atts ));
        $defaults =Cookie_Law_Info::get_default_settings('button_1_text');
        $settings = wp_parse_args(Cookie_Law_Info::get_settings(),$defaults);
        $button_1_text=__($settings['button_1_text'],'cookie-law-info');
        return '<a href="#" class="cli_action_button cli-accept-button medium cli-plugin-button ' . $colour . '" data-cli_action="accept" >' . stripslashes($button_1_text) . '</a>';
    }

    /** Returns HTML for a standard (green, medium sized) 'Reject' button */
    public function cookielawinfo_shortcode_reject_button( $atts ) 
    {
        $defaults = Cookie_Law_Info::get_default_settings();
        $settings = wp_parse_args(Cookie_Law_Info::get_settings(),$defaults);
        
        $classr = '';
        if($settings['button_3_as_button']) 
        {
            $classr=' class="' . $settings['button_3_button_size'] . ' cli-plugin-button cli-plugin-main-button cookie_action_close_header_reject cli_action_button"';
        }
        else 
        {
            $classr=' class="cookie_action_close_header_reject cli_action_button" '; 
        }
                
        $url_reject = ($settings['button_3_action'] == "CONSTANT_OPEN_URL" ) ? $settings['button_3_url'] : "#";    
        $link_tag = '';
        $link_tag .= ' <a href="'.$url_reject.'" id="'.Cookie_Law_Info_Public::cookielawinfo_remove_hash($settings['button_3_action']).'" ';
        $link_tag .= ($settings['button_3_new_win'] ) ? 'target="_blank" ' : '' ;
        $link_tag .= $classr . '  data-cli_action="reject">' . stripslashes(__($settings['button_3_text'],'cookie-law-info')) . '</a>';
        return $link_tag;
    }

    /** Returns HTML for a generic button */
    public function cookielawinfo_shortcode_more_link( $atts ) {
        return $this->cookielawinfo_shortcode_button_DRY_code('button_2');
    }


    /** Returns HTML for a generic button */
    public function cookielawinfo_shortcode_main_button( $atts ) 
    {
        $defaults =Cookie_Law_Info::get_default_settings();            
        $settings = wp_parse_args( Cookie_Law_Info::get_settings(),$defaults);        
        $class = '';
        if($settings['button_1_as_button']) 
        {
            $class = ' class="' . $settings['button_1_button_size'] . ' cli-plugin-button cli-plugin-main-button cookie_action_close_header cli_action_button"';
        }
        else {
            $class = ' class="cli-plugin-main-button cookie_action_close_header cli_action_button" ' ;
        }
        
        // If is action not URL then don't use URL!
        $url = ( $settings['button_1_action'] == "CONSTANT_OPEN_URL" ) ? $settings['button_1_url'] : "#";
            
        
        $link_tag = '<a href="'.$url.'" data-cli_action="accept" id="' . Cookie_Law_Info_Public::cookielawinfo_remove_hash ( $settings['button_1_action'] ) . '" ';
        $link_tag .= ( $settings['button_1_new_win'] ) ? 'target="_blank" ' : '' ;
        $link_tag .= $class . ' >' . stripslashes( __($settings['button_1_text'],'cookie-law-info') ) . '</a>';
            
        
        return $link_tag;
    }


    /** Returns HTML for a generic button */
    public function cookielawinfo_shortcode_button_DRY_code( $name ) {
        $arr = Cookie_Law_Info::get_settings();
        $settings = array();
        $class_name = '';
        
        if ( $name == "button_1" ) {
            $settings = array(
                'button_x_text' => stripslashes( $arr['button_1_text'] ),
                'button_x_url' => $arr['button_1_url'],
                'button_x_action' => $arr['button_1_action'],
                
                'button_x_link_colour' => $arr['button_1_link_colour'],
                'button_x_new_win' => $arr['button_1_new_win'],
                'button_x_as_button' => $arr['button_1_as_button'],
                'button_x_button_colour' => $arr['button_1_button_colour'],
                'button_x_button_size' => $arr['button_1_button_size']
            );
            $class_name = 'cli-plugin-main-button';
        }
        elseif ( $name == "button_2" ) {
            $settings = array(
                'button_x_text' => stripslashes( $arr['button_2_text'] ),
                'button_x_action' => $arr['button_2_action'],
                
                'button_x_link_colour' => $arr['button_2_link_colour'],
                'button_x_new_win' => $arr['button_2_new_win'],
                'button_x_as_button' => $arr['button_2_as_button'],
                'button_x_button_colour' => $arr['button_2_button_colour'],
                'button_x_button_size' => $arr['button_2_button_size']
            );
            if($arr['button_2_url_type']=='url')
            {
                $settings['button_x_url']=$arr['button_2_url'];
            }else
            {
                $privacy_page_exists=0;
                if($arr['button_2_page']>0) //page choosed
                {
                    $privacy_policy_page=get_post($arr['button_2_page']);                    
                    if($privacy_policy_page instanceof WP_Post)
                    {
                        if($privacy_policy_page->post_status==='publish') 
                        {
                            $privacy_page_exists=1;
                            $settings['button_x_url']=get_page_link($privacy_policy_page);
                        }  
                    }
                }
                if($privacy_page_exists==0)
                {
                    return '';   
                }
            }
            $class_name = 'cli-plugin-main-link';
        }
        
        $settings = apply_filters('wt_readmore_link_settings', $settings);            
        $class = '';
        if($settings['button_x_as_button'] ) 
        {
            $class .= ' class="' . $settings['button_x_button_size'] . ' cli-plugin-button ' . $class_name . '"';
        }
        else {
            $class .= ' class="' . $class_name . '" ' ;
        }
        
        // If is action not URL then don't use URL!
        $url = ( $settings['button_x_action'] == "CONSTANT_OPEN_URL" ) ? $settings['button_x_url'] : "#";
        
        $link_tag = '<a href="' . $url . '" id="' . Cookie_Law_Info_Public::cookielawinfo_remove_hash ( $settings['button_x_action'] ) . '" ';
        $link_tag .= ( $settings['button_x_new_win'] ) ? 'target="_blank" ' : '' ;
        $link_tag .= $class . ' >' . $settings['button_x_text'] . '</a>';       
        return $link_tag;
    }
}
new Cookie_Law_Info_Shortcode($this);