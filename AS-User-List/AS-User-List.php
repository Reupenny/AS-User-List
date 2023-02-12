<?php

/**
 * Plugin Name: AS User List
 * Description: A flexible favourite & Wishlist wordpress plugin that allows users to add any page, product or post to their list.
 * Version:     0.1.1 beta
 * Author:      Azure Studio
 * Author URI:  https://azurestudio.co.nz
 * Plugin URI:  https://azurestudio.co.nz/plugins/
 * Text Domain: AS-User-List
 */

// Update Core.
require 'plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://reupenny.github.io/AS-User-List/update.json',
    __FILE__, //Full path to the main plugin file or functions.php.
    'AS-User-List'
);

// Create database table to store favorites
function favorites_list_install()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "favorites";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      user_id mediumint(9) NOT NULL,
      post_id mediumint(9) NOT NULL,
      date_added datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      PRIMARY KEY  (id)
   ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'favorites_list_install');


function AS_User_List_create_menu()
{
    register_setting('AS-U-L-general', 'AS_User_List_icons_style');
    register_setting('AS-U-L-general', 'AS_U_L_list_title');
    register_setting('AS-U-L-general', 'AS_U_L_list_title_show');
    register_setting('AS-U-L-general', 'AS_User_List_icon_add_text');
    register_setting('AS-U-L-general', 'AS_User_List_icon_remove_text');
    register_setting('AS-U-L-general', 'AS_U_L_list_url');
    register_setting('AS-U-L-general', 'AS_U_L_show_woo');
    register_setting('AS-U-L-general', 'AS_U_L_show_woo_list');
    register_setting('AS-U-L-General', 'AS_U_L_custom_css');
    register_setting('AS-U-L-General', 'AS_U_L_custom_css', 'sanitize_text_field');

    //, 'sanitize_text_field'

    add_option('AS_User_List_icon_remove_text', 'Remove Favourite');

    if (!get_option('AS_User_List_icon_remove_text')) {
        update_option('AS_User_List_icon_remove_text', 'Remove Favourite');
    }

    add_option('AS_User_List_icon_add_text', 'Add to Favourites');

    if (!get_option('AS_User_List_icon_add_text')) {
        update_option('AS_User_List_icon_add_text', 'Add to Favourites');
    }



    add_option('AS_U_L_list_title', 'Favourites');

    if (!get_option('AS_U_L_list_title')) {
        update_option('AS_U_L_list_title', 'Favourites');
    }

    add_option('AS_User_List_icons_style', 'default');

    if (!get_option('AS_User_List_icons_style')) {
        update_option('AS_User_List_icons_style', 'default');
    }

    add_option('AS_User_List_icons_style', 'default');

    if (!get_option('AS_U_L_list_url')) {
        update_option('AS_U_L_list_url', 'favourites');
    }

    add_options_page(
        'AS User List',
        'AS User List',
        'manage_options',
        'AS-U-L-general',
        'AS_User_List_settings_page'
    );
}
//Menus
add_action('admin_menu', 'AS_User_List_create_menu');

require_once(plugin_dir_path(__FILE__) . 'settings-page.php');
require_once(plugin_dir_path(__FILE__) . 'add_to_list.php');
require_once(plugin_dir_path(__FILE__) . 'display_list.php');


add_action('wp_ajax_get_home_url', 'get_home_url_callback');

function get_home_url_callback()
{
    wp_send_json_success(home_url());
}

//Load Style Sheet
add_action('wp_enqueue_scripts', 'favorites_list_enqueue_styles');
function favorites_list_enqueue_styles()
{
    wp_enqueue_style('favorites-list-style', plugin_dir_url(__FILE__) . 'css/AS-U-L.css');
}
