<?php
add_shortcode('as_count_list', 'AS_count_list_shortcode');

/**
 * Render the favorites list count shortcode.
 * 
 * Default - [as_count_list]
 * displays the number of items is a users ilst
 * 
 * name - [as_count_list opt="name"]
 * Displays the list name and the number of items in a users list 
 *   e.g. Favourites (4)
 * 
 * link - [as_count_list opt="link"]
 * The same as above but when the user clicks on it it takes them to the list page.
 * 
 * 
 */
function AS_count_list_shortcode($atts)
{
    $atts = shortcode_atts(
        array(
            'opt' => 'default',
        ),
        $atts,
        'as_count_list'
    );

    global $wpdb;
    $current_user = wp_get_current_user();
    $table_name = $wpdb->prefix . "AS_User_List";
    $AS_U_L_item = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id = $current_user->ID ORDER BY date_added DESC");
    $AS_U_L_list_title = get_option('AS_U_L_list_title');
    $count = count($AS_U_L_item);

    $output = '';

    if ($atts['opt'] === 'default') {
        $output = $count;
    } elseif ($atts['opt'] === 'name') {
        $output = $AS_U_L_list_title . ' (' . $count . ')';
    } elseif ($atts['opt'] === 'link') {
        if (is_plugin_active('woocommerce/woocommerce.php')) {
            $fave_url = wc_get_account_endpoint_url('/list');
        } else {
            $fave_url = get_option('AS_U_L_list_url');
        }
        $output = '<a href="' . $fave_url . '">' . $AS_U_L_list_title . ' (' . $count . ')</a>';
    }

    return $output;
}
