<?php
function buildpro_project_overview_add_meta_box($post_type, $post)
{
    if ($post_type !== 'project') {
        return;
    }
    add_meta_box('buildpro_project_tab_overview', esc_html__('Project Overview', 'buildpro'), 'buildpro_project_overview_render_meta_box', 'project', 'normal', 'default');
}
add_action('add_meta_boxes', 'buildpro_project_overview_add_meta_box', 10, 2);

function buildpro_project_overview_render_meta_box($post)
{
    $overview = get_post_meta($post->ID, 'project_overview_project', true);
    echo '<div id="buildpro_project_tab_overview" class="buildpro-post-block">';
    ob_start();
    wp_editor($overview, 'buildpro_project_overview_editor', array('textarea_name' => 'project_overview_project', 'textarea_rows' => 10, 'media_buttons' => true));
    $editor_html = ob_get_clean();
    echo $editor_html;
    echo '</div>';
}
