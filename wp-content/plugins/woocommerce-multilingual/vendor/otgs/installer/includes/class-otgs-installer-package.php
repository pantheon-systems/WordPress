<?php

class OTGS_Installer_Package {

	private $key;
	private $id;
	private $name;
	private $description;
	private $image_url;
	private $order;
	private $parent;
	private $products = array();

	public function __construct( array $params = array() ) {
		foreach ( get_object_vars( $this ) as $property => $value ) {
			if ( array_key_exists( $property, $params ) ) {
				$this->$property = $params[ $property ];
			}
		}
	}

	public function get_key() {
		return $this->key;
	}

	public function get_products() {
		return $this->products;
	}

	public function get_product_by_subscription_type( $type ) {
		return $this->get_product_by( 'get_subscription_type', $type );
	}

	public function get_product_by_subscription_type_equivalent( $type ) {
		return $this->get_product_by( 'get_subscription_type_equivalent', $type );
	}

	public function get_product_by( $function, $type ) {
		foreach ( $this->products as $product ) {
			if ( $type === $product->$function() ) {
				return $product;
			}
		}
		return null;
	}

	public function get_product_by_subscription_type_on_upgrades( $type ) {
		foreach ( $this->products as $product ) {
			foreach ( $product->get_upgrades() as $upgrade ) {
				if ( $type === $upgrade['subscription_type'] ) {
					return $product;
				}
			}
		}
		return null;
	}

	public function get_id() {
		return $this->id;
	}

	public function get_name() {
		return $this->name;
	}

	public function get_description() {
		return $this->description;
	}

	public function get_image_url() {
		return $this->image_url;
	}

	public function get_order() {
		return $this->order;
	}

	public function get_parent() {
		return $this->parent;
	}
}