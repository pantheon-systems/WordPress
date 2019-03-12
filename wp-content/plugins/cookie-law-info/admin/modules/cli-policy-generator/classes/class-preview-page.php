<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
* Author: Scott Sherrill-Mix
* URI: http://scott.sherrillmix.com/blog/blogger/creating-a-better-fake-post-with-a-wordpress-plugin/
* @since 1.7.4
*/

class Cli_PreviewPage
{

    public function __construct()
    {
        add_filter('the_posts',array($this,'preview_page'));
    }

    /**
    * Generating preview page data
    * @since 1.7.4
    */
    private static function get_preview_page() 
    { 
    	$out=array();
        $out['cli-policy-preview'] = array(
            'title'   => __('Cookie Policy','cookie-law-info'),
            'content' =>Cookie_Law_Info_Cli_Policy_Generator::get_page_content()
        );
        return $out;
    }

    /**
     * 
     * @since 1.7.4
     * @param $posts
     * @return array|null
     */
    public function preview_page($posts)
    {
        global $wp,$wp_query;

        $preview_pages=self::get_preview_page();
        $preview_pages_slugs = array();
        foreach($preview_pages as $slug=>$v)
        {
            $preview_pages_slugs[]=$slug;
        }
        if(in_array(strtolower($wp->request),$preview_pages_slugs) || 
        	(isset($wp->query_vars['page_id']) && in_array(strtolower($wp->query_vars['page_id']),$preview_pages_slugs))) 
        {
            if(in_array(strtolower($wp->request),$preview_pages_slugs)) 
            {
                $preview_page=strtolower($wp->request);
            }else 
            {
                $preview_page=strtolower($wp->query_vars['page_id']);
            }

            $posts                  = null;
            $posts[]                = self::create_preview_page($preview_page,$preview_pages[$preview_page]);
            $wp_query->is_page      = true;
            $wp_query->is_singular  = true;
            $wp_query->is_home      = false;
            $wp_query->is_archive   = false;
            $wp_query->is_category  = false;
            $wp_query->is_fake_page = true;
            $wp_query->preview_page = $wp->request;
            unset( $wp_query->query["error"] );
            $wp_query->query_vars["error"] = "";
            $wp_query->is_404              = false;

            add_action('admin_bar_menu',array($this,'add_admin_bar_menu'),100);
            add_action('wp_footer',array($this,'reg_preview_auto_btn'),100);
        }
        return $posts;
    }

    public function add_admin_bar_menu($wp_admin_bar)
    {
        $wp_admin_bar->add_node(array(
        'id' => 'cli_pg_live_preview',
        'title' => '<span style="color:red;"><input type="checkbox" name="cli_pg_toggle_preview_autoreload" /> '.__('Auto reload preview','cookie-law-info').'</span>',
        ));
    }
    public function reg_preview_auto_btn()
    {
        ?>
        <script type="text/javascript">
            var cli_pg_autorealod_tmr=null;
            function cli_page_auto_reload()
            {
               if(jQuery('[name="cli_pg_toggle_preview_autoreload"]').is(':checked'))
                {
                    jQuery.get(window.location.href+'?rnd='+Math.random(), function(data) {
                        var html=jQuery('<div />').html(data).find('.cli_pg_page_contaner').html();
                        jQuery('.cli_pg_page_contaner').html(html);
                        cli_pg_autorealod_tmr=setTimeout(function(){
                            cli_page_auto_reload();
                        },2000);
                    });
                } 
            }
            function cli_page_reg_auto_reload()
            {
                clearTimeout(cli_pg_autorealod_tmr);
                if(jQuery('[name="cli_pg_toggle_preview_autoreload"]').is(':checked'))
                {                    
                    cli_page_auto_reload();
                }
            }
            jQuery(document).ready(function(){
                jQuery('[name="cli_pg_toggle_preview_autoreload"]').click(function(){
                    cli_page_reg_auto_reload();
                });
                if(jQuery('[name="cli_pg_toggle_preview_autoreload"]').is(':checked'))
                {
                    cli_page_reg_auto_reload();
                }           
            });
        </script>
        <?php
    }

    /**
     * Creates virtual preview page
     *
     * @param $pagename
     * @param $page
     *
     * @return stdClass
     */
    private static function create_preview_page($pagename,$page)
    {
        $post                 = new stdClass;
        $post->post_author    = 1;
        $post->post_name      = $pagename;
        $post->guid           = get_bloginfo('wpurl').'/'.$pagename;
        $post->post_title     = $page['title'];
        $post->post_content   = $page['content'];
        $post->ID             = -1;
        $post->post_status    = 'static';
        $post->comment_status = 'closed';
        $post->ping_status    = 'closed';
        $post->comment_count  = 0;
        $post->post_date      = current_time('mysql');
        $post->post_date_gmt  = current_time('mysql',1);
        $post->type    		  = 'page';
        return $post;
    }
}
new Cli_PreviewPage();