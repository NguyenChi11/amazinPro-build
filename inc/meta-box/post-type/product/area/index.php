<?php
function buildpro_product_area_render_meta_box($post)
{
    $area = get_post_meta($post->ID, 'buildpro_product_area', true);

    echo '<div class="buildpro-post-block">';
    echo '<p class="buildpro-post-field"><label>' . esc_html__('Area (sq ft)', 'buildpro') . '</label><input type="number" min="0" step="0.01" name="buildpro_product_area" class="regular-text" placeholder="e.g. 1200" value="' . esc_attr($area) . '"></p>';
    echo '</div>';
}
