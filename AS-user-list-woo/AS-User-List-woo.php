<?php

/**
 * Plugin Name: AS User List Woo
 * Description: A flexible favourite & Wishlist plugin that allows users to add any page, product or post to their list.
 * Version:     1.0.0
 * Author:      Azure Studio
 * Author URI:  https://azurestudio.co.nz
 * Plugin URI:  https://azurestudio.co.nz/plugins/
 * Text Domain: AS-User-List
 * 
 * Requires at least: 5.8.7
 * Tested up to: 6.2.2
 * Requires PHP: 7.4.33
 * 
 */

if (!defined('ABSPATH')) {
    exit;
}
function favorites_list_install()
{
    global $wpdb;
    $new_table_name = $wpdb->prefix . "AS_User_List_woo";
    $charset_collate = $wpdb->get_charset_collate();
    // Create the new table if the old table doesn't exist
    $sql = "CREATE TABLE $new_table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                user_id mediumint(9) NOT NULL,
                post_id mediumint(9) NOT NULL,
                date_added datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                receive_notifications tinyint(1) DEFAULT '0' NOT NULL,
                last_notification datetime DEFAULT NULL,
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

    // add_options_page(
    //    'AS User List',
    //    'AS User List',
    //    'manage_options',
    //    'AS-U-L-general',
    //    'AS_User_List_settings_page'
    //);
}


// Add the options page under the WooCommerce menu
add_filter('woocommerce_get_settings_pages', 'add_as_user_list_settings_page');

function add_as_user_list_settings_page($settings_pages)
{
    $settings_pages[] = array(
        'id' => 'as_user_list',
        'title' => 'AS User List',
        'callback' => 'as_user_list_settings_page',
        'option_name' => 'as_user_list_options',
        'fa_icon' => 'fa-list'
    );

    return $settings_pages;
}
//Menus
add_action('admin_menu', 'AS_User_List_create_menu');

require_once(plugin_dir_path(__FILE__) . 'includes/settings-page.php');
require_once(plugin_dir_path(__FILE__) . 'includes/add_to_list.php');
require_once(plugin_dir_path(__FILE__) . 'includes/display_list_shortcode.php');
require_once(plugin_dir_path(__FILE__) . 'includes/List_count_shortcode.php');
require_once(plugin_dir_path(__FILE__) . 'includes/display_list_acc_page.php');
require_once(plugin_dir_path(__FILE__) . 'includes/widget.php');

add_action('wp_ajax_get_home_url', 'get_home_url_callback');

function get_home_url_callback()
{
    wp_send_json_success(home_url());
}

// Define the function that will handle the activation redirection
function as_user_list_activation_redirect()
{
    // Redirect the user to the settings page after activation
    if (is_admin() && get_option('my_plugin_activation_redirect', false)) {
        delete_option('my_plugin_activation_redirect');
        wp_redirect(admin_url('options-general.php?page=AS-U-L-general'));
        exit;
    }
}

// Register the activation hook
register_activation_hook(__FILE__, 'as_user_list_activate_plugin');
add_action('admin_init', 'as_user_list_activation_redirect');

function as_user_list_activate_plugin()
{
    // Set the activation redirect flag
    add_option('my_plugin_activation_redirect', true);
}

// Define the function that will add the settings link
function my_plugin_settings_link($links)
{
    $settings_link = '<a href="' . admin_url('options-general.php?page=AS-U-L-general') . '">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}

// Hook the function to the plugin_action_links filter
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'my_plugin_settings_link');

//Load Style Sheet
add_action('wp_enqueue_scripts', 'favorites_list_enqueue_styles');
function favorites_list_enqueue_styles()
{
    wp_enqueue_style('favorites-list-style', plugin_dir_url(__FILE__) . 'includes/css/AS_U_L.css');
}
