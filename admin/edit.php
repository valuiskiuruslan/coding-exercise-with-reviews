<?php
/**
 * Adding additional columns to the review table on the edit.php page
 */

use CodingExerciseWithReviews\classes\Review;

defined('ABSPATH') || exit;

add_action('manage_cewr_review_posts_columns', 'cewr_manage_review_posts_columns', 10, 1);
function cewr_manage_review_posts_columns($posts_columns)
{
    $custom_posts_columns = [
        'cb' => $posts_columns['cb'],
        'cewr_review_thumbs' => __('Featured Image', 'coding-exercise-with-reviews'),
        'title' => $posts_columns['title'],
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