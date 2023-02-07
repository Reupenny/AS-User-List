<?php

/**
 * Plugin Name: Better Favourites
 * Description: A Wordpress plugin to better handle all Wordpress notifications and keep you notified of the important things.
 * Version:     0.1.0 beta
 * Author:      Azure Studio
 * Author URI:  https://azurestudio.co.nz
 * Plugin URI:  https://azurestudio.co.nz/plugins/
 * Text Domain: Better-Favourites
 */

// Update Core.
require 'plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://reupenny.github.io/Better-Favourites/update.json',
    __FILE__, //Full path to the main plugin file or functions.php.
    'Better-Notified'
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


function Better_Favourites_create_menu()
{
    register_setting('better-favourites-general', 'Better_Favourites_icons_style');
    register_setting('better-favourites-general', 'Better_fav_list_title');
    register_setting('better-favourites-general', 'Better_Favourites_icon_add_text');
    register_setting('better-favourites-general', 'Better_Favourites_icon_remove_text');
    register_setting('better-favourites-general', 'Better_fav_list_url');


    add_option('Better_Favourites_icon_remove_text', 'Remove Favourite');

    if (!get_option('Better_Favourites_icon_remove_text')) {
        update_option('Better_Favourites_icon_remove_text', 'Remove Favourite');
    }

    add_option('Better_Favourites_icon_add_text', 'Add to Favourites');

    if (!get_option('Better_Favourites_icon_add_text')) {
        update_option('Better_Favourites_icon_add_text', 'Add to Favourites');
    }



    add_option('Better_fav_list_title', 'Favourites');

    if (!get_option('Better_fav_list_title')) {
        update_option('Better_fav_list_title', 'Favourites');
    }

    add_option('Better_Favourites_icons_style', 'default');

    if (!get_option('Better_Favourites_icons_style')) {
        update_option('Better_Favourites_icons_style', 'default');
    }

    add_option('Better_Favourites_icons_style', 'default');

    if (!get_option('Better_fav_list_url')) {
        update_option('Better_fav_list_url', 'favourites');
    }

    add_options_page(
        'Better Favourites',
        'Better Favourites',
        'manage_options',
        'better-favourites-general',
        'Better_Favourites_settings_page'
    );
}
//Menus
add_action('admin_menu', 'Better_Favourites_create_menu');

require_once(plugin_dir_path(__FILE__) . 'settings-page.php');
require_once(plugin_dir_path(__FILE__) . 'add_to_list.php');
require_once(plugin_dir_path(__FILE__) . 'display_list.php');


add_action('wp_ajax_get_home_url', 'get_home_url_callback');

function get_home_url_callback()
{
    wp_send_json_success(home_url());
}
