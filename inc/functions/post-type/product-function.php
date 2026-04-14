<?php
function buildpro_save_product_meta($post_id)
{
    if (!isset($_POST['buildpro_product_meta_nonce']) || !wp_verify_nonce($_POST['buildpro_product_meta_nonce'], 'buildpro_product_meta_save')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (get_post_type($post_id) !== 'product') {
        return;
    }

    $bedrooms = isset($_POST['buildpro_product_bedrooms']) ? sanitize_text_field($_POST['buildpro_product_bedrooms']) : '';
    $bathrooms = isset($_POST['buildpro_product_bathrooms']) ? sanitize_text_field($_POST['buildpro_product_bathrooms']) : '';
    $area = isset($_POST['buildpro_product_area']) ? sanitize_text_field($_POST['buildpro_product_area']) : '';
    $location = isset($_POST['buildpro_product_location']) ? sanitize_text_field($_POST['buildpro_product_location']) : '';

    update_post_meta($post_id, 'buildpro_product_bedrooms', $bedrooms);
    update_post_meta($post_id, 'buildpro_product_bathrooms', $bathrooms);
    update_post_meta($post_id, 'buildpro_product_area', $area);
    update_post_meta($post_id, 'buildpro_product_location', $location);
}
add_action('save_post_product', 'buildpro_save_product_meta');
