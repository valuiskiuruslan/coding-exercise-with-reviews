<?php


namespace CodingExerciseWithReviews\classes;


class Template
{
    /**
     * Get template.
     *
     * Search for the template and include the file.
     *
     * @param string $template_name Template to load.
     * @param array $args Args passed for the template file.
     * @param string $string $template_path Path to templates.
     * @param string $default_path Default path to template files.
     * @param boolean $return_path Return path if true or display template if false
     * @since 1.0.0
     *
     */
    public static function get_template($template_name, $args = array(), $tempate_path = '', $default_path = '', $return_path = false)
    {
        if (is_array($args) && isset($args)) {
            extract($args);
        }

        $template_file = self::locate_template($template_name, $tempate_path, $default_path);

        if (!file_exists($template_file)) {
            _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $template_file), '1.0.0');
            return false;
        }

        if (!$return_path) {
            include $template_file;
        }

        return $template_file;
    }

    /**
     * Locate template.
     *
     * Locate the called template.
     * Search Order:
     * 1. /themes/theme/coding-exercise-with-reviews/$template_name
     * 2. /themes/theme/$template_name
     * 3. /plugins/coding-exercise-with-reviews/templates/$template_name.
     *
     * @param string $template_name Template to load.
     * @param string $string $template_path    Path to templates.
     * @param string $default_path Default path to template files.
     * @return string Path to the template file.
     * @since 1.0.0
     *
     */
    protected static function locate_template($template_name, $template_path = '', $default_path = '')
    {
        // Set variable to search in coding-exercise-with-reviews folder of theme.
        if (!$template_path) {
            $template_path = 'coding-exercise-with-reviews/';
        }

        // Set default plugin templates path.
        if (!$default_path) {
            $default_path = CEWR_PLUGIN_DIR . '/templates/'; // Path to the template folder
        }

        // Search template file in theme folder.
        $template = locate_template([
            $template_path . $template_name,
            $template_name
        ]);

        // Get plugins template file.
        if (!$template) {
            $template = $default_path . $template_name;
        }

        return apply_filters('cewr_locate_template', $template, $template_name, $template_path, $default_path);
    }
}