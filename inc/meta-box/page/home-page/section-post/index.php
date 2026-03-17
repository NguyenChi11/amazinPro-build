<?php
if (!function_exists('buildpro_post_section_add_meta_box')) {
    function buildpro_post_section_add_meta_box($post_type, $post)
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
            'buildpro_post_section_meta',
            esc_html__('Post', 'buildpro'),
            'buildpro_post_section_render_meta_box',
            'page',
            'normal',
            'default'
        );
    }
} // end if !function_exists buildpro_post_section_add_meta_box
add_action('add_meta_boxes', 'buildpro_post_section_add_meta_box', 10, 2);

if (!function_exists('buildpro_post_section_render_meta_box')) {
    function buildpro_post_section_render_meta_box($post)
    {
        wp_nonce_field('buildpro_post_section_meta_save', 'buildpro_post_section_meta_nonce');
        $title = get_post_meta($post->ID, 'title_post', true);
        $desc = get_post_meta($post->ID, 'description_post', true);
        $enabled = get_post_meta($post->ID, 'buildpro_post_enabled', true);
        $enabled = $enabled === '' ? 1 : (int) $enabled;
        wp_enqueue_style('buildpro-post-admin', get_theme_file_uri('template/meta-box/page/home/section-post/style.css'), array(), null);
        wp_enqueue_script('buildpro-post-admin', get_theme_file_uri('template/meta-box/page/home/section-post/script.js'), array(), null, true);
        wp_add_inline_script('buildpro-post-admin', 'window.buildproPostState=' . wp_json_encode(array('enabled' => $enabled)) . ';', 'before');
        include get_theme_file_path('template/meta-box/page/home/section-post/index.php');
    }
} // end if !function_exists buildpro_post_section_render_meta_box

if (!function_exists('buildpro_save_post_section_meta')) {
    function buildpro_save_post_section_meta($post_id)
    {
        if (!isset($_POST['buildpro_post_section_meta_nonce']) || !wp_verify_nonce($_POST['buildpro_post_section_meta_nonce'], 'buildpro_post_section_meta_save')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        $title = isset($_POST['title_post']) ? sanitize_text_field($_POST['title_post']) : '';
        $desc = isset($_POST['description_post']) ? sanitize_textarea_field($_POST['description_post']) : '';
        $enabled = isset($_POST['buildpro_post_enabled']) ? absint($_POST['buildpro_post_enabled']) : 1;
        update_post_meta($post_id, 'title_post', $title);
        update_post_meta($post_id, 'description_post', $desc);
        update_post_meta($post_id, 'buildpro_post_enabled', $enabled);
        set_theme_mod('title_post', $title);
        set_theme_mod('description_post', $desc);
        set_theme_mod('buildpro_post_enabled', $enabled);
    }
} // end if !function_exists buildpro_save_post_section_meta
add_action('save_post_page', 'buildpro_save_post_section_meta');
if (!function_exists('buildpro_enqueue_posts_data_script')) {
    function buildpro_enqueue_posts_data_script()
    {
        if (is_page_template('home-page.php')) {
            wp_enqueue_script('buildpro-posts-data', get_template_directory_uri() . '/assets/data/post-data.js', array(), _S_VERSION, true);
        }
    }
} // end if !function_exists buildpro_enqueue_posts_data_script
add_action('wp_enqueue_scripts', 'buildpro_enqueue_posts_data_script');
