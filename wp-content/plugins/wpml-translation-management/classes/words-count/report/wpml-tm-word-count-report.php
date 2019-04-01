<?php

class WPML_TM_Word_Count_Report {

	const OPTION_KEY          = 'wpml_word_count_report';
	const POSTS_PER_MINUTE    = 1200;
	const PACKAGES_PER_MINUTE = 5000;
	const POST_TYPES          = 'post_types';
	const PACKAGE_KINDS       = 'package_kinds';
	const IS_REQUESTED        = 'isRequested';

	/** @var WPML_TM_Word_Count_Records $records */
	private $records;

	/** @var WPML_TM_Word_Count_Report_View $view */
	private $view;

	/** @var SitePress $sitepress */
	private $sitepress;

	/** @var array $post_types */
	private $post_types;

	/** @var WPML_Package_Helper $st_package_helper */
	private $st_package_helper;

	/** @var array $package_kinds */
	private $package_kinds = array();

	/** @var bool $requested_types_status */
	private $requested_types_status;

	/** @var array $data */
	private $data;

	public function __construct(
		WPML_TM_Word_Count_Report_View $view,
		WPML_TM_Word_Count_Records $records,
		SitePress $sitepress,
		WPML_Package_Helper $st_package_helper = null,
		$requested_types_status
	) {
		$this->view                   = $view;
		$this->records                = $records;
		$this->sitepress              = $sitepress;
		$this->st_package_helper      = $st_package_helper;
		$this->requested_types_status = $requested_types_status;
	}

	/**
	 * @return string
	 */
	public function render() {
		$this->init_data();
		$data = array(
			self::POST_TYPES    => array(),
			self::PACKAGE_KINDS => array(),
		);

		foreach ( $this->get_post_types() as $post_type_ ) {
			$data[ self::POST_TYPES ][ $post_type_->name ] = $this->build_type_row( self::POST_TYPES, $post_type_ );
		}

		foreach ( $this->get_package_kinds() as $package_kind ) {
			$data[ self::PACKAGE_KINDS ][ $package_kind->name ] = $this->build_type_row( self::PACKAGE_KINDS, $package_kind );
		}

		$this->data = $data;
		$this->save_data();

		$model = array(
			'countInProgress' => $this->requested_types_status === WPML_TM_Word_Count_Hooks_Factory::PROCESS_IN_PROGRESS,
			'data'            => $this->data,
			'totals'          => $this->get_totals(),
		);

		return $this->view->show( $model );
	}

	private function is_requested( $group, $type ) {
		return isset( $this->data[ $group ][ $type ][ self::IS_REQUESTED ] )
		       && $this->data[ $group ][ $type ][ self::IS_REQUESTED ];
	}

	/**
	 * @param string                $group
	 * @param WP_Post_Type|stdClass $type_object
	 *
	 * @return array|null
	 */
	private function build_type_row( $group, $type_object ) {
		$count_items = $this->records->count_items_by_type( $group, $type_object->name );

		// Do not include in the report if it has no item
		if ( ! $count_items ) {
			return null;
		}

		$count_words     = '';
		$status          = WPML_TM_Word_Count_Hooks_Factory::PROCESS_PENDING;
		$completed_items = $this->records->count_word_counts_by_type( $group, $type_object->name );

		if ( $this->is_requested( $group, $type_object->name ) ) {
			$status = $completed_items < $count_items
				? WPML_TM_Word_Count_Hooks_Factory::PROCESS_IN_PROGRESS
				: WPML_TM_Word_Count_Hooks_Factory::PROCESS_COMPLETED;
			$count_words = $this->records->get_word_counts_by_type( $group, $type_object->name )->get_total_words();
		} elseif ( ! empty( $this->data[ $group ][ $type_object->name ]['countWords'] ) ) {
			$status      = WPML_TM_Word_Count_Hooks_Factory::PROCESS_COMPLETED;
			$count_words = $this->data[ $group ][ $type_object->name ]['countWords'];
		}

		$label            = $type_object->label;
		$items_per_minute = self::POSTS_PER_MINUTE;

		if ( self::PACKAGE_KINDS === $group ) {
			$items_per_minute = self::PACKAGES_PER_MINUTE;
		}

		return array(
			'group'          => $group,
			'type'           => $type_object->name,
			'typeLabel'      => $label,
			'countItems'     => $count_items,
			'completedItems' => $completed_items,
			'countWords'     => $count_words,
			'estimatedTime'  => ceil( $count_items / $items_per_minute ),
			'status'         => $status,
			'needsRefresh'   => $completed_items < $count_items,
			'isRequested'    => $this->is_requested( $group, $type_object->name ),
		);
	}

	private function get_totals() {
		$totals = array(
			'completedItems' => 0,
			'countItems'     => 0,
			'countWords'     => 0,
			'estimatedTime'  => 0,
			'requestedTypes' => 0,
		);

		foreach ( $this->data as $group ) {

			foreach ( $group as $type ) {
				$totals['completedItems'] += (int) $type['completedItems'];
				$totals['countItems']     += (int) $type['countItems'];
				$totals['countWords']     += (int) $type['countWords'];
				$totals['estimatedTime']  += (int) $type['estimatedTime'];
				$totals['requestedTypes'] += (int) $type['isRequested'];
			}
		}

		return $totals;
	}

	public function set_requested_types( array $requested_types ) {
		$this->init_data();
		$this->set_requested_group( self::POST_TYPES, $this->get_post_types(), $requested_types );
		$this->set_requested_group( self::PACKAGE_KINDS, $this->get_package_kinds(), $requested_types );
		$this->save_data();
	}

	private function set_requested_group( $group, $types, $requested_types ) {
		foreach ( $types as $type ) {

			if ( false === array_search( $type->name, (array) $requested_types[ $group ], true ) ) {
				$this->data[ $group ][ $type->name ][ self::IS_REQUESTED ] = false;
			} else {
				$this->data[ $group ][ $type->name ][ self::IS_REQUESTED ] = true;
			}
		}
	}

	private function init_data() {
		$this->data = get_option( self::OPTION_KEY, array() );
	}

	private function save_data() {
		$this->data[ self::POST_TYPES ]    = array_filter( $this->data[ self::POST_TYPES ] );
		$this->data[ self::PACKAGE_KINDS ] = array_filter( $this->data[ self::PACKAGE_KINDS ] );
		update_option( self::OPTION_KEY, $this->data, false );
	}

	private function get_post_types() {
		if ( ! $this->post_types ) {
			$this->post_types = $this->sitepress->get_translatable_documents();
		}

		return $this->post_types;
	}

	public function get_package_kinds() {
		if ( $this->st_package_helper && ! $this->package_kinds ) {
			$this->package_kinds = $this->st_package_helper->get_translatable_types( array() );
		}

		return $this->package_kinds;
	}
}
