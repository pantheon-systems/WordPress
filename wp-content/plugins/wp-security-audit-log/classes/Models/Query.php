<?php
/**
 * Class: Query Model Class
 *
 * Query model is the class for all the query conditions.
 *
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Query Class.
 *
 * Query model is the class for all the query conditions.
 *
 * @package Wsal
 */
class WSAL_Models_Query {

	/**
	 * Table Column.
	 *
	 * @var array
	 */
	protected $columns = array();

	/**
	 * Query Conditions.
	 *
	 * @var array
	 */
	protected $conditions = array();

	/**
	 * Order By.
	 *
	 * @var array
	 */
	protected $orderBy = array();

	/**
	 * Offset.
	 *
	 * @var mixed
	 */
	protected $offset = null;

	/**
	 * Limit.
	 *
	 * @var mixed
	 */
	protected $limit = null;

	/**
	 * From.
	 *
	 * @var array
	 */
	protected $from = array();

	/**
	 * Meta Join.
	 *
	 * @var bool
	 */
	protected $meta_join = false;

	/**
	 * Search Condition.
	 *
	 * @var mixed
	 */
	protected $searchCondition = null;

	/**
	 * Use Default Adapter.
	 *
	 * @var bool
	 */
	protected $useDefaultAdapter = false;

	/**
	 * Method: Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Initialize a connector singleton.
	 *
	 * @return WSAL_Connector_ConnectorInterface
	 */
	public function getConnector() {
		if ( ! empty( $this->connector ) ) {
			return $this->connector;
		}
		if ( $this->useDefaultAdapter ) {
			$this->connector = WSAL_Connector_ConnectorFactory::GetDefaultConnector();
		} else {
			$this->connector = WSAL_Connector_ConnectorFactory::GetConnector();
		}
		return $this->connector;
	}

	/**
	 * Gets the adapter.
	 *
	 * @return WSAL_Adapters_MySQL_Query
	 */
	public function getAdapter() {
		return $this->getConnector()->getAdapter( 'Query' );
	}

	/**
	 * Add a column.
	 *
	 * @param mixed $column - Column value.
	 * @return self
	 */
	public function addColumn( $column ) {
		$this->columns[] = $column;
		return $this;
	}

	/**
	 * Clear all columns.
	 *
	 * @return self
	 */
	public function clearColumns() {
		$this->columns = array();
		return $this;
	}

	/**
	 * Get columns.
	 *
	 * @return array $columns
	 */
	public function getColumns() {
		return $this->columns;
	}

	/**
	 * Set all columns.
	 *
	 * @param array $columns - Columns values.
	 * @return self
	 */
	public function setColumns( $columns ) {
		$this->columns = $columns;
		return $this;
	}

	/**
	 * Add conditions.
	 *
	 * @param string $field - Condition field.
	 * @param mixed  $value - Condition value.
	 * @return self
	 */
	public function addCondition( $field, $value ) {
		$this->conditions[ $field ] = $value;
		return $this;
	}

	/**
	 * Add OR condition.
	 *
	 * @param array $add_conditions - Multi conditions.
	 */
	public function addORCondition( $add_conditions ) {
		$this->conditions[] = $add_conditions;
	}

	/**
	 * Clear all conditions.
	 *
	 * @return self
	 */
	public function clearConditions() {
		$this->conditions = array();
		return $this;
	}

	/**
	 * Get all conditions.
	 *
	 * @return array $conditions
	 */
	public function getConditions() {
		return $this->conditions;
	}

	/**
	 * Add order by.
	 *
	 * @param string  $field - Field name.
	 * @param boolean $is_descending - (Optional) Ascending/descending.
	 * @return self
	 */
	public function addOrderBy( $field, $is_descending = false ) {
		$order = ($is_descending) ? 'DESC' : 'ASC';
		$this->orderBy[ $field ] = $order;
		return $this;
	}

	/**
	 * Clear order by.
	 *
	 * @return self
	 */
	public function clearOrderBy() {
		$this->orderBy = array();
		return $this;
	}

	/**
	 * Get order by.
	 *
	 * @return array $orderBy
	 */
	public function getOrderBy() {
		return $this->orderBy;
	}

	/**
	 * Add from.
	 *
	 * @param string $from_data_set - Data set.
	 * @return self
	 */
	public function addFrom( $from_data_set ) {
		$this->from[] = $from_data_set;
		return $this;
	}

	/**
	 * Reset from.
	 *
	 * @return self
	 */
	public function clearFrom() {
		$this->from = array();
		return $this;
	}

	/**
	 * Get from.
	 *
	 * @return string $from data set
	 */
	public function getFrom() {
		return $this->from;
	}

	/**
	 * Gets the value of limit.
	 *
	 * @return mixed
	 */
	public function getLimit() {
		return $this->limit;
	}

	/**
	 * Sets the value of limit.
	 *
	 * @param mixed $limit - The limit.
	 * @return self
	 */
	public function setLimit( $limit ) {
		$this->limit = $limit;
		return $this;
	}

	/**
	 * Gets the value of offset.
	 *
	 * @return mixed
	 */
	public function getOffset() {
		return $this->offset;
	}

	/**
	 * Sets the value of offset.
	 *
	 * @param mixed $offset - The offset.
	 * @return self
	 */
	public function setOffset( $offset ) {
		$this->offset = $offset;
		return $this;
	}

	/**
	 * Adds condition.
	 *
	 * @param mixed $value - Condition.
	 * @return self
	 */
	public function addSearchCondition( $value ) {
		$this->searchCondition = $value;
		return $this;
	}

	/**
	 * Gets condition.
	 *
	 * @return self
	 */
	public function getSearchCondition() {
		return $this->searchCondition;
	}

	/**
	 * Check meta join.
	 *
	 * @return boolean
	 */
	public function hasMetaJoin() {
		return $this->meta_join;
	}

	/**
	 * Adds meta join.
	 *
	 * @return self
	 */
	public function addMetaJoin() {
		$this->meta_join = true;
		return $this;
	}
}
