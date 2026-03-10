<?php
function buildpro_register_post_templates($post_templates, $wp_theme, $post, $post_type)
{
    if ($post_type === 'post') {
        return array();
    }
    return $post_templates;
}
add_filter('theme_post_templates', 'buildpro_register_post_templates', 10, 4);

// split into module files; keep only save and enqueue here

function buildpro_save_post_meta($post_id)
{
    if (!isset($_POST['buildpro_post_meta_nonce']) || !wp_verify_nonce($_POST['buildpro_post_meta_nonce'], 'buildpro_post_meta_save')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (get_post_type($post_id) !== 'post') {
        return;
    }
    $banner_id = isset($_POST['buildpro_post_banner_id']) ? absint($_POST['buildpro_post_banner_id']) : 0;
    $post_desc = isset($_POST['buildpro_post_description']) ? sanitize_textarea_field($_POST['buildpro_post_description']) : '';
    $paragraph = isset($_POST['buildpro_post_paragraph']) ? wp_kses_post($_POST['buildpro_post_paragraph']) : '';
    $quote_title = isset($_POST['buildpro_post_quote_title']) ? sanitize_text_field($_POST['buildpro_post_quote_title']) : '';
    $quote_desc = isset($_POST['buildpro_post_quote_description']) ? sanitize_textarea_field($_POST['buildpro_post_quote_description']) : '';
    $quote_gallery = isset($_POST['buildpro_post_quote_gallery']) && is_array($_POST['buildpro_post_quote_gallery']) ? array_map('absint', $_POST['buildpro_post_quote_gallery']) : array();
    $quote_gallery = array_values(array_filter($quote_gallery));
    $quote_kv_raw = isset($_POST['buildpro_post_quote_kv']) && is_array($_POST['buildpro_post_quote_kv']) ? $_POST['buildpro_post_quote_kv'] : array();
    $quote_kv = array();
    foreach ($quote_kv_raw as $row) {
        $k = isset($row['key']) ? sanitize_text_field($row['key']) : '';
        $v = isset($row['value']) ? sanitize_text_field($row['value']) : '';
        if ($k !== '' || $v !== '') {
            $quote_kv[] = array('key' => $k, 'value' => $v);
        }
    }
    $quote_desc_image_desc = isset($_POST['buildpro_post_quote_desc_image_desc']) ? sanitize_textarea_field($_POST['buildpro_post_quote_desc_image_desc']) : '';
    update_post_meta($post_id, 'buildpro_post_banner_id', $banner_id);
    update_post_meta($post_id, 'buildpro_post_description', $post_desc);
    update_post_meta($post_id, 'buildpro_post_paragraph', $paragraph);
    update_post_meta($post_id, 'buildpro_post_quote_title', $quote_title);
    update_post_meta($post_id, 'buildpro_post_quote_description', $quote_desc);
    update_post_meta($post_id, 'buildpro_post_quote_gallery', $quote_gallery);
    update_post_meta($post_id, 'buildpro_post_quote_kv', $quote_kv);
    update_post_meta($post_id, 'buildpro_post_quote_desc_image_desc', $quote_desc_image_desc);
}
add_action('save_post', 'buildpro_save_post_meta');

function buildpro_post_admin_enqueue($hook)
{
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'buildpro_post_admin_enqueue');
