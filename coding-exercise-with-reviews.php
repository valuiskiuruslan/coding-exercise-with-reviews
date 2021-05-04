<?php
/**
 * Plugin Name: Coding exercise with reviews
 * Description: The goal of this exercise is for you to create a Wordpress plugin which will fetch an array of reviews from an external REST API and display them in a nice list to the user.
 * Version: 1.0.0
 * Author: Ruslan Valuyskiy
 * License: GPLv2 or later
 * Text Domain: coding-exercise-with-reviews
 *
 * @package CodingExerciseWithReviews
 */

use CodingExerciseWithReviews\classes\Review;
use CodingExerciseWithReviews\classes\Template;

defined('ABSPATH') || exit;

define('CEWR_VERSION', '1.0.0');
define('CEWR_PLUGIN', __FILE__);
define('CEWR_PLUGIN_DIR', untrailingslashit(dirname(__FILE__)));
define('CEWR_REVIEWS_PAGE_TEMPLATE', 'reviews.php');

require_once CEWR_PLUGIN_DIR . '/functions.php';
require_once CEWR_PLUGIN_DIR . '/classes/Template.php';
require_once CEWR_PLUGIN_DIR . '/classes/Review.php';
require_once CEWR_PLUGIN_DIR . '/admin/edit.php';
require_once CEWR_PLUGIN_DIR . '/admin/settings.php';
require_once CEWR_PLUGIN_DIR . '/classes/IDataParser.php';
require_once CEWR_PLUGIN_DIR . '/classes/DemoDataParser.php';
require_once CEWR_PLUGIN_DIR . '/classes/Endpoint.php';
require_once CEWR_PLUGIN_DIR . '/classes/ReviewOrder.php';

// Load template from specific page
add_filter('page_template', 'cewr_page_template');
function cewr_page_template($page_template)
{
    if (get_page_template_slug() == CEWR_REVIEWS_PAGE_TEMPLATE) {
        $page_template = Template::get_template('reviews.php', '', '', '', true);
    }
    return $page_template;
}

/**
 * Add "Custom" templates to page attribute template section.
 */
add_filter('theme_page_templates', 'cewr_add_template_to_select', 10, 4);
function cewr_add_template_to_select($post_templates, $wp_theme, $post, $post_type)
{
    // Add custom template named template-custom.php to select dropdown
    $post_templates['reviews.php'] = __('Reviews', 'coding-exercise-with-reviews');
    return $post_templates;
}

add_action('wp_enqueue_scripts', 'cewr_do_enqueue_scripts', 10, 0);
function cewr_do_enqueue_scripts()
{
    if (get_page_template_slug() == CEWR_REVIEWS_PAGE_TEMPLATE) {
        wp_enqueue_style('cewr-style', cewr_plugin_url('assets/css/style.css'), array(), CEWR_VERSION);

        wp_enqueue_script('cewr-jquery-star-rating-svg', cewr_plugin_url('assets/js/jquery.star-rating-svg.min.js'),
            array('jquery'), CEWR_VERSION, true);
        wp_enqueue_script('cewr-script', cewr_plugin_url('assets/js/script.js'),
            array('cewr-jquery-star-rating-svg'), CEWR_VERSION, true);
    }
}

// Register Custom Post Type
add_action('init', 'cewr_review_type', 0);
function cewr_review_type()
{
    Review::register_post_type();
}

add_image_size('cewr_review_logo', 195, 75);

add_filter('wp_get_attachment_image_attributes', 'cewr_get_attachment_image_attributes', 11, 3);
function cewr_get_attachment_image_attributes( $attr, $attachment, $size ) {
    unset($attr['style']);
    return $attr;
}