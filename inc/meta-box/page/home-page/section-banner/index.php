<?php
function buildpro_banner_add_meta_box($post_type, $post)
{
    if ($post_type !== 'page') {
        return;
    }
    $template = get_page_template_slug($post->ID);
    $front_id = (int) get_option('page_on_front');
    if ($template !== 'home-page.php' && (int)$post->ID !== $front_id) {
        return;
    }
    add_meta_box(
        'buildpro_banner_meta',
        esc_html__('Banner', 'buildpro'),
        'buildpro_banner_render_meta_box',
        'page',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'buildpro_banner_add_meta_box', 10, 2);

function buildpro_banner_render_meta_box($post)
{
    wp_nonce_field('buildpro_banner_meta_save', 'buildpro_banner_meta_nonce');
    wp_enqueue_media();
    $items = get_post_meta($post->ID, 'buildpro_banner_items', true);
    $enabled = get_post_meta($post->ID, 'buildpro_banner_enabled', true);
    $enabled = $enabled === '' ? 1 : (int)$enabled;
    $items = is_array($items) ? $items : array();
    wp_enqueue_style('buildpro-banner-admin', get_theme_file_uri('template/meta-box/page/home/section-banner/style.css'), array(), null);
    $prepared = array();
    foreach ($items as $item) {
        $image_id = isset($item['image_id']) ? (int)$item['image_id'] : 0;
        $type = isset($item['type']) ? sanitize_text_field($item['type']) : '';
        $text = isset($item['text']) ? sanitize_text_field($item['text']) : '';
        $desc = isset($item['description']) ? sanitize_textarea_field($item['description']) : '';
        $link_url = isset($item['link_url']) ? esc_url_raw($item['link_url']) : '';
        $link_title = isset($item['link_title']) ? sanitize_text_field($item['link_title']) : '';
        $link_target = isset($item['link_target']) ? sanitize_text_field($item['link_target']) : '';
        $thumb = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';
        $prepared[] = array(
            'image_id' => $image_id,
            'type' => $type,
            'text' => $text,
            'description' => $desc,
            'link_url' => $link_url,
            'link_title' => $link_title,
            'link_target' => $link_target,
            'thumb_url' => $thumb,
        );
    }
    include get_theme_file_path('template/meta-box/page/home/section-banner/index.php');
    wp_enqueue_script(
        'buildpro-banner-admin',
        get_theme_file_uri('template/meta-box/page/home/section-banner/script.js'),
        array('wplink'),
        null,
        true
    );
    wp_add_inline_script(
        'buildpro-banner-admin',
        'window.buildproBannerData=' . wp_json_encode(array('items' => $prepared, 'enabled' => $enabled)) . ';',
        'before'
    );
    return;
}

function buildpro_banner_admin_enqueue($hook)
{
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        wp_enqueue_media();
        wp_enqueue_script('wplink');
        wp_enqueue_style('wp-link');
    }
}
add_action('admin_enqueue_scripts', 'buildpro_banner_admin_enqueue');
function buildpro_save_banner_meta($post_id)
{
    if (!isset($_POST['buildpro_banner_meta_nonce']) || !wp_verify_nonce($_POST['buildpro_banner_meta_nonce'], 'buildpro_banner_meta_save')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    $template = get_page_template_slug($post_id);
    $front_id = (int) get_option('page_on_front');
    if ($template !== 'home-page.php' && (int)$post_id !== $front_id) {
        return;
    }
    $items = isset($_POST['buildpro_banner_items']) && is_array($_POST['buildpro_banner_items']) ? $_POST['buildpro_banner_items'] : array();
    $enabled = isset($_POST['buildpro_banner_enabled']) ? absint($_POST['buildpro_banner_enabled']) : 1;
    $clean = array();
    foreach ($items as $item) {
        $clean[] = array(
            'image_id' => isset($item['image_id']) ? absint($item['image_id']) : 0,
            'type' => isset($item['type']) ? sanitize_text_field($item['type']) : '',
            'text' => isset($item['text']) ? sanitize_text_field($item['text']) : '',
            'description' => isset($item['description']) ? sanitize_textarea_field($item['description']) : '',
            'link_url' => isset($item['link_url']) ? esc_url_raw($item['link_url']) : '',
            'link_title' => isset($item['link_title']) ? sanitize_text_field($item['link_title']) : '',
            'link_target' => isset($item['link_target']) ? sanitize_text_field($item['link_target']) : '',
        );
    }
    if (empty($clean)) {
        $enabled = 0;
    } else {
        $enabled = 1;
    }
    update_post_meta($post_id, 'buildpro_banner_items', $clean);
    update_post_meta($post_id, 'buildpro_banner_enabled', $enabled);
    set_theme_mod('buildpro_banner_items', $clean);
    set_theme_mod('buildpro_banner_enabled', $enabled);
}
add_action('save_post_page', 'buildpro_save_banner_meta');
