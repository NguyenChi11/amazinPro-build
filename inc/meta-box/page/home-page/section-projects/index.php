<?php
if (!function_exists('buildpro_portfolio_add_meta_box')) {
    function buildpro_portfolio_add_meta_box($post_type, $post)
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
            'buildpro_portfolio_meta',
            esc_html__('Projects', 'buildpro'),
            'buildpro_portfolio_render_meta_box',
            'page',
            'normal',
            'default'
        );
    }
} // end if !function_exists buildpro_portfolio_add_meta_box
add_action('add_meta_boxes', 'buildpro_portfolio_add_meta_box', 10, 2);

if (!function_exists('buildpro_portfolio_render_meta_box')) {
    function buildpro_portfolio_render_meta_box($post)
    {
        wp_nonce_field('buildpro_portfolio_meta_save', 'buildpro_portfolio_meta_nonce');
        $title = get_post_meta($post->ID, 'projects_title', true);
        $desc = get_post_meta($post->ID, 'projects_description', true);
        $view_all_text = get_post_meta($post->ID, 'projects_view_all_text', true);
        $enabled = get_post_meta($post->ID, 'buildpro_portfolio_enabled', true);
        $enabled = $enabled === '' ? 1 : (int)$enabled;
        wp_enqueue_style('buildpro-portfolio-admin', get_theme_file_uri('template/meta-box/page/home/section-projects/style.css'), array(), null);
        wp_enqueue_script('buildpro-portfolio-admin', get_theme_file_uri('template/meta-box/page/home/section-projects/script.js'), array(), null, true);
        wp_add_inline_script('buildpro-portfolio-admin', 'window.buildproPortfolioState=' . wp_json_encode(array('enabled' => $enabled)) . ';', 'before');
        include get_theme_file_path('template/meta-box/page/home/section-projects/index.php');
    }
} // end if !function_exists buildpro_portfolio_render_meta_box

if (!function_exists('buildpro_save_portfolio_meta')) {
    function buildpro_save_portfolio_meta($post_id)
    {
        if (!isset($_POST['buildpro_portfolio_meta_nonce']) || !wp_verify_nonce($_POST['buildpro_portfolio_meta_nonce'], 'buildpro_portfolio_meta_save')) {
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
        $title = isset($_POST['projects_title']) ? sanitize_text_field($_POST['projects_title']) : '';
        $desc = isset($_POST['projects_description']) ? sanitize_textarea_field($_POST['projects_description']) : '';
        $view_all_text = isset($_POST['projects_view_all_text']) ? sanitize_text_field($_POST['projects_view_all_text']) : '';
        $enabled = isset($_POST['buildpro_portfolio_enabled']) ? absint($_POST['buildpro_portfolio_enabled']) : 1;
        update_post_meta($post_id, 'projects_title', $title);
        update_post_meta($post_id, 'projects_description', $desc);
        update_post_meta($post_id, 'projects_view_all_text', $view_all_text);
        update_post_meta($post_id, 'buildpro_portfolio_enabled', $enabled);
        set_theme_mod('projects_title', $title);
        set_theme_mod('projects_description', $desc);
        set_theme_mod('projects_view_all_text', $view_all_text);
        set_theme_mod('buildpro_portfolio_enabled', $enabled);
    }
} // end if !function_exists buildpro_save_portfolio_meta
add_action('save_post_page', 'buildpro_save_portfolio_meta');
