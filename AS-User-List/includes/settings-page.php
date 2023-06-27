<?php
function AS_User_List_admin_scripts()
{
    wp_enqueue_style('favorites-list-style', plugin_dir_url(__FILE__) . 'css/AS_U_L.css');
}
add_action('admin_enqueue_scripts', 'AS_User_List_admin_scripts');


$admin_colors;
add_action('admin_head', function () {
    global $_wp_admin_css_colors;
    $admin_colors = $_wp_admin_css_colors;
});

function get_favorited_items()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "favorites";
    $query = "SELECT post_id, COUNT(*) AS num_saves FROM $table_name GROUP BY post_id";
    $results = $wpdb->get_results($query);
    return $results;
}


//Create settings page
function AS_User_List_settings_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $default_tab = null;
    $tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;

    $AS_U_L_list_title = get_option('AS_U_L_list_title');
    $icon_add_text = get_option('AS_User_List_icon_add_text');
    $icon_remove_text = get_option('AS_User_List_icon_remove_text');
    $AS_U_L_list_url = get_option('AS_U_L_list_url');
?>

    <style>
        .ui-state-active,
        .ui-widget-content .ui-state-active,
        .ui-widget-header .ui-state-active,
        a.ui-button:active,
        .ui-button:active,
        .ui-button.ui-state-active:hover {

            border: #000000;
            background: #414141;
            -webkit-appearance: none;
        }

        .form-table td fieldset label,
        .form-table td fieldset li,
        .form-table td fieldset p {
            display: flex;
            align-items: center;
        }

        .num {
            width: 150px;
        }

        .button-search {
            margin-left: 5px !important;
        }

        .tablenav-pages {
            float: right;
            padding: 5px;
        }

        .AS_U_L_banner {
            background-color: #CDFF15;
            padding: 15px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0px 0px 20px 0px #0000004f;
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            align-items: center;
            justify-content: space-between;

        }

        .AS_U_L_banner_foot {
            margin-top: 30px;
            display: inline-block;
        }

        .AS_U_L_header {
            font-weight: bold !important;
            color: #F0488A !important;
            font-size: 2em !important;
        }

        .AS_U_L_text {
            color: #88B400;
            margin: 0px;
        }

        .AS_U_L_upgrade {
            align-items: baseline;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .AS_U_L_button {
            color: white !important;
            border-color: #F0488A !important;
            background: #F0488A !important;
        }

        .AS_U_L_button:hover {
            color: white !important;
            border-color: #F0488A !important;
            background: #b43567 !important;
        }

        .hidden {
            display: none;
        }

        @media screen and (max-width: 1200px) {
            .AS_U_L_upgrade {
                align-items: baseline;
                display: block;
            }

            .AS_U_L_banner {
                gap: 5px;
            }
        }

        .AS_U_L_button_warn {
            color: white !important;
            border-color: red !important;
            background: red !important;
        }

        .AS_U_L_button_warn:hover {
            color: white !important;
            border-color: red !important;
            background-color: #7c0000 !important;
        }
    </style>

    <div class="wrap fs-section">
        <h1 class="hidden"><?php echo esc_html(get_admin_page_title()); ?></h1>
        <div class="AS_U_L_banner">
            <div style="display: flex;gap: 20px;align-items: center;">
                <img alt="" src="<?php echo plugins_url('icon.png', __FILE__); ?>" height="80" width="80">
                <div>
                    <h1 class="AS_U_L_header"><?php echo esc_html(get_admin_page_title()); ?></h1>
                    <p class="AS_U_L_text">
                        A flexible favourite & Wishlist plugin
                        that allows users to add any page,
                        product or post to their list.
                    </p>
                </div>
            </div>
            <?php
            if (aul_fs()->is_not_paying()) {
                echo '<div class="AS_U_L_upgrade"><h2 class=".AS_U_L_header">' . __('Upgrade to Premium', 'as-user-list') . '</h2>';
                echo '<p>See what producs your customers have their eye on.</p>';
                echo '<a class="button AS_U_L_button" href="' . aul_fs()->get_upgrade_url() . '">' .
                    __('Upgrade Now!', 'as-user-list') .
                    '</a>';
                echo '</div>';
            } ?>
        </div>


        <h2 class="nav-tab-wrapper">
            <a href="?page=AS-U-L-general" class="nav-tab fs-tab home as-user <?php if ($tab === null) : ?>nav-tab-active<?php endif; ?>">General</a>
            <a href="?page=AS-U-L-general&tab=statistics" class="nav-tab fs-tab home as-user <?php if ($tab === 'statistics') : ?>nav-tab-active<?php endif; ?>">Statistics</a>
        </h2>
        <div class="tab-content">
            <?php switch ($tab):
                case 'statistics':
            ?>
                    <div id="statistics">

                        <h2><?php esc_html_e('Statistics', 'AS-U-L-general'); ?></h2>
                        <?php
                        if (aul_fs()->is_not_paying()) {
                            echo '<section><h1>' . __('Upgrade to see Statistics', 'as-user-list') . '</h1>';
                            echo '<p>To see what posts or products your customers are adding to their lits please upgrade.</p>';
                            echo '<a class="button AS_U_L_button" href="' . aul_fs()->get_upgrade_url() . '">' .
                                __('Upgrade Now!', 'as-user-list') .
                                '</a>';
                            echo '
                            </section>';
                        }
                        if (aul_fs()->is__premium_only()) {
                            if (aul_fs()->can_use_premium_code()) {
                                global $wpdb;
                                $table_name = $wpdb->prefix . "favorites";

                                // Get the search query from the user
                                $search_query = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
                                $user_id = isset($_GET['user']) ? intval($_GET['user']) : 0;

                                // Determine the order and sorting options
                                $order_by = isset($_GET['orderby']) ? $_GET['orderby'] : 'num_saves';
                                $order = isset($_GET['order']) ? $_GET['order'] : 'desc';

                                // Build the URL for the search form
                                $search_url = esc_url(add_query_arg(array('page' => 'AS-U-L-general', 'tab' => 'statistics'), admin_url('options-general.php')));

                                // Add the search query to the URL if it exists
                                if (!empty($search_query)) {
                                    $search_url = esc_url(add_query_arg('search', $search_query, $search_url));
                                }

                                // Build the SQL query with search and sorting options
                                $query = "SELECT p.ID AS post_id, COUNT(*) AS num_saves
                                FROM $wpdb->posts AS p
                                JOIN $table_name AS f ON p.ID = f.post_id
                                JOIN $wpdb->users AS u ON f.user_id = u.ID";

                                // Apply search filter if a search query is provided
                                if (!empty($search_query)) {
                                    $query .= " WHERE u.display_name LIKE '%$search_query%' OR p.post_title LIKE '%$search_query%'";
                                }

                                // Apply user filter if a user ID is provided
                                if ($user_id > 0) {
                                    $query .= $wpdb->prepare(" AND f.user_id = %d", $user_id);
                                }

                                $query .= " GROUP BY post_id
                                ORDER BY $order_by $order";

                                $results = $wpdb->get_results($query);

                                // Output the search form and results table
                                echo '<div class="wrap">';
                                echo '<form method="get" action="' . $search_url . '">';
                                echo '<input type="hidden" name="page" value="AS-U-L-general">';
                                echo '<input type="hidden" name="tab" value="statistics">';
                                echo '<input type="search" id="favorited-items-search" name="search" class="wp-filter-search" placeholder="Search items & users" value="' . esc_attr($search_query) . '">';
                                if (!empty($query)) {
                                    echo '<input type="submit" class="button button-search" value="Search">';
                                    echo '<a href="' . esc_url(add_query_arg(array('page' => 'AS-U-L-general', 'tab' => 'statistics'), admin_url('options-general.php'))) . '" class="button button-search">Reset</a>';
                                } else {
                                    echo '<input type="submit" class="button button-search" value="Search">';
                                }
                                echo '<div class="tablenav-pages"><span class="displaying-num">' . count($results) . ' items</span></div>';
                                echo '</form></br>';
                                echo '<table class="wp-list-table widefat striped">';
                                echo '<thead>';
                                echo '<tr>';
                                echo '<th class="manage-column num sortable ' . ($order_by === 'num_saves' ? strtolower($order) : 'sortable') . '" scope="col"><a href="' . esc_url(add_query_arg(array('page' => 'AS-U-L-general', 'tab' => 'statistics', 'orderby' => 'num_saves', 'order' => ($order_by === 'num_saves' && $order === 'asc') ? 'desc' : 'asc'), admin_url('options-general.php'))) . '"><span>Total Saves</span><span class="sorting-indicator ' . ($order_by === 'num_saves' ? strtolower($order) : 'none') . '"></span></a></th>';
                                echo '<th class="manage-column column-primary sortable ' . ($order_by === 'post_title' ? strtolower($order) : 'sortable') . '" scope="col"><a href="' . esc_url(add_query_arg(array('page' => 'AS-U-L-general', 'tab' => 'statistics', 'orderby' => 'post_title', 'order' => ($order_by === 'post_title' && $order === 'asc') ? 'desc' : 'asc'), admin_url('options-general.php'))) . '"><span>Product/ Page</span><span class="sorting-indicator ' . ($order_by === 'post_title' ? strtolower($order) : 'none') . '"></span></a></th>';
                                global $wpdb;
                                $user_count = $wpdb->get_var("SELECT COUNT(DISTINCT user_id) FROM $table_name");
                                echo '<th class="manage-column" scope="col">Users (' . $user_count . ')</th>';

                                echo '<th class="manage-column" scope="col">Current Price</th>';
                                echo '<th class="manage-column" scope="col">Categories</th>';
                                echo '</tr>';
                                echo '</thead>';
                                echo '<tbody>';

                                if ($results) {
                                    foreach ($results as $result) {
                                        $post_id = $result->post_id;
                                        $num_saves = $result->num_saves;
                                        $post_title = get_the_title($post_id);
                                        $post_link = get_permalink($post_id);

                                        echo '<tr>';
                                        echo '<td class="num" >' . $num_saves . '</td>';
                                        echo '<td><a href="' . $post_link . '">' . $post_title . '</a>';
                                        echo '<div class="row-actions">';
                                        echo '<span class="edit"><a href="' . get_edit_post_link($post_id) . '">Edit</a> | </span>';
                                        echo '<span class="view"><a href="' . $post_link . '">View</a></span>';
                                        echo '</div></td>';
                                        echo '<td>';

                                        // Get the users who have the post in their favorites list
                                        $post_users = $wpdb->get_col($wpdb->prepare("SELECT f.user_id FROM $table_name AS f JOIN $wpdb->users AS u ON f.user_id = u.ID WHERE f.post_id = %d", $post_id));

                                        if (!empty($post_users)) {
                                            $user_links = array();
                                            foreach ($post_users as $user_id) {
                                                $user = get_user_by('ID', $user_id);
                                                $user_links[] = '<a href="' . esc_url(add_query_arg(array('page' => 'AS-U-L-general', 'tab' => 'statistics', 'user' => $user_id), admin_url('options-general.php'))) . '">' . $user->display_name . '</a>';
                                            }
                                            echo implode(', ', $user_links);
                                        } else {
                                            echo '-';
                                        }

                                        echo '</td>';
                                        echo '<td>';

                                        // Check if the post is a product
                                        if (function_exists('wc_get_product') && $product = wc_get_product($post_id)) {
                                            echo $product->get_price() ? wc_price($product->get_price()) : '-';
                                        } else {
                                            echo '-';
                                        }

                                        echo '</td>';
                                        echo '<td>';

                                        // Get the categories for the post
                                        $post_categories = wp_get_post_categories($post_id);

                                        // Check if the post is a product
                                        if (function_exists('wc_get_product') && $product = wc_get_product($post_id)) {
                                            $product_categories = wp_get_post_terms($post_id, 'product_cat');
                                            $category_names = array();
                                            foreach ($product_categories as $product_category) {
                                                $category_names[] = $product_category->name;
                                            }
                                            echo implode(', ', $category_names);
                                        } else {
                                            // Display the categories
                                            $category_names = array();
                                            foreach ($post_categories as $category_id) {
                                                $category = get_category($category_id);
                                                $category_names[] = $category->name;
                                            }
                                            echo implode(', ', $category_names);
                                        }

                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr>';
                                    echo '<td colspan="4">No favorited items found.</td>';
                                    echo '</tr>';
                                }

                                echo '</tbody>';
                                echo '<tfoot>';
                                echo '<tr>';
                                echo '<th class="manage-column num sortable ' . ($order_by === 'num_saves' ? strtolower($order) : 'sortable') . '" scope="col"><a href="' . esc_url(add_query_arg(array('page' => 'AS-U-L-general', 'tab' => 'statistics', 'orderby' => 'num_saves', 'order' => ($order_by === 'num_saves' && $order === 'asc') ? 'desc' : 'asc'), admin_url('options-general.php'))) . '"><span>Total Saves</span><span class="sorting-indicator ' . ($order_by === 'num_saves' ? strtolower($order) : 'none') . '"></span></a></th>';
                                echo '<th class="manage-column column-primary sortable ' . ($order_by === 'post_title' ? strtolower($order) : 'sortable') . '" scope="col"><a href="' . esc_url(add_query_arg(array('page' => 'AS-U-L-general', 'tab' => 'statistics', 'orderby' => 'post_title', 'order' => ($order_by === 'post_title' && $order === 'asc') ? 'desc' : 'asc'), admin_url('options-general.php'))) . '"><span>Product/ Page</span><span class="sorting-indicator ' . ($order_by === 'post_title' ? strtolower($order) : 'none') . '"></span></a></th>';
                                echo '<th class="manage-column" scope="col">Users (' . $user_count . ')</th>';
                                echo '<th class="manage-column" scope="col">Current Price</th>';
                                echo '<th class="manage-column" scope="col">Categories</th>';
                                echo '</tr>';
                                echo '</tfoot>';
                                echo '</table>';

                                // Pagination
                                echo '<div class="tablenav-pages"><span class="displaying-num">' . count($results) . ' items</span></div>';
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                <?php

                    break;
                case 'notifications':
                ?>
                    <div id="notifications">
                        <h2><?php esc_html_e('Notifocations', 'AS-U-L-general'); ?></h2>
                        <p>New feature arriving in the next majour release.</br>Send your customers emails about items in their list going on sale!</p>
                    </div>
                <?php
                    break;
                default:
                ?>
                    <div id="general">
                        <h2><?php esc_html_e('Aparence', 'AS-U-L-general'); ?></h2>
                        <form method="post" action="options.php">
                            <?php
                            settings_fields('AS-U-L-general');
                            do_settings_sections('AS-U-L-general');
                            ?>
                            <table class="form-table">
                                <tbody>
                                    <tr>
                                        <th scope="row">
                                            <label for="admin_notifications"><?php esc_html_e('Choose icon style:', 'AS-U-L-general'); ?></label>
                                        </th>
                                        <td>
                                            <fieldset>
                                                <label for="AS_User_List_icons">
                                                    <input type="radio" name="AS_User_List_icons_style" value="default" <?php checked('default', get_option('AS_User_List_icons_style'), true); ?>><?php esc_html_e('Default', 'AS-U-L-general'); ?>
                                                </label>
                                                <p class="description" id="AS_User_List_icons_style-description">
                                                    <?php esc_html_e('Buttons that match the current theme with chosen or default wording.'); ?>
                                                </p><br>
                                                <label for="AS_User_List_icons">
                                                    <input type="radio" name="AS_User_List_icons_style" value="bookmark" <?php checked('bookmark', get_option('AS_User_List_icons_style'), true); ?>><?php esc_html_e('Bookmark icons', 'AS-U-L-general'); ?><span class="icon icon-bookmark"></span><span class="icon icon-bookmark-outline"></span>
                                                </label><br>
                                                <label for="AS_User_List_icons">
                                                    <input type="radio" name="AS_User_List_icons_style" value="heart" <?php checked('heart', get_option('AS_User_List_icons_style'), true); ?>><?php esc_html_e('Heart icons', 'AS-U-L-general'); ?><span class="icon icon-heart"></span><span class="icon icon-heart-outline"></span>
                                                </label>
                                            </fieldset>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="AS_User_List_icon_add_text"><?php esc_html_e('Add to favourites Title', 'AS-U-L-general'); ?></label>
                                        </th>
                                        <td>
                                            <input name="AS_User_List_icon_add_text" type="text" id="AS_User_List_icon_add_text" class="AS_User_List_icon_add_text" value="<?php echo esc_attr($icon_add_text); ?>" class="regular-text">
                                            <p class="description" id="AS_User_List_icon_add_text-description">
                                                <?php esc_html_e('Enter the text you want displayed with the Add to favourites button. (enter NA to have it blank)', 'AS-U-L-general-settings'); ?>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="AS_User_List_icon_remove_text"><?php esc_html_e('Remove favourite Title', 'AS-U-L-general'); ?></label>
                                        </th>
                                        <td>
                                            <input name="AS_User_List_icon_remove_text" type="text" id="AS_User_List_icon_remove_text" class="AS_User_List_icon_remove_text" value="<?php echo esc_attr($icon_remove_text); ?>" class="regular-text">
                                            <p class="description" id="AS_User_List_icon_remove_text-description">
                                                <?php esc_html_e('Enter the text you want displayed with the Remove favourite button. (enter NA to have it blank)', 'AS-U-L-general-settings'); ?>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="AS_U_L_list_title"><?php esc_html_e('List Title', 'AS-U-L-general'); ?></label>
                                        </th>
                                        <td>
                                            <input name="AS_U_L_list_title" type="text" id="AS_U_L_list_title" class="AS_U_L_list_title" value="<?php echo esc_attr($AS_U_L_list_title); ?>" class="regular-text">
                                            <p class="description" id="AS_U_L_list_title-description">
                                                <?php esc_html_e('Enter the name you want your list to be called (Wishlist/ Favourites/ Saved posts).', 'AS-U-L-general-settings'); ?>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="AS_U_L_list_title_show"><?php esc_html_e('Display List Title', 'AS-U-L-general'); ?></label>
                                        </th>
                                        <td>
                                            <input type="checkbox" name="AS_U_L_list_title_show" id="AS_U_L_list_title_show" value="1" <?php checked(1, get_option('AS_U_L_list_title_show'), true); ?>><?php esc_html_e('Show the prefered title above the list.'); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="AS_U_L_list_url"><?php esc_html_e('Link to Page:', 'AS-U-L-general'); ?></label>
                                        </th>
                                        <td>
                                            <select name="AS_U_L_list_url" id="AS_U_L_list_url">
                                                <option value="">-- Select a Page --</option>
                                                <?php
                                                $pages = get_pages();
                                                foreach ($pages as $page) {
                                                    $selected = '';
                                                    if (get_option('AS_U_L_list_url') == get_page_link($page->ID)) {
                                                        $selected = 'selected';
                                                    }
                                                    echo '<option value="' . get_page_link($page->ID) . '" ' . $selected . '>' . $page->post_title . '</option>';
                                                }
                                                ?>
                                            </select>
                                            <p class="description" id="AS_U_L_list_url-description">
                                                <?php esc_html_e('Select the page you are displaying your favourites/wishlist on using the shortcode [as_display_list].') ?><br><?php esc_html_e('This option is overwritten to "My Account" if WooCommerce is installed.') . $AS_U_L_list_url; ?>


                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="AS_U_L_show_woo"><?php esc_html_e('Show Woocommerce buttons:', 'AS-U-L-general'); ?></label>
                                        </th>
                                        <td>
                                            <fieldset>
                                                <label for="AS_U_L_show_woo">
                                                    <input type="checkbox" name="AS_U_L_show_woo" id="AS_U_L_show_woo" value="1" <?php checked(1, get_option('AS_U_L_show_woo'), true); ?>><?php esc_html_e('Show the add to button on product pages.'); ?>
                                                </label>
                                                <label for="AS_U_L_show_woo_list">
                                                    <input type="checkbox" name="AS_U_L_show_woo_list" id="AS_U_L_show_woo_list" value="1" <?php checked(1, get_option('AS_U_L_show_woo_list'), true); ?>><?php esc_html_e('Show the add to button on shop list pages.'); ?>
                                                </label>
                                            </fieldset>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php submit_button();
                            register_setting('AS-U-L-general', 'AS-U-L-general', 'AS-U-L-general', 'AS-U-L-general', 'AS-U-L-general', 'AS-U-L-general', 'AS-U-L-general', 'AS-U-L-general', 'AS-U-L-general', 'AS-U-L-general', 'AS-U-L-general', 'AS-U-L-general', 'AS-U-L-general', 'AS-U-L-general', 'AS-U-L-general');
                            add_settings_section('AS-U-L-general-section', 'AS User List Settings', '', 'AS-U-L-general');
                            ?>
                        </form>



                        <?php
                        function delete_all_data()
                        {
                            global $wpdb;
                            $table_name = $wpdb->prefix . 'favorites';
                            $wpdb->query("DROP TABLE IF EXISTS $table_name");
                            delete_option('AS_User_List_icons_style');
                            delete_option('AS_U_L_list_title');
                            delete_option('AS_U_L_list_title_show');
                            delete_option('AS_User_List_icon_add_text');
                            delete_option('AS_User_List_icon_remove_text');
                            delete_option('AS_U_L_list_url');
                            delete_option('AS_U_L_show_woo');
                            delete_option('AS_U_L_show_woo_list');

                            // Add any additional cleanup code here

                            return 'All data has been deleted!';
                            return 'Please refresh the page.';
                        }

                        if (isset($_POST['delete_data_button'])) {
                            if (isset($_POST['confirmed']) && $_POST['confirmed'] === 'true') {
                                $status = delete_all_data();
                                echo $status;
                                echo '<br>Please refresh the page.';
                            } else {
                                echo 'This will <b>REMOVE ALL ITEMS</b> added to lists and reset <b>ALL SETTINGS</b>.<br>';
                                echo 'This cannot be undone.';
                                echo '<form method="POST" action=""><br>
                <input type="hidden" name="confirmed" value="true">
                <input type="checkbox" name="checkbox_confirm" id="checkbox_confirm" required>
                <label for="checkbox_confirm">I confirm that I want to <b>DELETE</b> all user lists and settings.</label><br><br>
                <input class="button AS_U_L_button_warn" type="submit" name="delete_data_button" value="RESET NOW">
              </form>';
                            }
                        } else {
                            echo 'This will remove all items added to lists and reset all settings.';
                            echo '<form method="POST" action="">
            <input type="hidden" name="confirmed" value="false">
            <input class="button" type="submit" name="delete_data_button" value="Reset Settings">
          </form>';
                        }
                        ?>
                    </div>
            <?php
                    break;
            endswitch; ?>
        </div>
        <?php
        if (aul_fs()->is_not_paying()) {
            echo '<div class="AS_U_L_banner AS_U_L_banner_foot"><section><h2 class=".AS_U_L_header">' . __('Upgrade to Premium', 'as-user-list') . '</h2>';
            echo '<p>Upgrade to premium to see what producs your customers have their eye on.</p>';
            echo '<ol style="list-style-type: disc;">';
            echo '<li>Stock Status: View the stock status of products in the list</li>';
            echo '<li>Direct Cart Addition: Add products to the cart directly from the list</li>';
            echo '<li>Comprehensive Overview: View a comprehensive overview of the pages and products in your customers\' lists</li>';
            echo '<li>Widget: The top 5 pages or products in your customers\' lists displayed on the dashbord with easy quick actions</li>';
            echo '<li>And more features in the works</li>';
            echo '</ol>';
            echo '<a class="button AS_U_L_button" href="' . aul_fs()->get_upgrade_url() . '">' .
                __('Upgrade Now!', 'as-user-list') .
                '</a>';
            echo '
    </section></div>';
        }
        ?>
    </div>
<?php
}
