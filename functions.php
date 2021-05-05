<?php

defined('ABSPATH') || exit;

/**
 * Returns plugin url for given path
 * @param string $path
 * @return string
 */
function cewr_plugin_url($path = '')
{
    $url = plugins_url($path, CEWR_PLUGIN);

    if (is_ssl() && 'http:' == substr($url, 0, 5)) {
        $url = 'https:' . substr($url, 5);
    }

    return $url;
}

/**
 * Downloads and create new image attachment
 * @param $download_url
 * @return bool|int|WP_Error Returns attachment id or false
 */
function cewr_create_new_image_attachment($download_url, $post_id_to_attach = 0)
{
    $info_about_image = pathinfo($download_url);
    $image_name = $info_about_image['basename'];
    $upload_dir = wp_upload_dir();
    $base_guid = $upload_dir['url'];
    $upload_file = $upload_dir['path'];

    if (!function_exists('download_url')) {
        return false;
    }

    $tmp = download_url($download_url);

    if (!is_wp_error($tmp)) {
        $name = basename($download_url);

        if (!$info_about_image['extension']) {
            $tmp_image_info = getimagesize($tmp);
            $name = $name . '_' . uniqid() . image_type_to_extension($tmp_image_info[2]);
        }

        $file = array(
            'name' => $name, // ex: wp-header-logo.png
            'type' => mime_content_type($tmp),
            'tmp_name' => $tmp,
            'error' => 0,
            'size' => filesize($tmp),
        );

        if (file_exists($upload_file . '/' . $image_name)) {
            unlink($upload_file . '/' . $image_name);
        }

        $results = wp_handle_sideload($file, array('test_form' => false));
        if (isset($results['error']) && !empty($results['error'])) {
            return false;
        }

        $attachment = array(
            'guid' => $base_guid . '/' . basename($image_name),
            'post_mime_type' => $results['type'],
            'post_title' => preg_replace('/\.[^.]+$/', '', basename($image_name)),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        $image_arr = explode('/', $results['file']);
        $count = count($image_arr);
        $image_name = $image_arr[$count - 1];
        $attachmentId = wp_insert_attachment($attachment, $upload_file . '/' . $image_name, $post_id_to_attach);
        // Define attachment metadata
        $attachmentData = wp_generate_attachment_metadata($attachmentId, $upload_file . '/' . $image_name);
        // Assign metadata to attachment
        wp_update_attachment_metadata($attachmentId, $attachmentData);
        @unlink($tmp);

        return $attachmentId;
    }

    return false;
}

/**
 * Delete attachment from database and file from disk
 * @param $attachment_id
 */
function cewr_delete_attachment_and_file($attachment_id) {
    $attachment_path = get_attached_file( $attachment_id);
    // Delete attachment from database only, not file
    wp_delete_attachment($attachment_id, true);
    // Delete attachment file from disk
    @unlink($attachment_path);
}