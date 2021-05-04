<?php

// change the title to the brand id for review
use CodingExerciseWithReviews\classes\Review;

add_filter('enter_title_here', 'cewr_title_here', 10, 2);
function cewr_title_here($title, WP_Post $post)
{
    if (Review::POST_TYPE == $post->post_type) {
        return __('Brand ID', 'coding-exercise-with-reviews');
    }
    return $title;
}