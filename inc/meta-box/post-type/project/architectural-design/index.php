<?php
function buildpro_project_architectural_design_add_meta_box($post_type, $post)
{
    if ($post_type !== 'project') {
        return;
    }
    add_meta_box('buildpro_project_tab_architectural_design', esc_html__('Architectural Design', 'buildpro'), 'buildpro_project_architectural_design_render_meta_box', 'project', 'normal', 'default');
}
add_action('add_meta_boxes', 'buildpro_project_architectural_design_add_meta_box', 10, 2);

function buildpro_project_architectural_design_render_meta_box($post)
{
    $design = get_post_meta($post->ID, 'architectural_design_project', true);
    echo '<div id="buildpro_project_tab_architectural_design" class="buildpro-post-block">';
    ob_start();
    wp_editor($design, 'buildpro_project_architectural_design_editor', array('textarea_name' => 'architectural_design_project', 'textarea_rows' => 10, 'media_buttons' => true));
    $editor_html = ob_get_clean();
    echo $editor_html;
    echo '</div>';
}
