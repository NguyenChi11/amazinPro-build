<?php
function buildpro_project_infomations_add_meta_box($post_type, $post)
{
    if ($post_type !== 'project') {
        return;
    }
    add_meta_box(
        'buildpro_project_tab_infomations',
        esc_html__('Information', 'buildpro'),
        'buildpro_project_infomations_render_meta_box',
        'project',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'buildpro_project_infomations_add_meta_box', 10, 2);

function buildpro_project_infomations_render_meta_box($post)
{
    $total_area = get_post_meta($post->ID, 'total_area_project', true);
    $completion = get_post_meta($post->ID, 'completion_project', true);
    $arch_style = get_post_meta($post->ID, 'architectural_style_project', true);
    echo '<style>
    .buildpro-post-block{background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,0.05);padding:16px;margin-top:8px}
    .buildpro-post-field{margin:10px 0}
    .buildpro-post-block .regular-text{width:100%;max-width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px}
    </style>';
    echo '<div id="buildpro_project_tab_infomations" class="buildpro-post-block">';
    echo '<p class="buildpro-post-field"><label>' . esc_html__('Total Area', 'buildpro') . '</label><input type="text" name="total_area_project" class="regular-text" value="' . esc_attr($total_area) . '" placeholder="e.g. 1,200 m²"></p>';
    echo '<p class="buildpro-post-field"><label>' . esc_html__('Completion', 'buildpro') . '</label><input type="text" name="completion_project" class="regular-text" value="' . esc_attr($completion) . '" placeholder="e.g. 2025-08"></p>';
    echo '<p class="buildpro-post-field"><label>' . esc_html__('Architectural Style', 'buildpro') . '</label><input type="text" name="architectural_style_project" class="regular-text" value="' . esc_attr($arch_style) . '" placeholder="e.g. Modern Minimalist"></p>';
    echo '</div>';
}
