<?php

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WPFront_User_Role_Editor_Bulk_Edit_Extended_Permissions')) {

    /**
     * Bulk Edit Extended Permissions
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2015 WPFront.com
     */
    class WPFront_User_Role_Editor_Bulk_Edit_Extended_Permissions extends WPFront_User_Role_Editor_Personal_Pro_Controller_Base {

        private $step = 0;
        private $post_type = NULL;
        private $selected_posts = NULL;
        private $post_type_permissions_object = NULL;
        private $request_post = NULL;

        function __construct($main) {
            parent::__construct($main);

            $this->ajax_register('wp_ajax_wpfront_user_role_editor_bulk_edit_extended_permissions_posts_table', array($this, 'extended_permissions_posts_table'));
            $this->ajax_register('wp_ajax_wpfront_user_role_editor_bulk_edit_extended_permissions_update_post', array($this, 'extended_permissions_update_post'));
        }

        private function validate_post_type() {
            if (empty($_POST['post-type'])) {
                $this->step = 1;
            } else {
                $type = $_POST['post-type'];
                $types_data = $this->get_custom_post_types();
                if (isset($types_data[$type])) {
                    $this->post_type = get_post_type_object($type);
                    if (empty($this->post_type)) {
                        $this->step = 1;
                        $this->post_type = NULL;
                    }
                } else {
                    $this->step = 1;
                }
            }

            return $this->step !== 1;
        }

        private function validate_selected_posts() {
            if (empty($_POST['select-posts'])) {
                $this->step = 1;
            } else {
                if ($_POST['select-posts'] === 'all') {
                    $this->selected_posts = 'all';
                } elseif ($_POST['select-posts'] === 'selected') {
                    if (empty($_POST['selected-posts'])) {
                        $this->step = 2;
                    } else {
                        $this->selected_posts = $_POST['selected-posts'];
                    }
                } else {
                    $this->step = 1;
                }
            }

            return $this->step !== 1 && $this->step !== 2;
        }

        private function get_step() {
            if (!empty($this->step))
                return $this->step;

            if (!empty($_POST['step'])) {
                $this->step = $_POST['step'];
            }

            $this->step = $this->step + 1;

            switch ($this->step) {
                case 1:
                    break;
                case 2:
                    $this->validate_post_type();
                    break;
                case 3:
                    $this->validate_post_type();
                    $this->validate_selected_posts();
                    break;
                case 4:
                    $this->validate_post_type();
                    $this->validate_selected_posts();
                    $this->request_post = serialize($_POST);
                    break;
                default:
                    $this->step = 1;
                    break;
            }

            return $this->step;
        }

        public function bulk_edit() {
            $this->main->verify_nonce('bulk_edit');

            if ($this->get_step() === 3) {
                $this->post_type_permissions_object = new WPFront_User_Role_Editor_Custom_Post_Permissions($this->post_type->name, $this->main, NULL, NULL);
            }

            include($this->main->pluginDIR() . 'templates/personal-pro/bulk-edit-extended-permissions.php');
        }

        public function extended_permissions_posts_table() {
            $this->main->verify_nonce('bulk_edit');

            foreach ($_POST as $key => $value) {
                $_GET[$key] = $value;
            }

            $this->step = 1000;
            $this->post_type = get_post_type_object($_GET['post-type']);
            $this->bulk_edit();
        }

        private function get_custom_post_types() {
            $types_data = $this->main->get_extended_post_types_info();
            $result = array();

            $post_types = get_post_types(array(), 'object');
            foreach ($post_types as $value) {
                if (isset($types_data[$value->name]) && current_user_can($types_data[$value->name]->permission_cap) && current_user_can($value->cap->edit_posts)) {
                    $result[$value->name] = $value->labels->name;
                }
            }

            return $result;
        }

        private function get_posts_count() {
            if ($this->selected_posts === 'all') {
                $query = $this->get_wp_query('all');
                return $query->found_posts;
            }

            $posts = explode(' ', trim($this->selected_posts));
            return count($posts);
        }

        private function get_wp_query($type, $args = array()) {
            $query = $args;
            $query['post_type'] = $this->post_type->name;
            $query['orderby'] = 'ID';
            $query['order'] = 'asc';
            $query['posts_per_page'] = 1;
            $query['posts_per_archive_page'] = 1;
            $query['fields'] = 'ids';

            switch ($type) {
                case 'all':
                    break;
            }

            return new WP_Query($query);
        }

        public function extended_permissions_update_post() {
            $this->main->verify_nonce('bulk_edit');

            if (empty($_POST['post-type']) || empty($_POST['posts']) || empty($_POST['count']) || empty($_POST['request-post']) || !isset($_POST['index'])) {
                echo 'false';
                die();
            }

            if (!$this->validate_post_type()) {
                echo 'false';
                die();
            }

            $posts = $_POST['posts'];
            $count = $_POST['count'];
            $index = $_POST['index'];
            $this->request_post = '"' . $_POST['request-post'] . '"';
            $this->request_post = json_decode($this->request_post);
            $this->request_post = unserialize($this->request_post);

            $eof = $index >= $count - 1;
            $success = FALSE;
            $error = '';
            $title = '';
            $link = '';

            if ($posts === 'all') {
                $posts = $this->get_wp_query('all', array('paged' => $index + 1));
                if ($posts->post_count === 0) {
                    $error = $this->__('Post not found');
                    $posts = NULL;
                } else {
                    $posts = $posts->next_post();
                    $posts = get_post($posts);
                    if (empty($posts)) {
                        $error = $this->__('Post not found');
                        $posts = NULL;
                    }
                }
            } else {
                $posts = trim($posts);
                $posts = explode(' ', $posts);
                if ($index >= count($posts)) {
                    $error = $this->__('Post not found');
                    $posts = NULL;
                } else {
                    $posts = $posts[$index];
                    $posts = get_post($posts);
                    if (empty($posts)) {
                        $error = $this->__('Post not found');
                        $posts = NULL;
                    }
                }
            }

            if (!empty($posts)) {
                $title = $posts->post_title;
                $link = get_permalink($posts->ID);

                if (current_user_can('edit_post', $posts->ID)) {
                    foreach ($this->request_post as $key => $value) {
                        $_POST[$key] = $value;
                    }

                    $this->post_type_permissions_object = new WPFront_User_Role_Editor_Custom_Post_Permissions($this->post_type->name, $this->main, NULL, NULL);

                    if ($this->request_post['role-type'] === 'multiple') {
                        if ($this->post_type_permissions_object->save_post($posts->ID)) {
                            $success = TRUE;
                        } else {
                            $error = $this->__('Permission denied');
                            $posts = NULL;
                        }
                    } elseif ($this->request_post['role-type'] === 'single') {
                        $permissions = (OBJECT) array(
                                    'read' => !empty($this->request_post['single-role-permissions']) && !empty($this->request_post['single-role-permissions']['read']),
                                    'edit' => !empty($this->request_post['single-role-permissions']) && !empty($this->request_post['single-role-permissions']['edit']),
                                    'delete' => !empty($this->request_post['single-role-permissions']) && !empty($this->request_post['single-role-permissions']['delete'])
                        );

                        if ($this->post_type_permissions_object->save_post_role_data($posts->ID, $this->request_post['single-role-name'], TRUE, $permissions)) {
                            $success = TRUE;
                        } else {
                            $error = $this->__('Permission denied');
                            $posts = NULL;
                        }
                    } else {
                        $error = $this->__('Permission denied');
                        $posts = NULL;
                    }
                } else {
                    $error = $this->__('Permission denied');
                    $posts = NULL;
                }
            }

            $result = (OBJECT) array(
                        'eof' => $eof,
                        'success' => $success,
                        'error' => $error,
                        'title' => $title,
                        'link' => $link
            );

            echo json_encode($result);
            die();
        }

        private function get_list_table() {
            $GLOBALS['hook_suffix'] = '';

            $screen = WP_Screen::get();
            $screen->id = $this->post_type->name;
            $screen->post_type = $this->post_type->name;

            WP_Screen::get($screen)->set_current_screen();

            $_GET['post_type'] = $this->post_type->name;

            add_filter('page_row_actions', array($this, 'post_table_row_actions'), 1000);
            add_filter('post_row_actions', array($this, 'post_table_row_actions'), 1000);
            add_filter("bulk_actions-{$screen->id}", array($this, 'post_table_bulk_actions'), 1000);
            //add_filter('user_has_cap', array($this, 'post_table_current_user_can'), 1000, 3);

            return _get_list_table('WP_Posts_List_Table');
        }

        public function post_table_row_actions($actions) {
            return array();
        }

        public function post_table_bulk_actions($actions) {
            return array();
        }

        public function post_table_current_user_can($allcaps, $caps, $args) {
            if ($args[0] === "edit_{$this->post_type->name}")
                $args[0] = 'edit_post';
            elseif ($args[0] === "delete_{$this->post_type->name}")
                $args[0] = 'delete_post';

            if ($args[0] !== 'edit_post' && $args[0] !== 'delete_post') {
                return $allcaps;
            }

            if (empty($args[2]))
                return $allcaps;

            if (get_post_type($args[2]) !== $this->post_type->name)
                return $allcaps;

            foreach ($caps as $cap) {
                unset($allcaps[$cap]);
            }

            return $allcaps;
        }

        public function get_bulk_edit_url() {
            return parent::get_bulk_edit_url() . '&bulk-edit-type=extended-permissions';
        }

        protected function add_help_tab() {
            return array(
                array(
                    'id' => 'overview',
                    'title' => $this->__('Overview'),
                    'content' => '<p>'
                    . $this->__('Step 1: Select the post type you want to bulk edit.')
                    . '</p>'
                    . '<p>'
                    . $this->__('Step 2: Select the posts you want to bulk edit.')
                    . '</p>'
                    . '<p>'
                    . $this->__('Step 3: Select the role permissions.')
                    . '</p>'
                    . '<p>'
                    . $this->__('Step 4: Update role permissions.')
                    . '</p>'
                )
            );
        }

        protected function set_help_sidebar() {
            return array(
                array(
                    $this->__('Documentation on Bulk Edit'),
                    'bulk-edit/'
                )
            );
        }

    }

}