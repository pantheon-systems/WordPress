<?php

class WPML_Translations_Queue {

	/** @var  SitePress $sitepress */
	private $sitepress;

	/* @var WPML_UI_Screen_Options_Pagination */
	private $screen_options;

	/** @var WPML_Admin_Table_Sort $table_sort */
	private $table_sort;

	private $must_render_the_editor = false;
	/** @var WPML_Translation_Editor_UI */
	private $translation_editor;

	/**
	 * WPML_Translations_Queue constructor.
	 *
	 * @param SitePress $sitepress
	 * @param WPML_UI_Screen_Options_Factory $screen_options_factory
	 */
	public function __construct(
		$sitepress,
		$screen_options_factory
	) {
		$this->sitepress      = $sitepress;
		$this->screen_options = $screen_options_factory->create_pagination( 'tm_translations_queue_per_page',
			ICL_TM_DOCS_PER_PAGE );
		$this->table_sort     = $screen_options_factory->create_admin_table_sort();
	}

	public function init_hooks() {
		add_action( 'current_screen', array( $this, 'load' ) );
	}

	public function load() {
		if ( $this->must_open_the_editor() ) {
			$job_id     = $this->get_job_id_from_request();
			$job_object = wpml_tm_load_job_factory()->get_translation_job( $job_id, false, 0, true );
			if ( $job_object->get_translator_id() <= 0 ) {
				$job_object->assign_to( $this->sitepress->get_wp_api()
				                                        ->get_current_user_id() );
			}
			if ( $job_object && $job_object->user_can_translate( wp_get_current_user() ) ) {

				$this->attempt_opening_ATE( $job_id );

				global $wpdb;
				$this->must_render_the_editor = true;
				$this->translation_editor     = new WPML_Translation_Editor_UI( $wpdb,
					$this->sitepress,
					wpml_load_core_tm(),
					$job_object,
					new WPML_TM_Job_Action_Factory( wpml_tm_load_job_factory() ),
					new WPML_TM_Job_Layout( $wpdb,
						$this->sitepress->get_wp_api() ) );
			}
		}
	}

	/**
	 * @param array $icl_translation_filter
	 *
	 * @throws \InvalidArgumentException
	 */
	public function display( array $icl_translation_filter = array() ) {
		if ( $this->must_render_the_editor ) {
			$this->translation_editor->render();

			return;
		}

		/**
		 * @var TranslationManagement $iclTranslationManagement
		 * @var WPML_Translation_Job_Factory $wpml_translation_job_factory
		 */
		global $iclTranslationManagement, $wpml_translation_job_factory;

		$translation_jobs = array();
		$job_types        = array();
		$langs_from       = array();
		$lang_from        = array();
		$langs_to         = array();
		$lang_to          = array();
		$job_id           = null;

		if ( ! empty( $_GET['resigned'] ) ) {
			$iclTranslationManagement->add_message( array(
				'type' => 'updated',
				'text' => __( "You've resigned from this job.",
					'wpml-translation-management' )
			) );
		}
		if ( isset( $_SESSION['translation_ujobs_filter'] ) ) {
			$icl_translation_filter = $_SESSION['translation_ujobs_filter'];
		}
		$current_translator = $iclTranslationManagement->get_current_translator();
		$can_translate      = $current_translator && $current_translator->ID > 0 && $current_translator->language_pairs;
		$post_link_factory  = new WPML_TM_Post_Link_Factory( $this->sitepress );
		if ( $can_translate ) {
			$icl_translation_filter['translator_id']      = $current_translator->ID;
			$icl_translation_filter['include_unassigned'] = true;

			$element_type_prefix = isset( $_GET['element_type'] ) ? $_GET['element_type'] : 'post';
			if ( isset( $_GET['updated'] ) && $_GET['updated'] ) {
				$tm_post_link_updated = $post_link_factory->view_link( $_GET['updated'] );
				if ( $iclTranslationManagement->is_external_type( $element_type_prefix ) ) {
					$tm_post_link_updated = apply_filters( 'wpml_external_item_link',
						$tm_post_link_updated,
						$_GET['updated'],
						false );
				}
				$user_message = __( 'Translation updated: ', 'wpml-translation-management' ) . $tm_post_link_updated;
				$iclTranslationManagement->add_message( array( 'type' => 'updated', 'text' => $user_message ) );
			} elseif ( isset( $_GET['added'] ) && $_GET['added'] ) {
				$tm_post_link_added = $post_link_factory->view_link( $_GET['added'] );
				if ( $iclTranslationManagement->is_external_type( $element_type_prefix ) ) {
					$tm_post_link_added = apply_filters( 'wpml_external_item_link',
						$tm_post_link_added,
						$_GET['added'],
						false );
				}
				$user_message = __( 'Translation added: ', 'wpml-translation-management' ) . $tm_post_link_added;
				$iclTranslationManagement->add_message( array( 'type' => 'updated', 'text' => $user_message ) );
			} elseif ( isset( $_GET['job-cancelled'] ) ) {
				$user_message = __( 'Translation has been removed by admin', 'wpml-translation-management' );
				$iclTranslationManagement->add_message( array( 'type' => 'error', 'text' => $user_message ) );
			}

			if ( ! empty( $current_translator->language_pairs ) ) {
				$_langs_to = array();
				if ( 1 < count( $current_translator->language_pairs ) ) {
					foreach ( $current_translator->language_pairs as $lang => $to ) {
						$langs_from[] = $this->sitepress->get_language_details( $lang );
						$_langs_to    = array_merge( (array) $_langs_to, array_keys( $to ) );
					}
					$_langs_to = array_unique( $_langs_to );
				} else {
					$_langs_to                      = array_keys( current( $current_translator->language_pairs ) );
					$lang_from                      = $this->sitepress->get_language_details( key( $current_translator->language_pairs ) );
					$icl_translation_filter['from'] = $lang_from['code'];
				}

				if ( 1 < count( $_langs_to ) ) {
					foreach ( $_langs_to as $lang ) {
						$langs_to[] = $this->sitepress->get_language_details( $lang );
					}
				} else {
					$lang_to                      = $this->sitepress->get_language_details( current( $_langs_to ) );
					$icl_translation_filter['to'] = $lang_to['code'];
				}
				$job_types = $wpml_translation_job_factory->get_translation_job_types_filter( array(),
					array(
						'translator_id'      => $current_translator->ID,
						'include_unassigned' => true
					) );

				if ( isset( $_GET['orderby'] ) ) {
					$icl_translation_filter['order_by'] = filter_var( $_GET['orderby'], FILTER_SANITIZE_STRING );
				}

				if ( isset( $_GET['order'] ) ) {
					$icl_translation_filter['order'] = filter_var( $_GET['order'], FILTER_SANITIZE_STRING );
				}

				$translation_jobs = $wpml_translation_job_factory->get_translation_jobs( (array) $icl_translation_filter );
				$has_updated_jobs = $this->translation_jobs_require_update( $translation_jobs );

				if ( $has_updated_jobs ) {
					$translation_jobs = $wpml_translation_job_factory->get_translation_jobs( (array) $icl_translation_filter );
				}
			}
		}
		?>
        <div class="wrap">
            <h2><?php echo __( 'Translations queue', 'wpml-translation-management' ) ?></h2>

			<?php if ( empty( $current_translator->language_pairs ) ): ?>
                <div class="error below-h2"><p><?php _e( "No translation languages configured for this user.",
							'wpml-translation-management' ); ?></p></div>
			<?php endif; ?>
			<?php do_action( 'icl_tm_messages' ); ?>

			<?php if ( ! empty( $current_translator->language_pairs ) ): ?>

                <div class="alignright">
                    <form method="post"
                          name="translation-jobs-filter"
                          id="tm-queue-filter"
                          action="admin.php?page=<?php echo WPML_TM_FOLDER ?>/menu/translations-queue.php">
                        <input type="hidden" name="icl_tm_action" value="ujobs_filter"/>
                        <table class="">
                            <tbody>
                            <tr valign="top">
                                <td>
                                    <select name="filter[type]">
                                        <option value=""><?php _e( 'All types', 'wpml-translation-management' ) ?></option>
										<?php foreach ( $job_types as $job_type => $job_type_name ): ?>
                                            <option value="<?php echo $job_type ?>" <?php
											if ( ! empty( $icl_translation_filter['type'] )
											     && $icl_translation_filter['type']
											        === $job_type ): ?>selected="selected"<?php endif; ?>><?php echo $job_type_name ?></option>
										<?php endforeach; ?>
                                    </select>&nbsp;
                                    <label>
                                        <strong><?php _e( 'From', 'wpml-translation-management' ); ?></strong>
										<?php if ( 1 < count( $current_translator->language_pairs ) ) {

											$from_select = new WPML_Simple_Language_Selector( $this->sitepress );
											echo $from_select->render( array(
												'name'               => 'filter[from]',
												'please_select_text' => __( 'Any language',
													'wpml-translation-management' ),
												'style'              => '',
												'languages'          => $langs_from,
												'selected'           => isset( $icl_translation_filter['from'] )
													? $icl_translation_filter['from'] : ''
											) );
										} else { ?>
                                            <input type="hidden"
                                                   name="filter[from]"
                                                   value="<?php echo esc_attr( $lang_from['code'] ) ?>"/>
											<?php echo $this->sitepress->get_flag_img( $lang_from['code'] )
											           . ' '
											           . $lang_from['display_name']; ?>
										<?php } ?>
                                    </label>&nbsp;
                                    <label>
                                        <strong><?php _e( 'To', 'wpml-translation-management' ); ?></strong>
										<?php
										if ( 1 < @count( $langs_to ) ) {
											$to_select = new WPML_Simple_Language_Selector( $this->sitepress );
											echo $to_select->render( array(
												'name'               => 'filter[to]',
												'please_select_text' => __( 'Any language',
													'wpml-translation-management' ),
												'style'              => '',
												'languages'          => $langs_to,
												'selected'           => isset( $icl_translation_filter['to'] )
													? $icl_translation_filter['to'] : ''
											) );
										} else {
											?>
                                            <input type="hidden" name="filter[to]"
                                                   value="<?php echo esc_attr( $lang_to['code'] ) ?>"/>
											<?php
											echo $this->sitepress->get_flag_img( $lang_to['code'] ) . ' ' . $lang_to['display_name'];
										}

										$translation_filter_status = null;
										if ( array_key_exists( 'status', $icl_translation_filter ) ) {
											$translation_filter_status = (int) $icl_translation_filter['status'];
										}

										?>
                                    </label>
                                    &nbsp;
                                    <select name="filter[status]">
                                        <option value=""><?php _e( 'All statuses', 'wpml-translation-management' ) ?></option>
                                        <option value="<?php echo ICL_TM_COMPLETE ?>" <?php
										if ( $translation_filter_status === ICL_TM_COMPLETE ): ?>selected="selected"<?php endif; ?>><?php
											echo TranslationManagement::status2text( ICL_TM_COMPLETE ); ?></option>
                                        <option value="<?php echo ICL_TM_IN_PROGRESS ?>" <?php
										if ( $translation_filter_status
										     === ICL_TM_IN_PROGRESS ): ?>selected="selected"<?php endif; ?>><?php
											echo TranslationManagement::status2text( ICL_TM_IN_PROGRESS ); ?></option>
                                        <option value="<?php echo ICL_TM_WAITING_FOR_TRANSLATOR ?>" <?php
										if ( $translation_filter_status
										     && $icl_translation_filter['status']
										        === ICL_TM_WAITING_FOR_TRANSLATOR ): ?>selected="selected"<?php endif; ?>><?php
											_e( 'Available to translate', 'wpml-translation-management' ) ?></option>
                                    </select>
                                    &nbsp;
                                    <input class="button-secondary"
                                           type="submit"
                                           value="<?php _e( 'Filter', 'wpml-translation-management' ) ?>"/>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
				<?php
				$actions = apply_filters( 'wpml_translation_queue_actions', array() );

				/**
				 * @deprecated Use 'wpml_translation_queue_actions' instead
				 */
				$actions = apply_filters( 'WPML_translation_queue_actions', $actions );
				?>
				<?php if ( count( $actions ) > 0 ): ?>
                    <form method="post" name="translation-jobs-action" action="admin.php?page=<?php echo WPML_TM_FOLDER ?>/menu/translations-queue.php">
				<?php endif; ?>

				<?php
				do_action( 'wpml_xliff_select_actions', $actions, 'action', $translation_jobs );

				/**
				 * @deprecated Use 'wpml_xliff_select_actions' instead
				 */
				do_action( 'WPML_xliff_select_actions', $actions, 'action', $translation_jobs );
				?>

				<?php

				$translation_queue_pagination = new WPML_Translations_Queue_Pagination_UI( $translation_jobs,
					$this->screen_options->get_items_per_page() );
				$translation_jobs             = $translation_queue_pagination->get_paged_jobs();

				?>
				<?php // pagination - end ?>

				<?php
				$blog_translators = wpml_tm_load_blog_translators();
				$tm_api           = new WPML_TM_API( $blog_translators, $iclTranslationManagement );

				$translation_queue_jobs_model = new WPML_Translations_Queue_Jobs_Model( $this->sitepress,
					$iclTranslationManagement,
					$tm_api,
					$post_link_factory,
					$translation_jobs );
				$translation_jobs             = $translation_queue_jobs_model->get();

				$this->show_table( $translation_jobs, count( $actions ) > 0, $job_id );
				?>

                <div id="tm-queue-pagination" class="tablenav">
					<?php $translation_queue_pagination->show() ?>

					<?php
					do_action( 'wpml_xliff_select_actions', $actions, 'action2', $translation_jobs );

					/**
					 * @deprecated Use 'wpml_xliff_select_actions' instead
					 */
					do_action( 'WPML_xliff_select_actions', $actions, 'action2', $translation_jobs );
					?>
                </div>
				<?php // pagination - end ?>

				<?php if ( count( $actions ) > 0 ): ?>
                    </form>
				<?php endif; ?>

				<?php do_action( 'wpml_translation_queue_after_display', $translation_jobs ); ?>

			<?php endif; ?>
        </div>

		<?php
		// Check for any bulk actions
		if ( isset( $_POST['action'] ) || isset( $_POST["action2"] ) ) {
			$xliff_version = isset( $_POST['doaction'] ) ? $_POST['action'] : $_POST['action2'];
			do_action( 'wpml_translation_queue_do_actions_export_xliff', $_POST, $xliff_version );

			/**
			 * @deprecated Use 'wpml_translation_queue_do_actions_export_xliff' instead
			 */
			do_action( 'WPML_translation_queue_do_actions_export_xliff', $_POST, $xliff_version );
		}
	}

	public function translation_jobs_require_update( $jobs ) {
		return apply_filters( 'wpml_tm_translation_queue_jobs_require_update', false, $jobs, true );
	}

	/**
	 * @param $translation_jobs
	 * @param $has_actions
	 * @param $open_job
	 */
	public function show_table( $translation_jobs, $has_actions, $open_job ) {
		?>
        <table class="widefat striped fixed icl-translation-jobs" id="icl-translation-jobs" cellspacing="0">
		<?php foreach ( array( 'thead', 'tfoot' ) as $element_type ) { ?>
            <<?php echo $element_type; ?>>
            <tr>
				<?php if ( $has_actions ) { ?>
                    <td class="manage-column column-cb check-column js-check-all" scope="col">
                        <input title="<?php echo esc_attr( $translation_jobs['strings']['check_all'] ); ?>"
                               type="checkbox"/>
                    </td>
				<?php } ?>

                <th scope="col" class="cloumn-job_id <?php echo $this->table_sort->get_column_classes( 'job_id' ); ?>">
                    <a href="<?php echo $this->table_sort->get_column_url( 'job_id' ); ?>">
                        <span><?php echo esc_html( $translation_jobs['strings']['job_id'] ); ?></span>
                        <span class="sorting-indicator"></span>
                    </a>
                </th>
                <th scope="col"><?php echo esc_html( $translation_jobs['strings']['title'] ); ?></th>
                <th scope="col"><?php echo esc_html( $translation_jobs['strings']['type'] ); ?></th>
                <th scope="col"
                    class="column-language"><?php echo esc_html( $translation_jobs['strings']['language'] ); ?></th>
                <th scope="col"
                    class="column-status"><?php echo esc_html( $translation_jobs['strings']['status'] ); ?></th>
                <th scope="col"
                    class="column-deadline <?php echo $this->table_sort->get_column_classes( 'deadline' ); ?>">
                    <a href="<?php echo $this->table_sort->get_column_url( 'deadline' ); ?>">
                        <span><?php echo esc_html( $translation_jobs['strings']['deadline'] ); ?></span>
                        <span class="sorting-indicator"></span>
                    </a>
                </th>
                <th scope="col" class="manage-column">&nbsp;</th>
                <th scope="col" class="manage-column column-date column-resign">&nbsp;</th>
            </tr>
            </<?php echo $element_type; ?>>
		<?php } ?>

        <tbody>
		<?php if ( empty( $translation_jobs['jobs'] ) ) { ?>
            <tr>
                <td colspan="8"
                    align="center"><?php _e( 'No translation jobs found', 'wpml-translation-management' ) ?></td>
            </tr>
		<?php } else {

			$ate_jobs = apply_filters( 'wpml_tm_ate_jobs_data', array(), $translation_jobs['jobs'] );

			foreach ( $translation_jobs['jobs'] as $index => $job ) { ?>
                <tr<?php echo $this->get_row_css_class_attribute( $job ); ?>>
					<?php if ( $has_actions ) { ?>
                        <td>
                            <label><input type="checkbox" name="job[<?php echo $job->job_id ?>]"
                                          value="1"/>&nbsp;</label>
                        </td>
					<?php } ?>

                    <td width="60"><?php echo $job->job_id; ?></td>
                    <td><?php echo esc_html( $job->post_title ); ?>
                        <div class="row-actions">
                            <span class="view"><?php echo $job->tm_post_link; ?></span>
                        </div>
                    </td>
                    <td><?php echo esc_html( $job->post_type ); ?></td>
                    <td><?php echo $job->lang_text_with_flags ?></td>
                    <td>
                        <i class="<?php echo esc_attr( $job->icon ); ?>"></i><?php echo esc_html( $job->status_text ); ?>
                    </td>
                    <td><?php if ( $job->deadline_date ) {
							echo date( 'Y-m-d', strtotime( $job->deadline_date ) );
						} ?>
                    </td>
                    <td>
						<?php
						if ( $job->original_doc_id ) {

							$ate_job_id       = null;
							$ate_job_progress = array();
							if ( array_key_exists( $job->job_id, $ate_jobs ) ) {
								if ( array_key_exists( 'ate_job_id', $ate_jobs[ $job->job_id ] ) ) {
									$ate_job_id = $ate_jobs[ $job->job_id ]['ate_job_id'];
								}
								if ( array_key_exists( 'progress', $ate_jobs[ $job->job_id ] ) ) {
									$ate_job_progress = $ate_jobs[ $job->job_id ]['progress'];
								}
							}

							?>
                            <a class="button-secondary js-translation-queue-edit"
                               href="<?php echo esc_attr( $job->edit_url ); ?>"
                               data-job-id="<?php echo $job->job_id ?>"
                               data-ate-job-id="<?php echo esc_attr( $ate_job_id ); ?>"
                               data-ate-job-url="<?php echo esc_attr( apply_filters( 'wpml_tm_ate_jobs_editor_url',
								   $job->edit_url,
								   $job->job_id ) ); ?>"
                               data-job-progress="<?php echo esc_attr( wp_json_encode( $ate_job_progress ) ); ?>"
                               data-ate-auto-open="<?php echo $job->job_id === $open_job ?>"
                            >
								<?php echo $job->button_text; ?>
                            </a>
							<?php
						}
						?>
                    </td>
                    <td align="right">
						<?php if ( $job->is_doing_job ) { ?>
                            <a href="<?php echo esc_attr( $job->resign_url ); ?>"
                               onclick="if(!confirm('<?php echo esc_js( $translation_jobs['strings']['confirm'] ) ?>')) {return false;}"><?php echo $job->resign_text ?></a>
						<?php } else { ?>
                            &nbsp;
						<?php } ?>
                    </td>
                </tr>
			<?php }
		} ?>
        </tbody>
        </table>
		<?php
	}

	private function get_job_id_from_request() {
		/**
		 * @var TranslationManagement $iclTranslationManagement
		 * @var WPML_Post_Translation $wpml_post_translations
		 * @var WPML_Translation_Job_Factory $wpml_translation_job_factory
		 */
		global $iclTranslationManagement, $wpml_post_translations, $wpml_translation_job_factory, $sitepress;

		$job_id = filter_var( isset( $_GET['job_id'] ) ? $_GET['job_id'] : '', FILTER_SANITIZE_NUMBER_INT );

		list( $trid, $update_needed, $language_code, $element_type ) = $this->get_job_data_for_restore( $job_id );
		$source_language_code = filter_var( isset( $_GET['source_language_code'] ) ? $_GET['source_language_code'] : '', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( $trid && $language_code ) {
			if ( ! $job_id ) {
				$job_id = $iclTranslationManagement->get_translation_job_id( $trid,
					$language_code );
				if ( ! $job_id ) {
					if ( ! $source_language_code ) {
						$post_id = SitePress::get_original_element_id_by_trid( $trid );
					} else {
						$posts_in_trid = $wpml_post_translations->get_element_translations( false,
							$trid );
						$post_id       = isset( $posts_in_trid[ $source_language_code ] ) ? $posts_in_trid[ $source_language_code ] : false;
					}
					$blog_translators = wpml_tm_load_blog_translators();
					$args             = array(
						'lang_from' => $source_language_code,
						'lang_to'   => $language_code,
						'job_id'    => $job_id
					);
					if ( $post_id && $blog_translators->is_translator( $sitepress->get_current_user()->ID,
							$args )
					) {
						$job_id = $wpml_translation_job_factory->create_local_post_job( $post_id,
							$language_code );
					}
				}
			} else if ( $update_needed ) {
				$element_id = SitePress::get_original_element_id_by_trid( $trid );
				$job_id     = $wpml_translation_job_factory->create_local_job( $element_id, $language_code, null, $element_type );
			}
		}

		return $job_id;
	}

	/**
	 * @param $job_id
	 *
	 * @return array ( trid, updated_needed, language_code )
	 */
	private function get_job_data_for_restore( $job_id ) {
		$fields = array( 'trid', 'update_needed', 'language_code', 'element_type' );
		$result = array_fill_keys( $fields, false );

		if ( isset( $_GET['trid'] ) ) {
			$result['trid'] = filter_var( $_GET['trid'], FILTER_SANITIZE_NUMBER_INT );
		}
		if ( isset( $_GET['update_needed'] ) ) {
			$result['update_needed'] = filter_var( $_GET['update_needed'], FILTER_SANITIZE_NUMBER_INT );
		}
		if ( isset( $_GET['language_code'] ) ) {
			$result['language_code'] = filter_var( $_GET['language_code'], FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		}

		$wpdb = $this->sitepress->get_wpdb();

		if ( isset( $result['trid'] ) ) {
			$element_type_query = $wpdb->prepare(
				"SELECT element_type FROM {$wpdb->prefix}icl_translations WHERE trid = %d LIMIT 1",
				$result['trid']
			);

			$result['element_type'] = $wpdb->get_var( $element_type_query );
		}

		if ( ! $job_id || isset( $_GET['trid'], $_GET['update_needed'], $_GET['language_code'], $result['element_type'] ) ) {
			return array(
				$result['trid'],
				$result['update_needed'],
				$result['language_code'],
				$result['element_type']
			);
		}

		$sql = "
	        SELECT t.trid, ts.needs_update as update_needed, t.language_code, t.element_type
            FROM {$wpdb->prefix}icl_translations t
            INNER JOIN {$wpdb->prefix}icl_translation_status ts on ts.translation_id = t.translation_id
            INNER JOIN {$wpdb->prefix}icl_translate_job j ON j.rid = ts.rid
            WHERE j.job_id = %d;
	    ";

		$db_result = $wpdb->get_row( $wpdb->prepare( $sql, $job_id ), ARRAY_A );

		foreach ( $fields as $field ) {
			if ( ! isset( $_GET[ $field ] ) ) {
				$result[ $field ] = $db_result[ $field ];
			}
		}

		return array( $result['trid'], $result['update_needed'], $result['language_code'], $result['element_type'] );
	}

	/**
	 * @param stdClass $job
	 *
	 * @return string
	 */
	private function get_row_css_class_attribute( $job ) {
		$classes = array();

		if ( isset( $job->deadline_date ) && ICL_TM_COMPLETE !== (int) $job->status ) {
			$deadline_day = date( 'Y-m-d', strtotime( $job->deadline_date ) );
			$today        = date( 'Y-m-d' );

			if ( $deadline_day < $today ) {
				$classes[] = 'overdue';
			}
		}

		if ( $classes ) {
			return ' class="' . esc_attr( implode( ' ', $classes ) ) . '"';
		}

		return '';
	}

	/**
	 * @return bool
	 */
	private function must_open_the_editor() {
		return ( isset( $_GET['job_id'] ) && $_GET['job_id'] > 0 )
		       || ( isset( $_GET['trid'] ) && $_GET['trid'] > 0 );
	}

	/**
	 * @param $job_id
	 */
	private function attempt_opening_ATE( $job_id ) {
		$editor_url = apply_filters( 'wpml_tm_ate_jobs_editor_url', null, $job_id, $this->get_return_url() );

		if ( $editor_url ) {
			wp_safe_redirect( $editor_url );
			die();
		}
	}

	/**
	 * @return mixed|null|string
	 */
	private function get_return_url() {
		$return_url = null;

		if ( array_key_exists( 'return_url', $_GET ) ) {
			$return_url = filter_var( $_GET['return_url'], FILTER_SANITIZE_URL );

			$return_url_parts = wp_parse_url( $return_url );

			$admin_url       = get_admin_url();
			$admin_url_parts = wp_parse_url( $admin_url );

			if ( strpos( $return_url_parts['path'], $admin_url_parts['path'] ) === 0 ) {
				$admin_url_parts['path'] = $return_url_parts['path'];
			} else {
				$admin_url_parts = $return_url_parts;
			}

			if ( array_key_exists( 'query', $return_url_parts ) ) {
				$admin_url_parts['query'] = $return_url_parts['query'];
			}

			$return_url = http_build_url( $admin_url_parts );
		}

		return $return_url;
	}
}
