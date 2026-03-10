<?php
if (!function_exists('buildpro_about_banner_add_meta_box')) {
    function buildpro_about_banner_add_meta_box($post_type, $post)
    {
        if ($post_type !== 'page') {
            return;
        }
        $template = get_page_template_slug($post->ID);
        if ($template !== 'about-us-page.php' && $template !== 'about-page.php') {
            return;
        }
        add_meta_box(
            'buildpro_about_banner_meta',
            'About Us: Banner',
            'buildpro_about_banner_render_meta_box',
            'page',
            'normal',
            'default'
        );
    }
} // end if !function_exists buildpro_about_banner_add_meta_box
add_action('add_meta_boxes', 'buildpro_about_banner_add_meta_box', 10, 2);

if (!function_exists('buildpro_about_banner_render_meta_box')) {
    function buildpro_about_banner_render_meta_box($post)
    {
        wp_nonce_field('buildpro_about_banner_meta_save', 'buildpro_about_banner_meta_nonce');
        wp_enqueue_media();
        $enabled = get_post_meta($post->ID, 'buildpro_about_banner_enabled', true);
        $text = get_post_meta($post->ID, 'buildpro_about_banner_text', true);
        $title = get_post_meta($post->ID, 'buildpro_about_banner_title', true);
        $desc = get_post_meta($post->ID, 'buildpro_about_banner_description', true);
        $facts = get_post_meta($post->ID, 'buildpro_about_banner_facts', true);
        $image_id = (int) get_post_meta($post->ID, 'buildpro_about_banner_image_id', true);
        $enabled = $enabled === '' ? 1 : (int) $enabled;
        $facts = is_array($facts) ? array_values($facts) : array();
        $thumb = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';
        include get_theme_file_path('template/meta-box/page/about-us/section-banner/index.php');
    }
} // end if !function_exists buildpro_about_banner_render_meta_box

if (!function_exists('buildpro_about_banner_admin_enqueue')) {
    function buildpro_about_banner_admin_enqueue($hook)
    {
        if ($hook === 'post.php' || $hook === 'post-new.php') {
            wp_enqueue_media();
            wp_enqueue_style(
                'buildpro-about-us-banner-admin',
                get_theme_file_uri('template/meta-box/page/about-us/section-banner/style.css'),
                array(),
                null
            );
            wp_enqueue_script(
                'buildpro-about-us-banner-admin',
                get_theme_file_uri('template/meta-box/page/about-us/section-banner/script.js'),
                array('jquery'),
                null,
                true
            );
        }
    }
} // end if !function_exists buildpro_about_banner_admin_enqueue
add_action('admin_enqueue_scripts', 'buildpro_about_banner_admin_enqueue');

if (!function_exists('buildpro_save_about_banner_meta')) {
    function buildpro_save_about_banner_meta($post_id)
    {
        if (!isset($_POST['buildpro_about_banner_meta_nonce']) || !wp_verify_nonce($_POST['buildpro_about_banner_meta_nonce'], 'buildpro_about_banner_meta_save')) {
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
        $enabled = isset($_POST['buildpro_about_banner_enabled']) ? 1 : 0;
        $text = isset($_POST['buildpro_about_banner_text']) ? sanitize_text_field(wp_unslash($_POST['buildpro_about_banner_text'])) : '';
        $title = isset($_POST['buildpro_about_banner_title']) ? sanitize_text_field(wp_unslash($_POST['buildpro_about_banner_title'])) : '';
        $desc = isset($_POST['buildpro_about_banner_description']) ? sanitize_textarea_field(wp_unslash($_POST['buildpro_about_banner_description'])) : '';
        $facts = isset($_POST['buildpro_about_banner_facts']) && is_array($_POST['buildpro_about_banner_facts']) ? $_POST['buildpro_about_banner_facts'] : array();
        $clean_facts = array();
        foreach ($facts as $f) {
            $clean_facts[] = array(
                'label' => isset($f['label']) ? sanitize_text_field($f['label']) : '',
                'value' => isset($f['value']) ? sanitize_text_field($f['value']) : '',
            );
        }
        $clean_facts = array_values(array_slice($clean_facts, 0, 4));
        $image_id = isset($_POST['buildpro_about_banner_image_id']) ? absint($_POST['buildpro_about_banner_image_id']) : 0;
        update_post_meta($post_id, 'buildpro_about_banner_enabled', $enabled);
        update_post_meta($post_id, 'buildpro_about_banner_text', $text);
        update_post_meta($post_id, 'buildpro_about_banner_title', $title);
        update_post_meta($post_id, 'buildpro_about_banner_description', $desc);
        update_post_meta($post_id, 'buildpro_about_banner_facts', $clean_facts);
        update_post_meta($post_id, 'buildpro_about_banner_image_id', $image_id);
        set_theme_mod('buildpro_about_banner_enabled', $enabled);
        set_theme_mod('buildpro_about_banner_text', $text);
        set_theme_mod('buildpro_about_banner_title', $title);
        set_theme_mod('buildpro_about_banner_description', $desc);
        set_theme_mod('buildpro_about_banner_facts', $clean_facts);
        set_theme_mod('buildpro_about_banner_image_id', $image_id);
    }
} // end if !function_exists buildpro_save_about_banner_meta
add_action('save_post_page', 'buildpro_save_about_banner_meta');
