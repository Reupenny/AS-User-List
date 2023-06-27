<?php
add_shortcode('as_add_to_list', 'add_to_favorites_button_shortcode');

add_action('wp_enqueue_scripts', 'favorites_list_script');
add_action('wp_ajax_add_to_favorites', 'add_to_favorites');
add_action('wp_ajax_remove_from_favorites', 'remove_from_favorites');
add_action('wp_enqueue_scripts', 'favorites_list_remove_script');


//checks if woocommerce options are selected
$woo_on = get_option('AS_U_L_show_woo');
if ($woo_on == '1') {
    add_action('woocommerce_after_add_to_cart_button', 'add_text_after_add_to_cart_button');
    add_action('woocommerce_single_product_summary', 'add_text_after_add_to_cart_button_no_stock', 35);
}
$woo_on_list = get_option('AS_U_L_show_woo_list');
if ($woo_on_list == '1') {
    add_action('woocommerce_after_shop_loop_item', 'add_text_after_add_to_cart_button_shop_loop', 9);
}

// Shortcode for the "Add to Favorites" button
function add_to_favorites_button_shortcode()
{
    wp_enqueue_style('favorites-list-style', plugin_dir_url(__FILE__) . 'css/AS_U_L.css');
    $AS_U_L_list_title = get_option('AS_U_L_list_title');
    global $wpdb, $post;
    $current_user = wp_get_current_user();
    $table_name = $wpdb->prefix . "favorites";
    $favorite = $wpdb->get_row("SELECT * FROM $table_name WHERE user_id = $current_user->ID AND post_id = $post->ID");
    // Check if WooCommerce is installed and active
    if (is_plugin_active('woocommerce/woocommerce.php')) {
        $fave_url = wc_get_account_endpoint_url('/list');
    } else {
        $fave_url = get_option('AS_U_L_list_url');
    }
    $modal = '<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"><div class="modal-background" id="modal_back_close"></div><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><h4 class="modal-title" id="myModalLabel">Item added to ' . $AS_U_L_list_title . '</h4></div><div class="modal-body"><p>Check out your ' . $AS_U_L_list_title . ' <a href="' . $fave_url . '">here</a>.</p></div><div class="modal-footer"><button type="button" id="modal_close" class=" modal_close wp-element-button" data-dismiss="modal">Close</button></div></div></div></div>';
    $icon_add_text = get_option('AS_User_List_icon_add_text');
    if ($icon_add_text == 'NA') {
        $icon_add_text = '';
    }
    $icon_remove_text = get_option('AS_User_List_icon_remove_text');
    if ($icon_remove_text == 'NA') {
        $icon_remove_text = '';
    }
    $icon_style = get_option('AS_User_List_icons_style');
    if ($icon_style == 'default') {
        if ($favorite) {
            return '<br><span data-post-id="' . $post->ID . '" class="remove-favorite button wp-element-button">' . $icon_remove_text . '</span>' . $modal;
        } else {
            return '<br><span id="add-to-favorites-product-' . $post->ID . '" class="add-to-favorites button wp-element-button" data-post-id="' . $post->ID . '">' . $icon_add_text . '</span>' . $modal;
        }
    }
    if ($icon_style == 'bookmark') {
        if ($favorite) {
            return '<br><span data-post-id="' . $post->ID . '" class="remove-favorite AS_U_L_button"><span class="icon icon-bookmark"></span> ' . $icon_remove_text . '</span>' . $modal;
        } else {
            return '<br><span id="add-to-favorites-product-' . $post->ID . '" class="add-to-favorites AS_U_L_button" data-post-id="' . $post->ID . '"><span class="icon icon-bookmark-outline"></span> ' . $icon_add_text . '</span>' . $modal;
        }
    }
    if ($icon_style == 'heart') {
        if ($favorite) {
            return '<br><span data-post-id="' . $post->ID . '" class="remove-favorite AS_U_L_button"><span class="icon icon-heart"></span>  ' . $icon_remove_text . '</span>' . $modal;
        } else {
            return '<br><span id="add-to-favorites-product-' . $post->ID . '" class="add-to-favorites AS_U_L_button" data-post-id="' . $post->ID . '"><span class="icon icon-heart-outline"></span> ' . $icon_add_text . '</span>' . $modal;
        }
    }
    return $output;
}

//AJAX action to handle adding items to the favorites list
function add_to_favorites()
{
    global $wpdb;
    $current_user = wp_get_current_user();
    $table_name = $wpdb->prefix . "favorites";
    $wpdb->insert(
        $table_name,
        array(
            'user_id' => $current_user->ID,
            'post_id' => $_POST['post_id'],
            'date_added' => current_time('mysql')
        )
    );
    wp_die();
}

// JavaScript to handle the button click
function favorites_list_script()
{
    if (is_user_logged_in()) {
        wp_enqueue_script('favorites-list', plugin_dir_url(__FILE__) . 'js/favorites-list.js', array('jquery'), false, true);
        wp_localize_script('favorites-list', 'favorites_list', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('favorites_list_nonce'),
        ));
    }
}

// AJAX action to handle removing items from the favorites list
function remove_from_favorites()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "favorites";
    $wpdb->delete(
        $table_name,
        array(
            'user_id' => get_current_user_id(),
            'post_id' => $_POST['post_id']
        )
    );
    wp_die();
}

//JavaScript to handle the remove button click
function favorites_list_remove_script()
{
    if (is_user_logged_in()) {
        wp_enqueue_script('favorites-list-remove', plugin_dir_url(__FILE__) . 'js/favorites-list-remove.js', array('jquery'), false, true);
        wp_localize_script('favorites-list-remove', 'favorites_list_remove', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('favorites_list_remove_nonce'),
        ));
    }
}

// Button after 'Add to cart' button
function add_text_after_add_to_cart_button()
{
    echo do_shortcode('[as_add_to_list]');
}
function add_text_after_add_to_cart_button_no_stock()
{
    global $product;
    if (!$product->is_in_stock()) {
        echo do_shortcode('[as_add_to_list]');
    }
}

// Button after 'Add to cart' button on shop pages
function add_text_after_add_to_cart_button_shop_loop()
{

    echo '<div class="favorites-shop-loop">';
    echo do_shortcode('[as_add_to_list]');
    echo '</div>';
}
