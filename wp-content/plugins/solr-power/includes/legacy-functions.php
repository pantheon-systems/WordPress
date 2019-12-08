<?php
/**
 * Legacy Functions
 *
 * @package Solr_Power
 */

/**
 * Render the Solr search form
 */
function s4wp_search_form() {
	$sort   = filter_input( INPUT_GET, 'sort', FILTER_SANITIZE_STRING );
	$order  = filter_input( INPUT_GET, 'order', FILTER_SANITIZE_STRING );
	$server = filter_input( INPUT_GET, 'server', FILTER_SANITIZE_STRING );

	$score_str         = esc_html__( 'Score', 'solr-for-wordpress-on-pantheon' );
	$date_str          = esc_html__( 'Date', 'solr-for-wordpress-on-pantheon' );
	$last_modified_str = esc_html__( 'Last Modified', 'solr-for-wordpress-on-pantheon' );

	if ( 'date' === $sort ) {
		$sortval = '<option value="score">' . $score_str . '</option><option value="date" selected="selected">' . $date_str . '</option><option value="modified">' . $last_modified_str . '</option>';
	} elseif ( 'modified' === $sort ) {
		$sortval = '<option value="score">' . $score_str . '</option><option value="date">' . $date_str . '</option><option value="modified" selected="selected">' . $last_modified_str . '</option>';
	} else {
		$sortval = '<option value="score" selected="selected">' . $score_str . '</option><option value="date">' . $date_str . '</option><option value="modified">' . $last_modified_str . '</option>';
	}

	$desc_str = esc_html__( 'Descending', 'solr-for-wordpress-on-pantheon' );
	$asc_str  = esc_html__( 'Ascending', 'solr-for-wordpress-on-pantheon' );

	if ( 'asc' === $order ) {
		$orderval = '<option value="desc">' . $desc_str . '</option><option value="asc" selected="selected">' . $asc_str . '</option>';
	} else {
		$orderval = '<option value="desc" selected="selected">' . $desc_str . '</option><option value="asc">' . $asc_str . '</option>';
	}
	// if server id has been defined keep hold of it.
	if ( $server ) {
		$serverval = '<input name="server" type="hidden" value="' . $server . '" />';
	}
	$form = '<form name="searchbox" method="get" id="searchbox" action=""><input type="text" id="qrybox" name="ssearch" value="%s"/><input type="submit" id="searchbtn" /><label for="sortselect" id="sortlabel">' . esc_html__( 'Sort By:', 'solr-for-wordpress-on-pantheon' ) . '</label><select name="sort" id="sortselect">%s</select><label for="orderselect" id="orderlabel">' . __( 'Order By:', 'solr-for-wordpress-on-pantheon' ) . '</label><select name="order" id="orderselect">%s</select>%s</form>';

	printf( $form, filter_input( INPUT_GET, 'ssearch', FILTER_SANITIZE_STRING ), $sortval, $orderval, $serverval );
}

/**
 * Render Solr search results
 */
function s4wp_search_results() {
	$qry    = filter_input( INPUT_GET, 'ssearch', FILTER_SANITIZE_STRING );
	$offset = filter_input( INPUT_GET, 'offset', FILTER_SANITIZE_STRING );
	$count  = filter_input( INPUT_GET, 'count', FILTER_SANITIZE_STRING );
	$fq     = filter_input( INPUT_GET, 'fq', FILTER_SANITIZE_STRING );
	$sort   = filter_input( INPUT_GET, 'sort', FILTER_SANITIZE_STRING );
	$order  = filter_input( INPUT_GET, 'order', FILTER_SANITIZE_STRING );
	$isdym  = filter_input( INPUT_GET, 'isdym', FILTER_SANITIZE_STRING );
	$server = filter_input( INPUT_GET, 'server', FILTER_SANITIZE_STRING );

	$plugin_s4wp_settings = s4wp_get_option();
	$output_info          = $plugin_s4wp_settings['s4wp_output_info'];
	$output_pager         = $plugin_s4wp_settings['s4wp_output_pager'];
	$output_facets        = $plugin_s4wp_settings['s4wp_output_facets'];
	$results_per_page     = $plugin_s4wp_settings['s4wp_num_results'];
	$categoy_as_taxonomy  = $plugin_s4wp_settings['s4wp_cat_as_taxo'];

	$out         = array();
	$out['hits'] = '0';

	$qry = html_entity_decode( $qry );
	$qry = trim( $qry, '"' );
	$qry = trim( $qry, "'" );
	if ( ! $qry ) {
		$qry = '';
	}
	global $wpdb;
	// Sql Injection Prevention.
	$qry = $wpdb->_real_escape( trim( $qry ) );

	// if server value has been set lets set it up here
	// and add it to all the search urls henceforth.
	$serverval = isset( $server ) ? ( '&server=' . $server ) : '';

	// set some default values.
	if ( ! $offset ) {
		$offset = 0;
	}

	// only use default if not specified in post information.
	if ( ! $count ) {
		$count = $results_per_page;
	}

	if ( ! $fq ) {
		$fq = '';
	}

	if ( $sort && $order ) {
		$sortby = $sort;
	} else {
		$sortby = '';
		$order  = '';
	}

	if ( ! $isdym ) {
		$isdym = 0;
	}

	$fqstr          = '';
	$fqitms         = explode( '||', $fq );
	$selectedfacets = array();

	foreach ( $fqitms as $fqitem ) {
		if ( $fqitem && 'any' !== $fqitem ) {
			$splititm              = explode( ':', $fqitem );
			$selectedfacet         = array();
			$selectedfacet['name'] = sprintf( '%s:%s', ucwords( preg_replace( '/_str$/i', '', $splititm[0] ) ), str_replace( '^^', '/', $splititm[1] ) );
			$removelink            = '';
			foreach ( $fqitms as $fqitem2 ) {
				if ( $fqitem2 && ! ( $fqitem2 === $fqitem ) ) {
					$splititm2  = explode( ':', $fqitem2 );
					$removelink = $removelink . urlencode( '||' ) . $splititm2[0] . ':' . urlencode( $splititm2[1] );
				}
			}

			if ( $removelink ) {
				$selectedfacet['removelink'] = htmlspecialchars( sprintf( '?ssearch=%s&fq=%s', urlencode( $qry ), $removelink ) );
			} else {
				$selectedfacet['removelink'] = htmlspecialchars( sprintf( '?ssearch=%s', urlencode( $qry ) ) );
			}
			// if server is set add it on the end of the url.
			$selectedfacet['removelink'] .= $serverval;

			$fqstr = $fqstr . urlencode( '||' ) . $splititm[0] . ':' . urlencode( $splititm[1] );

			$selectedfacets[] = $selectedfacet;
		}
	}

	if ( $qry ) {

		$results = s4wp_query( $qry, $offset, $count, $fqitms, $sortby, $order, $server );

		if ( $results ) {
			$data     = $results->getData();
			$response = $data['response'];
			$header   = $data['responseHeader'];
			$teasers  = $results->getHighlighting()->getResults();
			if ( $output_info ) {
				$out['hits']  = $response['numFound'];
				$out['qtime'] = sprintf( '%.3f', $header['QTime'] / 1000 );
			} else {
				$out['hits'] = 0;
			}

			if ( $output_pager ) {
				// calculate the number of pages.
				$numpages	 = ceil( $response['numFound'] / $count );
				$currentpage = ceil( $offset / $count ) + 1;
				$pagerout	 = array();

				if ( 0 == $numpages ) {
					$numpages = 1;
				}

				foreach ( range( 1, $numpages ) as $pagenum ) {
					if ( $pagenum != $currentpage ) {
						$offsetnum        = ( $pagenum - 1 ) * $count;
						$pageritm         = array();
						$pageritm['page'] = sprintf( '%d', $pagenum );
						if ( ! isset( $sortby ) || ! $sortby ) {
							$pagersortby = 'date';
							$pagerorder	 = 'desc';
						} else {
							$pagersortby = $sortby;
							$pagerorder	 = $order;
						}
						$pagerlink = sprintf( '?ssearch=%s&offset=%d&count=%d&sort=%s&order=%s', urlencode( $qry ), $offsetnum, $count, $pagersortby, $pagerorder );
						if ( $fqstr ) {
							$pagerlink .= '&fq=' . $fqstr;
						}
						$pageritm['link'] = htmlspecialchars( $pagerlink );
						// if server is set add it on the end of the url.
						$pageritm['link'] .= isset( $pageritm['link'] ) ? $serverval : '';
						$pagerout[]        = $pageritm;
					} else {
						$pageritm         = array();
						$pageritm['page'] = sprintf( '%d', $pagenum );
						$pageritm['link'] = '';
						$pagerout[]       = $pageritm;
					}
				}

				$out['pager'] = $pagerout;
			} // End if().

			if ( $output_facets ) {
				// handle facets.
				$facetout = array();

				if ( $results->getFacetSet() ) {
					foreach ( $results->getFacetSet()->getFacets() as $facetfield => $facet ) {
						$facetinfo         = array();
						$facetitms         = array();
						$facetinfo['name'] = ucwords( preg_replace( '/_str$/i', '', $facetfield ) );

						// categories is a taxonomy.
						if ( $categoy_as_taxonomy && 'categories' === $facetfield ) {
							// generate taxonomy and counts.
							$taxo = array();
							foreach ( $facet as $facetval => $facetcnt ) {
								$taxovals = explode( '^^', rtrim( $facetval, '^^' ) );
								$taxo     = s4wp_gen_taxo_array( $taxo, $taxovals );
							}

							$facetitms = s4wp_get_output_taxo( $facet, $taxo, '', $fqstr . $serverval, $facetfield );
						} else {
							foreach ( $facet as $facetval => $facetcnt ) {
								$facetitm          = array();
								$facetitm['count'] = sprintf( '%d', $facetcnt );
								$facetitm['link']  = htmlspecialchars( sprintf( '?ssearch=%s&fq=%s:%s%s', urlencode( $qry ), $facetfield, urlencode( '"' . $facetval . '"' ), $fqstr ) );
								// if server is set add it on the end of the url.
								$facetitm['link'] .= $serverval;
								$facetitm['name']  = $facetval;
								$facetitms[]       = $facetitm;
							}
						}

						$facetinfo['items']      = $facetitms;
						$facetout[ $facetfield ] = $facetinfo;
					}
				}

				$facetout['selected'] = $selectedfacets;
				$out['facets']        = $facetout;
			} // End if().

			$resultout = array();

			if ( 0 != $response['numFound'] ) {
				foreach ( $response['docs'] as $doc ) {

					$resultinfo              = array();
					$docid                   = strval( $doc['id'] );
					$resultinfo['permalink'] = $doc['permalink'];
					$resultinfo['title']     = $doc['title'];
					if ( isset( $doc['author'] ) ) {
						$resultinfo['author'] = $doc['author'];
					}
					if ( isset( $doc['author_s'] ) ) {
						$resultinfo['authorlink'] = htmlspecialchars( $doc['author_s'] );
					}
					$resultinfo['numcomments'] = $doc['numcomments'];
					$resultinfo['date']        = $doc['displaydate'];

					if ( 0 === $doc['numcomments'] ) {
						$resultinfo['comment_link'] = $doc['permalink'] . '#respond';
					} else {
						$resultinfo['comment_link'] = $doc['permalink'] . '#comments';
					}

					$resultinfo['score'] = $doc['score'];
					$resultinfo['id']    = $docid;

					$docteaser = $teasers[ $docid ];
					$docteaser = $docteaser->getFields();

					if ( $docteaser ) {
						$resultinfo['teaser'] = sprintf( '...%s...', implode( '...', $docteaser['content'] ) );
					} else {
						$words                = explode( ' ', $doc['content'] );
						$teaser               = implode( ' ', array_slice( $words, 0, 30 ) );
						$resultinfo['teaser'] = sprintf( '%s...', $teaser );
					}
					$resultout[] = $resultinfo;
				} // End foreach().
			} // End if().
			$out['results'] = $resultout;
		} // End if().
	} else {
		$out['hits'] = '0';
	} // End if().

	// pager and results count helpers.
	$out['query']       = htmlspecialchars( $qry );
	$out['offset']      = strval( $offset );
	$out['count']       = strval( $count );
	$out['firstresult'] = strval( $offset + 1 );
	$out['lastresult']  = strval( min( $offset + $count, $out['hits'] ) ); // hits doesn't exist.
	$out['sortby']      = $sortby;
	$out['order']       = $order;
	$out['sorting']     = array(
		'scoreasc'     => htmlspecialchars( sprintf( '?ssearch=%s&fq=%s&sort=score&order=asc%s', urlencode( $qry ), stripslashes( $fq ), $serverval ) ),
		'scoredesc'    => htmlspecialchars( sprintf( '?ssearch=%s&fq=%s&sort=score&order=desc%s', urlencode( $qry ), stripslashes( $fq ), $serverval ) ),
		'dateasc'      => htmlspecialchars( sprintf( '?ssearch=%s&fq=%s&sort=date&order=asc%s', urlencode( $qry ), stripslashes( $fq ), $serverval ) ),
		'datedesc'     => htmlspecialchars( sprintf( '?ssearch=%s&fq=%s&sort=date&order=desc%s', urlencode( $qry ), stripslashes( $fq ), $serverval ) ),
		'modifiedasc'  => htmlspecialchars( sprintf( '?ssearch=%s&fq=%s&sort=modified&order=asc%s', urlencode( $qry ), stripslashes( $fq ), $serverval ) ),
		'modifieddesc' => htmlspecialchars( sprintf( '?ssearch=%s&fq=%s&sort=modified&order=desc%s', urlencode( $qry ), stripslashes( $fq ), $serverval ) ),
		'commentsasc'  => htmlspecialchars( sprintf( '?ssearch=%s&fq=%s&sort=numcomments&order=asc%s', urlencode( $qry ), stripslashes( $fq ), $serverval ) ),
		'commentsdesc' => htmlspecialchars( sprintf( '?ssearch=%s&fq=%s&sort=numcomments&order=desc%s', urlencode( $qry ), stripslashes( $fq ), $serverval ) ),
	);
	return $out;
}

/**
 * Print facet items
 *
 * @param array  $items  Facet items.
 * @param string $pre    HTML element to render before.
 * @param string $post   HTML element to render after.
 * @param string $before HTML element to render at the beginning of an item.
 * @param string $after  HTML element to render at the end of an item.
 * @param string $nestedpre HTML element to render before nesting.
 * @param string $nestedpost HTML element to render after nesting.
 * @param string $nestedbefore HTML element to render before a nested item.
 * @param string $nestedafter HTML element to render after a nested item.
 * @return string
 */
function s4wp_print_facet_items( $items, $pre = '<ul>', $post = '</ul>', $before = '<li>', $after = '</li>',
	$nestedpre = '<ul>', $nestedpost = '</ul>', $nestedbefore = '<li>', $nestedafter = '</li>' ) {
	if ( ! $items ) {
		return;
	}
	printf( "%s\n", $pre );
	foreach ( $items as $item ) {
		printf( "%s<a href=\"%s\">%s (%s)</a>%s\n", $before, $item['link'], $item['name'], $item['count'], $after );
		$item_items = isset( $item['items'] ) ? true : false;

		if ( $item_items ) {
			s4wp_print_facet_items( $item['items'], $nestedpre, $nestedpost, $nestedbefore, $nestedafter, $nestedpre, $nestedpost, $nestedbefore, $nestedafter );
		}
	}
	printf( "%s\n", $post );
}

/**
 * Get an array for output
 *
 * @param string $facet  Facet.
 * @param array  $taxo   Taxonomy.
 * @param string $prefix Search prefix.
 * @param string $fqstr  Facet query string.
 * @param string $field  Facet field.
 * @return array
 */
function s4wp_get_output_taxo( $facet, $taxo, $prefix, $fqstr, $field ) {
	$qry = filter_input( INPUT_GET, 's', FILTER_SANITIZE_STRING );

	if ( count( $taxo ) == 0 ) {
		return;
	} else {
		$facetitms = array();
		foreach ( $taxo as $taxoname => $taxoval ) {
			$newprefix         = $prefix . $taxoname . '^^';
			$facetvars         = $facet->getValues();
			$facetitm          = array();
			$facetitm['count'] = sprintf( '%d', $facetvars[ $newprefix ] );
			$facetitm['link']  = htmlspecialchars( sprintf( '?ssearch=%s&fq=%s:%s%s', $qry, $field, urlencode( '"' . $newprefix . '"' ), $fqstr ) );
			$facetitm['name']  = $taxoname;
			$outitms           = s4wp_get_output_taxo( $facet, $taxoval, $newprefix, $fqstr, $field );
			if ( $outitms ) {
				$facetitm['items'] = $outitms;
			}
			$facetitms[] = $facetitm;
		}

		return $facetitms;
	}
}

/**
 * Generate a tax array
 *
 * @param array $in   Input array.
 * @param array $vals Values to merge.
 * @return array
 */
function s4wp_gen_taxo_array( $in, $vals ) {

	if ( count( $vals ) == 1 ) {
		if ( isset( $in[ $vals[0] ] ) && ! $in[ $vals[0] ] ) {
			$in[ $vals[0] ] = array();
		}
		return $in;
	} else {
		$in[ $vals[0] ] = s4wp_gen_taxo_array( $in[ $vals[0] ], array_slice( $vals, 1 ) );
		return $in;
	}
}
