<?php
function buildpro_post_paragraph_add_meta_box($post_type, $post)
{
    if ($post_type !== 'post') {
        return;
    }
    add_meta_box('buildpro_post_tab_paragraph', 'Paragraph', 'buildpro_post_paragraph_render_meta_box', 'post', 'normal', 'default');
}
add_action('add_meta_boxes', 'buildpro_post_paragraph_add_meta_box', 10, 2);

function buildpro_post_paragraph_render_meta_box($post)
{
    $paragraph = get_post_meta($post->ID, 'buildpro_post_paragraph', true);
    echo '<div class="buildpro-post-block">';
    ob_start();
    wp_editor($paragraph, 'buildpro_post_paragraph_editor', array('textarea_name' => 'buildpro_post_paragraph', 'textarea_rows' => 8, 'media_buttons' => true));
    $editor_html = ob_get_clean();
    echo $editor_html;
    echo '</div>';
}
