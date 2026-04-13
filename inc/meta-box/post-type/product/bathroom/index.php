<?php
function buildpro_product_bathroom_render_meta_box($post)
{
    $bathrooms = get_post_meta($post->ID, 'buildpro_product_bathrooms', true);

    echo '<div class="buildpro-post-block">';
    echo '<p class="buildpro-post-field"><label>' . esc_html__('Bathroom', 'buildpro') . '</label><input type="number" min="0" step="0.5" name="buildpro_product_bathrooms" class="regular-text" value="' . esc_attr($bathrooms) . '"></p>';
    echo '</div>';
}
