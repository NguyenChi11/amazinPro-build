<?php
function buildpro_about_policy_add_meta_box($post_type, $post)
{
    if ($post_type !== 'page') {
        return;
    }
    $template = get_page_template_slug($post->ID);
    if ($template !== 'about-page.php' && $template !== 'about-us-page.php') {
        return;
    }
    add_meta_box(
        'buildpro_about_policy_meta',
        esc_html__('About Us: Policy', 'buildpro'),
        'buildpro_about_policy_render_meta_box',
        'page',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'buildpro_about_policy_add_meta_box', 10, 2);

function buildpro_about_policy_render_meta_box($post)
{
    wp_nonce_field('buildpro_about_policy_meta_save', 'buildpro_about_policy_meta_nonce');
    if (function_exists('buildpro_about_us_admin_print_i18n')) {
        buildpro_about_us_admin_print_i18n();
    }
    $enabled = get_post_meta($post->ID, 'buildpro_about_policy_enabled', true);
    $enabled = $enabled === '' ? 1 : (int)$enabled;
    $title_left = get_post_meta($post->ID, 'buildpro_about_policy_title_left', true);
    $business_registration = get_post_meta($post->ID, 'buildpro_about_policy_business_registration', true);
    $general_contractor = get_post_meta($post->ID, 'buildpro_about_policy_general_contractor', true);
    $duns_number = get_post_meta($post->ID, 'buildpro_about_policy_duns_number', true);
    $cert_image_id = (int) get_post_meta($post->ID, 'buildpro_about_policy_certification_image_id', true);
    $cert_image_url = $cert_image_id ? wp_get_attachment_image_url($cert_image_id, 'thumbnail') : '';
    $cert_url = get_post_meta($post->ID, 'buildpro_about_policy_certification_url', true);
    $cert_title = get_post_meta($post->ID, 'buildpro_about_policy_certification_title', true);
    $cert_desc = get_post_meta($post->ID, 'buildpro_about_policy_certification_desc', true);
    $certifications = get_post_meta($post->ID, 'buildpro_about_policy_certifications', true);
    $certifications = is_array($certifications) ? array_values($certifications) : array();
    $title_right = get_post_meta($post->ID, 'buildpro_about_policy_title_right', true);
    $warranty_desc = get_post_meta($post->ID, 'buildpro_about_policy_warranty_desc', true);
    $items = get_post_meta($post->ID, 'buildpro_about_policy_items', true);
    $items = is_array($items) ? array_values($items) : array();
    include get_theme_file_path('template/meta-box/page/about-us/section-policy/index.php');
}

function buildpro_about_policy_admin_enqueue($hook)
{
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        wp_enqueue_media();
        wp_enqueue_style(
            'buildpro-about-us-policy-admin',
            get_theme_file_uri('template/meta-box/page/about-us/section-policy/style.css'),
            array(),
            null
        );
        wp_enqueue_script(
            'buildpro-about-us-policy-admin',
            get_theme_file_uri('template/meta-box/page/about-us/section-policy/script.js'),
            array('jquery'),
            null,
            true
        );
    }
}
add_action('admin_enqueue_scripts', 'buildpro_about_policy_admin_enqueue');

function buildpro_save_about_policy_meta($post_id)
{
    if (!isset($_POST['buildpro_about_policy_meta_nonce']) || !wp_verify_nonce($_POST['buildpro_about_policy_meta_nonce'], 'buildpro_about_policy_meta_save')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    $template = get_page_template_slug($post_id);
    if ($template !== 'about-page.php' && $template !== 'about-us-page.php') {
        return;
    }
    $enabled = isset($_POST['buildpro_about_policy_enabled']) ? 1 : 0;
    $title_left = isset($_POST['buildpro_about_policy_title_left']) ? sanitize_text_field(wp_unslash($_POST['buildpro_about_policy_title_left'])) : '';
    $business_registration = isset($_POST['buildpro_about_policy_business_registration']) ? sanitize_text_field(wp_unslash($_POST['buildpro_about_policy_business_registration'])) : '';
    $general_contractor = isset($_POST['buildpro_about_policy_general_contractor']) ? sanitize_text_field(wp_unslash($_POST['buildpro_about_policy_general_contractor'])) : '';
    $duns_number = isset($_POST['buildpro_about_policy_duns_number']) ? sanitize_text_field(wp_unslash($_POST['buildpro_about_policy_duns_number'])) : '';
    $cert_image_id = isset($_POST['buildpro_about_policy_certification_image_id']) ? absint($_POST['buildpro_about_policy_certification_image_id']) : 0;
    $cert_url = isset($_POST['buildpro_about_policy_certification_url']) ? esc_url_raw($_POST['buildpro_about_policy_certification_url']) : '';
    $cert_title = isset($_POST['buildpro_about_policy_certification_title']) ? sanitize_text_field(wp_unslash($_POST['buildpro_about_policy_certification_title'])) : '';
    $cert_desc = isset($_POST['buildpro_about_policy_certification_desc']) ? sanitize_textarea_field(wp_unslash($_POST['buildpro_about_policy_certification_desc'])) : '';
    $title_right = isset($_POST['buildpro_about_policy_title_right']) ? sanitize_text_field(wp_unslash($_POST['buildpro_about_policy_title_right'])) : '';
    $warranty_desc = isset($_POST['buildpro_about_policy_warranty_desc']) ? sanitize_textarea_field(wp_unslash($_POST['buildpro_about_policy_warranty_desc'])) : '';
    $items = isset($_POST['buildpro_about_policy_items']) && is_array($_POST['buildpro_about_policy_items']) ? $_POST['buildpro_about_policy_items'] : array();
    $clean_items = array();
    foreach ($items as $it) {
        $clean_items[] = array(
            'icon_id' => isset($it['icon_id']) ? absint($it['icon_id']) : 0,
            'icon_url' => isset($it['icon_url']) ? esc_url_raw($it['icon_url']) : '',
            'title' => isset($it['title']) ? sanitize_text_field($it['title']) : '',
            'desc' => isset($it['desc']) ? sanitize_textarea_field($it['desc']) : '',
        );
    }
    update_post_meta($post_id, 'buildpro_about_policy_enabled', $enabled);
    update_post_meta($post_id, 'buildpro_about_policy_title_left', $title_left);
    update_post_meta($post_id, 'buildpro_about_policy_business_registration', $business_registration);
    update_post_meta($post_id, 'buildpro_about_policy_general_contractor', $general_contractor);
    update_post_meta($post_id, 'buildpro_about_policy_duns_number', $duns_number);
    update_post_meta($post_id, 'buildpro_about_policy_certification_image_id', $cert_image_id);
    update_post_meta($post_id, 'buildpro_about_policy_certification_url', $cert_url);
    update_post_meta($post_id, 'buildpro_about_policy_certification_title', $cert_title);
    update_post_meta($post_id, 'buildpro_about_policy_certification_desc', $cert_desc);
    update_post_meta($post_id, 'buildpro_about_policy_title_right', $title_right);
    update_post_meta($post_id, 'buildpro_about_policy_warranty_desc', $warranty_desc);
    $certs = isset($_POST['buildpro_about_policy_certifications']) && is_array($_POST['buildpro_about_policy_certifications']) ? $_POST['buildpro_about_policy_certifications'] : array();
    $clean_certs = array();
    foreach ($certs as $c) {
        $clean_certs[] = array(
            'image_id' => isset($c['image_id']) ? absint($c['image_id']) : 0,
            'image_url' => isset($c['image_url']) ? esc_url_raw($c['image_url']) : '',
            'url' => isset($c['url']) ? esc_url_raw($c['url']) : '',
            'title' => isset($c['title']) ? sanitize_text_field($c['title']) : '',
            'desc' => isset($c['desc']) ? sanitize_textarea_field($c['desc']) : '',
        );
    }
    update_post_meta($post_id, 'buildpro_about_policy_items', array_values($clean_items));
    update_post_meta($post_id, 'buildpro_about_policy_certifications', array_values($clean_certs));
    // Sync to theme_mod
    set_theme_mod('buildpro_about_policy_enabled', $enabled);
    set_theme_mod('buildpro_about_policy_title_left', $title_left);
    set_theme_mod('buildpro_about_policy_business_registration', $business_registration);
    set_theme_mod('buildpro_about_policy_general_contractor', $general_contractor);
    set_theme_mod('buildpro_about_policy_duns_number', $duns_number);
    set_theme_mod('buildpro_about_policy_title_right', $title_right);
    set_theme_mod('buildpro_about_policy_warranty_desc', $warranty_desc);
    set_theme_mod('buildpro_about_policy_items', array_values($clean_items));
    set_theme_mod('buildpro_about_policy_certifications', array_values($clean_certs));
}
add_action('save_post', 'buildpro_save_about_policy_meta');
