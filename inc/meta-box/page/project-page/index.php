<?php
require get_template_directory() . '/inc/meta-box/page/project-page/section-title/index.php';

function buildpro_project_group_meta_box_add($post_type, $post)
{
    if ($post_type !== 'page') {
        return;
    }
    $template = get_page_template_slug($post->ID);
    if ($template !== 'projects-page.php') {
        return;
    }
    add_meta_box('buildpro_project_group', 'ProjectPage', 'buildpro_project_group_meta_box_render', 'page', 'normal', 'high');
}
add_action('add_meta_boxes', 'buildpro_project_group_meta_box_add', 10, 2);

function buildpro_project_group_meta_box_render($post)
{
    $template = get_page_template_slug($post->ID);
    if ($template !== 'projects-page.php') {
        return;
    }
    // Render tabs navigation
    require get_template_directory() . '/template/meta-box/page/projects/section-tabs/index.php';
    render_projects_page_tabs();
}
