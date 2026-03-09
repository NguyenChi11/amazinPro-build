<?php
function buildpro_option_add_meta_box($post_type, $post)
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
        'buildpro_option_meta',
        'Option',
        'buildpro_option_render_meta_box',
        'page',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'buildpro_option_add_meta_box', 10, 2);

function buildpro_option_render_meta_box($post)
{
    $template = get_page_template_slug($post->ID);
    $front_id = (int) get_option('page_on_front');
    if ($template !== 'home-page.php' && (int)$post->ID !== $front_id) {
        return;
    }
    wp_nonce_field('buildpro_option_meta_save', 'buildpro_option_meta_nonce');
    wp_enqueue_media();
    $items = get_post_meta($post->ID, 'buildpro_option_items', true);
    $enabled = get_post_meta($post->ID, 'buildpro_option_enabled', true);
    $enabled = $enabled === '' ? 1 : (int) $enabled;
    $enabled = absint(get_theme_mod('buildpro_option_enabled', $enabled));
    $items = is_array($items) ? $items : array();
    $prepared = array();
    foreach ($items as $it) {
        $icon_id = isset($it['icon_id']) ? (int) $it['icon_id'] : 0;
        $thumb = $icon_id ? wp_get_attachment_image_url($icon_id, 'thumbnail') : '';
        $prepared[] = array(
            'icon_id' => $icon_id,
            'text' => isset($it['text']) ? (string) $it['text'] : '',
            'description' => isset($it['description']) ? (string) $it['description'] : '',
            'thumb_url' => $thumb ? (string) $thumb : '',
        );
    }
    $template_file = get_theme_file_path('template/meta-box/page/home/section-option/index.php');
    if (file_exists($template_file)) {
        include $template_file;
    }
    wp_add_inline_script(
        'buildpro-option-script',
        'window.buildproOptionData=' . wp_json_encode(array('items' => $prepared, 'enabled' => $enabled)) . ';',
        'before'
    );
}

function buildpro_option_admin_enqueue($hook)
{
    if ($hook !== 'post.php' && $hook !== 'post-new.php') {
        return;
    }
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->post_type !== 'page') {
        return;
    }
    $pid = isset($_GET['post']) ? absint($_GET['post']) : (isset($_POST['post_ID']) ? absint($_POST['post_ID']) : 0);
    if ($pid <= 0) {
        return;
    }
    $template = get_page_template_slug($pid);
    $front_id = (int) get_option('page_on_front');
    if ($template !== 'home-page.php' && (int)$pid !== $front_id) {
        return;
    }
    wp_enqueue_media();
    $base_dir = get_theme_file_path('template/meta-box/page/home/section-option');
    $base_uri = get_theme_file_uri('template/meta-box/page/home/section-option');
    $style_ver = file_exists($base_dir . '/style.css') ? filemtime($base_dir . '/style.css') : false;
    $script_ver = file_exists($base_dir . '/script.js') ? filemtime($base_dir . '/script.js') : false;
    wp_enqueue_style('buildpro-option-style', $base_uri . '/style.css', array(), $style_ver);
    wp_enqueue_script('buildpro-option-script', $base_uri . '/script.js', array(), $script_ver, true);
}
add_action('admin_enqueue_scripts', 'buildpro_option_admin_enqueue');

function buildpro_save_option_meta($post_id)
{
    if (!isset($_POST['buildpro_option_meta_nonce']) || !wp_verify_nonce($_POST['buildpro_option_meta_nonce'], 'buildpro_option_meta_save')) {
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
    $items = isset($_POST['buildpro_option_items']) && is_array($_POST['buildpro_option_items']) ? $_POST['buildpro_option_items'] : array();
    $enabled = isset($_POST['buildpro_option_enabled']) ? absint($_POST['buildpro_option_enabled']) : 1;
    $clean = array();
    foreach ($items as $item) {
        $clean[] = array(
            'icon_id' => isset($item['icon_id']) ? absint($item['icon_id']) : 0,
            'text' => isset($item['text']) ? sanitize_text_field($item['text']) : '',
            'description' => isset($item['description']) ? sanitize_textarea_field($item['description']) : '',
        );
    }
    update_post_meta($post_id, 'buildpro_option_items', $clean);
    update_post_meta($post_id, 'buildpro_option_enabled', $enabled);
    set_theme_mod('buildpro_option_items', $clean);
    set_theme_mod('buildpro_option_enabled', $enabled);
}
add_action('save_post_page', 'buildpro_save_option_meta');
