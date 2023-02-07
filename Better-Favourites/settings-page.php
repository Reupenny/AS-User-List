<?php

function Better_Favourites_admin_scripts()
{
    wp_enqueue_script('jquery-ui-tabs');
    wp_enqueue_style('jquery-ui-css', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
    wp_enqueue_style('favorites-list-style', plugin_dir_url(__FILE__) . 'css/better-favourites.css');
}
add_action('admin_enqueue_scripts', 'Better_Favourites_admin_scripts');


$admin_colors;
add_action('admin_head', function () {
    global $_wp_admin_css_colors;
    $admin_colors = $_wp_admin_css_colors;
});


//Create settings page
function Better_favourites_settings_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }
    $Better_fav_list_title = get_option('Better_fav_list_title');
    $icon_add_text = get_option('Better_Favourites_icon_add_text');
    $icon_remove_text = get_option('Better_Favourites_icon_remove_text');
    $Better_fav_list_url = get_option('Better_fav_list_url');
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
        <h1><?php esc_html_e('Better Favourites Settings', 'Better-Favourites-option'); ?></h1>
        <div class="tabs-container">
            <ul>
                <li><a href="#general"><?php esc_html_e('General', 'Better-Favourites-option'); ?></a></li>
            </ul>
            <div id="general">
                <h2><?php esc_html_e('Aparence', 'Better-Favourites-option'); ?></h2>

                <form method="post" action="options.php">
                    <?php
                    settings_fields('better-favourites-general');
                    do_settings_sections('better-favourites-general');
                    ?>
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="admin_notifications"><?php esc_html_e('Choose icon style:', 'better-favourites-general'); ?></label>
                                </th>
                                <td>
                                    <fieldset>
                                        <label for="Better_Favourites_icons">
                                            <input type="radio" name="Better_Favourites_icons_style" value="default" <?php checked('default', get_option('Better_Favourites_icons_style'), true); ?>><?php esc_html_e('Default', 'better-favourites-General'); ?>
                                        </label>
                                        <p class="description" id="Better_Favourites_icons_style-description">
                                            <?php esc_html_e('Buttons that match the current theme with chosen or default wording.'); ?>
                                        </p><br>
                                        <label for="Better_Favourites_icons">
                                            <input type="radio" name="Better_Favourites_icons_style" value="bookmark" <?php checked('bookmark', get_option('Better_Favourites_icons_style'), true); ?>><?php esc_html_e('Bookmark icons', 'better-favourites-General'); ?><span class="icon icon-bookmark"></span><span class="icon icon-bookmark-outline"></span>
                                        </label><br>
                                        <label for="Better_Favourites_icons">
                                            <input type="radio" name="Better_Favourites_icons_style" value="heart" <?php checked('heart', get_option('Better_Favourites_icons_style'), true); ?>><?php esc_html_e('Heart icons', 'better-favourites-General'); ?><span class="icon icon-heart"></span><span class="icon icon-heart-outline"></span>
                                        </label>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="Better_Favourites_icon_add_text"><?php esc_html_e('Add to favourites Title', 'Better-Favourites-general'); ?></label>
                                </th>
                                <td>
                                    <input name="Better_Favourites_icon_add_text" type="text" id="Better_Favourites_icon_add_text" class="Better_Favourites_icon_add_text" value="<?php echo esc_attr($icon_add_text); ?>" class="regular-text">
                                    <p class="description" id="Better_Favourites_icon_add_text-description">
                                        <?php esc_html_e('Enter the text you want displayed with the Add to favourites button. (enter NA to have it blank)', 'Better-Favourites-general-settings'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="Better_Favourites_icon_remove_text"><?php esc_html_e('Remove favourite Title', 'Better-Favourites-general'); ?></label>
                                </th>
                                <td>
                                    <input name="Better_Favourites_icon_remove_text" type="text" id="Better_Favourites_icon_remove_text" class="Better_Favourites_icon_remove_text" value="<?php echo esc_attr($icon_remove_text); ?>" class="regular-text">
                                    <p class="description" id="Better_Favourites_icon_remove_text-description">
                                        <?php esc_html_e('Enter the text you want displayed with the Remove favourite button. (enter NA to have it blank)', 'Better-Favourites-general-settings'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="Better_fav_list_title"><?php esc_html_e('List Title', 'Better-Favourites-general'); ?></label>
                                </th>
                                <td>
                                    <input name="Better_fav_list_title" type="text" id="Better_fav_list_title" class="Better_fav_list_title" value="<?php echo esc_attr($Better_fav_list_title); ?>" class="regular-text">
                                    <p class="description" id="Better_fav_list_title-description">
                                        <?php esc_html_e('Enter the name you want your list to be called (Wishlist/ Favourites/ Saved posts).', 'Better-Favourites-general-settings'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="Better_fav_list_url"><?php esc_html_e('Link to Page:', 'better-favourites-General'); ?></label>
                                </th>
                                <td>
                                    <select name="Better_fav_list_url" id="Better_fav_list_url">
                                        <option value="">-- Select a Page --</option>
                                        <?php
                                        $pages = get_pages();
                                        foreach ($pages as $page) {
                                            $selected = '';
                                            if (get_option('Better_fav_list_url') == get_page_link($page->ID)) {
                                                $selected = 'selected';
                                            }
                                            echo '<option value="' . get_page_link($page->ID) . '" ' . $selected . '>' . $page->post_title . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <p class="description" id="Better_fav_list_url-description">
                                        <?php esc_html_e('Select the page you are displaying your favourites/ wishlist on. using the shortcode [favorites_list]') . $Better_fav_list_url; ?>
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?php submit_button();
                    register_setting('better-favourites-general', 'better-favourites-general', 'better-favourites-general', 'better-favourites-general', 'better-favourites-general', 'better-favourites-general', 'better-favourites-general');
                    add_settings_section('better-favourites-general-section', 'Better Favourites Settings', '', 'better-favourites-general');
                    ?>
                </form>
            </div>

        </div>
        <div class="coffee"><a href="https://azurestudio.co.nz" target="_blank"><img class="coffee_img" src="https://reupenny.github.io/Better-Favourites/public/coffee.png" width=""></a></br>
            <a href="https://github.com/Reupenny/Better-Favourites" target="_blank">View GitHub Page</a>
        </div>
    </div>
    <script>
        jQuery(document).ready(function($) {
            $('.tabs-container').tabs();
        });
    </script>
<?php
}
