<?php

/**
 * Cart page – AJAX handler for applying WooCommerce coupons.
 */
function buildpro_apply_coupon()
{
    check_ajax_referer('woocommerce-cart', 'nonce');

    if (!class_exists('WooCommerce') || !WC()->cart) {
        wp_send_json_error(['message' => 'WooCommerce is not active.']);
    }

    $coupon_code = isset($_POST['coupon_code']) ? sanitize_text_field(wp_unslash($_POST['coupon_code'])) : '';

    if (empty($coupon_code)) {
        wp_send_json_error(['message' => 'Please enter a coupon code.']);
    }

    if (WC()->cart->has_discount($coupon_code)) {
        wp_send_json_error(['message' => 'This coupon has already been applied.']);
    }

    $result = WC()->cart->apply_coupon($coupon_code);

    if ($result) {
        WC()->cart->calculate_totals();
        $discount = floatval(WC()->cart->get_discount_total());
        wp_send_json_success([
            'message'  => 'Coupon applied successfully!',
            'discount' => $discount,
        ]);
    } else {
        $notices = wc_get_notices('error');
        $message = '';
        if (!empty($notices)) {
            $message = wp_strip_all_tags(is_array($notices[0]) ? $notices[0]['notice'] : $notices[0]);
        }
        wc_clear_notices();
        wp_send_json_error(['message' => $message ?: 'Invalid coupon code.']);
    }
}
add_action('wp_ajax_buildpro_apply_coupon',        'buildpro_apply_coupon');
add_action('wp_ajax_nopriv_buildpro_apply_coupon', 'buildpro_apply_coupon');

/* ---- Order Note: Save ---- */
add_action('wp_ajax_buildpro_save_order_note',        'buildpro_save_order_note');
add_action('wp_ajax_nopriv_buildpro_save_order_note', 'buildpro_save_order_note');
function buildpro_save_order_note()
{
    check_ajax_referer('buildpro_mini_cart', 'nonce');
    if (!function_exists('WC') || !WC()->cart) {
        wp_send_json_error(['message' => 'WC not available']);
    }
    $note = isset($_POST['note']) ? sanitize_textarea_field(wp_unslash($_POST['note'])) : '';
    WC()->session->set('order_comments', $note);
    wp_send_json_success(['note' => $note, 'nonce' => wp_create_nonce('buildpro_mini_cart')]);
}

/* ---- Order Note: Delete ---- */
add_action('wp_ajax_buildpro_delete_order_note',        'buildpro_delete_order_note');
add_action('wp_ajax_nopriv_buildpro_delete_order_note', 'buildpro_delete_order_note');
function buildpro_delete_order_note()
{
    check_ajax_referer('buildpro_mini_cart', 'nonce');
    if (!function_exists('WC') || !WC()->cart) {
        wp_send_json_error(['message' => 'WC not available']);
    }
    WC()->session->set('order_comments', '');
    wp_send_json_success(['nonce' => wp_create_nonce('buildpro_mini_cart')]);
}
