<?php

/*
 * User Role Editor plugin: advertisement showing class
 * Author: Vladimir Garagulya
 * email: vladimir@shinephp.com
 * site: http://shinephp.com
 * 
 */

class URE_Advertisement {

    private $slots_quantity = 1;
    private $slots = array();
				
    
    function __construct() {

        $this->init();
        
    }
    // end of __construct

    /**
      * Returns random number not included into input array
      * 
      * @param array $used - array of numbers used already
      * 
      * @return int
      */
     private function rand_unique( $used = array(-1), $max_ind ) {
        if ( $max_ind<0 ) {
            $max_ind = 0;
        }
        $index = rand( 0, $max_ind );
        $iterations = 0;
        while ( in_array( $index, $used ) && $iterations<=$max_ind * 3 ) {
            $index = rand( 0, $max_ind );
            $iterations++;
        }

        return $index;
    }
    // return rand_unique()
    
    
    private function init() {
 
        $this->slots = array();
        $used = array(-1);
        $max_ind = $this->slots_quantity - 1;
        $index = $this->rand_unique( $used, $max_ind );
        $this->slots[$index] = $this->admin_menu_editor();
        /*
        $used[] = $index;        
        $index = $this->rand_unique( $used, $max_ind );
        $this->slots[$index] = $this->some_other_slot();        
        ksort( $this->slots );
         * 
         */
    }
    // end of init()
    
/*    
    private function some_other_slot() {
        $output = '
			<div style="text-align: center;">
   bla-bla-bla;
   </div>';
        return $output;
    }
*/    
    
    // content of Admin Menu Editor advertisement slot
    private function admin_menu_editor() {

        $output = '
			<div style="text-align: center;">
				<a href="https://adminmenueditor.com/?utm_source=UserRoleEditor&utm_medium=banner&utm_campaign=Plugins" target="_new" >
					<img src="' . URE_PLUGIN_URL . 'images/admin-menu-editor-pro.jpg' . '" alt="Admin Menu Editor Pro" 
									title="Move, rename, hide, add admin menu items, restrict access" width="250" height="250" />
				</a>
			</div>  
			';

        return $output;
    }
    // end of admin_menu_editor()
    

    /**
     * Output all existed ads slots
     */
    public function display() {
        
        if ( empty( $this->slots ) ) {
            return;
        }
?>
    <div id="ure-sidebar" class="ure_table_cell" >
<?php
        foreach ($this->slots as $slot) {
            echo $slot . "\n";
        }
?>
    </div>     
        <?php
    }
    // end of display()
    
    
}
// end of URE_Advertisement class