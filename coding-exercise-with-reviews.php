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

use CodingExerciseWithReviews\classes\Template;

defined('ABSPATH') || exit;

define('CEWR_VERSION', '1.1');
define('CEWR_PLUGIN', __FILE__);
define('CEWR_PLUGIN_DIR', untrailingslashit(dirname(__FILE__)));

require_once CEWR_PLUGIN_DIR . '/classes/Template.php';

// Load template from specific page
add_filter('page_template', 'cewr_page_template');
function cewr_page_template($page_template)
{
    if (get_page_template_slug() == 'reviews.php') {
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