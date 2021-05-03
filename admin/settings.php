<?php

use CodingExerciseWithReviews\classes\DemoDataParser;
use CodingExerciseWithReviews\classes\Review;
use CodingExerciseWithReviews\classes\ReviewEndpoint;

add_action('admin_menu', function() {
    add_menu_page('Reviews API', 'Reviews API', 'manage_options', 'reviews-api', 'reviewsAPIMenuPage');
});

function reviewsAPIMenuPage() {
    ?>
    <style>
        .ajax-button {
            position: relative;
        }
        .ajax-button:disabled:after {
            content: ' ';
            display: block;
            position: absolute;
            width: 32px;
            height: 32px;
            background-image: url('<?= home_url('/wp-includes/images/wpspin-2x.gif') ?>');
            right: -36px;
            top: -2px;
        }
    </style>
    <h1><?= __('Reviews API', 'coding-exercise-with-reviews') ?></h1>
    <p>
        <button class="button button-primary ajax-button">
            <?= __('Refresh reviews', 'coding-exercise-with-reviews') ?>
        </button>
    </p>
    <script>
        jQuery(document).ready(function ($) {
            $('.ajax-button').click(function () {
                var self = this;

                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: 'refresh_reviews'
                    },
                    beforeSend: function() {
                        self.disabled = true;
                    },
                    complete: function () {
                        self.disabled = false;
                    }
                }).done(function (result) {
                    console.log(result);
                });
            });
        });
    </script>
    <?php
}

add_action('wp_ajax_refresh_reviews', 'cewr_refresh_reviews');
function cewr_refresh_reviews()
{
    $response = array();

    $reviewEndpoint = new ReviewEndpoint(new DemoDataParser(), CEWR_PLUGIN_DIR . '/data.json');
    $reviews = $reviewEndpoint->parseReviewResponse();

    if (!empty($reviews)) {
        foreach ($reviews as $review) {
            $reviewObj = Review::find_by_brand_id($review['brand_id']);
            $reviewObj->set_brand_id($review['brand_id']);
            $reviewObj->set_position($review['position']);
            $reviewObj->set_properties([
                Review::META_RATING => $review['info']['rating'],
                Review::META_BONUS => $review['info']['bonus'],
                Review::META_FEATURES => implode(PHP_EOL, $review['info']['features']),
                Review::META_PLAY_URL => $review['play_url'],
                Review::META_TERMS_AND_CONDITIONS => $review['terms_and_conditions'],
            ]);

            $reviewObj->save();

            $image_id = cewr_create_new_image_attachment($review['logo'], $reviewObj->get_id());
            if ($image_id) {
                // removing previous image
                $thumbnail_id = get_post_thumbnail_id($reviewObj->get_id());
                if ($thumbnail_id) {
                    cewr_delete_attachment_and_file($thumbnail_id);
                }

                set_post_thumbnail($reviewObj->get_id(), $image_id);
            }
        }
        $response['result'] = true;
    }
    else {
        $response['result'] = false;
    }

    header( "Content-Type: application/json" );
    echo json_encode($response);

    // Don't forget to always exit in the ajax function.
    exit();
}