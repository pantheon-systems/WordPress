<?php

class PMXE_Handler extends PMXE_Session 
{
	/** cookie name */
	private $_cookie;

	/** session due to expire timestamp */
	private $_session_expiring;

	/** session expiration timestamp */
	private $_session_expiration;

	/** Bool based on whether a cookie exists **/
	private $_has_cookie = false;

	/**
	 * Constructor for the session class.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() 
	{
		
		$this->set_session_expiration();

		$this->_import_id = $this->generate_import_id();		

		$this->_data = $this->get_session_data();		    	

    }      

    /**
     * Return true if the current user has an active session, i.e. a cookie to retrieve values
     * @return boolean
     */
    public function has_session() 
    {
    	return isset( $_COOKIE[ $this->_cookie ] ) || $this->_has_cookie || is_user_logged_in();
    }

    /**
     * set_session_expiration function.
     *
     * @access public
     * @return void
     */
    public function set_session_expiration() 
    {
	    $this->_session_expiring    = time() + intval( apply_filters( 'wpallexport_session_expiring', 60 * 60 * 47 ) ); // 47 Hours
		$this->_session_expiration  = time() + intval( apply_filters( 'wpallexport_session_expiration', 60 * 60 * 48 ) ); // 48 Hours
    }
	
	public function generate_import_id() 
	{
		$input = new PMXE_Input();
		$import_id = $input->get('id', 'new');
		
		return $import_id;
	}

	/**
	 * get_session_data function.
	 *
	 * @access public
	 * @return array
	 */
	public function get_session_data() 
	{					
		global $wpdb;

		$session = $wpdb->get_row( $wpdb->prepare("SELECT option_name, option_value FROM $wpdb->options WHERE option_name = %s", '_wpallexport_session_' . $this->_import_id . '_'), ARRAY_A );

		return empty($session) ? array() : maybe_unserialize($session['option_value']);
	}

	/**
	 * get_session_data function.
	 *
	 * @access public
	 * @return array
	 */
	public function get_clear_session_data() 
	{
		$this->_data = $this->get_session_data();			
		$clear_data = array();
		foreach ($this->_data as $key => $value) {
			$ckey = sanitize_key( $key );
			$clear_data[ $ckey ] = maybe_unserialize( $value );			
		}

		return $clear_data;
	}

    /**
     * save_data function.
     *
     * @access public
     * @return void
     */
    public function save_data() 
    {
    	// Dirty if something changed - prevents saving nothing new
    	if ( $this->_dirty && $this->has_session() ) 
    	{
			$session_option        = '_wpallexport_session_' . $this->_import_id . '_';
			$session_expiry_option = '_wpallexport_session_expires_' . $this->_import_id . '_';			

			global $wpdb;

			$session = $wpdb->get_row( $wpdb->prepare("SELECT option_name, option_value FROM $wpdb->options WHERE option_name = %s", $session_option), ARRAY_A );			

	    	if ( empty($session) ) 
	    	{
	    		$wpdb->query($wpdb->prepare("INSERT INTO `$wpdb->options` (`option_name`, `option_value`, `autoload`) VALUES (%s, %s, 'no')", $session_option, serialize($this->_data)));
	    		$wpdb->query($wpdb->prepare("INSERT INTO `$wpdb->options` (`option_name`, `option_value`, `autoload`) VALUES (%s, %s, 'no')", $session_expiry_option, $this->_session_expiration));
	    		// add_option( $session_option, $this->_data, '', 'no' );
		    	// add_option( $session_expiry_option, $this->_session_expiration, '', 'no' );		    	
	    	} else {
		    	// update_option( $session_option, $this->_data );
		    	$wpdb->query($wpdb->prepare("UPDATE `$wpdb->options` SET `option_value` = %s WHERE `option_name` = %s", serialize($this->_data), $session_option));
	    	}	    						
	    }	    
    }    

    public function clean_session( $import_id = 'new' )
    {    
		global $wpdb;

    	$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name = %s", '_wpallexport_session_' . $import_id . '_') );
    	$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name = %s", '_wpallexport_session_expires_' . $import_id . '_') );
    }
}