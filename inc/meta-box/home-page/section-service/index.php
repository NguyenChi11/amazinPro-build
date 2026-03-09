<?php
function buildpro_services_add_meta_box($post_type, $post)
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
        'buildpro_services_meta',
        'Services',
        'buildpro_services_render_meta_box',
        'page',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'buildpro_services_add_meta_box', 10, 2);

function buildpro_services_render_meta_box($post)
{
    wp_nonce_field('buildpro_services_meta_save', 'buildpro_services_meta_nonce');
    wp_enqueue_media();
    $service_title = get_post_meta($post->ID, 'buildpro_service_title', true);
    $service_desc = get_post_meta($post->ID, 'buildpro_service_desc', true);
    $service_enabled = get_post_meta($post->ID, 'buildpro_service_enabled', true);
    $service_enabled = $service_enabled === '' ? 1 : (int)$service_enabled;
    $service_enabled = absint(get_theme_mod('buildpro_service_enabled', $service_enabled));
    $items = get_post_meta($post->ID, 'buildpro_service_items', true);
    $items = is_array($items) ? $items : array();
    $template_file = get_template_directory() . '/template/meta-box/page/home/section-services/index.php';
    if (file_exists($template_file)) {
        include $template_file;
    }
    wp_add_inline_script(
        'buildpro-services-script',
        'window.buildproServicesData=' . wp_json_encode(array('enabled' => $service_enabled)) . ';',
        'before'
    );
}

function buildpro_services_admin_enqueue($hook)
{
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        wp_enqueue_media();
        wp_enqueue_script('wplink');
        wp_enqueue_style('wp-link');
        $base_dir = get_template_directory() . '/template/meta-box/page/home/section-services';
        $base_uri = get_template_directory_uri() . '/template/meta-box/page/home/section-services';
        $style_ver = file_exists($base_dir . '/style.css') ? filemtime($base_dir . '/style.css') : false;
        $script_ver = file_exists($base_dir . '/script.js') ? filemtime($base_dir . '/script.js') : false;
        wp_enqueue_style('buildpro-services-style', $base_uri . '/style.css', array(), $style_ver);
        wp_enqueue_script('buildpro-services-script', $base_uri . '/script.js', array('jquery'), $script_ver, true);
    }
}
add_action('admin_enqueue_scripts', 'buildpro_services_admin_enqueue');

function buildpro_save_services_meta($post_id)
{
    if (!isset($_POST['buildpro_services_meta_nonce']) || !wp_verify_nonce($_POST['buildpro_services_meta_nonce'], 'buildpro_services_meta_save')) {
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
    $service_title = isset($_POST['buildpro_service_title']) ? sanitize_text_field($_POST['buildpro_service_title']) : '';
    $service_desc = isset($_POST['buildpro_service_desc']) ? sanitize_textarea_field($_POST['buildpro_service_desc']) : '';
    $service_enabled = isset($_POST['buildpro_service_enabled']) ? absint($_POST['buildpro_service_enabled']) : 1;
    $items = isset($_POST['buildpro_service_items']) && is_array($_POST['buildpro_service_items']) ? $_POST['buildpro_service_items'] : array();
    $clean = array();
    foreach ($items as $item) {
        $clean[] = array(
            'icon_id' => isset($item['icon_id']) ? absint($item['icon_id']) : 0,
            'title' => isset($item['title']) ? sanitize_text_field($item['title']) : '',
            'description' => isset($item['description']) ? sanitize_textarea_field($item['description']) : '',
            'link_url' => isset($item['link_url']) ? esc_url_raw($item['link_url']) : '',
            'link_title' => isset($item['link_title']) ? sanitize_text_field($item['link_title']) : '',
            'link_target' => isset($item['link_target']) ? sanitize_text_field($item['link_target']) : '',
        );
    }
    update_post_meta($post_id, 'buildpro_service_title', $service_title);
    update_post_meta($post_id, 'buildpro_service_desc', $service_desc);
    update_post_meta($post_id, 'buildpro_service_items', $clean);
    update_post_meta($post_id, 'buildpro_service_enabled', $service_enabled);
    set_theme_mod('buildpro_service_title', $service_title);
    set_theme_mod('buildpro_service_desc', $service_desc);
    set_theme_mod('buildpro_service_items', $clean);
    set_theme_mod('buildpro_service_enabled', $service_enabled);
}
add_action('save_post_page', 'buildpro_save_services_meta');
