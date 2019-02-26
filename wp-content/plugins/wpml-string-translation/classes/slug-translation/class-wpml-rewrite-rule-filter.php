<?php

class WPML_Rewrite_Rule_Filter implements IWPML_ST_Rewrite_Rule_Filter {

	/** @var IWPML_ST_Rewrite_Rule_Filter[] */
	private $filters;

	/**
	 * @param IWPML_ST_Rewrite_Rule_Filter[] $filters
	 */
	public function __construct( $filters ) {
		$this->filters = $filters;
	}

	/**
	 * @param array|false|null $rules
	 *
	 * @return array
	 */
	function rewrite_rules_filter( $rules ) {
		foreach ( $this->filters as $filter ) {
			$rules = $filter->rewrite_rules_filter( $rules );
		}

		return $rules;
	}
}
