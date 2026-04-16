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

    $text_fields = array(
        'buildpro_product_bedrooms',
        'buildpro_product_bathrooms',
        'buildpro_product_area',
        'buildpro_product_location',
        'typical_range',
        'buildpro_product_lot_size',
        'buildpro_product_garage',
        'buildpro_product_year_built',
        'buildpro_product_floors',
    );

    foreach ($text_fields as $field_key) {
        if (!isset($_POST[$field_key])) {
            continue;
        }
        $value = sanitize_text_field(wp_unslash($_POST[$field_key]));
        update_post_meta($post_id, $field_key, $value);
    }

    $textarea_fields = array(
        'buildpro_product_overview',
    );

    foreach ($textarea_fields as $field_key) {
        if (!isset($_POST[$field_key])) {
            continue;
        }
        $value = sanitize_textarea_field(wp_unslash($_POST[$field_key]));
        update_post_meta($post_id, $field_key, $value);
    }

    $list_fields = array(
        'buildpro_product_features_items' => 'buildpro_product_features',
        'buildpro_product_interior_features_items' => 'buildpro_product_interior_features',
    );

    foreach ($list_fields as $request_key => $meta_key) {
        if (!isset($_POST[$request_key])) {
            continue;
        }

        $raw_items = wp_unslash($_POST[$request_key]);
        if (!is_array($raw_items)) {
            $raw_items = array($raw_items);
        }

        $clean_items = array();
        foreach ($raw_items as $item) {
            $item = sanitize_text_field($item);
            if ($item !== '') {
                $clean_items[] = $item;
            }
        }

        update_post_meta($post_id, $meta_key, implode("\n", $clean_items));
    }
}
add_action('save_post_product', 'buildpro_save_product_meta');
