<?php

if (!function_exists('buildpro_checkout_get_cart_items')) {
    function buildpro_checkout_get_cart_items()
    {
        $wc_active = function_exists('WC') && WC()->cart;
        $cart_items = $wc_active ? WC()->cart->get_cart() : [];
        return [$wc_active, $cart_items];
    }
}

if (!function_exists('buildpro_checkout_get_totals')) {
    function buildpro_checkout_get_totals($cart_items)
    {
        $regular_total_raw = 0.0;
        $sale_total_raw = 0.0;

        foreach ($cart_items as $item) {
            $product = isset($item['data']) ? $item['data'] : null;
            $qty = isset($item['quantity']) ? intval($item['quantity']) : 0;
            if (!$product || $qty <= 0) {
                continue;
            }

            $regular_price = floatval($product->get_regular_price());
            $current_price = floatval($product->get_price());
            $sale_price = $product->get_sale_price() !== '' ? floatval($product->get_sale_price()) : $current_price;

            if ($regular_price <= 0) {
                $regular_price = $current_price;
            }

            $regular_total_raw += $regular_price * $qty;
            $sale_total_raw += $sale_price * $qty;
        }

        return [
            'regular_total_raw' => $regular_total_raw,
            'sale_total_raw' => $sale_total_raw,
            'you_save_raw' => max(0, $regular_total_raw - $sale_total_raw),
            'total' => $sale_total_raw,
        ];
    }
}

if (!function_exists('buildpro_checkout_get_country_data')) {
    function buildpro_checkout_get_country_data()
    {
        $wc_countries = function_exists('WC') ? WC()->countries->get_allowed_countries() : [];
        $wc_base_country = function_exists('wc_get_base_location') ? wc_get_base_location()['country'] : '';
        return [$wc_countries, $wc_base_country];
    }
}

if (!function_exists('buildpro_checkout_get_gateway_data')) {
    function buildpro_checkout_get_gateway_data()
    {
        $available_gateways = [];
        if (function_exists('WC') && WC()->payment_gateways()) {
            $available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
        }

        $paypal_gateway = null;
        $paypal_gateway_id = '';

        $preferred_paypal_ids = ['ppcp-gateway', 'paypal', 'ppec_paypal'];
        foreach ($preferred_paypal_ids as $gateway_id) {
            if (!empty($available_gateways[$gateway_id])) {
                $paypal_gateway = $available_gateways[$gateway_id];
                $paypal_gateway_id = $gateway_id;
                break;
            }
        }

        if (!$paypal_gateway && !empty($available_gateways)) {
            foreach ($available_gateways as $gateway_id => $gateway_obj) {
                if (strpos($gateway_id, 'paypal') !== false || strpos($gateway_id, 'ppcp') !== false) {
                    $paypal_gateway = $gateway_obj;
                    $paypal_gateway_id = $gateway_id;
                    break;
                }
            }
        }

        $paypal_enabled = !empty($paypal_gateway_id);
        $paypal_title = $paypal_enabled ? wp_strip_all_tags($paypal_gateway->get_title()) : 'PayPal';
        $paypal_description = $paypal_enabled
            ? wp_kses_post(wpautop(wptexturize($paypal_gateway->get_description())))
            : 'PayPal is currently unavailable. Please enable a PayPal gateway in WooCommerce settings.';

        $payment_tab_count = $paypal_enabled ? 4 : 3;

        $bacs_settings = get_option('woocommerce_bacs_settings', []);
        $bacs_enabled = isset($bacs_settings['enabled']) && $bacs_settings['enabled'] === 'yes';
        $bacs_title = isset($bacs_settings['title']) ? $bacs_settings['title'] : 'Bank Transfer';
        $bacs_desc = isset($bacs_settings['description']) ? $bacs_settings['description'] : 'Make your payment directly into our bank account. Please use your Order ID as the payment reference.';
        $bacs_accounts = get_option('woocommerce_bacs_accounts', []);

        return [
            'available_gateways' => $available_gateways,
            'paypal_gateway' => $paypal_gateway,
            'paypal_gateway_id' => $paypal_gateway_id,
            'paypal_enabled' => $paypal_enabled,
            'paypal_title' => $paypal_title,
            'paypal_description' => $paypal_description,
            'payment_tab_count' => $payment_tab_count,
            'bacs_enabled' => $bacs_enabled,
            'bacs_title' => $bacs_title,
            'bacs_desc' => $bacs_desc,
            'bacs_accounts' => $bacs_accounts,
        ];
    }
}

if (!function_exists('buildpro_checkout_get_bill_page_url')) {
    function buildpro_checkout_get_bill_page_url()
    {
        $bill_page_url = home_url('/bill-page/');
        $bill_pages = get_pages([
            'meta_key'   => '_wp_page_template',
            'meta_value' => 'bill-page.php',
            'number'     => 1,
        ]);
        if (!empty($bill_pages) && !empty($bill_pages[0]->ID)) {
            $bill_page_url = get_permalink($bill_pages[0]->ID);
        }
        return $bill_page_url;
    }
}

if (!function_exists('buildpro_checkout_get_price_formatter')) {
    function buildpro_checkout_get_price_formatter()
    {
        $currency_symbol = function_exists('get_woocommerce_currency_symbol') ? get_woocommerce_currency_symbol() : '$';
        $wc_decimals = function_exists('wc_get_price_decimals') ? wc_get_price_decimals() : 2;
        $wc_dec_sep = function_exists('wc_get_price_decimal_separator') ? wc_get_price_decimal_separator() : '.';
        $wc_thou_sep = function_exists('wc_get_price_thousand_separator') ? wc_get_price_thousand_separator() : ',';
        $wc_price_fmt = function_exists('get_woocommerce_price_format') ? get_woocommerce_price_format() : '%1$s%2$s';

        return function ($amount) use ($currency_symbol, $wc_decimals, $wc_dec_sep, $wc_thou_sep, $wc_price_fmt) {
            return sprintf($wc_price_fmt, $currency_symbol, number_format((float) $amount, $wc_decimals, $wc_dec_sep, $wc_thou_sep));
        };
    }
}

if (!function_exists('buildpro_checkout_get_page_data')) {
    function buildpro_checkout_get_page_data()
    {
        list($wc_active, $cart_items) = buildpro_checkout_get_cart_items();
        $totals = buildpro_checkout_get_totals($cart_items);
        list($wc_countries, $wc_base_country) = buildpro_checkout_get_country_data();
        $gateway_data = buildpro_checkout_get_gateway_data();

        $referer = '/';
        if (isset($_SERVER) && isset($_SERVER['REQUEST_URI'])) {
            $referer = $_SERVER['REQUEST_URI'];
        }

        return [
            'wc_active' => $wc_active,
            'cart_items' => $cart_items,
            'regular_total_raw' => $totals['regular_total_raw'],
            'sale_total_raw' => $totals['sale_total_raw'],
            'you_save_raw' => $totals['you_save_raw'],
            'total' => $totals['total'],
            'wc_countries' => $wc_countries,
            'wc_base_country' => $wc_base_country,
            'available_gateways' => $gateway_data['available_gateways'],
            'paypal_gateway' => $gateway_data['paypal_gateway'],
            'paypal_gateway_id' => $gateway_data['paypal_gateway_id'],
            'paypal_enabled' => $gateway_data['paypal_enabled'],
            'paypal_title' => $gateway_data['paypal_title'],
            'paypal_description' => $gateway_data['paypal_description'],
            'payment_tab_count' => $gateway_data['payment_tab_count'],
            'bacs_enabled' => $gateway_data['bacs_enabled'],
            'bacs_title' => $gateway_data['bacs_title'],
            'bacs_desc' => $gateway_data['bacs_desc'],
            'bacs_accounts' => $gateway_data['bacs_accounts'],
            'bill_page_url' => buildpro_checkout_get_bill_page_url(),
            'bp_price' => buildpro_checkout_get_price_formatter(),
            'checkout_localize' => [
                'ajaxUrl' => esc_url_raw(add_query_arg('wc-ajax', 'checkout', home_url('/'))),
                'nonce' => wp_create_nonce('woocommerce-process_checkout'),
                'referer' => esc_url_raw($referer),
                'billUrl' => esc_url_raw(buildpro_checkout_get_bill_page_url()),
                'paypalEnabled' => $gateway_data['paypal_enabled'],
                'paypalMethodId' => $gateway_data['paypal_gateway_id'],
                'paypalTitle' => $gateway_data['paypal_title'],
            ],
        ];
    }
}

// Ensure WooCommerce PayPal Payments smart buttons treat our custom Checkout Page template
// as a checkout context, so its JS reads from `form.checkout`.
add_action('wp', function () {
    if (!function_exists('is_page_template') || !is_page_template('checkout-page.php')) {
        return;
    }

    add_filter('woocommerce_is_checkout', '__return_true', 99);
    add_filter('woocommerce_paypal_payments_context', function ($context) {
        return 'checkout';
    }, 99);
}, 20);
