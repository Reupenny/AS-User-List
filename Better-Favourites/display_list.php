<?php

add_shortcode('favorites_list', 'favorites_list_shortcode');

add_action('wp_enqueue_scripts', 'favorites_list_enqueue_styles');
//Style Sheet
function favorites_list_enqueue_styles()
{
    wp_enqueue_style('favorites-list-style', plugin_dir_url(__FILE__) . 'css/better-favourites.css');
}

// Shortcode to display the favorites list
function favorites_list_shortcode()
{
    global $wpdb, $post;
    $current_user = wp_get_current_user();
    $table_name = $wpdb->prefix . "favorites";
    $favorites = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id = $current_user->ID ORDER BY date_added DESC");
    $Better_fav_list_title = get_option('Better_fav_list_title');
    if (count($favorites) > 0) {
        $output = '<h3>' . $Better_fav_list_title . '</h3>';
        $output .= '<table class="bettter_fav_table">';
        //$output .= '<tr>';
        //$output .= '<th></th>';
        //$output .= '<th></th>';
        //$output .= '<th></th>';
        //$output .= '<th></th>';
        //$output .= '<th></th>';
        //$output .= '</tr>';
        foreach ($favorites as $favorite) {
            $post = get_post($favorite->post_id);
            $product = wc_get_product($post->ID);
            if ('product' === $post->post_type) {
                $output .= '<tr>';
                $output .= '<td><a href="' . get_permalink($post->ID) . '">' . get_the_post_thumbnail($post->ID, 'thumbnail') . '</a></td>';
                $output .= '<td><a href="' . get_permalink($post->ID) . '">' . $post->post_title . '</a></td>';
                $output .= '<td>' . wc_price($product->get_price()) . '</td>';
                $output .= '<td><form action="' . esc_url(wc_get_cart_url()) . '" method="post" enctype="multipart/form-data">';
                $output .= '<input type="hidden" name="add-to-cart" value="' . absint($post->ID) . '">';
                $output .= '<button type="submit" class="single_add_to_cart_button alt bettter_fav_cart bettter_fav_button"><span class="dashicons dashicons-cart" alt="Add to cart"></span></button>';
                $output .= '</form></td>';
                $output .= '<td><div data-post-id="' . $post->ID . '" class="remove-favorite bettter_fav_button bettter_fav_cross"><span class="dashicons dashicons-no-alt" alt="Remove from list"></span></div></td>';
                $output .= '</tr>';
            } else {
                $output .= '<tr>';
                $output .= '<td><a href="' . get_permalink($post->ID) . '">' . get_the_post_thumbnail($post->ID, 'thumbnail') . '</a></td>';
                $output .= '<td><a href="' . get_permalink($post->ID) . '">' . $post->post_title . '</a></td>';
                $output .= '<td></td>';
                $output .= '<td></td>';
                $output .= '<td><div data-post-id="' . $post->ID . '" class="remove-favorite bettter_fav_button bettter_fav_cross"><i class="dashicons dashicons-no-alt" alt="Remove from list"></i></div></td>';
                $output .= '</tr>';
            }
        }


        /*    $output .= '<div class="favorites-add-all-to-cart">';
        $output .= '<form action="' . WC()->cart->get_cart_url() . '" method="post" enctype="multipart/form-data">';
        $product_ids = array();
        foreach ($favorites as $favorite) {
            $post = get_post($favorite->post_id);
            $product = wc_get_product($post->ID);
            if ('product' === $post->post_type) {
                $product_ids[] = absint($post->ID);
            }
        }

        foreach ($product_ids as $product_id) {
            $output .= '<input type="hidden" name="add-to-cart" value="' .  $product_id . '">';
        }

        $output .= '<button type="submit" class="single_add_to_cart_button button alt bettter_fav_cart bettter_fav_button">Add all to cart</button>';
        $output .= '</form>';
        $output .= '</div>';
*/


        $output .= '</table>';
        return $output;
    } else {
        return '<h3>' . $Better_fav_list_title . '</h3><br><p>You have not added any items to your favorites list.</p>';
    }
}
