<?php

class WPML_ST_String_Dependencies_Builder {

	/** @var WPML_ST_String_Dependencies_Records $records */
	private $records;

	private $types_map = array(
		'post'    => 'package',
		'package' => 'string',
	);

	public function __construct( WPML_ST_String_Dependencies_Records $records ) {
		$this->records = $records;
	}

	/**
	 * @param string $type
	 * @param int    $id
	 *
	 * @return WPML_ST_String_Dependencies_Node
	 */
	public function from( $type, $id ) {
		$parent_id = $this->records->get_parent_id_from( $type, $id );

		if ( $parent_id ) {
			$parent       = $this->from( $this->get_parent_type( $type ), $parent_id );
			$initial_node = $parent->search( $id, $type );

			if ( $initial_node ) {
				$initial_node->set_needs_refresh( true );
			}

			return $parent;
		}

		$node = new WPML_ST_String_Dependencies_Node( $id, $type );
		$node->set_needs_refresh( true );
		return $this->populate_node( $node );
	}

	/**
	 * @param WPML_ST_String_Dependencies_Node $node
	 *
	 * @return WPML_ST_String_Dependencies_Node
	 */
	private function populate_node( WPML_ST_String_Dependencies_Node $node ) {
		$child_ids = $this->records->get_child_ids_from( $node->get_type(), $node->get_id() );

		if ( $child_ids ) {
			$child_type = $this->get_child_type( $node->get_type() );

			foreach ( $child_ids as $id ) {
				$child_node = new WPML_ST_String_Dependencies_Node( $id, $child_type );
				$node->add_child( $child_node );
				$this->populate_node( $child_node );
			}
		}

		return $node;
	}

	/**
	 * @param string $type
	 *
	 * @return false|string
	 */
	private function get_parent_type( $type ) {
		return array_search( $type, $this->types_map, true );
	}

	/**
	 * @param string $type
	 *
	 * @return null|string
	 */
	private function get_child_type( $type ) {
		return isset( $this->types_map[ $type ] ) ? $this->types_map[ $type ] : null;
	}
}