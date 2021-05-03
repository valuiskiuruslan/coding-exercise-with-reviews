<?php
/**
 * @package CodingExerciseWithReviews/Templates
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

use CodingExerciseWithReviews\classes\Review;

get_header();
?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <header class="reviews-header">
            <h1><?php the_title() ?></h1>
        </header>

        <div class="review-table-wrap">
            <table class="review-table">
                <thead>
                <tr>
                    <th><?= __('Casino', 'coding-exercise-with-reviews') ?></th>
                    <th><?= __('Bonus', 'coding-exercise-with-reviews') ?></th>
                    <th><?= __('Features', 'coding-exercise-with-reviews') ?></th>
                    <th><?= __('Play', 'coding-exercise-with-reviews') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $reviews = Review::find([
                    'post_status' => 'publish',
                    'orderby' => 'menu_order',
                ]);

                if (!empty($reviews) && is_array($reviews)) {
                    foreach ($reviews as $review) {
                        /** @var Review $review */

                        $properties = $review->get_properties();
                        $rating = !empty($properties[Review::META_RATING]) ? $properties[Review::META_RATING] : 0;
                        $bonus = !empty($properties[Review::META_BONUS]) ? $properties[Review::META_BONUS] : '';
                        $play_url = !empty($properties[Review::META_PLAY_URL]) ? $properties[Review::META_PLAY_URL] : '#';
                        $terms_and_conditions = !empty($properties[Review::META_TERMS_AND_CONDITIONS])
                            ? $properties[Review::META_TERMS_AND_CONDITIONS]
                            : '';

                        $features = !empty($properties[Review::META_FEATURES])
                            ? preg_split('/\r\n|\r|\n/', $properties[Review::META_FEATURES])
                            : null;
                        ?>
                        <tr>
                            <td data-label="<?= __('Casino', 'coding-exercise-with-reviews') ?>">
                                <div>
                                    <div class="review-logo">
                                        <?= get_the_post_thumbnail($review->get_id()) ?>
                                    </div>
                                    <a href="<?= get_permalink($review->get_id()) ?>">
                                        <?= __('Review', 'coding-exercise-with-reviews') ?>
                                    </a>
                                </div>
                            </td>
                            <td data-label="<?= __('Bonus', 'coding-exercise-with-reviews') ?>">
                                <div>
                                    <div class="svg-star-rating" data-rating="<?= $rating ?>"></div>
                                    <span class="bonus-description"><?= $bonus ?></span>
                                </div>
                            </td>
                            <td data-label="<?= __('Features', 'coding-exercise-with-reviews') ?>">
                                <?php
                                if (!empty($features) && is_array($features)) { ?>
                                    <ul class="review-features">
                                        <?php
                                        foreach ($features as $feature) { ?>
                                            <li><?= $feature ?></li>
                                            <?php
                                        } ?>
                                    </ul>
                                    <?php
                                } ?>
                            </td>
                            <td data-label="<?= __('Play', 'coding-exercise-with-reviews') ?>">
                                <div>
                                    <a href="<?= $play_url ?>" class="review-btn">
                                        <?= __('Play now', 'coding-exercise-with-reviews') ?>
                                    </a>
                                    <div class="review-terms-and-conditions">
                                        <?= $terms_and_conditions ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                } ?>
                </tbody>
            </table>
        </div>
    </article>
<?php
get_footer();