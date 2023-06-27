<?php
add_shortcode('as_display_list', 'favorites_list_shortcode');

// Shortcode to display the favorites list
/**
 * Render the favorites list shortcode.
 */
function favorites_list_shortcode()
{
    wp_enqueue_style('favorites-list-style', plugin_dir_url(__FILE__) . 'css/AS_U_L.css');

    global $wpdb, $post;
    $current_user = wp_get_current_user();
    $table_name = $wpdb->prefix . "favorites";
    $favorites = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id = $current_user->ID ORDER BY date_added DESC");
    $AS_U_L_list_title = get_option('AS_U_L_list_title');
    $AS_U_L_list_title_show = get_option('AS_U_L_list_title_show');

    if ($AS_U_L_list_title_show === '1') {
        $AS_U_L_list_title_show = '<h3>' . $AS_U_L_list_title . '</h3>';
    }

    if (count($favorites) > 0) {
        // Start building the output
        $output = '<div class="AS_User_List_list">' . $AS_U_L_list_title_show;
        $output .= '<table class="AS_U_L_table">';

        foreach ($favorites as $favorite) {
            $post = get_post($favorite->post_id);
            if (function_exists('wc_get_product') && 'product' === $post->post_type) {
                $product = wc_get_product($post->ID);
                if (aul_fs()->is__premium_only()) {
                    if (aul_fs()->can_use_premium_code()) {
                        $stock_status = $product->get_stock_status();

                        if ($stock_status === 'instock') {
                            $stock_status = 'in stock';
                        } elseif ($stock_status === 'outofstock') {
                            $stock_status = 'out of stock';
                        } elseif ($stock_status === 'onbackorder') {
                            $stock_status = 'on backorder';
                        }
                    }
                }
                // Add row for product
                $output .= '<tr>';
                $output .= '<td class="AS_U_L_mobile"><a class="AS_U_L_mobile_img" href="' . get_permalink($post->ID) . '">' . get_the_post_thumbnail($post->ID, 'thumbnail') . '<br>' . $post->post_title . '</a></td>';
                $output .= '<td class="AS_U_L_mobile_hide"><a href="' . get_permalink($post->ID) . '">' . get_the_post_thumbnail($post->ID, 'thumbnail') . '</a></td>';
                $output .= '<td class="AS_U_L_mobile_hide"><a href="' . get_permalink($post->ID) . '">' . $post->post_title . '</a></td>';
                $output .= '<td class="AS_U_L_mobile_hide">' . wc_price($product->get_price()) . '</td>';
                if (aul_fs()->is__premium_only()) {
                    if (aul_fs()->can_use_premium_code()) {
                        $output .= '<td class="AS_U_L_mobile_hide">' . $stock_status . '</td>';
                    }
                }
                $output .= '<td class="AS_U_L_mobile">' . wc_price($product->get_price());
                if (aul_fs()->is__premium_only()) {
                    if (aul_fs()->can_use_premium_code()) {
                        $output .= '<br>' . $stock_status;
                    }
                }
                $output .= '</td>';

                if (aul_fs()->is__premium_only()) {
                    if (aul_fs()->can_use_premium_code()) {
                        $output .= '<td class="AS_U_L_table_buttons"><form class="form_cart" action="' . esc_url(wc_get_cart_url()) . '" method="post" enctype="multipart/form-data">';
                        $output .= '<input type="hidden" name="add-to-cart" value="' . absint($post->ID) . '">';
                        $output .= '<span class="single_add_to_cart_button alt AS_U_L_cart AS_U_L_button" onclick="submitForm(this)"><span class="icon icon-shopping-cart" alt="Add to cart"></span></span>';
                        $output .= '</form></td>';
                        $output .= '<script>
                            function submitForm(element) {
                              element.closest(\'form\').submit();
                            }
                        </script>';
                    }
                }

                // Add remove favorite button
                $output .= '<td class="AS_U_L_table_buttons"><div data-post-id="' . $post->ID . '" class="remove-favorite AS_U_L_button AS_U_L_cross"><span class="icon icon-close-outline" alt="Remove from list"></span></div></td>';
                $output .= '</tr>';
            } else {
                // Add row for non-product post
                $output .= '<tr>';
                $output .= '<td class="AS_U_L_mobile"><a class="AS_U_L_mobile_img" href="' . get_permalink($post->ID) . '">' . get_the_post_thumbnail($post->ID, 'thumbnail') . '<br>' . $post->post_title . '</a></td>';
                $output .= '<td class="AS_U_L_mobile_hide"><a class="AS_U_L_mobile_img" href="' . get_permalink($post->ID) . '">' . get_the_post_thumbnail($post->ID, 'thumbnail') . '</a></td>';
                $output .= '<td class="AS_U_L_mobile_hide"><a href="' . get_permalink($post->ID) . '">' . $post->post_title . '</a></td>';

                if (aul_fs()->is__premium_only()) {
                    if (aul_fs()->can_use_premium_code()) {
                        // Add placeholder cell for premium users
                        $output .= '<td></td>';
                        $output .= '<td class="AS_U_L_mobile_hide"></td>';
                    }
                }

                // Add placeholder cells
                $output .= '<td></td>';


                // Add remove favorite button
                $output .= '<td class="AS_U_L_table_buttons"><div data-post-id="' . $post->ID . '" class="remove-favorite AS_U_L_button AS_U_L_cross"><i class="icon icon-close-outline" alt="Remove from list"></i></div></td>';
                $output .= '</tr>';
            }
        }

        $output .= '</table></div>';

        // Return the output
        return $output;
    } else {
        // Display message when no favorites found
        return '<div class="AS_User_List_list"><h3>' . $AS_U_L_list_title . '</h3><p>You have not added any items to your ' . $AS_U_L_list_title . '.</p></div>';
    }
}
