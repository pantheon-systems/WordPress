<?php


/* Quit */
defined('ABSPATH') OR exit;


/**
* Antispam_Bee_Columns
*/

final class Antispam_Bee_Columns {


	/**
	* Register plugin columns on comments screen
	*
	* @since   2.6.0
	* @change  2.6.0
	*
	* @param   array  $columns  Array with existing columns
	* @return  array            Array with extended columns
	*/

	public static function register_plugin_columns($columns)
	{
		return array_merge(
			$columns,
			array(
				'antispam_bee_reason' => esc_html__('Spam Reason', 'antispam-bee')
			)
		);
	}


	/**
	* Display plugin column values on comments screen
	*
	* @since   2.6.0
	* @change  2.6.0
	*
	* @param   string   $column      Currently selected column
	* @param   integer  $comment_id  Comment ID
	*/

	public static function print_plugin_column($column, $comment_id)
	{
		/* Only Antispam Bee column */
		if ( $column !== 'antispam_bee_reason' ) {
			return;
		}

		/* Init data */
		$spam_reason = get_comment_meta($comment_id, $column, true);
		$spam_reasons = Antispam_Bee::$defaults['reasons'];

		/* Empty values? */
		if ( empty($spam_reason) OR empty($spam_reasons[$spam_reason]) ) {
			return;
		}

		/* Escape & Print */
		echo esc_html__(
			$spam_reasons[$spam_reason],
			'antispam-bee'
		);
	}


	/**
	* Register plugin sortable columns on comments screen
	*
	* @since   2.6.3
	* @change  2.6.3
	*
	* @param   array  $columns  Registered columns
	* @return  array  $columns  Columns with AB field
	*/

	public static function register_sortable_columns($columns)
	{
		$columns['antispam_bee_reason'] = 'antispam_bee_reason';

		return $columns;
	}


	/**
	* Adjust orderby query
	*
	* @since   2.6.3
	* @change  2.6.3
	*
	* @param   object  $query  Current WordPress query
	*/

	public static function set_orderby_query($query)
	{
		/* Order by value */
		$orderby = $query->get('orderby');

		/* Skip if not our case */
		if ( empty($orderby) OR $orderby !== 'antispam_bee_reason' ) {
			return;
		}

		/* Set orderby values */
		$query->set( 'meta_key', 'antispam_bee_reason' );
		$query->set( 'orderby', 'meta_value' );
	}


	/**
	* Print CSS for the plugin column
	*
	* @since   2.6.1
	* @change  2.6.1
	*/

	public static function print_column_styles() { ?>
        <style>
            .column-antispam_bee_reason {
                width: 10%;
            }
        </style>
    <?php }
}
