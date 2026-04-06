<?php
function buildpro_materials_add_meta_box($post_type, $post)
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
        'buildpro_materials_meta',
        esc_html__('Products', 'buildpro'),
        'buildpro_materials_render_meta_box',
        'page',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'buildpro_materials_add_meta_box', 10, 2);

function buildpro_materials_render_meta_box($post)
{
    $template = get_page_template_slug($post->ID);
    $front_id = (int) get_option('page_on_front');
    if ($template !== 'home-page.php' && (int)$post->ID !== $front_id) {
        return;
    }
    wp_nonce_field('buildpro_materials_meta_save', 'buildpro_materials_meta_nonce');
    $materials_title = get_post_meta($post->ID, 'materials_title', true);
    $materials_description = get_post_meta($post->ID, 'materials_description', true);
    $materials_view_all_text = get_post_meta($post->ID, 'materials_view_all_text', true);
    $materials_enabled = get_post_meta($post->ID, 'materials_enabled', true);
    $materials_enabled = $materials_enabled === '' ? 1 : (int)$materials_enabled;
    $materials_enabled = absint(get_theme_mod('materials_enabled', $materials_enabled));
    $template_file = get_template_directory() . '/template/meta-box/page/home/section-products/index.php';
    if (file_exists($template_file)) {
        include $template_file;
    }
    wp_add_inline_script(
        'buildpro-materials-script',
        'window.buildproMaterialsData=' . wp_json_encode(array('enabled' => $materials_enabled)) . ';',
        'before'
    );
}

function buildpro_save_materials_meta($post_id)
{
    if (!isset($_POST['buildpro_materials_meta_nonce']) || !wp_verify_nonce($_POST['buildpro_materials_meta_nonce'], 'buildpro_materials_meta_save')) {
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
    $materials_title = isset($_POST['materials_title']) ? sanitize_text_field($_POST['materials_title']) : '';
    $materials_description = isset($_POST['materials_description']) ? sanitize_textarea_field($_POST['materials_description']) : '';
    $materials_view_all_text = isset($_POST['materials_view_all_text']) ? sanitize_text_field($_POST['materials_view_all_text']) : '';
    $materials_enabled = isset($_POST['materials_enabled']) ? absint($_POST['materials_enabled']) : 1;
    update_post_meta($post_id, 'materials_title', $materials_title);
    update_post_meta($post_id, 'materials_description', $materials_description);
    update_post_meta($post_id, 'materials_view_all_text', $materials_view_all_text);
    update_post_meta($post_id, 'materials_enabled', $materials_enabled);
    set_theme_mod('materials_title', $materials_title);
    set_theme_mod('materials_description', $materials_description);
    set_theme_mod('materials_view_all_text', $materials_view_all_text);
    set_theme_mod('materials_enabled', $materials_enabled);
}
add_action('save_post_page', 'buildpro_save_materials_meta');

function buildpro_materials_admin_enqueue($hook)
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
    $base_dir = get_theme_file_path('template/meta-box/page/home/section-products');
    $base_uri = get_theme_file_uri('template/meta-box/page/home/section-products');
    $style_ver = file_exists($base_dir . '/style.css') ? filemtime($base_dir . '/style.css') : false;
    $script_ver = file_exists($base_dir . '/script.js') ? filemtime($base_dir . '/script.js') : false;
    wp_enqueue_style('buildpro-materials-style', $base_uri . '/style.css', array(), $style_ver);
    wp_enqueue_script('buildpro-materials-script', $base_uri . '/script.js', array(), $script_ver, true);
}
add_action('admin_enqueue_scripts', 'buildpro_materials_admin_enqueue');
