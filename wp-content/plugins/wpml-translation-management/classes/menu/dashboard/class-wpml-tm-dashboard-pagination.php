<?php

/**
 * Class WPML_TM_Dashboard_Pagination
 */
class WPML_TM_Dashboard_Pagination {

	public function add_hooks() {
		add_filter( 'wpml_tm_dashboard_external_type_sql_query', array( $this, 'filter_external_type_query_for_pagination' ), 10, 2 );
		add_filter( 'wpml_tm_dashboard_post_query_args', array( $this, 'filter_dashboard_post_query_args_for_pagination' ), 10, 2 );
		add_action( 'wpml_tm_dashboard_pagination', array( $this, 'add_tm_dashboard_pagination' ), 10, 2 );
	}

	/**
	 * @param string $sql
	 * @param array $args
	 *
	 * @return string
	 */
	public function filter_external_type_query_for_pagination( $sql, $args ) {
		if ( ! empty( $args['type'] ) && false === strpos( $sql, 'SQL_CALC_FOUND_ROWS' ) ) {
			$sql = str_replace( 'SELECT DISTINCT ', 'SELECT DISTINCT SQL_CALC_FOUND_ROWS ', $sql );
		}

		return $sql;
	}

	/**
	 * @param array $query_args
	 * @param array $args
	 *
	 * @return array
	 */
	public function filter_dashboard_post_query_args_for_pagination( $query_args, $args ) {
		if ( ! empty( $args['type'] ) ) {
			unset( $query_args['no_found_rows'] );
		}

		return $query_args;
	}

	/**
	 * @param integer $posts_per_page
	 * @param integer $found_documents
	 */
	public function add_tm_dashboard_pagination( $posts_per_page, $found_documents ) {
		$found_documents = $found_documents;
		$total_pages     = ceil( $found_documents / $posts_per_page );
		$paged           = array_key_exists( 'paged', $_GET ) ? filter_var( $_GET['paged'], FILTER_SANITIZE_NUMBER_INT ) : false;
		$paged           = $paged ? $paged : 1;
		$page_links      = paginate_links( array(
			'base'      => add_query_arg( 'paged', '%#%' ),
			'format'    => '',
			'prev_text' => '&laquo;',
			'next_text' => '&raquo;',
			'total'     => $total_pages,
			'current'   => $paged,
		) );
		if ( $page_links ) {
			?>
			<div class="tablenav-pages">
				<?php
				$page_from  = number_format_i18n( ( $paged - 1 ) * $posts_per_page + 1 );
				$page_to    = number_format_i18n( min( $paged * $posts_per_page, $found_documents ) );
				$page_total = number_format_i18n( $found_documents );
				?>
				<span class="displaying-num">
                    <?php echo sprintf( esc_html__( 'Displaying %s&#8211;%s of %s', 'wpml-translation-management' ), $page_from, $page_to, $page_total ); ?>
                </span>
				<?php echo $page_links; ?>
			</div>
			<?php
		}
	}
}
