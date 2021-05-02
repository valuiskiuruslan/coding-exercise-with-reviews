<?php
/**
 * @package CodingExerciseWithReviews/Templates
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

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
                    <th>Casino</th>
                    <th>Bonus</th>
                    <th>Features</th>
                    <th>Play</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td data-label="Casino">
                        <div>
                            <div class="review-logo">
                                <img src="https://picsum.photos/195/75" alt="">
                            </div>
                            <a href="#">Review</a>
                        </div>
                    </td>
                    <td data-label="Bonus">
                        <div>
                            <div class="svg-star-rating" data-rating="4"></div>
                            <span class="bonus-description">Free $25 bonus and 100% deposit up to $1000</span>
                        </div>
                    </td>
                    <td data-label="Features">
                        <ul class="review-features">
                            <li>Easy cash back options</li>
                            <li>Good payment options</li>
                            <li>Exclusive game</li>
                        </ul>
                    </td>
                    <td data-label="Play">
                        <div>
                            <a href="#" class="review-btn">Play now</a>
                            <div class="review-terms-and-conditions">
                                21+ | <a href="https://generator.lorem-ipsum.info/terms-and-conditions">T&CS Apply</a> |
                                Gamble
                                Responsibly
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td data-label="Casino">
                        <div>
                            <div class="review-logo">
                                <img src="https://picsum.photos/195/75" alt="">
                            </div>
                            <a href="#">Review</a>
                        </div>
                    </td>
                    <td data-label="Bonus">
                        <div>
                            <div class="svg-star-rating" data-rating="4"></div>
                            <span class="bonus-description">Free $25 bonus and 100% deposit up to $1000</span>
                        </div>
                    </td>
                    <td data-label="Features">
                        <ul class="review-features">
                            <li>Easy cash back options</li>
                            <li>Good payment options</li>
                            <li>Exclusive game</li>
                        </ul>
                    </td>
                    <td data-label="Play">
                        <div>
                            <a href="#" class="review-btn">Play now</a>
                            <div class="review-terms-and-conditions">
                                21+ | <a href="https://generator.lorem-ipsum.info/terms-and-conditions">T&CS Apply</a> |
                                Gamble
                                Responsibly
                            </div>
                        </div>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>
    </article>
<?php
get_footer();