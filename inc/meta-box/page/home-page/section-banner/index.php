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

    $option_items = get_post_meta($post->ID, 'buildpro_option_items', true);
    $option_enabled = get_post_meta($post->ID, 'buildpro_option_enabled', true);
    $option_enabled = $option_enabled === '' ? 1 : (int) $option_enabled;
    $option_items = is_array($option_items) ? $option_items : array();

    wp_enqueue_style('buildpro-banner-admin', get_theme_file_uri('template/meta-box/page/home/section-banner/style.css'), array(), null);
    wp_enqueue_style('buildpro-option-admin-merged', get_theme_file_uri('template/meta-box/page/home/section-banner/option-style.css'), array(), null);

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

    $option_prepared = array();
    foreach ($option_items as $it) {
        $icon_id = isset($it['icon_id']) ? (int) $it['icon_id'] : 0;
        $thumb = $icon_id ? wp_get_attachment_image_url($icon_id, 'thumbnail') : '';
        $option_prepared[] = array(
            'icon_id' => $icon_id,
            'text' => isset($it['text']) ? (string) $it['text'] : '',
            'description' => isset($it['description']) ? (string) $it['description'] : '',
            'thumb_url' => $thumb ? (string) $thumb : '',
        );
    }

    $banner_enabled = $enabled;
    include get_theme_file_path('template/meta-box/page/home/section-banner/index.php');
    $enabled = $option_enabled;
    include get_theme_file_path('template/meta-box/page/home/section-banner/option-index.php');

    wp_enqueue_script(
        'buildpro-banner-admin',
        get_theme_file_uri('template/meta-box/page/home/section-banner/script.js'),
        array('wplink'),
        null,
        true
    );
    wp_add_inline_script(
        'buildpro-banner-admin',
        'window.buildproBannerData=' . wp_json_encode(array('items' => $prepared, 'enabled' => $banner_enabled)) . ';',
        'before'
    );

    wp_enqueue_script(
        'buildpro-option-admin-merged',
        get_theme_file_uri('template/meta-box/page/home/section-banner/option-script.js'),
        array(),
        null,
        true
    );
    wp_add_inline_script(
        'buildpro-option-admin-merged',
        'window.buildproOptionData=' . wp_json_encode(array('items' => $option_prepared, 'enabled' => $option_enabled)) . ';',
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
    $option_items = isset($_POST['buildpro_option_items']) && is_array($_POST['buildpro_option_items']) ? $_POST['buildpro_option_items'] : array();
    $option_enabled = isset($_POST['buildpro_option_enabled']) ? absint($_POST['buildpro_option_enabled']) : 1;
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

    $option_clean = array();
    foreach ($option_items as $item) {
        $option_clean[] = array(
            'icon_id' => isset($item['icon_id']) ? absint($item['icon_id']) : 0,
            'text' => isset($item['text']) ? sanitize_text_field($item['text']) : '',
            'description' => isset($item['description']) ? sanitize_textarea_field($item['description']) : '',
        );
    }

    if (empty($clean)) {
        $enabled = 0;
    } else {
        $enabled = 1;
    }

    if (empty($option_clean)) {
        $option_enabled = 0;
    } else {
        $option_enabled = 1;
    }

    update_post_meta($post_id, 'buildpro_banner_items', $clean);
    update_post_meta($post_id, 'buildpro_banner_enabled', $enabled);
    set_theme_mod('buildpro_banner_items', $clean);
    set_theme_mod('buildpro_banner_enabled', $enabled);

    update_post_meta($post_id, 'buildpro_option_items', $option_clean);
    update_post_meta($post_id, 'buildpro_option_enabled', $option_enabled);
    set_theme_mod('buildpro_option_items', $option_clean);
    set_theme_mod('buildpro_option_enabled', $option_enabled);
}
add_action('save_post_page', 'buildpro_save_banner_meta');
