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
define('CEWR_REVIEWS_PAGE_TEMPLATE', 'reviews.php');

require_once CEWR_PLUGIN_DIR . '/classes/Template.php';

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

/**
 * Returns plugin url for given path
 * @param string $path
 * @return string
 */
function cewr_plugin_url($path = '')
{
    $url = plugins_url($path, CEWR_PLUGIN);

    if (is_ssl()
        and 'http:' == substr($url, 0, 5)) {
        $url = 'https:' . substr($url, 5);
    }

    return $url;
}

// Register Custom Post Type
add_action('init', 'cewr_review_type', 0);
function cewr_review_type()
{
    $labels = array(
        'name' => _x('Reviews', 'Post Type General Name', 'coding-exercise-with-reviews'),
        'singular_name' => _x('Review', 'Post Type Singular Name', 'coding-exercise-with-reviews'),
        'menu_name' => __('Reviews', 'coding-exercise-with-reviews'),
        'name_admin_bar' => __('Review', 'coding-exercise-with-reviews'),
        'archives' => __('Review Archives', 'coding-exercise-with-reviews'),
        'attributes' => __('Review Attributes', 'coding-exercise-with-reviews'),
        'parent_item_colon' => __('Parent Item:', 'coding-exercise-with-reviews'),
        'all_items' => __('All Items', 'coding-exercise-with-reviews'),
        'add_new_item' => __('Add New Item', 'coding-exercise-with-reviews'),
        'add_new' => __('Add New', 'coding-exercise-with-reviews'),
        'new_item' => __('New Item', 'coding-exercise-with-reviews'),
        'edit_item' => __('Edit Item', 'coding-exercise-with-reviews'),
        'update_item' => __('Update Item', 'coding-exercise-with-reviews'),
        'view_item' => __('View Item', 'coding-exercise-with-reviews'),
        'view_items' => __('View Items', 'coding-exercise-with-reviews'),
        'search_items' => __('Search Item', 'coding-exercise-with-reviews'),
        'not_found' => __('Not found', 'coding-exercise-with-reviews'),
        'not_found_in_trash' => __('Not found in Trash', 'coding-exercise-with-reviews'),
        'featured_image' => __('Featured Image', 'coding-exercise-with-reviews'),
        'set_featured_image' => __('Set featured image', 'coding-exercise-with-reviews'),
        'remove_featured_image' => __('Remove featured image', 'coding-exercise-with-reviews'),
        'use_featured_image' => __('Use as featured image', 'coding-exercise-with-reviews'),
        'insert_into_item' => __('Insert into item', 'coding-exercise-with-reviews'),
        'uploaded_to_this_item' => __('Uploaded to this item', 'coding-exercise-with-reviews'),
        'items_list' => __('Items list', 'coding-exercise-with-reviews'),
        'items_list_navigation' => __('Items list navigation', 'coding-exercise-with-reviews'),
        'filter_items_list' => __('Filter items list', 'coding-exercise-with-reviews'),
    );

    $args = array(
        'label' => __('Review', 'coding-exercise-with-reviews'),
        'description' => __('Reviews of casino', 'coding-exercise-with-reviews'),
        'labels' => $labels,
        'supports' => array('title', 'thumbnail', 'custom-fields', 'page-attributes'),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'page',
    );

    register_post_type('review', $args);
}

add_action('manage_review_posts_columns', 'cewr_manage_review_posts_columns', 10, 1);
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

add_action('manage_review_posts_custom_column', 'cewr_manage_review_posts_custom_column', 10, 2);
function cewr_manage_review_posts_custom_column($column_name, $id)
{
    if ('cewr_review_thumbs' === $column_name) { ?>
        <a href="<?= get_edit_post_link($id) ?>">
            <?php the_post_thumbnail(array(100, 100)); // size of the thumbnail ?>
        </a>
        <?php
    }
    else if ('cewr_review_rating' === $column_name) {
        echo get_post_meta($id, 'Rating', true);
    }
    else if ('cewr_review_bonus' === $column_name) {
        echo get_post_meta($id, 'Bonus', true);
    }
}