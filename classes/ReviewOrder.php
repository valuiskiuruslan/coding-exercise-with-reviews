<?php


namespace CodingExerciseWithReviews\classes;


class ReviewOrder
{
    public function __construct()
    {
        add_action('wp_ajax_update_review_order_table', array($this, 'update_review_order_table'));
    }

    public function init()
    {
        add_action('admin_enqueue_scripts', array($this, 'load_table_drag_drop_styles'), 10);
    }

    /**
     * Load drag&drop sorting dependencies
     */
    public function load_table_drag_drop_styles()
    {
        wp_enqueue_style( 'cewr-drag-drop', cewr_plugin_url('assets/css/drag-drop.css'), array(), CEWR_VERSION);

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-sortable');
        wp_register_script('cewr-drag-drop', cewr_plugin_url('assets/js/drag-drop.js'),
            array('jquery-ui-sortable'),CEWR_VERSION, true);

        // Localize the script with new data
        global $userdata;
        $cewr_variables = array(
            'action' => 'update_review_order_table',
            'post_type' => Review::POST_TYPE,
            'table_sort_nonce' => wp_create_nonce('cewr_table_sort_nonce_' . $userdata->ID)
        );
        wp_localize_script('cewr-drag-drop', 'cewr_drag_drop', $cewr_variables);

        // Enqueued script with localized data.
        wp_enqueue_script('cewr-drag-drop');
    }

    /**
     * Update table order after drag&drop
     */
    public function update_review_order_table()
    {
        set_time_limit(600);

        global $wpdb, $userdata;

        $post_type = filter_var($_POST['post_type'], FILTER_SANITIZE_STRING);
        $paged = filter_var($_POST['paged'], FILTER_SANITIZE_NUMBER_INT);
        $nonce = $_POST['table_sort_nonce'];

        // verify the nonce
        if (!wp_verify_nonce($nonce, 'cewr_table_sort_nonce_' . $userdata->ID)) {
            die();
        }

        parse_str($_POST['order'], $data);

        if (!is_array($data) || count($data) < 1) {
            die();
        }

        // retrieve a list of all objects
        $mysql_query = $wpdb->prepare("SELECT ID FROM "
            . $wpdb->posts
            . " WHERE post_type = %s AND post_status IN ('publish', 'pending', 'draft', 'private', 'future', 'inherit')
                ORDER BY menu_order, post_date DESC", $post_type);

        $results = $wpdb->get_results($mysql_query);

        if (!is_array($results) || count($results) < 1) {
            die();
        }

        // create the list of ID's
        $objects_ids = array();
        foreach ($results as $result) {
            $objects_ids[] = (int)$result->ID;
        }

        $objects_per_page = get_user_meta($userdata->ID, 'edit_' . $post_type . '_per_page', true);

        if (empty($objects_per_page)) {
            $objects_per_page = 20;
        }

        $edit_start_at = $paged * $objects_per_page - $objects_per_page;
        $index = 0;
        for ($i = $edit_start_at; $i < ($edit_start_at + $objects_per_page); $i++) {
            if (!isset($objects_ids[$i]))
                break;

            $objects_ids[$i] = (int)$data['post'][$index];
            $index++;
        }

        // update the menu_order within database
        foreach ($objects_ids as $menu_order => $id) {
            $data = array(
                'menu_order' => $menu_order
            );

            $wpdb->update($wpdb->posts, $data, array('ID' => $id));

            clean_post_cache($id);
        }

        wp_die();
    }
}