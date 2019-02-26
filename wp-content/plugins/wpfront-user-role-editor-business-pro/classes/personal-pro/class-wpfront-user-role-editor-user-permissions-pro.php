<?php

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WPFront_User_Role_Editor_User_Permissions_Pro')) {

    /**
     * User Permissions
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2015 WPFront.com
     */
    class WPFront_User_Role_Editor_User_Permissions_Pro extends WPFront_User_Role_Editor_User_Permissions {

        protected function can_edit_user($user_id, $edit_user_id) {
            return $this->has_permission($user_id, $edit_user_id, 'edit_users_higher_level');
        }

        protected function can_delete_user($user_id, $edit_user_id) {
            return $this->has_permission($user_id, $edit_user_id, 'delete_users_higher_level');
        }

        protected function can_promote_user($user_id, $edit_user_id) {
            return $this->has_permission($user_id, $edit_user_id, 'promote_users_higher_level');
        }

        private function has_permission($user_id, $edit_user_id, $cap) {
            $user = new WP_User($user_id);
            if (empty($user) || is_wp_error($user))
                return FALSE;

            if (!$user->exists())
                return FALSE;

            if ($user->has_cap($cap))
                return TRUE;

            $edit_user = new WP_User($edit_user_id);
            if (empty($edit_user) || is_wp_error($edit_user))
                return FALSE;

            if (!$edit_user->exists())
                return FALSE;

            for ($i = 10; $i > -1; $i--) {
                $user_cap = $user->has_cap('level_' . $i);
                $edit_user_cap = $edit_user->has_cap('level_' . $i);

                if ($user_cap)
                    return TRUE;

                if ($edit_user_cap)
                    return FALSE;
            }

            return TRUE;
        }

        public function assignable_roles($all_roles) {
            $user = wp_get_current_user();
            if (empty($user) || is_wp_error($user))
                return $all_roles;

            if (!$user->exists())
                return $all_roles;
            
            if (current_user_can('promote_users_to_higher_level')) {
                return $all_roles;
            }

            foreach ($all_roles as $key => $value) {
                $caps = $value['capabilities'];
                for ($i = 10; $i > -1; $i--) {
                    $user_cap = $user->has_cap('level_' . $i);
                    $role_cap = isset($caps['level_' . $i]) && $caps['level_' . $i];
                    
                    if($user_cap)
                        break;
                    
                    if($role_cap) {
                        unset($all_roles[$key]);
                        break;
                    }
                }
            }

            return $all_roles;
        }

    }

}

