<?php
function buildpro_projects_title_add_meta_box($post_type, $post)
{
    if ($post_type !== 'page') {
        return;
    }
    $template = get_page_template_slug($post->ID);
    if ($template !== 'projects-page.php') {
        return;
    }
    add_meta_box(
        'buildpro_projects_title_meta',
        'Projects Title',
        'buildpro_projects_title_render_meta_box',
        'page',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'buildpro_projects_title_add_meta_box', 10, 2);
function buildpro_projects_title_render_meta_box($post)
{
    wp_nonce_field('buildpro_projects_title_meta_save', 'buildpro_projects_title_meta_nonce');
    $title = get_post_meta($post->ID, 'projects_title', true);
    $desc = get_post_meta($post->ID, 'projects_description', true);
    wp_enqueue_style('buildpro-projects-title-admin', get_theme_file_uri('template/meta-box/page/projects/section-title/style.css'), array(), null);
    wp_enqueue_script('buildpro-projects-title-admin', get_theme_file_uri('template/meta-box/page/projects/section-title/script.js'), array(), null, true);
    wp_add_inline_script('buildpro-projects-title-admin', 'window.buildproProjectsTitleState=' . wp_json_encode(array()) . ';', 'before');
    include get_theme_file_path('template/meta-box/page/projects/section-title/index.php');
}
function buildpro_projects_title_save_meta($post_id)
{
    if (!isset($_POST['buildpro_projects_title_meta_nonce']) || !wp_verify_nonce($_POST['buildpro_projects_title_meta_nonce'], 'buildpro_projects_title_meta_save')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    $title = isset($_POST['projects_title']) ? sanitize_text_field($_POST['projects_title']) : '';
    $desc = isset($_POST['projects_description']) ? sanitize_textarea_field($_POST['projects_description']) : '';
    update_post_meta($post_id, 'projects_title', $title);
    update_post_meta($post_id, 'projects_description', $desc);
    set_theme_mod('projects_title', $title);
    set_theme_mod('projects_description', $desc);
}
add_action('save_post_page', 'buildpro_projects_title_save_meta');
