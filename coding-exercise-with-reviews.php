<?php

/*
Plugin Name: Coding exercise with reviews
Description: The goal of this exercise is for you to create a Wordpress plugin which will fetch an array of reviews from an external REST API and display them in a nice list to the user.
Version: 1.0.0
Author: Ruslan Valuyskiy
License: GPLv2 or later
Text Domain: coding-exercise-with-reviews

* @package CodingExerciseWithReviews
*/

defined( 'ABSPATH' ) || exit;

define('CEWR_VERSION', '1.1');
define('CEWR_PLUGIN', __FILE__);
define('CEWR_PLUGIN_DIR', untrailingslashit(dirname(__FILE__)));