<?php
add_filter('woocommerce_create_pages', function ($pages) {
    return array();
}, 99);
add_filter('woocommerce_enable_setup_wizard', '__return_false');

/**
 * Tell WooCommerce that our custom cart-page.php template IS the cart page.
 * This makes is_cart() work and ensures WC loads the session properly.
 */
add_filter('woocommerce_get_cart_page_id', function ($id) {
    static $cart_page_id = null;
    if ($cart_page_id === null) {
        $pages = get_pages(array(
            'meta_key'   => '_wp_page_template',
            'meta_value' => 'cart-page.php',
            'number'     => 1,
        ));
        $cart_page_id = !empty($pages) ? (int) $pages[0]->ID : (int) $id;
    }
    return $cart_page_id;
});

/**
 * Also override wc_get_cart_url() so it points to our custom page.
 */
add_filter('woocommerce_get_cart_url', function ($url) {
    $pages = get_pages(array(
        'meta_key'   => '_wp_page_template',
        'meta_value' => 'cart-page.php',
        'number'     => 1,
    ));
    return !empty($pages) ? get_permalink($pages[0]->ID) : $url;
});

/**
 * Ensure the cart & session are loaded for our custom cart page template.
 * template_redirect fires before the template is included.
 */
add_action('template_redirect', function () {
    if (function_exists('wc_load_cart') && is_page_template('cart-page.php')) {
        wc_load_cart();
    }
});
