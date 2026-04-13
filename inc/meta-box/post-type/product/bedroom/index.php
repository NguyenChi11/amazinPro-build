<?php
function buildpro_product_bedroom_render_meta_box($post)
{
    $bedrooms = get_post_meta($post->ID, 'buildpro_product_bedrooms', true);

    echo '<div class="buildpro-post-block">';
    echo '<p class="buildpro-post-field"><label>' . esc_html__('Bedroom', 'buildpro') . '</label><input type="number" min="0" step="1" name="buildpro_product_bedrooms" class="regular-text" value="' . esc_attr($bedrooms) . '"></p>';
    echo '</div>';
}
