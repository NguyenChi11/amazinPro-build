<?php
if (!function_exists('buildpro_about_core_values_add_meta_box')) {
    function buildpro_about_core_values_add_meta_box($post_type, $post)
    {
        if ($post_type !== 'page') {
            return;
        }
        $template = get_page_template_slug($post->ID);
        if ($template !== 'about-page.php' && $template !== 'about-us-page.php') {
            return;
        }
        add_meta_box(
            'buildpro_about_core_values_meta',
            esc_html__('About Us: Core Values', 'buildpro'),
            'buildpro_about_core_values_render_meta_box',
            'page',
            'normal',
            'default'
        );
    }
} // end if !function_exists buildpro_about_core_values_add_meta_box
add_action('add_meta_boxes', 'buildpro_about_core_values_add_meta_box', 10, 2);

if (!function_exists('buildpro_about_core_values_render_meta_box')) {
    function buildpro_about_core_values_render_meta_box($post)
    {
        wp_nonce_field('buildpro_about_core_values_meta_save', 'buildpro_about_core_values_meta_nonce');
        if (function_exists('buildpro_about_us_admin_print_i18n')) {
            buildpro_about_us_admin_print_i18n();
        }
        $enabled = get_post_meta($post->ID, 'buildpro_about_core_values_enabled', true);
        $enabled = $enabled === '' ? 1 : (int) $enabled;
        $title = get_post_meta($post->ID, 'buildpro_about_core_values_title', true);
        $desc = get_post_meta($post->ID, 'buildpro_about_core_values_description', true);
        $items = get_post_meta($post->ID, 'buildpro_about_core_values_items', true);
        $items = is_array($items) ? array_values($items) : array();
        include get_theme_file_path('template/meta-box/page/about-us/section-core-values/index.php');
    }
} // end if !function_exists buildpro_about_core_values_render_meta_box

if (!function_exists('buildpro_about_core_values_admin_enqueue')) {
    function buildpro_about_core_values_admin_enqueue($hook)
    {
        if ($hook === 'post.php' || $hook === 'post-new.php') {
            wp_enqueue_media();
            wp_enqueue_style(
                'buildpro-about-us-core-values-admin',
                get_theme_file_uri('template/meta-box/page/about-us/section-core-values/style.css'),
                array(),
                null
            );
            wp_enqueue_script(
                'buildpro-about-us-core-values-admin',
                get_theme_file_uri('template/meta-box/page/about-us/section-core-values/script.js'),
                array('jquery'),
                null,
                true
            );
        }
    }
} // end if !function_exists buildpro_about_core_values_admin_enqueue
add_action('admin_enqueue_scripts', 'buildpro_about_core_values_admin_enqueue');

if (!function_exists('buildpro_save_about_core_values_meta')) {
    function buildpro_save_about_core_values_meta($post_id)
    {
        if (!isset($_POST['buildpro_about_core_values_meta_nonce']) || !wp_verify_nonce($_POST['buildpro_about_core_values_meta_nonce'], 'buildpro_about_core_values_meta_save')) {
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
        $enabled = isset($_POST['buildpro_about_core_values_enabled']) ? 1 : 0;
        $title = isset($_POST['buildpro_about_core_values_title']) ? sanitize_text_field(wp_unslash($_POST['buildpro_about_core_values_title'])) : '';
        $desc = isset($_POST['buildpro_about_core_values_description']) ? sanitize_textarea_field(wp_unslash($_POST['buildpro_about_core_values_description'])) : '';
        $items = isset($_POST['buildpro_about_core_values_items']) && is_array($_POST['buildpro_about_core_values_items']) ? $_POST['buildpro_about_core_values_items'] : array();
        $clean_items = array();
        foreach ($items as $it) {
            $clean_items[] = array(
                'icon_id' => isset($it['icon_id']) ? absint($it['icon_id']) : 0,
                'icon_url' => isset($it['icon_url']) ? esc_url_raw($it['icon_url']) : '',
                'icon' => isset($it['icon']) ? sanitize_text_field($it['icon']) : '',
                'title' => isset($it['title']) ? sanitize_text_field($it['title']) : '',
                'description' => isset($it['description']) ? sanitize_textarea_field($it['description']) : '',
                'url' => isset($it['url']) ? esc_url_raw($it['url']) : '',
            );
        }
        update_post_meta($post_id, 'buildpro_about_core_values_enabled', $enabled);
        update_post_meta($post_id, 'buildpro_about_core_values_title', $title);
        update_post_meta($post_id, 'buildpro_about_core_values_description', $desc);
        update_post_meta($post_id, 'buildpro_about_core_values_items', array_values($clean_items));
        set_theme_mod('buildpro_about_core_values_enabled', $enabled);
        set_theme_mod('buildpro_about_core_values_title', $title);
        set_theme_mod('buildpro_about_core_values_description', $desc);
        set_theme_mod('buildpro_about_core_values_items', array_values($clean_items));
    }
} // end if !function_exists buildpro_save_about_core_values_meta
add_action('save_post', 'buildpro_save_about_core_values_meta');
