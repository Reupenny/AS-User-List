<?php
add_shortcode('favorites_list', 'favorites_list_shortcode');

// Shortcode to display the favorites list
function favorites_list_shortcode()
{
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    global $wpdb, $post;
    $current_user = wp_get_current_user();
    $table_name = $wpdb->prefix . "favorites";
    $favorites = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id = $current_user->ID ORDER BY date_added DESC");
    $Better_fav_list_title = get_option('Better_fav_list_title');
    $Better_fav_list_title_show = get_option('Better_fav_list_title_show');
    if ($Better_fav_list_title_show === '1') {
        $Better_fav_list_title_show = '<h3>' . $Better_fav_list_title . '</h3>';
    }
    if (count($favorites) > 0) {
        $output = '<div class="better_favourites_list">' . $Better_fav_list_title_show . '';
        $output .= '<table class="bettter_fav_table">';
        foreach ($favorites as $favorite) {
            $post = get_post($favorite->post_id);
            if ('product' === $post->post_type) {
                $product = wc_get_product($post->ID);
                $stock_status = $product->get_stock_status();
                if ($stock_status === 'instock') {
                    $stock_status = 'in stock';
                } elseif ($stock_status === 'outofstock') {
                    $stock_status = 'out of stock';
                } elseif ($stock_status === 'onbackorder') {
                    $stock_status = 'on backorder';
                }
                $output .= '<tr>';
                $output .= '<td class="Better_fav_mobile"><a class="Better_fav_mobile_img" href="' . get_permalink($post->ID) . '">' . get_the_post_thumbnail($post->ID, 'thumbnail') . '<br>' . $post->post_title . '</a></td>';
                $output .= '<td class="Better_fav_mobile_hide"><a href="' . get_permalink($post->ID) . '">' . get_the_post_thumbnail($post->ID, 'thumbnail') . '</a></td>';
                $output .= '<td class="Better_fav_mobile_hide"><a href="' . get_permalink($post->ID) . '">' . $post->post_title . '</a></td>';
                $output .= '<td class="Better_fav_mobile_hide">' . wc_price($product->get_price()) . '</td>';
                $output .= '<td class="Better_fav_mobile_hide"><p>' . $stock_status . '</p></td>';
                $output .= '<td class="Better_fav_mobile">' . wc_price($product->get_price()) . '<br>' . $stock_status . '</td>';
                $output .= '<td class="Better_fav_table_buttons" ><form action="' . esc_url(wc_get_cart_url()) . '" method="post" enctype="multipart/form-data">';
                $output .= '<input type="hidden" name="add-to-cart" value="' . absint($post->ID) . '">';
                $output .= '<button type="submit" class="single_add_to_cart_button alt bettter_fav_cart bettter_fav_button"><span class="icon icon-shopping-cart" alt="Add to cart"></span></button>';
                $output .= '</form></td>';
                $output .= '<td class="Better_fav_table_buttons"><div data-post-id="' . $post->ID . '" class="remove-favorite bettter_fav_button bettter_fav_cross"><span class="icon icon-close-outline" alt="Remove from list"></span></div></td>';
                $output .= '</tr>';
            } else {
                $output .= '<tr>';
                $output .= '<td class="Better_fav_mobile"><a class="Better_fav_mobile_img" href="' . get_permalink($post->ID) . '">' . get_the_post_thumbnail($post->ID, 'thumbnail') . '<br>' . $post->post_title . '</a></td>';
                $output .= '<td class="Better_fav_mobile_hide"><a class="Better_fav_mobile_img" href="' . get_permalink($post->ID) . '">' . get_the_post_thumbnail($post->ID, 'thumbnail') . '</a></td>';
                $output .= '<td class="Better_fav_mobile_hide"><a href="' . get_permalink($post->ID) . '">' . $post->post_title . '</a></td>';
                $output .= '<td></td>';
                $output .= '<td></td>';
                $output .= '<td class="Better_fav_mobile_hide"></td>';
                $output .= '<td class="Better_fav_table_buttons" ><div data-post-id="' . $post->ID . '" class="remove-favorite bettter_fav_button bettter_fav_cross"><i class="icon icon-close-outline" alt="Remove from list"></i></div></td>';
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
        $output .= '</table></div>';
        return $output;
    } else {
        return '<div class="better_favourites_list"><h3>' . $Better_fav_list_title . '</h3><p>You have not added any items to your favorites list.</p></div>';
    }
}
