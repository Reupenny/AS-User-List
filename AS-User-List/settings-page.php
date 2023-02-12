<?php

function AS_User_List_admin_scripts()
{
    wp_enqueue_script('jquery-ui-tabs');
    wp_enqueue_style('jquery-ui-css', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
    wp_enqueue_style('favorites-list-style', plugin_dir_url(__FILE__) . 'css/AS_U_L.css');
}
add_action('admin_enqueue_scripts', 'AS_User_List_admin_scripts');


$admin_colors;
add_action('admin_head', function () {
    global $_wp_admin_css_colors;
    $admin_colors = $_wp_admin_css_colors;
});
//Custom CSS front end
function AS_User_List_custom_css()
{
    $custom_css = get_option('AS_U_L_custom_css');
    if (!empty($custom_css)) {
        wp_add_inline_style('favorites-list-style', $custom_css);
    }
}


//Create settings page
function AS_User_List_settings_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }
    $AS_U_L_list_title = get_option('AS_U_L_list_title');
    $icon_add_text = get_option('AS_User_List_icon_add_text');
    $icon_remove_text = get_option('AS_User_List_icon_remove_text');
    $AS_U_L_list_url = get_option('AS_U_L_list_url');
    $AS_U_L_custom_css = get_option('AS_U_L_custom_css');
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

        .coffee {
            width: 100%;
            align-items: center;
            align-content: center;
            text-align: center;
            margin: 20px;
            margin-left: 0;
        }

        .coffee_img {
            width: 100%;
            max-width: 500px;

        }

        .form-table td fieldset label,
        .form-table td fieldset li,
        .form-table td fieldset p {
            display: flex;
            align-items: center;
        }
    </style>
    <div class="wrap">
        <h1><?php esc_html_e('AS User List Settings', 'AS_U_L-option'); ?></h1>
        <div class="tabs-container">
            <ul>
                <li><a href="#general"><?php esc_html_e('General', 'AS_U_L-option'); ?></a></li>
            </ul>
            <div id="general">
                <h2><?php esc_html_e('Aparence', 'AS_U_L-option'); ?></h2>

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
                                        <?php esc_html_e('Select the page you are displaying your favourites/ wishlist on. using the shortcode [favorites_list]') . $AS_U_L_list_url; ?>
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
                            <tr>
                                <th scope="row">
                                    <label for="AS_U_L_custom_css"><?php esc_html_e('Custom CSS:', 'AS-U-L-general'); ?></label>
                                </th>
                                <td>
                                    <fieldset>
                                        <textarea name="AS_U_L_custom_css" rows="5" cols="50" id="AS_U_L_custom_css"><?php echo get_option('AS_U_L_custom_css'); ?></textarea>
                                        <p class="description" id="AS_U_L_custom_css-description">
                                            <?php esc_html_e('Enter your custom CSS here, it will be applied to the front end.'); ?>
                                        </p>
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
            </div>

        </div>
        <div class="coffee"><a href="https://azurestudio.co.nz" target="_blank"><img class="coffee_img" src="https://reupenny.github.io/AS_User_List/public/coffee.png" width=""></a></br>
            <a href="https://github.com/Reupenny/AS_User_List" target="_blank">View GitHub Page</a>
        </div>
    </div>
    <script>
        jQuery(document).ready(function($) {
            $('.tabs-container').tabs();
        });
    </script>
<?php
}
