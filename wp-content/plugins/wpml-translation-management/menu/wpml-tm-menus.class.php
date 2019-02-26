<?php
if ( filter_input( INPUT_GET, 'sm', FILTER_SANITIZE_STRING ) === 'basket' ) {
    add_action( 'admin_enqueue_scripts', array( 'SitePress_Table_Basket', 'enqueue_js' ) );
}

abstract class WPML_TM_Menus {

    protected $post_types;
    protected $tab_items;
    private $base_target_url;
    protected $current_shown_item;

    /** @var  WPML_UI_Screen_Options_Pagination|null $dashboard_pagination */
    protected $dashboard_pagination;

	function __construct() {
		$this->current_shown_item = isset( $_GET['sm'] ) ? $_GET['sm'] : $this->get_default_tab();
		$this->base_target_url    = dirname( __FILE__ );
	}

	public function display_main( WPML_UI_Screen_Options_Pagination $dashboard_pagination = null ) {
		$this->dashboard_pagination = $dashboard_pagination;
		if ( true !== apply_filters( 'wpml_tm_lock_ui', false ) ) {
			$this->render_main();
		}
	}

	abstract protected function render_main();

    private function build_tab_item_target_url($target)
    {
        return $this->base_target_url . $target;
    }

	abstract protected function build_tab_items();

    /**
     * @return string
     */
    private function get_current_shown_item()
    {
        return $this->current_shown_item;
    }

    private function build_tabs()
    {
        $tm_sub_menu = $this->get_current_shown_item();
        foreach ($this->tab_items as $id => $tab_item) {
            if (!isset($tab_item['caption'])) {
                continue;
            }
            if (!isset($tab_item['target']) && !isset($tab_item['callback'])) {
                continue;
            }

            $caption = $tab_item['caption'];
            if ( ! $this->current_user_can_access( $tab_item )) {
                continue;
            }

            $classes = array(
                'nav-tab'
            );
            if ($tm_sub_menu == $id) {
                $classes[] = 'nav-tab-active';
            }

            $class = implode(' ', $classes);
            $href = 'admin.php?page=' . WPML_TM_FOLDER . $this->get_page_slug() . '&sm=' . $id;
            ?>
            <a class="<?php echo esc_attr( $class ); ?>" href="<?php echo esc_attr( $href ); ?>">
                <?php echo $caption; ?>
            </a>
        <?php
        }
    }

    private function build_content() {
        $tm_sub_menu = $this->get_current_shown_item();
        foreach ($this->tab_items as $id => $tab_item) {
            if (!isset($tab_item['caption'])) {
                continue;
            }
            if (!isset($tab_item['target']) && !isset($tab_item['callback'])) {
                continue;
            }

            if ($tm_sub_menu == $id) {
            	if ( $this->current_user_can_access( $tab_item ) ) {
		            if ( isset( $tab_item['target'] ) ) {
			            $target = $tab_item['target'];
			            /** @noinspection PhpIncludeInspection */
			            include_once $this->build_tab_item_target_url( $target );
		            }
		            if ( isset( $tab_item['callback'] ) ) {
			            $callback = $tab_item['callback'];
			            call_user_func( $callback );
		            }
	            }
            }
        }
        do_action('icl_tm_menu_' . $tm_sub_menu);
    }

    protected function render_items()
    {
        if ($this->tab_items) {
            ?>
            <p class="icl-translation-management-menu wpml-tabs">
                <?php
                $this->build_tabs();
                ?>
            </p>
            <div class="icl_tm_wrap">
                <?php
                $this->build_content();
                ?>
            </div>
        <?php
        }
    }

	public function build_content_dashboard_fetch_translations_box() {
		if ( TranslationProxy::is_current_service_active_and_authenticated() ) {
			$tp_polling_box = new WPML_TP_Polling_Box();
			echo $tp_polling_box->render();
		}
	}

    /**
     * Used only by unit tests at the moment
     * @return mixed
     */
    public function get_post_types(){
        return $this->post_types;
    }


	protected function heading($text) {
		?>
		<h3 class="wpml-tm-section-header"><?php echo esc_html( $text ) ?></h3>
		<?php
	}

	private function current_user_can_access( $tab_item ) {
		$current_user_can = isset( $tab_item['current_user_can'] ) ? $tab_item['current_user_can'] : false;

		if ( is_array( $current_user_can ) ) {
			foreach ( $current_user_can as $capability ) {
				if ( current_user_can( $capability ) ) {
					return true;
				}
			}
			return false;
		} else {
			return current_user_can( $current_user_can );
		}
	}

	abstract protected function get_page_slug();

    abstract protected function get_default_tab();
}
