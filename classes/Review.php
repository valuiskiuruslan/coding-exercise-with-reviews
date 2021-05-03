<?php


namespace CodingExerciseWithReviews\classes;


use WP_Query;

class Review
{
    const POST_TYPE = 'cewr_review';

    const META_RATING = 'Rating';
    const META_BONUS = 'Bonus';
    const META_FEATURES = 'Features';
    const META_PLAY_URL = 'Play URL';
    const META_TERMS_AND_CONDITIONS = 'Terms and conditions';

    private static $found_items = 0;

    private $id = 0;
    private $brand_id = 0; // post_title
    private $position = 0; // menu_order
    private $properties = array();

    public static function count()
    {
        return self::$found_items;
    }

    public static function register_post_type()
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

        register_post_type(self::POST_TYPE, $args);
    }

    public static function find($args = '')
    {
        $defaults = array(
            'post_status' => 'any',
            'posts_per_page' => -1,
            'offset' => 0,
            'orderby' => 'ID',
            'order' => 'ASC',
        );

        $args = wp_parse_args($args, $defaults);

        $args['post_type'] = self::POST_TYPE;

        $q = new WP_Query();
        $posts = $q->query($args);

        self::$found_items = $q->found_posts;

        $objs = array();

        foreach ((array)$posts as $post) {
            $objs[] = new self($post);
        }

        return $objs;
    }

    public static function find_by_brand_id($brand_id)
    {
        $post = get_page_by_title($brand_id, OBJECT, self::POST_TYPE);
        return new self($post);
    }

    public function __construct($post = null)
    {
        $post = get_post($post);

        if ($post && self::POST_TYPE == get_post_type($post)) {
            $this->id = $post->ID;
            $this->brand_id = $post->post_title;
            $this->position = $post->menu_order;

            $properties = $this->get_properties();

            foreach ($properties as $key => $value) {
                if (metadata_exists('post', $post->ID, $key)) {
                    $properties[$key] = get_post_meta($post->ID, $key, true);
                }
            }

            $this->properties = $properties;
        }
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_brand_id()
    {
        return $this->brand_id;
    }

    public function set_brand_id($brand_id)
    {
        $this->brand_id = $brand_id;
    }

    public function get_position()
    {
        return $this->position;
    }

    public function set_position($position)
    {
        $this->position = $position;
    }

    public function get_properties()
    {
        $properties = (array)$this->properties;

        $properties = wp_parse_args($properties, array(
            self::META_RATING => 0,
            self::META_BONUS => '',
            self::META_FEATURES => array(),
            self::META_PLAY_URL => '',
            self::META_TERMS_AND_CONDITIONS => '',
        ));

        return $properties;
    }

    public function set_properties($properties)
    {
        $defaults = $this->get_properties();

        $properties = wp_parse_args($properties, $defaults);
        $properties = array_intersect_key($properties, $defaults);

        $this->properties = $properties;
    }

    public function save()
    {
        $props = $this->get_properties();

        if (empty($this->id)) {
            $post_id = wp_insert_post(array(
                'post_type' => self::POST_TYPE,
                'post_status' => 'publish',
                'post_title' => $this->brand_id,
                'menu_order' => $this->position,
            ));
        }
        else {
            $post_id = wp_update_post(array(
                'ID' => (int)$this->id,
                'post_status' => 'publish',
                'post_title' => $this->brand_id,
                'menu_order' => $this->position,
            ));
        }

        if ($post_id) {
            foreach ($props as $prop => $value) {
                update_post_meta($post_id, $prop, $value);
            }
        }

        $this->id = $post_id;

        return $post_id;
    }
}