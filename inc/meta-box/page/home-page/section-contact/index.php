<?php

if (!function_exists('buildpro_contact_meta_defaults')) {
    function buildpro_contact_meta_defaults()
    {
        return array(
            'enabled' => 1,
            'title' => __('Get Expert Advice for Your Dream Home', 'buildpro'),
            'description' => __('Leave your email and our construction experts will contact you with personalized solutions.', 'buildpro'),
            'placeholder' => __('Enter your email', 'buildpro'),
            'image_url' => get_theme_file_uri('/assets/images/image_contact.jpg'),
        );
    }
}

function buildpro_contact_add_meta_box($post_type, $post)
{
    if ($post_type !== 'page') {
        return;
    }

    $template = get_page_template_slug($post->ID);
    $front_id = (int) get_option('page_on_front');
    if ($template !== 'home-page.php' && (int) $post->ID !== $front_id) {
        return;
    }

    add_meta_box(
        'buildpro_contact_section_meta',
        esc_html__('Contact', 'buildpro'),
        'buildpro_contact_render_meta_box',
        'page',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'buildpro_contact_add_meta_box', 10, 2);

function buildpro_contact_render_meta_box($post)
{
    $template = get_page_template_slug($post->ID);
    $front_id = (int) get_option('page_on_front');
    if ($template !== 'home-page.php' && (int) $post->ID !== $front_id) {
        return;
    }

    wp_nonce_field('buildpro_contact_meta_save', 'buildpro_contact_meta_nonce');
    wp_enqueue_media();

    $defaults = buildpro_contact_meta_defaults();

    $enabled = get_post_meta($post->ID, 'buildpro_contact_enabled', true);
    $enabled = $enabled === '' ? (int) $defaults['enabled'] : (int) $enabled;

    $contact_title = (string) get_post_meta($post->ID, 'buildpro_contact_title', true);
    if ($contact_title === '') {
        $contact_title = (string) $defaults['title'];
    }

    $contact_description = (string) get_post_meta($post->ID, 'buildpro_contact_description', true);
    if ($contact_description === '') {
        $contact_description = (string) $defaults['description'];
    }

    $contact_placeholder = (string) get_post_meta($post->ID, 'buildpro_contact_placeholder', true);
    if ($contact_placeholder === '') {
        $contact_placeholder = (string) $defaults['placeholder'];
    }

    $contact_image_id = absint(get_post_meta($post->ID, 'buildpro_contact_image_id', true));
    $contact_image_url = '';
    if ($contact_image_id > 0) {
        $contact_image_url = (string) wp_get_attachment_image_url($contact_image_id, 'thumbnail');
    }
    if ($contact_image_url === '') {
        $contact_image_url = (string) get_post_meta($post->ID, 'buildpro_contact_image_url', true);
    }
    if ($contact_image_url === '') {
        $contact_image_url = (string) $defaults['image_url'];
    }

    include get_theme_file_path('template/meta-box/page/home/section-contact/index.php');

    wp_add_inline_script(
        'buildpro-contact-meta-script',
        'window.buildproHomeContactAdminData=' . wp_json_encode(array(
            'enabled' => $enabled,
            'imageId' => $contact_image_id,
            'imageUrl' => $contact_image_url,
            'defaultImageUrl' => $defaults['image_url'],
        )) . ';',
        'before'
    );
}

function buildpro_save_contact_meta($post_id)
{
    if (!isset($_POST['buildpro_contact_meta_nonce']) || !wp_verify_nonce($_POST['buildpro_contact_meta_nonce'], 'buildpro_contact_meta_save')) {
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
    if ($template !== 'home-page.php' && (int) $post_id !== $front_id) {
        return;
    }

    $defaults = buildpro_contact_meta_defaults();

    $enabled = isset($_POST['buildpro_contact_enabled']) ? absint($_POST['buildpro_contact_enabled']) : (int) $defaults['enabled'];
    $enabled = $enabled ? 1 : 0;

    $title = isset($_POST['buildpro_contact_title'])
        ? sanitize_text_field(wp_unslash($_POST['buildpro_contact_title']))
        : (string) $defaults['title'];

    $description = isset($_POST['buildpro_contact_description'])
        ? sanitize_textarea_field(wp_unslash($_POST['buildpro_contact_description']))
        : (string) $defaults['description'];

    $placeholder = isset($_POST['buildpro_contact_placeholder'])
        ? sanitize_text_field(wp_unslash($_POST['buildpro_contact_placeholder']))
        : (string) $defaults['placeholder'];

    $image_id = isset($_POST['buildpro_contact_image_id']) ? absint($_POST['buildpro_contact_image_id']) : 0;
    $image_url = '';
    if ($image_id > 0) {
        $image_url = (string) wp_get_attachment_image_url($image_id, 'full');
    }

    update_post_meta($post_id, 'buildpro_contact_enabled', $enabled);
    update_post_meta($post_id, 'buildpro_contact_title', $title);
    update_post_meta($post_id, 'buildpro_contact_description', $description);
    update_post_meta($post_id, 'buildpro_contact_placeholder', $placeholder);
    update_post_meta($post_id, 'buildpro_contact_image_id', $image_id);
    update_post_meta($post_id, 'buildpro_contact_image_url', $image_url);

    set_theme_mod('buildpro_contact_enabled', $enabled);
    set_theme_mod('buildpro_contact_title', $title);
    set_theme_mod('buildpro_contact_description', $description);
    set_theme_mod('buildpro_contact_placeholder', $placeholder);
    set_theme_mod('buildpro_contact_image_id', $image_id);
    set_theme_mod('buildpro_contact_image_url', $image_url);

    if (function_exists('buildpro_cf7_update_home_form_if_needed')) {
        buildpro_cf7_update_home_form_if_needed();
    }
}
add_action('save_post_page', 'buildpro_save_contact_meta');

function buildpro_contact_admin_enqueue($hook)
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
    if ($template !== 'home-page.php' && (int) $pid !== $front_id) {
        return;
    }

    wp_enqueue_media();

    $base_dir = get_theme_file_path('template/meta-box/page/home/section-contact');
    $base_uri = get_theme_file_uri('template/meta-box/page/home/section-contact');
    $style_ver = file_exists($base_dir . '/style.css') ? filemtime($base_dir . '/style.css') : false;
    $script_ver = file_exists($base_dir . '/script.js') ? filemtime($base_dir . '/script.js') : false;

    wp_enqueue_style('buildpro-contact-meta-style', $base_uri . '/style.css', array(), $style_ver);
    wp_enqueue_script('buildpro-contact-meta-script', $base_uri . '/script.js', array(), $script_ver, true);
}
add_action('admin_enqueue_scripts', 'buildpro_contact_admin_enqueue');
