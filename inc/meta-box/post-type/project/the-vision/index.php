<?php
function buildpro_project_the_vision_add_meta_box($post_type, $post)
{
    if ($post_type !== 'project') {
        return;
    }
    add_meta_box('buildpro_project_tab_the_vision', esc_html__('The Vision', 'buildpro'), 'buildpro_project_the_vision_render_meta_box', 'project', 'normal', 'default');
}
add_action('add_meta_boxes', 'buildpro_project_the_vision_add_meta_box', 10, 2);

function buildpro_project_the_vision_render_meta_box($post)
{
    $vision = get_post_meta($post->ID, 'the_vision_project', true);
    echo '<div id="buildpro_project_tab_the_vision" class="buildpro-post-block">';
    ob_start();
    wp_editor($vision, 'buildpro_project_the_vision_editor', array('textarea_name' => 'the_vision_project', 'textarea_rows' => 10, 'media_buttons' => true));
    $editor_html = ob_get_clean();
    echo $editor_html;
    echo '</div>';
}
