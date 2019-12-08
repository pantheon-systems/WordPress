<?php
$args  = array(
	// solr_integrate required for Solr.
	'solr_integrate' => true,
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'posts_per_page' => 50,
	'meta_query'     => array(
		'relation' => 'AND',
		array(
			'key'     => 'foo',
			'value'   => 'bar',
			'compare' => '='

		),
		array(
			'key'     => 'oof',
			'value'   => 'baz',
			'compare' => 'LIKE'
		),
	),
	'tax_query'      => array(
		'relation' => 'AND',
		array(
			'taxonomy' => 'foo',
			'field'    => 'term_id',
			'terms'    => 4,

		),
		array(
			'taxonomy' => 'oof',
			'field'    => 'slug',
			'terms'    => array( 'bar', 'baz' ),

		),
	),
	'date_query'     => array(
		// All items between 2000 and 2010.
		array(
			'before' => array(
				'year'  => 2011,
				'month' => 1,
				'day'   => 1,
			),
			'after'  => array(
				'year'  => 1999,
				'month' => 12,
				'day'   => 31,
			),
		),
	),
);
$query = new WP_Query( $args );
