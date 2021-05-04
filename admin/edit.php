<?php
/**
 * Adding additional columns to the review table on the edit.php page
 */

use CodingExerciseWithReviews\classes\Review;
use CodingExerciseWithReviews\classes\ReviewOrder;

defined('ABSPATH') || exit;

add_action('manage_cewr_review_posts_columns', 'cewr_manage_review_posts_columns', 10, 1);
function cewr_manage_review_posts_columns($posts_columns)
{
    $custom_posts_columns = [
        'cb' => $posts_columns['cb'],
        'cewr_review_thumbs' => __('Featured Image', 'coding-exercise-with-reviews'),
        'title' => __('Brand ID', 'coding-exercise-with-reviews'),
        'cewr_review_rating' => __('Rating', 'coding-exercise-with-reviews'),
        'cewr_review_bonus' => __('Bonus', 'coding-exercise-with-reviews'),
        'date' => $posts_columns['date'],
    ];

    return $custom_posts_columns;
}

add_action('manage_cewr_review_posts_custom_column', 'cewr_manage_review_posts_custom_column', 10, 2);
function cewr_manage_review_posts_custom_column($column_name, $id)
{
    if ('cewr_review_thumbs' === $column_name) { ?>
        <a href="<?= get_edit_post_link($id) ?>">
            <?php the_post_thumbnail(array(100, 100)); // size of the thumbnail ?>
        </a>
        <?php
    }
    else if ('cewr_review_rating' === $column_name) {
        echo get_post_meta($id, Review::META_RATING, true);
    }
    else if ('cewr_review_bonus' === $column_name) {
        echo get_post_meta($id, Review::META_BONUS, true);
    }
}

// ===================== Adding drag&drop functionality to change reviews order =======================

add_action('plugins_loaded', 'cewr_class_load');
function cewr_class_load()
{
    global $review_order;
    $review_order = new ReviewOrder();
}

add_action('wp_loaded', 'cewr_init_review_order');
function cewr_init_review_order()
{
    global $pagenow, $review_order;

    if (is_admin() && 'edit.php' == $pagenow && isset($_GET['post_type']) && $_GET['post_type'] == Review::POST_TYPE) {
        /** @var ReviewOrder $review_order */
        $review_order->init();
    }
}

// change review order on the edit.php page
add_filter('pre_get_posts', 'set_post_order_in_admin', 5);
function set_post_order_in_admin($wp_query)
{
    global $pagenow;

    if (is_admin() && 'edit.php' == $pagenow && !isset($_GET['orderby'])
        && isset($_GET['post_type']) && $_GET['post_type'] == Review::POST_TYPE) {
        $wp_query->set('orderby', 'menu_order');
        $wp_query->set('order', 'ASC');
    }

    return $wp_query;
}