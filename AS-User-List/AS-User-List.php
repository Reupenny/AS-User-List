<?php

/**
 * Plugin Name: AS User List
 * Description: A flexible favourite & Wishlist plugin that allows users to add any page, product or post to their list.
 * Version:     1.1.0
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

if (function_exists('aul_fs')) {
    aul_fs()->set_basename(true, __FILE__);
} else {
    // DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
    if (!function_exists('aul_fs')) {
        // freemius Core.
        // Create a helper function for easy SDK access.
        function aul_fs()
        {
            global $aul_fs;

            if (!isset($aul_fs)) {
                // Include Freemius SDK.
                require_once dirname(__FILE__) . '/freemius/start.php';

                $aul_fs = fs_dynamic_init(array(
                    'id'                  => '12914',
                    'slug'                => 'as-user-list',
                    'type'                => 'plugin',
                    'public_key'          => 'pk_80ef1e15c9fc6ffb64af3f4d52b79',
                    'is_premium'          => true,
                    'premium_suffix'      => 'Premium',
                    // If your plugin is a serviceware, set this option to false.
                    'has_premium_version' => true,
                    'has_addons'          => false,
                    'has_paid_plans'      => true,
                    'navigation'          => 'tabs',
                    'menu'                => array(
                        'slug'           => 'AS-U-L-general',
                        'contact'        => true,
                        'support'        => false,
                        'parent'         => array(
                            'slug' => 'options-general.php',
                        ),
                    ),
                    'secret_key'          => 'sk_5a0>7[NzL*74]Gf7({]ZRfAV6*dbs',
                ));
            }

            return $aul_fs;
        }

        // Init Freemius.
        aul_fs();
        // Signal that SDK was initiated.
        do_action('aul_fs_loaded');

        function aul_fs_settings_url()
        {
            return admin_url('options-general.php?page=AS-U-L-general');
        }

        aul_fs()->add_filter('connect_url', 'aul_fs_settings_url');
        aul_fs()->add_filter('after_skip_url', 'aul_fs_settings_url');
        aul_fs()->add_filter('after_connect_url', 'aul_fs_settings_url');
        aul_fs()->add_filter('after_pending_connect_url', 'aul_fs_settings_url');
    }

    function favorites_list_install()
    {
        global $wpdb;
        $old_table_name = $wpdb->prefix . "favorites";
        $new_table_name = $wpdb->prefix . "AS_User_List";
        $charset_collate = $wpdb->get_charset_collate();

        // Check if the old table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$old_table_name'") === $old_table_name) {
            // Rename the old table to the new table
            $wpdb->query("RENAME TABLE $old_table_name TO $new_table_name");
        } else {
            // Create the new table if the old table doesn't exist
            $sql = "CREATE TABLE $new_table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                user_id mediumint(9) NOT NULL,
                post_id mediumint(9) NOT NULL,
                date_added datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                PRIMARY KEY  (id)
            ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
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

    require_once(plugin_dir_path(__FILE__) . 'includes/settings-page.php');
    require_once(plugin_dir_path(__FILE__) . 'includes/add_to_list.php');
    require_once(plugin_dir_path(__FILE__) . 'includes/display_list_shortcode.php');
    require_once(plugin_dir_path(__FILE__) . 'includes/List_count_shortcode.php');
    require_once(plugin_dir_path(__FILE__) . 'includes/display_list_acc_page.php');

    if (aul_fs()->is__premium_only()) {
        if (aul_fs()->can_use_premium_code()) {
            require_once(plugin_dir_path(__FILE__) . 'includes/widget__premium_only.php');
        }
    }

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
}
