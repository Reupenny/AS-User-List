<?php

/**
 * Add a new tab to the WooCommerce account page
 */
function add_custom_tab_to_account_page($items)
{
    $new_items = array();
    $AS_U_L_list_title = get_option('AS_U_L_list_title');

    // Add existing menu items first
    foreach ($items as $key => $value) {
        $new_items[$key] = $value;

        // Rename and set the link for the custom tab
        if ($key === 'dashboard') {
            $new_items['list'] = __($AS_U_L_list_title, 'your-textdomain');
        }
    }

    return $new_items;
}
add_filter('woocommerce_account_menu_items', 'add_custom_tab_to_account_page', 20);

/**
 * Display content for the custom tab
 */
function display_custom_tab_content()
{
    /**
     * Render the favorites list shortcode.
     */
    wp_enqueue_style('favorites-list-style', plugin_dir_url(__FILE__) . 'css/AS_U_L.css');

    global $wpdb, $post;
    $current_user = wp_get_current_user();
    $table_name = $wpdb->prefix . "favorites";
    $favorites = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id = $current_user->ID ORDER BY date_added DESC");

    $AS_U_L_list_title_show = get_option('AS_U_L_list_title_show');

    if (count($favorites) > 0) {
?>
        <div class="AS_User_List_list">
            <table class="AS_U_L_table">
                <?php foreach ($favorites as $favorite) {
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
                ?>
                        <tr>
                            <td class="AS_U_L_mobile">
                                <a class="AS_U_L_mobile_img" href="<?php echo get_permalink($post->ID); ?>">
                                    <?php echo get_the_post_thumbnail($post->ID, 'thumbnail') . '<br>' . $post->post_title; ?>
                                </a>
                            </td>
                            <td class="AS_U_L_mobile_hide">
                                <a href="<?php echo get_permalink($post->ID); ?>">
                                    <?php echo get_the_post_thumbnail($post->ID, 'thumbnail'); ?>
                                </a>
                            </td>
                            <td class="AS_U_L_mobile_hide">
                                <a href="<?php echo get_permalink($post->ID); ?>">
                                    <?php echo $post->post_title; ?>
                                </a>
                            </td>
                            <td class="AS_U_L_mobile_hide">
                                <?php echo wc_price($product->get_price()); ?>
                            </td>
                            <?php
                            if (aul_fs()->is__premium_only()) {
                                if (aul_fs()->can_use_premium_code()) {
                            ?>
                                    <td class="AS_U_L_mobile_hide">
                                        <?php echo $stock_status; ?>
                                    </td>
                            <?php   }
                            } ?>
                            <td class="AS_U_L_mobile">
                                <?php echo wc_price($product->get_price());
                                if (aul_fs()->is__premium_only()) {
                                    if (aul_fs()->can_use_premium_code()) {
                                ?>
                                        <br>
                                <?php
                                        echo $stock_status;
                                    }
                                } ?>
                            </td>
                            <?php if (aul_fs()->is__premium_only()) {
                                if (aul_fs()->can_use_premium_code()) {
                                    // Add Add-to-Cart form and button
                            ?>
                                    <td class="AS_U_L_table_buttons">
                                        <form class="form_cart" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post" enctype="multipart/form-data">
                                            <input type="hidden" name="add-to-cart" value="<?php echo absint($post->ID); ?>">
                                            <span class="single_add_to_cart_button alt AS_U_L_cart AS_U_L_button" onclick="submitForm(this)">
                                                <span class="icon icon-shopping-cart" alt="Add to cart"></span>
                                            </span>
                                        </form>
                                    </td>
                                    <script>
                                        function submitForm(element) {
                                            element.closest('form').submit();
                                        }
                                    </script>
                            <?php
                                }
                            } ?>
                            <td class="AS_U_L_table_buttons">
                                <div data-post-id="<?php echo $post->ID; ?>" class="remove-favorite AS_U_L_button AS_U_L_cross">
                                    <span class="icon icon-close-outline" alt="Remove from list"></span>
                                </div>
                            </td>
                        </tr>
                    <?php
                    } else {
                        // Add row for non-product item
                    ?>
                        <tr>
                            <td class="AS_U_L_mobile">
                                <a class="AS_U_L_mobile_img" href="<?php echo get_permalink($post->ID); ?>">
                                    <?php echo get_the_post_thumbnail($post->ID, 'thumbnail') . '<br>' . $post->post_title; ?>
                                </a>
                            </td>
                            <td class="AS_U_L_mobile_hide">
                                <a class="AS_U_L_mobile_img" href="<?php echo get_permalink($post->ID); ?>">
                                    <?php echo get_the_post_thumbnail($post->ID, 'thumbnail'); ?>
                                </a>
                            </td>
                            <td class="AS_U_L_mobile_hide">
                                <a href="<?php echo get_permalink($post->ID); ?>">
                                    <?php echo $post->post_title; ?>
                                </a>
                            </td>
                            <?php if (aul_fs()->is__premium_only()) {
                                if (aul_fs()->can_use_premium_code()) {
                            ?>
                                    <td></td>
                                    <td class="AS_U_L_mobile_hide"></td>
                            <?php
                                }
                            } ?>
                            <td></td>
                            <td class="AS_U_L_table_buttons">
                                <div data-post-id="<?php echo $post->ID; ?>" class="remove-favorite AS_U_L_button AS_U_L_cross">
                                    <i class="icon icon-close-outline" alt="Remove from list"></i>
                                </div>
                            </td>
                        </tr>
                <?php
                    }
                } ?>
            </table>
        </div>
<?php
    } else {
        // No favorites found
        return '<div class="AS_User_List_list"><h3>' . $AS_U_L_list_title . '</h3><p>You have not added any items to your ' . $AS_U_L_list_title . '.</p></div>';
    }
}
add_action('woocommerce_account_list_endpoint', 'display_custom_tab_content');

/**
 * Add a new endpoint to WooCommerce account endpoints
 */
function add_custom_endpoint()
{
    add_rewrite_endpoint('list', EP_ROOT | EP_PAGES);
    flush_rewrite_rules(); // Flush rewrite rules after adding the custom endpoint
}
add_action('init', 'add_custom_endpoint');

/**
 * Load the custom tab template
 */
function load_custom_tab_template($template)
{
    if (get_query_var('lists')) {
        $new_template = locate_template(array('woocommerce/account/custom-tab.php'));
        if (!empty($new_template)) {
            return $new_template;
        }
    }
    return $template;
}
add_filter('woocommerce_account_template', 'load_custom_tab_template');
