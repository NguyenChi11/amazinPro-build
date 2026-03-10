<?php
function buildpro_project_location_add_meta_box($post_type, $post)
{
    if ($post_type !== 'project') {
        return;
    }
    add_meta_box('buildpro_project_tab_location', 'Location', 'buildpro_project_location_render_meta_box', 'project', 'normal', 'default');
}
add_action('add_meta_boxes', 'buildpro_project_location_add_meta_box', 10, 2);

function buildpro_project_location_render_meta_box($post)
{
    $loc = get_post_meta($post->ID, 'location_project', true);
    echo '<style>
    .buildpro-post-block{background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,0.05);padding:16px;margin-top:8px}
    .buildpro-post-field{margin:10px 0}
    .buildpro-post-block .regular-text{width:100%;max-width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px}
    </style>';
    echo '<div id="buildpro_project_tab_location" class="buildpro-post-block">';
    echo '<p class="buildpro-post-field"><label>Location</label><input type="text" name="location_project" class="regular-text" value="' . esc_attr($loc) . '" placeholder="Location"></p>';
    echo '</div>';
}
