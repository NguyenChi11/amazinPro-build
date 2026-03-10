<?php
function buildpro_about_contact_add_meta_box($post_type, $post)
{
    if ($post_type !== "page") {
        return;
    }
    $template = get_page_template_slug($post->ID);
    if ($template !== "about-page.php" && $template !== "about-us-page.php") {
        return;
    }
    add_meta_box(
        "buildpro_about_contact_meta",
        "About Us : Contact",
        "buildpro_about_contact_render_meta_box",
        'page',
        "normal",
        "default"
    );
}
add_action("add_meta_boxes", "buildpro_about_contact_add_meta_box", 10, 2);

function buildpro_about_contact_render_meta_box($post)
{
    wp_nonce_field("buildpro_about_contact_meta_save", "buildpro_about_contact_meta_nonce");
    $enabled = get_post_meta($post->ID, 'buildpro_about_contact_enabled', true);
    $enabled = $enabled === '' ? 1 : (int) $enabled;
    $title = get_post_meta($post->ID, "buildpro_about_contact_title", true);
    $text = get_post_meta($post->ID, "buildpro_about_contact_text", true);
    $address = get_post_meta($post->ID, "buildpro_about_contact_address", true);
    $phone = get_post_meta($post->ID, "buildpro_about_contact_phone", true);
    $email = get_post_meta($post->ID, "buildpro_about_contact_email", true);

    include get_theme_file_path('template/meta-box/page/about-us/section-contact/index.php');
}


function buildpro_about_contact_admin_enqueue($hook)
{
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        wp_enqueue_media();
        wp_enqueue_style(
            'buildpro-about-us-contact-admin',
            get_theme_file_uri('template/meta-box/page/about-us/section-contact/style.css'),
            array(),
            null
        );
        wp_enqueue_script(
            'buildpro-about-us-contact-admin',
            get_theme_file_uri('template/meta-box/page/about-us/section-contact/script.js'),
            array('jquery'),
            null,
            true
        );
    }
}
add_action('admin_enqueue_scripts', 'buildpro_about_contact_admin_enqueue');

function buildpro_save_about_contact_meta($post_id)
{
    if (!isset($_POST['buildpro_about_contact_meta_nonce']) || !wp_verify_nonce($_POST['buildpro_about_contact_meta_nonce'], 'buildpro_about_contact_meta_save')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    $template = get_page_template_slug($post_id);
    if ($template !== "about-page.php" && $template !== "about-us-page.php") {
        return;
    }
    $enabled = isset($_POST['buildpro_about_contact_enabled']) ? 1 : 0;
    $title = isset($_POST['buildpro_about_contact_title']) ? sanitize_text_field(wp_unslash($_POST['buildpro_about_contact_title'])) : '';
    $text = isset($_POST['buildpro_about_contact_text']) ? sanitize_textarea_field(wp_unslash($_POST['buildpro_about_contact_text'])) : '';
    $address = isset($_POST['buildpro_about_contact_address']) ? sanitize_text_field(wp_unslash($_POST['buildpro_about_contact_address'])) : '';
    $phone = isset($_POST['buildpro_about_contact_phone']) ? sanitize_text_field(wp_unslash($_POST['buildpro_about_contact_phone'])) : '';
    $email = isset($_POST['buildpro_about_contact_email']) ? sanitize_email(wp_unslash($_POST['buildpro_about_contact_email'])) : '';
    $map_image_id = isset($_POST['buildpro_about_contact_form_map_image_id']) ? absint($_POST['buildpro_about_contact_form_map_image_id']) : 0;
    update_post_meta($post_id, 'buildpro_about_contact_enabled', $enabled);
    update_post_meta($post_id, 'buildpro_about_contact_title', $title);
    update_post_meta($post_id, 'buildpro_about_contact_text', $text);
    update_post_meta($post_id, 'buildpro_about_contact_address', $address);
    update_post_meta($post_id, 'buildpro_about_contact_phone', $phone);
    update_post_meta($post_id, 'buildpro_about_contact_email', $email);
    update_post_meta($post_id, 'buildpro_about_contact_form_map_image_id', $map_image_id);
    set_theme_mod('buildpro_about_contact_enabled', $enabled);
    set_theme_mod('buildpro_about_contact_title', $title);
    set_theme_mod('buildpro_about_contact_text', $text);
    set_theme_mod('buildpro_about_contact_address', $address);
    set_theme_mod('buildpro_about_contact_phone', $phone);
    set_theme_mod('buildpro_about_contact_email', $email);
    set_theme_mod('buildpro_about_contact_form_map_image_id', $map_image_id);
}
add_action('save_post', 'buildpro_save_about_contact_meta');
