<?php

class WCML_The_Events_Calendar{

	/** @var  SitePress */
	private $sitepress;
	/** @var  woocommerce_wpml */
	private $woocommerce_wpml;

	/** @var WPML_Element_Translation_Package */
	private $tp;

	function __construct( $sitepress, $woocommerce_wpml ){

    	$this->sitepress        =& $sitepress;
    	$this->woocommerce_wpml =& $woocommerce_wpml;

    	if( isset( $_POST['action'] ) && strpos( $_POST['action'], 'tribe-ticket-add-') === 0 ){
		    add_action( 'tribe_tickets_ticket_add', array( $this, 'unset_post_post_id' ) );
		    add_action( 'event_tickets_after_save_ticket', array( $this, 'restore_post_post_id' ) );

	    }

	    if( is_admin() ){
		    $this->tp = new WPML_Element_Translation_Package;

		    add_action( 'save_post', array( $this, 'synchronize_event_for_ticket' ), 20, 3 );

		    add_action( 'wpml_pb_shortcode_content_for_translation' , array( $this, 'maybe_mark_event_as_needs_update' ), 100, 2 );

		    add_filter( 'wpml_tm_translation_job_data', array( $this, 'append_RSVP_tickets_to_translation_job' ), 10, 2 );
		    add_action( 'wpml_pro_translation_completed', array( $this, 'save_RSVP_tickets_translations' ), 10, 3 );

		    add_filter( 'wpml_tm_translation_job_data', array( $this, 'append_woo_tickets_to_translation_job' ), 10, 2 );
		    add_action( 'wpml_pro_translation_completed', array( $this, 'save_woo_tickets_translations' ), 10, 3 );

		    add_action( 'save_post', array( $this, 'synchronize_venue_for_event' ), 20, 3 );
		    add_action( 'admin_footer', array( $this, 'pre_select_translated_venue' ), 1000 );
		    add_action( 'wpml_pro_translation_completed', array( $this, 'save_venue_for_translation' ), 10, 3 );

	    } else {
		    add_action( 'event_tickets_rsvp_tickets_generated', array( $this, 'sync_rsvp_fields_on_attendee_created' ), 10, 3 );
		    add_filter( 'tribe_get_organizer_ids', array( $this, 'get_translated_organizer_ids' ), 10, 2 );
	    }

    }

    public function unset_post_post_id(){
		if( isset( $_POST['post_ID'] ) ){
			$this->ticket_post_id_backup = $_POST['post_ID'];
			unset( $_POST['post_ID'] );
		}
    }

    public function restore_post_post_id(){
    	if( isset( $this->ticket_post_id_backup ) ){
		    $_POST['post_ID'] = $this->ticket_post_id_backup;
	    }
	}

    public function synchronize_event_for_ticket( $post_id, $post, $update ){

    	if( $post->post_type == 'product' ){

			if( !$this->woocommerce_wpml->products->is_original_product( $post_id ) ){

				$original_product_id = apply_filters( 'translate_object_id', $post_id, 'product', false, $this->sitepress->get_default_language() );
				if( $original_product_id ){
					$original_event_id = get_post_meta( $original_product_id, '_tribe_wooticket_for_event', true );
					if( $original_event_id ){
						$event_id = apply_filters( 'translate_object_id', $original_event_id, 'tribe_events', false );
						if( $event_id ){
							update_post_meta( $post_id, '_tribe_wooticket_for_event', $event_id );
						}
					}
				}

			}

	    }
    }

    public function maybe_mark_event_as_needs_update( $content, $post_id ){
    	$post = get_post( $post_id );

	    if( $post->post_type == 'tribe_events' ){

	    	if( $post_id == $this->sitepress->get_original_element_id( $post_id, 'post_tribe_events' ) ){

			    $tickets = array();
			    $ticket_meta = array();

	    		if( class_exists('Tribe__Tickets__RSVP') ){
				    $ticket_ids = Tribe__Tickets__RSVP::get_instance()->get_tickets_ids( $post_id );
				    foreach ( $ticket_ids as $ticket_id ) {
				    	$ticket = Tribe__Tickets__RSVP::get_instance()->get_ticket( $post_id, $ticket_id );
					    $tickets[] = $ticket->name . "|#|" . $ticket->description;
					    $ticket_meta[] = get_post_meta( $ticket_id, '_tribe_tickets_meta', true );
				    }
			    }

			    if( class_exists('Tribe__Tickets_Plus__Commerce__WooCommerce__Main') ){
				    $ticket_ids  = Tribe__Tickets_Plus__Commerce__WooCommerce__Main::get_instance()->get_tickets_ids( $post->ID );
				    foreach ( $ticket_ids as $ticket_id ) {
					    $ticket = Tribe__Tickets_Plus__Commerce__WooCommerce__Main::get_instance()->get_ticket( $post_id, $ticket_id );
					    $tickets[] = $ticket->name . "|#|" . $ticket->description;
					    $ticket_meta[] = get_post_meta( $ticket_id, '_tribe_tickets_meta', true );
				    }
			    }

			    $content .= md5( serialize( $tickets ) );
			    $content .= md5( serialize( $ticket_meta ) );
		    }


	    }

	    return $content;
    }

    public function append_RSVP_tickets_to_translation_job( $package, $post ){

    	if( $post->post_type == 'tribe_events' && class_exists('Tribe__Tickets__RSVP') ){

    		$ticket_lang = $this->sitepress->get_language_for_element( $post->ID, 'post_tribe_events' );
    		$this->sitepress->switch_lang( $ticket_lang );
		    $ticket_ids = Tribe__Tickets__RSVP::get_instance()->get_tickets_ids( $post->ID );
		    $this->sitepress->switch_lang();

		    if( $ticket_ids ){
		    	foreach( $ticket_ids as $ticket_id ){

		    		$ticket_post = get_post( $ticket_id );
				    $original_ticket_id = $this->sitepress->get_original_element_id( $ticket_id, 'post_tribe_rsvp_tickets' );

		    		if( !empty( $ticket_post->post_title ) ) {
					    $package['contents'][ 'rsvp_tickets_' . $original_ticket_id . '_title' ] = array(
						    'translate' => 1,
						    'data'      => $this->tp->encode_field_data( $ticket_post->post_title, 'base64' ),
						    'format'    => 'base64',
					    );
				    }
				    if( !empty( $ticket_post->post_excerpt ) ) {
					    $package['contents'][ 'rsvp_tickets_' . $original_ticket_id . '_excerpt' ] = array(
						    'translate' => 1,
						    'data'      => $this->tp->encode_field_data( $ticket_post->post_excerpt, 'base64' ),
						    'format'    => 'base64',
					    );
				    }

				    // fieldsets
				    $package = $this->append_tickets_meta( $package, $ticket_id, $original_ticket_id );

			    }
		    }


	    }

	    return $package;
    }

    public function save_RSVP_tickets_translations( $post_id, $data, $job ){

	    $translations = array();

	    foreach ( $data as $key => $value ) {

		    if ( preg_match( '/rsvp_tickets_([0-9]+)_title/', $key, $matches ) ) {
				$rsvp_post_id = $matches[1];
				if( $value['finished'] == 'on' ){
					$translations[ $rsvp_post_id ]['post_title'] = $value['data'];
				}
		    } elseif ( preg_match( '/rsvp_tickets_([0-9]+)_excerpt/', $key, $matches ) ) {
			    $rsvp_post_id = $matches[1];
			    if( $value['finished'] == 'on' ){
				    $translations[ $rsvp_post_id ]['post_excerpt'] = $value['data'];
			    }
		    }

	    }

	    foreach ( $translations as $rsvp_post_id => $translation ){
		    
	    	$translated_rsvp_post_id = apply_filters( 'translate_object_id', $rsvp_post_id, 'tribe_rsvp_tickets', false, $job->language_code );

		    $postarr = array(
			    'post_type'   => 'tribe_rsvp_tickets',
		        'post_status' => 'publish',
			    'post_title'   => $translation['post_title'],
			    'post_excerpt' => $translation['post_excerpt'],
		        'post_name'   => sanitize_title_with_dashes( $translation['post_title'] )
		    );

	    	if( $translated_rsvp_post_id && $translated_rsvp_post_id != $rsvp_post_id ){
			    global $wpml_post_translations;
			    $postarr['ID'] = $translated_rsvp_post_id;
			    remove_action( 'save_post', array( $wpml_post_translations, 'save_post_actions' ), 100 );
				wp_update_post( $postarr );
			    add_action( 'save_post', array( $wpml_post_translations, 'save_post_actions' ), 100 );

		    } else{
			    $translated_rsvp_post_id = wp_insert_post( $postarr );
			    $trid = $this->sitepress->get_element_trid( $rsvp_post_id, 'post_tribe_rsvp_tickets' );
			    $this->sitepress->set_element_language_details( $translated_rsvp_post_id, 'post_tribe_rsvp_tickets', $trid, $job->language_code );
		    }

		    $event_id = get_post_meta( $rsvp_post_id, '_tribe_rsvp_for_event', true);

	    	$translated_event_id = apply_filters( 'translate_object_id', $event_id, 'tribe_events', false, $job->language_code);
		    update_post_meta( $translated_rsvp_post_id, '_tribe_rsvp_for_event', $translated_event_id);

		    $this->sync_custom_fields( $rsvp_post_id, $translated_rsvp_post_id );

			$this->save_ticket_meta_translations( $rsvp_post_id, $translated_rsvp_post_id );

	    }
    }

	public function append_woo_tickets_to_translation_job( $package, $post ){

		if( $post->post_type == 'tribe_events' && class_exists('Tribe__Tickets_Plus__Commerce__WooCommerce__Main') ){

			$ticket_lang = $this->sitepress->get_language_for_element( $post->ID, 'post_tribe_events' );
			$this->sitepress->switch_lang( $ticket_lang );
			$ticket_ids  = Tribe__Tickets_Plus__Commerce__WooCommerce__Main::get_instance()->get_tickets_ids( $post->ID );
			$this->sitepress->switch_lang();

			if( $ticket_ids ){
				foreach( $ticket_ids as $ticket_id ){

					$ticket_post = get_post( $ticket_id );
					$original_ticket_id = $this->sitepress->get_original_element_id( $ticket_id, 'post_product' );

					if( !empty( $ticket_post->post_title ) ) {
						$package['contents'][ 'woo_tickets_' . $original_ticket_id . '_title' ] = array(
							'translate' => 1,
							'data'      => $this->tp->encode_field_data( $ticket_post->post_title, 'base64' ),
							'format'    => 'base64',
						);
					}
					if( !empty( $ticket_post->post_excerpt ) ) {
						$package['contents'][ 'woo_tickets_' . $original_ticket_id . '_excerpt' ] = array(
							'translate' => 1,
							'data'      => $this->tp->encode_field_data( $ticket_post->post_excerpt, 'base64' ),
							'format'    => 'base64',
						);
					}

					// fieldsets
					$package = $this->append_tickets_meta( $package, $ticket_id, $original_ticket_id );

				}
			}

		}

		return $package;
	}

	public function save_woo_tickets_translations( $post_id, $data, $job ){
		global $wpml_post_translations;

		$translations = array();

		foreach ( $data as $key => $value ) {

			if ( preg_match( '/woo_tickets_([0-9]+)_title/', $key, $matches ) ) {
				$ticket_post_id = $matches[1];
				if( $value['finished'] == 'on' ){
					$translations[ $ticket_post_id ]['post_title'] = $value['data'];
				}
			} elseif ( preg_match( '/woo_tickets_([0-9]+)_excerpt/', $key, $matches ) ) {
				$ticket_post_id = $matches[1];
				if( $value['finished'] == 'on' ){
					$translations[ $ticket_post_id ]['post_excerpt'] = $value['data'];
				}
			}

		}

		foreach ( $translations as $ticket_post_id => $translation ){

			$translated_ticket_post_id = apply_filters( 'translate_object_id', $ticket_post_id, 'product', false, $job->language_code );

			$postarr = array(
				'post_type'   => 'product',
				'post_status' => 'publish',
				'post_title'   => $translation['post_title'],
				'post_excerpt' => $translation['post_excerpt'],
				'post_name'   => sanitize_title_with_dashes( $translation['post_title'] )
			);

			remove_action( 'save_post', array( $wpml_post_translations, 'save_post_actions' ), 100, 2 );
			if( $translated_ticket_post_id && $translated_ticket_post_id != $ticket_post_id ){
				$postarr['ID'] = $translated_ticket_post_id;
				wp_update_post( $postarr );
			} else{
				$translated_ticket_post_id = wp_insert_post( $postarr );
				$trid = $this->sitepress->get_element_trid( $ticket_post_id, 'post_product' );
				$this->sitepress->set_element_language_details( $translated_ticket_post_id, 'post_product', $trid, $job->language_code );
			}
			add_action( 'save_post', array( $wpml_post_translations, 'save_post_actions' ), 100, 2 );

			$event_id = get_post_meta( $ticket_post_id, '_tribe_wooticket_for_event', true);

			$translated_event_id = apply_filters( 'translate_object_id', $event_id, 'tribe_events', false, $job->language_code);
			update_post_meta( $translated_ticket_post_id, '_tribe_wooticket_for_event', $translated_event_id);

			$this->sync_custom_fields( $ticket_post_id, $translated_ticket_post_id );

			$this->save_ticket_meta_translations( $ticket_post_id, $translated_ticket_post_id );

		}
	}

	private function append_tickets_meta( $package, $ticket_id, $original_ticket_id ){

		$ticket_meta = get_post_meta( $ticket_id, '_tribe_tickets_meta', true );
		if( is_array( $ticket_meta ) ) {
			foreach ( $ticket_meta as $k => $meta ) {
				$package['contents'][ 'rsvp_tickets_' . $original_ticket_id . '_meta_' . $k . '_label' ] = array(
					'translate' => 1,
					'data'      => $this->tp->encode_field_data( $meta['label'], 'base64' ),
					'format'    => 'base64',
				);
				$package['contents'][ 'rsvp_tickets_' . $original_ticket_id . '_meta_' . $k . '_slug' ]  = array(
					'translate' => 1,
					'data'      => $this->tp->encode_field_data( $meta['slug'], 'base64' ),
					'format'    => 'base64',
				);
				if ( isset( $meta['extra']['options'] ) ) {
					foreach ( $meta['extra']['options'] as $option_id => $option_name ) {

						$package['contents'][ 'rsvp_tickets_' . $original_ticket_id . '_meta_' . $k . '_option_' . $option_id ] = array(
							'translate' => 1,
							'data'      => $this->tp->encode_field_data( $option_name, 'base64' ),
							'format'    => 'base64',
						);

					}

				}
			}
		}

		return $package;
	}

	private function save_ticket_meta_translations( $ticket_id, $translated_ticket_id ){

		$ticket_meta = get_post_meta( $ticket_id, '_tribe_tickets_meta', true );
		$translated_ticket_meta = $ticket_meta;
		if( is_array( $ticket_meta ) ){

			foreach ( $ticket_meta as $k => $meta ) {

				$key = 'rsvp_tickets_' . $ticket_id . '_meta_' . $k . '_label';
				if( isset( $data[$key] ) && $data[$key]['finished'] == 'on' ){
					$translated_ticket_meta[$k]['label'] = $data[$key]['data'];
				}

				$key = 'rsvp_tickets_' . $ticket_id . '_meta_' . $k . '_slug';
				if( isset( $data[$key] ) && $data[$key]['finished'] == 'on' ){
					$translated_ticket_meta[$k]['slug'] = $data[$key]['data'];
				}

				if ( isset( $meta['extra']['options'] ) ) {
					foreach ( $meta['extra']['options'] as $option_id => $option_name ) {
						$key = 'rsvp_tickets_' . $ticket_id . '_meta_' . $k . '_option_' . $option_id;
						if( isset( $data[$key] ) && $data[$key]['finished'] == 'on' ){
							$translated_ticket_meta[$k]['extra']['options'][$option_id] = $data[$key]['data'];
						}
					}
				}

			}

		}

		update_post_meta( $translated_ticket_id, '_tribe_tickets_meta', $translated_ticket_meta );

		update_post_meta( $translated_ticket_id, '_tribe_tickets_meta_enabled',
			get_post_meta( $ticket_id, '_tribe_tickets_meta_enabled', true )
		);


	}

	private function sync_custom_fields( $original_ticket_id, $translated_ticket_id ){
		// sync custom fields
		$custom_fields_sync = array( '_stock', '_manage_stock', 'total_sales', '_price' );
		foreach( $custom_fields_sync as $custom_field ){
			$value = get_post_meta( $original_ticket_id, $custom_field, true );
			if( $value !== false ){
				update_post_meta( $translated_ticket_id, $custom_field, $value);
			}
		}
	}

    public function synchronize_venue_for_event( $post_id, $post, $update ){

    	if( $post->post_type == 'tribe_event' ){

    		$venue_id = get_post_meta( $post_id, '_EventVenueID', true );

		    $original_event_id = $this->sitepress->get_original_element_id( $post_id, 'post_tribe_event' );

		    if( $original_event_id == $post_id ) {
			    $event_trid         = $this->sitepress->get_element_trid( $post_id, 'post_tribe_venue' );
			    $event_translations = $this->sitepress->get_element_translations( $event_trid, 'post_tribe_venue' );

			    if ( $venue_id ) {
				    foreach ( $event_translations as $language_code => $event_translation ) {
					    if ( $event_translation->element_id != $original_event_id ) {
						    $translated_venue_id = apply_filters( 'translate_object_id', $venue_id, 'tribe_venue', false, $language_code );
						    if ( $translated_venue_id ) {
							    update_post_meta( $event_translation->element_id, '_EventVenueID', $translated_venue_id );
						    }
					    }
				    }
			    } else { // delete venue from translations
				    foreach ( $event_translations as $language_code => $event_translation ) {
					    if ( $event_translation->element_id != $original_event_id ) {
						    delete_post_meta( $event_translation->element_id, '_EventVenueID' );
					    }
				    }
			    }

		    }

	    }

    }

    public function pre_select_translated_venue(){
		$current_screen = get_current_screen();
		if( $current_screen->base === 'post' && $current_screen->action === 'add' && $current_screen->id === 'tribe_events' ){
			if( isset( $_GET['trid'] ) && isset( $_GET['lang'] ) && isset( $_GET['source_lang'] ) ){
				$event_translations = $this->sitepress->get_element_translations( $_GET['trid'], 'post_tribe_events' );
				$original_event_id = $event_translations[ $_GET['source_lang'] ]->element_id;
				$original_venue_id = get_post_meta( $original_event_id, '_EventVenueID', true );
				if( $original_venue_id ){
					$translated_venue_id = apply_filters( 'translate_object_id', $original_venue_id, 'tribe_venue', false, $_GET['lang'] );
					if( $translated_venue_id ){
						echo "<script type=\"text/javascript\">
								jQuery('#saved_tribe_venue').val($translated_venue_id);
							  </script>";
					}
				}
			}
		}
    }

    public function save_venue_for_translation( $post_id, $data, $job ){

	    $original_event_id = $this->sitepress->get_original_element_id( $post_id, 'post_tribe_event' );
	    $original_venue_id = get_post_meta( $original_event_id, '_EventVenueID', true );

	    if( $original_venue_id ){
		    $translated_venue_id = apply_filters( 'translate_object_id', $original_venue_id, 'tribe_venue', false, $job->language_code );
		    if( $translated_venue_id ){
			    update_post_meta( $post_id, '_EventVenueID', $translated_venue_id );
		    }

	    }

    }

    public function sync_rsvp_fields_on_attendee_created( $order_id, $post_id, $attendee_order_status ){

    	$rsvp_post_id = isset( $_POST['product_id'][0] ) ? $_POST['product_id'][0] : false;
    	if( $rsvp_post_id ) {
		    $rsvp_trid         = $this->sitepress->get_element_trid( $rsvp_post_id, 'post_tribe_rsvp_tickets' );
		    $rsvp_translations = $this->sitepress->get_element_translations( $rsvp_trid, 'post_tribe_rsvp_tickets' );

		    foreach ( $rsvp_translations as $translation ) {
			    if ( $translation->element_id != $rsvp_post_id ) {
				    $this->sync_custom_fields( $rsvp_post_id, $translation->element_id );
			    }
		    }
	    }
    }

    public function get_translated_organizer_ids( $organizer_ids, $event_id ){
    	foreach( $organizer_ids as $key => $organizer_id ){
		    $organizer_ids[$key] = apply_filters( 'translate_object_id', $organizer_id, 'tribe_organizer', true );
	    }
    	return $organizer_ids;
    }
}

