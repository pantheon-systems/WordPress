<?php
/*
  Plugin Name: WP Charities Report
 */
 
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

function ewc_render_list_page() {
 
    $testListTable = new Elix_Woo_Charity_Table();
    $testListTable->prepare_items();
    print '<div class="wrap">';
    print '<div id="icon-users" class="icon32"><br/></div>';
    print '<h2>' . __('Charity Report') . '</h2>';
    print $testListTable->display();
    print '</div>';
}
 

class Elix_Woo_Charity_Table extends WP_List_Table {
 
    function __construct() {
        global $status, $page;

        parent::__construct(array(
            'singular' => __('Charity Selection'),
            'plural' => __('Charity Selections'),
            'ajax' => false
        ));
    }

    function column_default($item, $column_name) {
        switch ($column_name) {
            case 'post_id':
            case 'post_date':
            case 'first_name':
            case 'last_name':
            case 'total':
            case 'charity':
                return $item->$column_name;
            default:
                return "col name = $column_name , " . print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    function get_columns() {
        return $columns = array(
            'post_id' => __('ID'),
            'post_date' => __('Date'),
            'first_name' => __('First Name'),
            'last_name' => __('Last Name'),
            'total' => __('Order Total'),
            'charity' => __('Charity')
        );
    }

    function prepare_items() {
        global $wpdb;
    
        $charity = (isset($_GET['charity'])) ? sanitize_text_field($_GET['charity']) : '';
        $charity_where = (strlen($charity)) ? " AND charity = '" . urldecode($charity) . "' " : '';
        $range = (isset($_GET['range'])) ? sanitize_text_field($_GET['range']) : '';
        $range_where = (strlen($range)) ? " AND post_date LIKE '" . $range . "%' " : '';

        $query = "SELECT * FROM wp_charity_report WHERE 1 " . $range_where . " " . $charity_where;
        if (isset($_GET['action']) && $_GET['action'] == 'download_csv') {
            return $this->_write_csv($query);
        }
        $orderby = (isset($_GET['orderby']) && is_numeric($_GET['orderby'])) ? $_GET['orderby'] : 'post_id';
        $order = (isset($_GET['order']) && is_numeric($_GET['order'])) ? $_GET['order'] : 'DESC';
        $totalitems = $wpdb->query($query);
        $perpage = 20;
        $paged = (isset($_GET['paged']) && is_numeric($_GET['paged'])) ? $_GET['paged'] : '1';
        $totalpages = ceil($totalitems / $perpage);
        $offset = ($paged - 1) * $perpage;

        $query .= ' ORDER BY ' . $orderby . ' ' . $order;
        $query .= ' LIMIT ' . (int) $offset . ',' . (int) $perpage;

        $this->set_pagination_args([
            'total_items' => $totalitems,
            'total_pages' => $totalpages,
            'per_page' => $perpage,
        ]);
        $this->_column_headers = [$this->get_columns(), [], []];
        $this->items = $wpdb->get_results($query);
    }

    function extra_tablenav( $which ) {
        global $wpdb, $testiURL, $tablename, $tablet;

        $current_charity = (isset($_GET['charity'])) ? sanitize_text_field($_GET['charity']) : '';
        $current_range = (isset($_GET['range'])) ? sanitize_text_field($_GET['range']) : '';

        if ( $which == "top" ){
            $move_on_url = '';

            // Month Filter
            print '<div class="alignleft actions bulkactions">';
            $ranges = [
                date('Y-m') => date('F Y'),
                date('Y-m', strtotime('-1 month')) => date('F Y', strtotime('-1 month')),
                date('Y-m', strtotime('-2 month')) => date('F Y', strtotime('-2 month')),
                date('Y-m', strtotime('-3 month')) => date('F Y', strtotime('-3 month')),
                date('Y') => date('Y'),
                date('Y', strtotime('-1 year')) => date('Y', strtotime('-1 year')),
            ];
            if( $ranges ){
                $move_on_url = '&charity=' . $current_charity . '&range=';
                print '<select name="range-filter" class="ewc-filter-range"><option value="">' . __('Filter by Date') . '</option>';
                foreach( $ranges as $key => $range ){
                    $selected = '';
                    if( '-' . $key . '-' == '-' . $current_range . '-' ){
                        $selected = ' selected = "selected"';
                    }
                    print '<option value="' . $move_on_url . $key . '" ' . $selected . '>' . $ranges[$key] . '</option>';
                }
                print '</select>';
            }
            print '</div>';

            // Charity Filter
            print '<div class="alignleft actions bulkactions">';
            $charities = $wpdb->get_results("SELECT meta_value FROM wp_postmeta WHERE meta_value <> '' AND meta_key = '_billing_donations' GROUP BY meta_value", ARRAY_A);
            if( $charities ){
                $move_on_url = '&range=' . $current_range . '&charity=';
                print '<select name="charity-filter" class="ewc-filter-charity"><option value="">' . __('Filter by Charity') . '</option>';
                foreach( $charities as $charity ){
                    $selected = '';
                    if( $_GET['charity'] == $charity['meta_value'] ){
                        $selected = ' selected = "selected"';   
                    }
                    print '<option value="' . $move_on_url . $charity['meta_value'] . '" ' . $selected . '>' . $charity['meta_value'] . '</option>';
                }
                print '</select>';
            }
            print '</div>';

            // Download CSV
            print '<div class="alignleft actions bulkactions"><a class="button" href="admin.php?page=ewc_list_table&action=download_csv&range=' . $current_range . '&charity=' . $current_charity . '">' . __('Download CSV') . '</a>';
            print '</div>';
        }
    }

    function _write_csv($query) {
        global $wpdb;
        $results = $wpdb->get_results($query);
        $delimiter = ',';
        $fields = array('Order ID', 'Date', 'First Name', 'Last Name', 'Total', 'Charity');

        $file = fopen('php://output', 'w');
        ob_start();
        fputcsv($file, $fields, $delimiter);
        foreach ($results as $result) {
            $lineData = array($result->post_id, $result->post_date, $result->first_name, $result->last_name, $result->total, $result->charity);
            fputcsv($file, $lineData, $delimiter);
        }
        $string = ob_get_clean();
        ob_clean();
        $filename = "../wp-content/uploads/charity_report_" . date('Y-m-d') . ".csv";
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false);
        header("Content-Type: application/octet-stream");
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        header("Content-Transfer-Encoding: binary");
        exit($string);
    }

}
