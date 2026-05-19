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

function buildpro_is_default_wordpress_post($post)
{
    if (!$post instanceof WP_Post || $post->post_type !== 'post') {
        return false;
    }

    $title = strtolower(trim(wp_strip_all_tags((string) $post->post_title)));
    $slug = sanitize_title((string) $post->post_name);
    $content = strtolower(wp_strip_all_tags((string) $post->post_content));

    if ($slug === 'hello-world' || $title === 'hello world!') {
        return true;
    }

    return (
        strpos($content, 'welcome to wordpress') !== false
        && strpos($content, 'this is your first post') !== false
        && strpos($content, 'edit or delete it') !== false
    );
}

function buildpro_delete_default_wordpress_post_on_first_import()
{
    if ((defined('REST_REQUEST') && REST_REQUEST) || (defined('DOING_AJAX') && DOING_AJAX)) {
        return;
    }

    if (get_option('buildpro_default_content_imported') === '1' || get_option('buildpro_wp_default_post_removed') === '1') {
        return;
    }

    $default_posts = get_posts(array(
        'post_type' => 'post',
        'post_status' => array('publish', 'draft', 'pending', 'private', 'future'),
        'posts_per_page' => 5,
        'orderby' => 'ID',
        'order' => 'ASC',
        'suppress_filters' => true,
    ));

    foreach ($default_posts as $post) {
        if (buildpro_is_default_wordpress_post($post)) {
            wp_delete_post((int) $post->ID, true);
        }
    }

    update_option('buildpro_wp_default_post_removed', '1');
}
add_action('init', 'buildpro_delete_default_wordpress_post_on_first_import', 45);

function buildpro_increment_post_views($post_id)
{
    $post_id = absint($post_id);
    if ($post_id <= 0 || get_post_type($post_id) !== 'post') {
        return 0;
    }

    $views = (int) get_post_meta($post_id, 'buildpro_post_views', true);
    $views++;
    update_post_meta($post_id, 'buildpro_post_views', $views);

    return $views;
}

function buildpro_track_single_post_view()
{
    if (is_admin() || is_preview() || is_feed() || is_trackback() || is_robots()) {
        return;
    }

    if ((defined('REST_REQUEST') && REST_REQUEST) || (defined('DOING_AJAX') && DOING_AJAX)) {
        return;
    }

    if (!is_singular('post')) {
        return;
    }

    $post_id = get_queried_object_id();
    if ($post_id > 0) {
        buildpro_increment_post_views($post_id);
    }
}
add_action('wp', 'buildpro_track_single_post_view');

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
