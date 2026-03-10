<?php
function buildpro_post_desc_add_meta_box($post_type, $post)
{
    if ($post_type !== 'post') {
        return;
    }
    add_meta_box('buildpro_post_tab_desc', 'Description', 'buildpro_post_desc_render_meta_box', 'post', 'normal', 'default');
}
add_action('add_meta_boxes', 'buildpro_post_desc_add_meta_box', 10, 2);

function buildpro_post_desc_render_meta_box($post)
{
    $post_desc = get_post_meta($post->ID, 'buildpro_post_description', true);
    echo '<style>
    .buildpro-post-block{background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,0.05);padding:16px;margin-top:8px}
    .buildpro-post-field{margin:10px 0}
    .buildpro-post-block .large-text{width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px}
    </style>';
    echo '<div class="buildpro-post-block">';
    echo '<p class="buildpro-post-field"><label>Description</label><textarea name="buildpro_post_description" rows="5" class="large-text">' . esc_textarea($post_desc) . '</textarea></p>';
    echo '</div>';
}
