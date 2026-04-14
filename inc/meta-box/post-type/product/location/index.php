<?php
function buildpro_product_location_render_meta_box($post)
{
    $location = get_post_meta($post->ID, 'buildpro_product_location', true);

    echo '<div class="buildpro-post-block">';
    echo '<p class="buildpro-post-field"><label>' . esc_html__('Location', 'buildpro') . '</label><input type="text" name="buildpro_product_location" class="regular-text" placeholder="e.g. New York, USA" value="' . esc_attr($location) . '"></p>';
    echo '</div>';
}
