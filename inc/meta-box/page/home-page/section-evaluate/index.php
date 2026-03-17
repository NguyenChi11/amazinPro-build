<?php
if (!function_exists('buildpro_evaluate_add_meta_box')) {
    function buildpro_evaluate_add_meta_box($post_type, $post)
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
            'buildpro_evaluate_meta',
            esc_html__('Evaluate', 'buildpro'),
            'buildpro_evaluate_render_meta_box',
            'page',
            'normal',
            'default'
        );
    }
} // end if !function_exists buildpro_evaluate_add_meta_box
add_action('add_meta_boxes', 'buildpro_evaluate_add_meta_box', 10, 2);

if (!function_exists('buildpro_evaluate_render_meta_box')) {
    function buildpro_evaluate_render_meta_box($post)
    {
        wp_nonce_field('buildpro_evaluate_meta_save', 'buildpro_evaluate_meta_nonce');
        $title = get_post_meta($post->ID, 'buildpro_evaluate_title', true);
        $text = get_post_meta($post->ID, 'buildpro_evaluate_text', true);
        $desc = get_post_meta($post->ID, 'buildpro_evaluate_desc', true);
        $items = get_post_meta($post->ID, 'buildpro_evaluate_items', true);
        $items = is_array($items) ? $items : array();
        $enabled = get_post_meta($post->ID, 'buildpro_evaluate_enabled', true);
        $enabled = $enabled === '' ? 1 : (int) $enabled;
        wp_enqueue_media();
        wp_enqueue_style('buildpro-evaluate-admin', get_theme_file_uri('template/meta-box/page/home/section-evaluate/style.css'), array(), null);
        wp_enqueue_script('buildpro-evaluate-admin', get_theme_file_uri('template/meta-box/page/home/section-evaluate/script.js'), array(), null, true);
        wp_add_inline_script('buildpro-evaluate-admin', 'window.buildproEvaluateState=' . wp_json_encode(array('enabled' => $enabled)) . ';', 'before');
        include get_theme_file_path('template/meta-box/page/home/section-evaluate/index.php');
    }
} // end if !function_exists buildpro_evaluate_render_meta_box

if (!function_exists('buildpro_save_evaluate_meta')) {
    function buildpro_save_evaluate_meta($post_id)
    {
        if (!isset($_POST['buildpro_evaluate_meta_nonce']) || !wp_verify_nonce($_POST['buildpro_evaluate_meta_nonce'], 'buildpro_evaluate_meta_save')) {
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
        $title = isset($_POST['buildpro_evaluate_title']) ? sanitize_text_field($_POST['buildpro_evaluate_title']) : '';
        $text = isset($_POST['buildpro_evaluate_text']) ? sanitize_text_field($_POST['buildpro_evaluate_text']) : '';
        $desc = isset($_POST['buildpro_evaluate_desc']) ? sanitize_textarea_field($_POST['buildpro_evaluate_desc']) : '';
        $items_raw = isset($_POST['buildpro_evaluate_items']) && is_array($_POST['buildpro_evaluate_items']) ? $_POST['buildpro_evaluate_items'] : array();
        $clean = array();
        foreach ($items_raw as $it) {
            $clean[] = array(
                'name' => isset($it['name']) ? sanitize_text_field($it['name']) : '',
                'position' => isset($it['position']) ? sanitize_text_field($it['position']) : '',
                'description' => isset($it['description']) ? sanitize_textarea_field($it['description']) : '',
                'avatar_id' => isset($it['avatar_id']) ? absint($it['avatar_id']) : 0,
            );
        }
        $enabled = isset($_POST['buildpro_evaluate_enabled']) ? absint($_POST['buildpro_evaluate_enabled']) : 1;
        update_post_meta($post_id, 'buildpro_evaluate_title', $title);
        update_post_meta($post_id, 'buildpro_evaluate_text', $text);
        update_post_meta($post_id, 'buildpro_evaluate_desc', $desc);
        update_post_meta($post_id, 'buildpro_evaluate_items', $clean);
        update_post_meta($post_id, 'buildpro_evaluate_enabled', $enabled);
        set_theme_mod('buildpro_evaluate_title', $title);
        set_theme_mod('buildpro_evaluate_text', $text);
        set_theme_mod('buildpro_evaluate_desc', $desc);
        set_theme_mod('buildpro_evaluate_items', $clean);
        set_theme_mod('buildpro_evaluate_data', array(
            'title' => $title,
            'text' => $text,
            'desc' => $desc,
            'items' => $clean,
        ));
        set_theme_mod('buildpro_evaluate_enabled', $enabled);
    }
} // end if !function_exists buildpro_save_evaluate_meta
add_action('save_post_page', 'buildpro_save_evaluate_meta');
