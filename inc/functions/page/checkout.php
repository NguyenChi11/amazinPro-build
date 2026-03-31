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
            'cod_enabled' => $gateway_data['cod_enabled'],
            'cod_title' => $gateway_data['cod_title'],
            'ppcp_available' => $gateway_data['ppcp_available'],
            'ppcp_gateway_id' => $gateway_data['ppcp_gateway_id'],
            'paypal_gateway' => $gateway_data['paypal_gateway'],
            'paypal_gateway_id' => $gateway_data['paypal_gateway_id'],
            'paypal_enabled' => $gateway_data['paypal_enabled'],
            'paypal_title' => $gateway_data['paypal_title'],
            'paypal_description' => $gateway_data['paypal_description'],
            'wcpay_gateway' => $gateway_data['wcpay_gateway'],
            'wcpay_gateway_id' => $gateway_data['wcpay_gateway_id'],
            'wcpay_enabled' => $gateway_data['wcpay_enabled'],
            'wcpay_title' => $gateway_data['wcpay_title'],
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
                'wcpayEnabled' => $gateway_data['wcpay_enabled'],
                'wcpayMethodId' => $gateway_data['wcpay_gateway_id'],
                'codEnabled' => $gateway_data['cod_enabled'],
                'codMethodId' => 'cod',
                'bankMethodId' => 'bacs',
            ],
        ];
    }
}
