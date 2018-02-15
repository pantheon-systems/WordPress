<?php

abstract class PMXI_Session {

	/** @var int $_customer_id */
	protected $_import_id;

    /** @var array $_data  */
    protected $_data = array();

    /** @var bool $_dirty When something changes */
    protected $_dirty = false;

    /**
     * __get function.
     *
     * @access public
     * @param mixed $key
     * @return mixed
     */
    public function __get( $key ) {
        return $this->get( $key );
    }

    /**
     * __set function.
     *
     * @access public
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function __set( $key, $value ) {
        $this->set( $key, $value );
    }

     /**
     * __isset function.
     *
     * @access public
     * @param mixed $key
     * @return bool
     */
    public function __isset( $key ) {
        return isset( $this->_data[ sanitize_title( $key ) ] );
    }

    /**
     * __unset function.
     *
     * @access public
     * @param mixed $key
     * @return void
     */
    public function __unset( $key ) {

        if ( isset( $this->_data[ $key ] ) ) {
            unset( $this->_data[ $key ] );       		
            $this->_dirty = true;
        }
                
    }

    /**
     * Get a session variable
     *
     * @param string $key
     * @param  mixed $default used if the session variable isn't set
     * @return mixed value of session variable
     */
    public function get( $key, $default = null ) {
        $key = sanitize_key( $key );
        return isset( $this->_data[ $key ] ) ? maybe_unserialize( $this->_data[ $key ] ) : $default;
    }

    /**
     * Set a session variable
     *
     * @param string $key
     * @param mixed $value
     */
    public function set( $key, $value ) {
        $this->_data[ sanitize_key( $key ) ] = maybe_serialize( $value );
        $this->_dirty = true;
    }

   	/**
	 * get_import_id function.
	 *
	 * @access public
	 * @return int
	 */
	public function get_import_id() {
		return $this->_import_id;
	}
}