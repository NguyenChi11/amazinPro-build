<?php
add_action('wp_ajax_buildpro_mini_cart', 'buildpro_mini_cart_ajax');
add_action('wp_ajax_nopriv_buildpro_mini_cart', 'buildpro_mini_cart_ajax');

function buildpro_mini_cart_ajax()
{
    check_ajax_referer('buildpro_mini_cart', 'nonce');
    if (!function_exists('WC') || !WC()->cart) {
        wp_send_json_error(array('message' => 'WC not available'));
    }
    WC()->cart->get_cart();
    WC()->cart->calculate_totals();
    ob_start();
    get_template_part('template/template-parts/header/cart/index');
    $html = ob_get_clean();
    wp_send_json_success(array(
        'html'  => $html,
        'count' => (int) WC()->cart->get_cart_contents_count(),
        'nonce' => wp_create_nonce('buildpro_mini_cart'),
    ));
}

add_action('wp_ajax_buildpro_update_cart_qty', 'buildpro_update_cart_qty_ajax');
add_action('wp_ajax_nopriv_buildpro_update_cart_qty', 'buildpro_update_cart_qty_ajax');

function buildpro_update_cart_qty_ajax()
{
    check_ajax_referer('buildpro_mini_cart', 'nonce');
    if (!function_exists('WC') || !WC()->cart) {
        wp_send_json_error(array('message' => 'WC not available'));
    }

    wc_nocache_headers();

    // Load cart from session before modifying
    $cart = WC()->cart->get_cart();

    $cart_key = isset($_POST['cart_key']) ? sanitize_text_field(wp_unslash($_POST['cart_key'])) : '';
    $qty      = isset($_POST['qty'])      ? max(1, (int) $_POST['qty'])                          : 1;

    if ($cart_key && isset($cart[$cart_key])) {
        // false = do not auto-recalculate; we do it explicitly below
        WC()->cart->set_quantity($cart_key, $qty, false);
    }

    // Recalculate totals
    WC()->cart->calculate_totals();

    // Use WC's official method to write all cart state to the session object
    if (method_exists(WC()->cart, 'set_session')) {
        WC()->cart->set_session();
    }

    // Persist the session to the database / cookie
    if (!is_null(WC()->session)) {
        WC()->session->save_data();
    }

    WC()->cart->maybe_set_cart_cookies();

    ob_start();
    get_template_part('template/template-parts/header/cart/index');
    $html = ob_get_clean();

    wp_send_json_success(array(
        'html'  => $html,
        'count' => (int) WC()->cart->get_cart_contents_count(),
        'nonce' => wp_create_nonce('buildpro_mini_cart'),
    ));
}

add_action('wp_ajax_buildpro_remove_cart_item', 'buildpro_remove_cart_item_ajax');
add_action('wp_ajax_nopriv_buildpro_remove_cart_item', 'buildpro_remove_cart_item_ajax');

function buildpro_remove_cart_item_ajax()
{
    check_ajax_referer('buildpro_mini_cart', 'nonce');
    if (!function_exists('WC') || !WC()->cart) {
        wp_send_json_error(array('message' => 'WC not available'));
    }

    wc_nocache_headers();

    $cart_key = isset($_POST['cart_key']) ? sanitize_text_field(wp_unslash($_POST['cart_key'])) : '';

    if ($cart_key) {
        WC()->cart->remove_cart_item($cart_key);
    }

    WC()->cart->calculate_totals();

    if (!is_null(WC()->session)) {
        WC()->session->save_data();
    }

    WC()->cart->maybe_set_cart_cookies();

    ob_start();
    get_template_part('template/template-parts/header/cart/index');
    $html = ob_get_clean();

    wp_send_json_success(array(
        'html'  => $html,
        'count' => (int) WC()->cart->get_cart_contents_count(),
        'nonce' => wp_create_nonce('buildpro_mini_cart'),
    ));
}

// Output buildproCart config as a direct inline script on wp_footer priority 5,
// which is guaranteed to run BEFORE footer scripts are printed (priority 20).
// This is more reliable than wp_localize_script, which can silently fail
// if the script handle isn't registered at the time it is called.
add_action('wp_footer', 'buildpro_output_cart_config', 5);
function buildpro_output_cart_config()
{
    if (!class_exists('WooCommerce')) return;
    $data = wp_json_encode(array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('buildpro_mini_cart'),
    ));
    echo '<script id="buildpro-cart-config">window.buildproCart=' . $data . ';</script>' . "\n";
}
