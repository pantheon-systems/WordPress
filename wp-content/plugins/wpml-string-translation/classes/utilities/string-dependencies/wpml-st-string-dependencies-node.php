<?php

class WPML_ST_String_Dependencies_Node {

	/** @var WPML_ST_String_Dependencies_Node|null $parent */
	private $parent;

	/** @var WPML_ST_String_Dependencies_Node[] $children */
	private $children = array();

	/** @var bool $iteration_completed */
	private $iteration_completed = false;

	/** @var int|null $id */
	private $id;

	/** @var string|null $type */
	private $type;

	/** @var bool|null $needs_refresh */
	private $needs_refresh;

	public function __construct( $id = null, $type = null ) {
		$this->id   = $id;
		$this->type = $type;
	}

	public function get_id() {
		return $this->id;
	}

	public function get_type() {
		return $this->type;
	}

	public function set_needs_refresh( $needs_refresh ) {
		$this->needs_refresh = $needs_refresh;
	}

	public function get_needs_refresh() {
		return $this->needs_refresh;
	}

	public function set_parent( WPML_ST_String_Dependencies_Node $node ) {
		$node->add_child( $this );
		$this->parent = $node;
	}

	public function get_parent() {
		return $this->parent;
	}

	public function add_child( WPML_ST_String_Dependencies_Node $node ) {
		if ( ! isset( $this->children[ $node->get_hash() ] ) ) {
			$this->children[ $node->get_hash() ] = $node;
			$node->set_parent( $this );
		}
	}

	public function remove_child( WPML_ST_String_Dependencies_Node $node ) {
		unset( $this->children[ $node->get_hash() ] );
		unset( $node );
	}

	public function detach() {
		if ( $this->parent ) {
			$this->parent->remove_child( $this );
		}
	}

	/**
	 * Iteration DFS in post-order
	 *
	 * @return WPML_ST_String_Dependencies_Node
	 */
	public function get_next() {
		if ( $this->children ) {
			$first_child = reset( $this->children );

			if ( $first_child ) {
				/** @var WPML_ST_String_Dependencies_Node $first_child */
				return $first_child->get_next();
			}
		}

		if ( ! $this->parent && ! $this->iteration_completed ) {
			$this->iteration_completed = true;
		}
		
		return $this;
	}

	/**
	 * Search DFS in pre-order
	 *
	 * @param int    $id
	 * @param string $type
	 *
	 * @return bool|WPML_ST_String_Dependencies_Node
	 */
	public function search( $id, $type ) {
		if ( $this->id === $id && $this->type === $type ) {
			return $this;
		}

		if ( $this->children ) {

			foreach ( $this->children as $child ) {
				$node = $child->search( $id, $type );

				if ( $node ) {
					return $node;
				}
			}
		}

		return false;
	}

	public function iteration_completed() {
		return $this->iteration_completed;
	}

	/**
	 * @return string|stdClass
	 */
	public function to_json() {
		$object = new stdClass();

		foreach ( $this->get_item_properties() as $property ) {
			if ( null !== $this->{$property} ) {
				$object->{$property} = $this->{$property};
			}
		}

		if ( $this->children ) {
			foreach ( $this->children as $child ) {
				$object->children[] = $child->to_json();
			}
		}

		if ( ! $this->parent ) {
			return json_encode( $object );
		}

		return $object;
	}

	/**
	 * @param string|self $object
	 */
	public function from_json( $object ) {
		if ( is_string( $object ) ) {
			$object = json_decode( $object );
		}

		foreach ( $this->get_item_properties() as $property ) {
			if ( isset( $object->{$property} ) ) {
				$this->{$property} = $object->{$property};
			}
		}

		if ( isset( $object->children ) ) {
			foreach ( $object->children as $child ) {
				$child_node = new self( $child->id, $child->type );
				$child_node->from_json( $child );
				$child_node->set_parent( $this );
			}
		}
	}

	private function get_item_properties() {
		return array( 'id', 'type', 'needs_refresh' );
	}

	private function get_hash() {
		return spl_object_hash( $this );
	}
}
