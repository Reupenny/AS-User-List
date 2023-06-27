<?php
function AS_U_L_dashboard()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'favorites';

    // Retrieve the top 5 products with the most saves
    $query = "SELECT post_id, COUNT(*) AS num_saves FROM $table_name GROUP BY post_id ORDER BY num_saves DESC LIMIT 5";
    $results = $wpdb->get_results($query);
    $AS_U_L_list_title = get_option('AS_U_L_list_title');
    echo '<h2 style="font-weight: bold;">Top ' . $AS_U_L_list_title . '</h2>';
    echo '</br>';

    if ($results) {
        echo '<table style="width: 100%; border-collapse: collapse;">';

        echo '<tr style="text-align: center; font-size: 14px; font-weight: bold;"><td>Total Saves</td><td>Product/ Page</td><td></td></tr>';

        foreach ($results as $result) {
            $post_id = $result->post_id;
            $num_saves = $result->num_saves;
            $post_title = get_the_title($post_id);
            $post_link = get_permalink($post_id);
            $edit_link = get_edit_post_link($post_id);
            echo '<tr>';
            echo '<td style="padding: 10px; text-align: center; border-bottom: 1px solid #ddd;">' . $num_saves . '</td>';
            echo '<td style="padding: 10px; text-align: left; border-bottom: 1px solid #ddd;"><a href="' . $post_link . '">' . $post_title . '</a></td>';
            echo '<td style="padding: 10px; text-align: right; border-bottom: 1px solid #ddd;"><a style="display: inline-block; padding: 6px 12px; margin: 5px; font-size: 14px; font-weight: bold; text-align: center; text-decoration: none; cursor: pointer; white-space: nowrap; color: white;border-color: #F0488A;background: #F0488A; border-radius: 4px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);" href="' . $post_link . '">View</a><a  style="display: inline-block; padding: 6px 12px; margin: 5px; font-size: 14px; font-weight: bold; text-align: center; text-decoration: none; cursor: pointer; white-space: nowrap; color: white;border-color: #F0488A;background: #F0488A; border-radius: 4px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);" href="' . $edit_link . '">Edit</a></td>';
            echo '</tr>';
        }

        echo '</table>';
    } else {
        echo 'No favorited items found.';
    }
    echo '<br/><a style="display: inline-block; padding: 6px 12px; margin: 5px; font-size: 14px; font-weight: bold; text-align: center; text-decoration: none; cursor: pointer; white-space: nowrap; color: white;border-color: #F0488A;background: #F0488A; border-radius: 4px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);" href="' . esc_url(admin_url('options-general.php?page=AS-U-L-general&tab=statistics')) . '">View More</a>';
}

function add_AS_U_L_dashboard()
{
    wp_add_dashboard_widget(
        'AS_U_L_dashboard',
        'AS User List',
        'AS_U_L_dashboard'
    );
}
add_action('wp_dashboard_setup', 'add_AS_U_L_dashboard');
