<?php
/**
 * SeedProd Framework - Inspired by Yoast's Plugins and WooThemes Framework
 *
 * @package WordPress
 * @subpackage Ultimate_Coming_Soon_Page
 * @since 0.1
 */
if (!class_exists('SeedProd_Framework_UMM')) {
	class SeedProd_Framework_UMM {
	
        /**
         * Define the Version of the Plugin
         */
        public $plugin_version = '';
        public $plugin_type = ''; // free,lite and pro
        public $plugin_name = '';
        public $plugin_support_url = '';
        public $plugin_short_url = '';
        public $plugin_seedprod_url = '';
        public $plugin_donate_url = '';
        public $plugin_official_url = '';
        private $framework_version = '0.1';

        /**
         * Define if we are deploying a theme and add the theme params
         */
        public $deploy_theme = 0;
        public $deploy_theme_name = array('template' =>'', 'stylesheet' => '');

        /**
         * Global we set in seedprod_admin_enqueue_scripts and use in create_menu
         */
        public $pages = array();

        /**
         *  Define the menus that will be rendered.
         *  Do not replace callback function.
         */
        public $menu = array();
        
        /**
         *  Define options, sections and fields
         */
        public $options = array();
	
    	/**
    	 * Load Hooks
    	 */
    	function __construct() {
    	    add_action('admin_enqueue_scripts', array(&$this,'admin_enqueue_scripts'));
    	    add_action('admin_menu',array(&$this,'create_menu'));
    	    add_action('admin_init', array(&$this,'set_settings'));
    	}
    	
    	/**
         * Set the base url to use in the plugin
         *
         * @since  0.1
         * @return string
         */
    	function base_url(){
            return plugins_url('',dirname(__FILE__));
        }
    	    
	
        /**
         * Properly enqueue styles and scripts for our theme options page.
         *
         * This function is attached to the admin_enqueue_scripts action hook.
         *
         * @since  0.1
         * @param string $hook_suffix The name of the current page we are on.
         */
        function admin_enqueue_scripts( $hook_suffix ) {
            wp_enqueue_style( 'seedprod_plugin', plugins_url('inc/css/admin-style.css',dirname(__FILE__)), false, $this->plugin_version );
            if(!in_array($hook_suffix, $this->pages))
                return;
            wp_enqueue_script('dashboard');
            wp_enqueue_script('editor');
        	wp_enqueue_script( 'seedprod_framework', plugins_url('framework.js',__FILE__), array( 'jquery','media-upload','thickbox','farbtastic' ), $this->plugin_version );
        	wp_enqueue_style( 'seedprod_framework', plugins_url('framework.css',__FILE__), false, $this->plugin_version );
        	wp_enqueue_script( 'seedprod_plugin', plugins_url('inc/js/admin-script.js',dirname(__FILE__)), array( 'jquery','media-upload','thickbox','farbtastic' ), $this->plugin_version );
        	wp_enqueue_style( 'seedprod_plugin', plugins_url('inc/css/admin-style.css',dirname(__FILE__)), false, $this->plugin_version );
        	wp_enqueue_style('thickbox');
            wp_enqueue_style('farbtastic'); 
        }

        /**
         * Creates WordPress Menu pages from an array in the config file.
         *
         * This function is attached to the admin_menu action hook.
         *
         * @since 0.1
         */
        function create_menu(){
            foreach ($this->menu as $v) {
                $this->pages[] = call_user_func_array($v['type'],array($v['page_name'],$v['menu_name'],$v['capability'],$v['menu_slug'],$v['callback'],$v['icon_url']));
            }
    
        }

        /**
         * Render the option pages.
         *
         * @since 0.1
         */
        function option_page() {
            $page = $_REQUEST['page'];
        	?>
        	<div class="wrap seedprod">
        	    <?php screen_icon(); ?>
        		<h2><?php echo $this->plugin_name; ?> </h2>
        		<?php //settings_errors(); ?> 
        		<div id="poststuff" class="metabox-holder">
                    <div id="post-body">
                        <div id="post-body-content" >
                            <div class="meta-box-sortables ui-sortable">
                                
                                <form action="options.php" method="post">
                                <?php
                                foreach ($this->options as $v) {
                                    if(isset($v['menu_slug'])){
                                        if($v['menu_slug'] == $page){
                                            switch ($v['type']) {
                                                case 'setting':
                            				        settings_fields($v['id']);
                            				        break;
                            				    case 'section':
                            				        echo '<div class="postbox seedprod-postbox"><div class="handlediv" title="Click to toggle"><br /></div>';
                                            		$this->seedprod_do_settings_sections($v['id']);
                                        		    echo '</div>';
                                        		    break;
                        		    
                            		        }
                    		            }
            		                }
                                }
                                ?>
                        		<input name="Submit" type="submit" value="<?php _e('Save Changes', 'ultimate-maintenance-mode') ?>" class="button-primary"/>
                        	    </form>
                                <p><?php echo __('Note: The automatic screenshot is produced from a service on WordPress.com. There is no way to manually refresh the screenshot once taken. WordPress.com should refresh it within a few days.','ultimate-maintenance-mode'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
        	</div>	
        	<?php
        }

        /**
         * Create the settings options, sections and fields via the WordPress Settings API
         *
         * This function is attached to the admin_init action hook.
         *
         * @since 0.1
         */
        function set_settings(){
            foreach ($this->options as $k) {
                switch ($k['type']) {
                    case 'setting':
                        if(empty($k['validate_function'])){
                	        $k['validate_function'] = array(&$this,'validate_machine');
                	    }
                    	register_setting(
                    		$k['id'],
                    		$k['id'],
                    		$k['validate_function']
                    	);
                    	break;
                	case 'section':
                	    if(empty($k['desc_callback'])){
                	        $k['desc_callback'] = array(&$this,'section_dummy_desc');
                	    }else{
                	        $k['desc_callback'] = array(&$this, $k['desc_callback']);
                	    }
                    	add_settings_section(
                    		$k['id'],
                    		$k['label'],
                    		$k['desc_callback'],
                    		$k['id']
                    	);
                    	break;
                	default:
                    	if(empty($k['callback'])){
                	        $k['callback'] = array(&$this,'field_machine');
                	    }
                    	add_settings_field(
                    		$k['id'],
                    		$k['label'],
                    		$k['callback'],
                    		$k['section_id'],
                    		$k['section_id'],
                    		array('id' => $k['id'], 
                    		'desc' => (isset($k['desc']) ? $k['desc'] : ''),
                    		'setting_id' => $k['setting_id'], 
                    		'class' => (isset($k['class']) ? $k['class'] : ''), 
                    		'type' => $k['type'],
                    		'default_value' => (isset($k['default_value']) ? $k['default_value'] : ''),
                    		'option_values' => (isset($k['option_values']) ? $k['option_values'] : ''))
                    	);
                	    
        	    }
            }
        }

        /**
         * Create a field based on the field type passed in.
         *
         * @since 0.1
         */
        function field_machine($args) {
            extract($args);
        	$options = get_option( $setting_id );
        	switch($type){
        	    case 'textbox':
        	        echo "<input id='$id' class='".(empty($class) ? 'regular-text' : $class)."' name='{$setting_id}[$id]' type='text' value='".esc_attr(empty($options[$id]) ? $default_value : $options[$id])."' />
        	        <br><small class='description'>".(empty($desc) ? '' : $desc)."</small>";
        	        break;
                case 'image':
        	        echo "<input id='$id' class='".(empty($class) ? 'regular-text' : $class)."' name='{$setting_id}[$id]' type='text' value='".(empty($options[$id]) ? $default_value : $options[$id])."' />
        	        <input id='{$id}_upload_image_button' class='button-secondary upload-button' type='button' value='". __('Media Image Library', 'ultimate-maintenance-mode')."' />
        	        <br><small class='description'>".(empty($desc) ? '' : $desc)."</small>
        	        ";
        	        break;
        	    case 'select':
            	    echo "<select id='$id' class='".(empty($class) ? '' : $class)."' name='{$setting_id}[$id]'>";
            	    foreach($option_values as $k=>$v){
            	        if(preg_match("/optgroupend/i",$k)){
            	            echo "</optgroup>";
            	        }else{
            	            if(preg_match("/optgroup/i",$k)){
                	            echo "<optgroup label='$v'>";
                	        }else{

                	            if(preg_match("/empty/i",$k) && empty($default_value)){             
                	                echo "<option value=''>$v</option>";
                	            }else{
            	                    echo "<option value='$k' ".((preg_match("/empty/i",$options[$id] || isset($options[$id]) === false) ? $default_value : $options[$id]) == $k ? 'selected' : '').">$v</option>";
        	                    }
        	                }
        	            }

            	    }
            	    echo "</select>
                    <br><small class='description'>".(empty($desc) ? '' : $desc)."</small>";
                    break;
        	    case 'textarea':
                    echo "<textarea id='$id' class='".(empty($class) ? '' : $class)."' name='{$setting_id}[$id]'>".(empty($options[$id]) ? $default_value : $options[$id])."</textarea>
        	        <br><small class='description'>".(empty($desc) ? '' : $desc)."</small>";
        	        break;
                case 'wpeditor':
                    $content = (empty($options[$id]) ? $default_value : $options[$id]);
                    $editor_id = $id;
                    $args = array('textarea_name'=>"{$setting_id}[$id]"); // Optional arguments.
                    wp_editor( $content, $editor_id, $args );

                    // echo "<textarea id='$id' class='".(empty($class) ? '' : $class)."' name='{$setting_id}[$id]'>".(empty($options[$id]) ? $default_value : $options[$id])."</textarea>
                    // <br><small class='description'>".(empty($desc) ? '' : $desc)."</small>";
                    echo "<small class='description'>".(empty($desc) ? '' : $desc)."</small>";
                    break;
        	    case 'radio':
        	        foreach($option_values as $k=>$v){
        	            echo "<input type='radio' name='{$setting_id}[$id]' value='$k'".((empty($options[$id]) ? $default_value : $options[$id]) == $k ? 'checked' : '')."  /> $v<br/>";
                    }
        	        echo "<small class='description'>".(empty($desc) ? '' : $desc)."</small>";
        	        break;
        	    case 'checkbox':
        	        $count = 0;
        	        foreach($option_values as $k=>$v){
        	            echo "<input type='checkbox' name='{$setting_id}[$id][]' value='$k'".(in_array($k,(empty($options[$id]) ? (empty($default_value) ? array(): $default_value) : $options[$id])) ? 'checked' : '')."  /> $v<br/>";
                        $count++;
                    }
        	        echo "<small class='description'>".(empty($desc) ? '' : $desc)."</small>";
        	        break;
        	    case 'color':
        	        echo "
            	        <input id='$id' type='text' name='{$setting_id}[$id]' value='".(empty($options[$id]) ? $default_value : $options[$id])."' style='background-color:".(empty($options[$id]) ? $default_value : $options[$id]).";' />
                        <input type='button' class='pickcolor button-secondary' value='Select Color'>
                        <div id='colorpicker' style='z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;'></div>
                        <br />
                        <small class='description'>".(empty($desc) ? '' : $desc)."</small>
                        ";
        	        break;
        	}
	
        }

        /**
         * Validates user input before we save it via the Options API. If error add_setting_error
         *
         * @since 0.1
         * @param array $input Contains all the values submited to the POST.
         * @return array $input Contains sanitized values.
         * @todo Figure out best way to validate values.
         */
        function validate_machine($input) {
            $error = false;
            foreach ($this->options as $k) {
                switch($k['type']){
                    case 'setting':
                        break;
                    case 'section':
                        break;
                    default:
                        // Validate a pattern
                        if(isset($pattern) && $pattern){
                    	    if(!preg_match( $pattern, $input[$k['id']])) {
                    	        $error = true;
                        		add_settings_error(
                        			$k['id'],
                        			'seedprod_error',
                        			$k['error_msg'],
                        			'error'
                        		);
                        		unset($input[$k['id']]);
                        	}		
                        }
                        // Sanitize 
                	    if($k['type'] == 'image'){
                	        $input[$k['id']] = esc_url_raw($input[$k['id']]);
                	    }
        	    }
            }
            if(!$error){
 				global $wp_settings_errors;
				$display = true;
				if(!empty($wp_settings_errors)){
					foreach($wp_settings_errors as $k=>$v){
						if($v['code'] == 'seedprod_settings_updated')
							$display = false;
					}
				}
				if($display)
		        	add_settings_error('general', 'seedprod_settings_updated', sprintf(__("Settings saved.  <a target='_blank' href='%s/?mm_preview=true'>Preview &raquo;</a>", 'ultimate-maintenance-mode'),home_url()), 'updated');
            }
        	return $input;
        }

        /**
         * Dummy function to be called by all sections from the Settings API. Define a custom function in the config.
         *
         * @since 0.1
         * @return string Empty
         */
        function section_dummy_desc() {
        	echo '';
        }
        
        /**
         * Returns Font Families
         *
         * @since 0.1
         * @return string or array
         */
        function font_families($family=null) {
            $fonts = array();
            $fonts['_arial'] = 'Helvetica, Arial, sans-serif';
            $fonts['_arial_black'] = 'Arial Black, Arial Black, Gadget, sans-serif';
            $fonts['_georgia'] = 'Georgia,serif';
            $fonts['_helvetica_neue'] = '"Helvetica Neue", Helvetica, Arial, sans-serif';
            $fonts['_impact'] = 'Charcoal,Impact,sans-serif';
            $fonts['_lucida'] = 'Lucida Grande,Lucida Sans Unicode, sans-serif';
            $fonts['_palatino'] = 'Palatino,Palatino Linotype, Book Antiqua, serif';
            $fonts['_tahoma'] = 'Geneva,Tahoma,sans-serif';
            $fonts['_times'] = 'Times,Times New Roman, serif';
            $fonts['_trebuchet'] = 'Trebuchet MS, sans-serif';
            $fonts['_verdana'] = 'Verdana, Geneva, sans-serif';
            if($family){
                $font_family=$fonts[$family];
                if(empty($font_family)){
                    $font_family = '"'. urldecode($family) . '",sans-serif' ;
                }
            }else{
                $font_family=$fonts;  
            }
        	return $font_family;
        }
        
        /**
         * Get list of fonts from google and web safe fonts.
         *
         * @since 0.1
         * @return array 
         */
         function font_field_list($show_google_fonts = true){
              $fonts = unserialize(get_transient('seedprod_fonts'));
              if($fonts === false){
                  if($show_google_fonts){
                      //$query = urlencode('select * from html where url="http://www.google.com/webfonts" and xpath=\'//div[@class="preview"]/span\'');
                      //$request = "http://query.yahooapis.com/v1/public/yql?q={$query}&format=json";
                      //$reponse = wp_remote_get($request);
                      //$result = json_decode($reponse['body']);
                      $result = array('Aclonica','Allan','Allerta','Allerta Stencil','Amaranth','Annie Use Your Telescope','Anonymous Pro','Anton','Architects Daughter','Arimo','Artifika','Arvo','Asset','Astloch','Bangers','Bentham','Bevan','Bigshot One','Bowlby One SC','Brawler','Buda','Cabin','Cabin Sketch','Calligraffitti','Candal','Cantarell','Cardo','Carter One','Caudex','Cedarville Cursive','Cherry Cream Soda','Chewy','Coda','Coming Soon','Copse','Corben','Cousine','Covered By Your Grace','Crafty Girls','Crimson Text','Crushed','Cuprum','Damion','Dancing Script','Dawning of a New Day','Didact Gothic','Droid Sans','Droid Sans Mono','Droid Serif','EB Garamond','Expletus Sans','Fontdiner Swanky','Forum','Francois One','Geo','Goblin One','Goudy Bookletter','Gravitas One','Gruppo','Hammersmith One','Holtwood One SC','Homemade Apple','IM Fell','Inconsolata','Indie Flower','Irish Grover','Josefin Sans','Josefin Slab','Judson','Jura','Just Another Hand','Just Me Again Down Here','Kameron','Kenia','Kranky','Kreon','Kristi','La Belle Aurore','Lato','League Script','Lekton','Limelight','Lobster','Lobster Two','Lora','Love Ya Like A Sister','Loved by the King','Luckiest Guy','Maiden Orange','Mako','Maven Pro','Meddon','MedievalSharp','Megrim','Merriweather','Metrophobic','Michroma','Miltonian','Molengo','Monofett','Mountains of Christmas','Muli','Neucha','Neuton','News Cycle','Nobile','Nova','Nunito','OFL Sorts Mill Goudy TT','Old Standard TT','Open Sans','Orbitron','Oswald','Over the Rainbow','PT Sans','PT Serif','Pacifico','Patrick Hand','Paytone One','Permanent Marker','Philosopher','Play','Playfair Display','Podkova','Puritan','Quattrocento','Quattrocento Sans','Radley','Raleway','Redressed','Reenie Beanie','Rock Salt','Rokkitt','Ruslan Display','Schoolbell','Shadows Into Light','Shanti','Sigmar One','Six Caps','Slackey','Smythe','Sniglet','Special Elite','Stardos Stencil','Sue Ellen Francisco','Sunshiney','Swanky and Moo Moo','Syncopate','Tangerine','Tenor Sans','Terminal Dosis Light','The Girl Next Door','Tinos','Ubuntu','Ultra','UnifrakturCook','UnifrakturMaguntia','Unkempt','VT','Varela','Vibur','Vollkorn','Waiting for the Sunrise','Wallpoet','Walter Turncoat','Wire One','Yanone Kaffeesatz','Zeyada');
                      foreach($result as $v){
                         $google_fonts[urlencode($v)] = $v;
                      }
                      asort($google_fonts);
                      $pre2["optgroup_2"] = "Google Fonts";
                      $post2["optgroupend_2"] = "";
                 }
                 $post1["optgroupend_1"] = "";
                 $system_fonts['_arial'] = 'Arial';
                 $system_fonts['_arial_black'] = 'Arial Black';
                 $system_fonts['_georgia'] = 'Georgia';
                 $system_fonts['_helvetica_neue'] = 'Helvetica Neue';
                 $system_fonts['_impact'] = 'Impact';
                 $system_fonts['_lucida'] = 'Lucida Grande';
                 $system_fonts['_palatino'] = 'Palatino';
                 $system_fonts['_tahoma'] = 'Tahoma';
                 $system_fonts['_times'] = 'Times New Roman';
                 $system_fonts['_trebuchet'] = 'Trebuchet';
                 $system_fonts['_verdana'] = 'Verdana';
                 $pre0["empty_0"] = "Select a Font";
                 $pre1["optgroup_1"] = "System Fonts";
                 $pre2["optgroup_2"] = "Google Fonts";
                 $fonts =  $pre0 + $pre1 + $system_fonts+ $post1+ $pre2 + $google_fonts + $post2;
                 if(!empty($google_fonts)){
                     set_transient('seedprod_fonts',serialize( $fonts ),86400);
                }
             }
             return $fonts;
         }
         
         /**
          * SeedProd version of WP's do_settings_sections
          *
          * @since 0.1
          */
         function seedprod_do_settings_sections($page) {
             global $wp_settings_sections, $wp_settings_fields;

             if ( !isset($wp_settings_sections) || !isset($wp_settings_sections[$page]) )
                 return;

             foreach ( (array) $wp_settings_sections[$page] as $section ) {
                 echo "<h3 class='hndle'>{$section['title']}</h3>\n";
                 echo '<div class="inside">';
                 call_user_func($section['callback'], $section);
                 if ( !isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id']]) )
                     continue;
                 echo '<table class="form-table">';
                 do_settings_fields($page, $section['id']);
                 echo '</table>';
                 echo '</div>';
             }
         }

    }
}
?>