<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

require_once dirname(__FILE__) . '/classes/class-wpfront-user-role-editor.php';

if (is_multisite() && class_exists('WPFront_User_Role_Editor_Business_Pro_Controller_Base')) {
    $entity = new WPFront_User_Role_Editor_Business_Pro_Controller_Base(NULL);
    $blogids = $entity->get_ms_blog_ids();

    switch_to_blog(WPFront_User_Role_Editor_Options::get_ms_options_blog_id());
    $entity = new WPFront_User_Role_Editor_Options(NULL);
    if ($entity->remove_data_on_uninstall()) {
        foreach ($blogids as $blogid) {
            switch_to_blog($blogid);

            WPFront_User_Role_Editor_Entity_Options::uninstall();
            WPFront_User_Role_Editor_Entity_Menu_Editor::uninstall();
            WPFront_User_Role_Editor_Entity_Post_Type_Permissions::uninstall();
            WPFront_User_Role_Editor_Entity_Content_Shortcodes::uninstall();
            WPFront_User_Role_Editor_Entity_Login_Redirect::uninstall();

            WPFront_User_Role_Editor_Nav_Menu::uninstall();
            WPFront_User_Role_Editor_Widget_Permissions::uninstall();
        }
    }

    restore_current_blog();
} else {
    $entity = new WPFront_User_Role_Editor_Options(NULL);
    if ($entity->remove_data_on_uninstall()) {
        if (class_exists('WPFront_User_Role_Editor_Entity_Options'))
            WPFront_User_Role_Editor_Entity_Options::uninstall();

        if (class_exists('WPFront_User_Role_Editor_Entity_Menu_Editor'))
            WPFront_User_Role_Editor_Entity_Menu_Editor::uninstall();

        if (class_exists('WPFront_User_Role_Editor_Entity_Post_Type_Permissions'))
            WPFront_User_Role_Editor_Entity_Post_Type_Permissions::uninstall();

        if (class_exists('WPFront_User_Role_Editor_Entity_Content_Shortcodes'))
            WPFront_User_Role_Editor_Entity_Content_Shortcodes::uninstall();

        if (class_exists('WPFront_User_Role_Editor_Entity_Login_Redirect'))
            WPFront_User_Role_Editor_Entity_Login_Redirect::uninstall();


        if (class_exists('WPFront_User_Role_Editor_Nav_Menu'))
            WPFront_User_Role_Editor_Nav_Menu::uninstall();
        
        if (class_exists('WPFront_User_Role_Editor_Widget_Permissions'))
            WPFront_User_Role_Editor_Widget_Permissions::uninstall();
    }
}


