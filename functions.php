<?php
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