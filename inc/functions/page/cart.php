<?php

if (!function_exists('buildpro_cart_get_page_data')) {
    function buildpro_cart_get_page_data()
    {
        $wc_active = function_exists('WC') && WC()->cart;
        $cart_items = $wc_active ? WC()->cart->get_cart() : [];

        $_checkout_pages = get_pages([
            'meta_key'   => '_wp_page_template',
            'meta_value' => 'checkout-page.php',
            'number'     => 1,
        ]);
        $checkout_url = !empty($_checkout_pages)
            ? get_permalink($_checkout_pages[0]->ID)
            : (function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/checkout/'));

        $_products_pages = get_pages([
            'meta_key'   => '_wp_page_template',
            'meta_value' => 'products-page.php',
            'number'     => 1,
        ]);
        $products_page_url = !empty($_products_pages)
            ? get_permalink($_products_pages[0]->ID)
            : ($wc_active ? get_permalink(wc_get_page_id('shop')) : home_url('/products/'));

        $shipping_cost = 120.00;
        $tax_rate = 0.08;
        $subtotal_raw = 0.0;
        foreach ($cart_items as $item) {
            $subtotal_raw += floatval($item['data']->get_price()) * intval($item['quantity']);
        }

        $wc_discount = 0.0;
        if ($wc_active) {
            $wc_discount = floatval(WC()->cart->get_discount_total());
            $wc_shipping = floatval(WC()->cart->get_shipping_total());
            if ($wc_shipping > 0) {
                $shipping_cost = $wc_shipping;
            }
        }

        $tax_base = $subtotal_raw + $shipping_cost - $wc_discount;
        $tax_amount = $tax_base * $tax_rate;
        $total = $tax_base + $tax_amount;

        $summary_regular = 0.0;
        $summary_sale = 0.0;
        foreach ($cart_items as $ci) {
            $cp = $ci['data'];
            $qty = intval($ci['quantity']);
            $summary_regular += floatval($cp->get_regular_price()) * $qty;
            $summary_sale += floatval($cp->get_price()) * $qty;
        }

        return [
            'wc_active' => $wc_active,
            'cart_items' => $cart_items,
            'checkout_url' => $checkout_url,
            'products_page_url' => $products_page_url,
            'mini_nonce' => wp_create_nonce('buildpro_mini_cart'),
            'cart_nonce' => wp_create_nonce('woocommerce-cart'),
            'shipping_cost' => $shipping_cost,
            'tax_rate' => $tax_rate,
            'subtotal_raw' => $subtotal_raw,
            'wc_discount' => $wc_discount,
            'tax_base' => $tax_base,
            'tax_amount' => $tax_amount,
            'total' => $total,
            'summary_regular' => $summary_regular,
            'summary_sale' => $summary_sale,
        ];
    }
}

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
