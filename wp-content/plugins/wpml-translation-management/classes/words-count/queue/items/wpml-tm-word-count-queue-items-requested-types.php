<?php

class WPML_TM_Word_Count_Queue_Items_Requested_Types implements IWPML_TM_Word_Count_Queue_Items {

	const OPTION_KEY = 'wpml_word_count_queue_items_requested_types';

	const STEP_STANDALONE_PACKAGES = 1;
	const STEP_POST_PACKAGES       = 2;
	const STEP_POSTS               = 3;
	const STEP_COMPLETED           = 4;

	/** @var WPML_TM_Word_Count_Records $records */
	private $records;

	/** @var array $requested_types to be processed */
	private $requested_types;

	/** @var string $step */
	private $step;

	/** @var array|null $items */
	private $items = array(
		'string'  => array(),
		'package' => array(),
		'post'    => array(),
	);

	public function __construct( WPML_TM_Word_Count_Records $records ) {
		$this->records = $records;
	}

	/**
	 * @return array|null a tuple containing the element id and type or null if queue is empty
	 */
	public function get_next() {
		$this->init_queue();

		foreach ( array( 'string', 'package', 'post' ) as $type ) {

			if ( $this->items[ $type ] ) {
				return array( reset( $this->items[ $type ] ), $type );
			}
		}

		return null;
	}

	private function init_queue() {
		if ( ! $this->step ) {
			$this->restore_queue_from_db();
		}

		if ( ! $this->has_items() ) {
			$this->init_step();
		}
	}

	private function restore_queue_from_db() {
		$this->step = self::STEP_STANDALONE_PACKAGES;
		$options    = get_option( self::OPTION_KEY, array() );

		if ( isset( $options['step'] ) ) {
			$this->step = $options['step'];
		}

		if ( isset( $options['requested_types'] ) ) {
			$this->requested_types = $options['requested_types'];
		}

		if ( isset( $options['items'] ) ) {
			$this->items = $options['items'];
		}
	}

	private function init_step() {
		switch ( $this->step ) {

			case self::STEP_STANDALONE_PACKAGES:
				$this->add_standalone_packages_to_queue();
				break;

			case self::STEP_POST_PACKAGES:
				$this->add_post_packages_to_queue();
				break;

			case self::STEP_POSTS:
				$this->add_posts_to_queue();
				break;
		}

		$this->make_item_keys_equals_to_id();
		$this->maybe_move_to_next_step();
	}

	private function add_standalone_packages_to_queue() {
		if ( ! empty( $this->requested_types['package_kinds'] ) ) {
			$this->items['package'] = $this->records->get_package_ids_from_kind_slugs( $this->requested_types['package_kinds'] );
			$this->items['string'] = $this->records->get_strings_ids_from_package_ids( $this->items['package'] );
		}
	}

	private function add_post_packages_to_queue() {
		if ( ! empty( $this->requested_types['post_types'] ) ) {
			$this->items['package'] = $this->records->get_package_ids_from_post_types( $this->requested_types['post_types'] );
			$this->items['string'] = $this->records->get_strings_ids_from_package_ids( $this->items['package'] );
		}
	}

	private function add_posts_to_queue() {
		if ( ! empty( $this->requested_types['post_types'] ) ) {
			$this->items['post'] = $this->records->get_post_source_ids_from_types( $this->requested_types['post_types'] );
		}
	}

	private function make_item_keys_equals_to_id() {
		foreach ( $this->items as $type => $ids ) {
			if ( $this->items[ $type ] ) {
				$this->items[ $type ] = array_combine( array_values( $this->items[ $type ] ), $this->items[ $type ] );
			}
		}
	}

	private function maybe_move_to_next_step() {
		if ( ! $this->has_items() && ! $this->is_completed() ) {
			$this->step++;
			$this->init_step();
		}
	}

	/**
	 * @param int    $id
	 * @param string $type
	 */
	public function remove( $id, $type ) {
		if ( isset( $this->items[ $type ][ $id ] ) ) {
			unset( $this->items[ $type ][ $id ] );
		}

		$this->maybe_move_to_next_step();
	}

	/** @return bool */
	private function has_items() {
		return ! empty( $this->items['string'] )
		       || ! empty( $this->items['package'] )
		       || ! empty( $this->items['post'] );
	}

	/** @return bool */
	public function is_completed() {
		return $this->step === self::STEP_COMPLETED;
	}

	public function save() {
		$options = array(
			'step'            => $this->step,
			'requested_types' => $this->requested_types,
			'items'           => $this->items,
		);

		update_option( self::OPTION_KEY, $options, false );
	}

	public function reset( array $requested_types ) {
		$this->step            = self::STEP_STANDALONE_PACKAGES;
		$this->requested_types = $requested_types;
		$this->items           = null;
		$this->save();
	}
}
