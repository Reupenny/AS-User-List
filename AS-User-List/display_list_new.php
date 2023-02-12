<?php
add_shortcode('favorites_list', 'favorites_list_shortcode');


// Shortcode to display the favorites list
function favorites_list_shortcode()
{

    global $wpdb, $post;
    $current_user = wp_get_current_user();
    $table_name = $wpdb->prefix . "favorites";
    $favorites = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id = $current_user->ID ORDER BY date_added DESC");
    $AS_U_L_list_title = get_option('AS_U_L_list_title');
    if (count($favorites) > 0) {
        $output = '<div class="AS_User_List_list">';
        if ($AS_U_L_list_title) {
            $output .= '<h3>' . esc_html($AS_U_L_list_title) . '</h3>';
        }
        $output .= '<table class="AS_U_L_table">';
        foreach ($favorites as $favorite) {
            $post = get_post($favorite->post_id);
            $product = wc_get_product($post->ID);
            if ('product' === $post->post_type) {
                $stock_status = $product->get_stock_status();
                $stock_status = str_replace('instock', 'in stock', $stock_status);
                $stock_status = str_replace('outofstock', 'out of stock', $stock_status);
                $stock_status = str_replace('onbackorder', 'on backorder', $stock_status);
                $output .= '<tr>';
                $output .= '<td class="AS_U_L_mobile">';
                $output .= '<a class="AS_U_L_mobile_img" href="' . esc_url(get_permalink($post->ID)) . '">';
                $output .= get_the_post_thumbnail($post->ID, 'thumbnail');
                $output .= '<br>' . esc_html($post->post_title) . '</a></td>';
                $output .= '<td class="AS_U_L_mobile_hide">';
                $output .= '<a href="' . esc_url(get_permalink($post->ID)) . '">';
                $output .= get_the_post_thumbnail($post->ID, 'thumbnail');
                $output .= '</a></td>';
                $output .= '<td class="AS_U_L_mobile_hide">';
                $output .= '<a href="' . esc_url(get_permalink($post->ID)) . '">';
                $output .= esc_html($post->post_title) . '</a></td>';
                $output .= '<td class="AS_U_L_mobile_hide">' . wc_price($product->get_price()) . '</td>';
                $output .= '<td class="AS_U_L_mobile_hide">' . esc_html($stock_status) . '</td>';
                $output .= '<td class="AS_U_L_mobile_hide">';
                $output .= '<a href="' . esc_url(get_permalink($post->ID)) . '" class="AS_U_L_view_product_button">View Product</a></td>';
                $output .= '<td class="AS_U_L_mobile_hide">';
                $output .= '<div data-post-id="' . $post->ID . '" class="remove-favorite AS_U_L_button AS_U_L_cross"><i class="icon icon-close-outline" alt="Remove from list"></i></div></td>';
                $output .= '</tr>';
            }
        }
        $output .= '</table>';
        $output .= '</div>';
        return $output;
    } else {
        return '<div class="AS_User_List_list"><h3>' . $AS_U_L_list_title . '</h3><p>You have not added any items to your favorites list.</p></div>';
    }
}

//new AS_U_Lorites_Shortcode();
