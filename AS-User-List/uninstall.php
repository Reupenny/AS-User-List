<?php
// if uninstall.php is not called by WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// define the uninstall function
function favorites_list_uninstall()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'favorites';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
    delete_option('AS_User_List_icons_style');
    delete_option('AS_U_L_list_title');
    delete_option('AS_U_L_list_title_show');
    delete_option('AS_User_List_icon_add_text');
    delete_option('AS_User_List_icon_remove_text');
    delete_option('AS_U_L_list_url');
    delete_option('AS_U_L_show_woo');
    delete_option('AS_U_L_show_woo_list');
    delete_option('AS_U_L_custom_css');
}

// call the uninstall function
favorites_list_uninstall();
