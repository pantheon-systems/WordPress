<?php

class WPML_TM_Word_Count_Single_Process {

	/** @var IWPML_TM_Word_Count_Set[] $setters */
	private $setters;

	/** @var WPML_ST_String_Dependencies_Builder $dependencies_builder */
	private $dependencies_builder;

	/**
	 * @param IWPML_TM_Word_Count_Set[]           $setters
	 * @param WPML_ST_String_Dependencies_Builder $dependencies_builder
	 */
	public function __construct( array $setters, WPML_ST_String_Dependencies_Builder $dependencies_builder = null ) {
		$this->setters              = $setters;
		$this->dependencies_builder = $dependencies_builder;
	}

	/**
	 * @param string $element_type
	 * @param int    $element_id
	 */
	public function process( $element_type, $element_id ) {
		if ( $this->dependencies_builder ) {
			$dependencies_tree = $this->dependencies_builder->from( $element_type, $element_id );

			while ( ! $dependencies_tree->iteration_completed() ) {
				$node = $dependencies_tree->get_next();
				$this->setters[ $node->get_type() ]->process( $node->get_id() );
				$node->detach();
			}
		} elseif ( 'post' === $element_type ) {
			$this->setters['post']->process( $element_id );
		}
	}
}
