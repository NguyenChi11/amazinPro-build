<?php

if (!function_exists('buildpro_bill_get_value_from_request')) {
    function buildpro_bill_get_value_from_request($key, $default = '')
    {
        return (isset($_GET) && isset($_GET[$key])) ? sanitize_text_field(wp_unslash($_GET[$key])) : $default;
    }
}

if (!function_exists('buildpro_bill_get_cart_items')) {
    function buildpro_bill_get_cart_items()
    {
        $wc_active = function_exists('WC') && WC()->cart;
        $cart_items = $wc_active ? WC()->cart->get_cart() : [];
        return [$wc_active, $cart_items];
    }
}

if (!function_exists('buildpro_bill_get_totals')) {
    function buildpro_bill_get_totals($cart_items)
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

if (!function_exists('buildpro_bill_get_gateway_data')) {
    function buildpro_bill_get_gateway_data()
    {
        $available_gateways = [];
        if (function_exists('WC') && WC()->payment_gateways()) {
            $available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
        }

        $paypal_enabled = false;
        $paypal_gateway_id = '';
        $paypal_gateway_title = 'PayPal';

        foreach ($available_gateways as $gateway_id => $gateway_obj) {
            if (strpos($gateway_id, 'paypal') !== false || strpos($gateway_id, 'ppcp') !== false) {
                $paypal_enabled = true;
                $paypal_gateway_id = $gateway_id;
                if (is_object($gateway_obj) && method_exists($gateway_obj, 'get_title')) {
                    $paypal_gateway_title = wp_strip_all_tags($gateway_obj->get_title());
                }
                break;
            }
        }

        $bacs_settings = get_option('woocommerce_bacs_settings', []);
        $bacs_enabled = isset($bacs_settings['enabled']) && $bacs_settings['enabled'] === 'yes';

        $payment_options = [
            'cod' => ['label' => 'Cash on Delivery'],
            // 'card' => ['label' => 'Credit Card'],
            'bank' => ['label' => 'Bank Transfer'],
        ];
        if ($paypal_enabled) {
            $payment_options['paypal'] = ['label' => 'PayPal'];
        }
        if (!$bacs_enabled) {
            unset($payment_options['bank']);
        }

        return [
            'available_gateways' => $available_gateways,
            'paypal_enabled' => $paypal_enabled,
            'paypal_gateway_id' => $paypal_gateway_id,
            'paypal_gateway_title' => $paypal_gateway_title,
            'bacs_enabled' => $bacs_enabled,
            'payment_options' => $payment_options,
        ];
    }
}

if (!function_exists('buildpro_bill_get_country_data')) {
    function buildpro_bill_get_country_data()
    {
        $wc_countries = function_exists('WC') ? WC()->countries->get_allowed_countries() : [];
        $wc_base_country = function_exists('wc_get_base_location') ? wc_get_base_location()['country'] : '';
        return [$wc_countries, $wc_base_country];
    }
}

if (!function_exists('buildpro_bill_get_price_formatter')) {
    function buildpro_bill_get_price_formatter()
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

if (!function_exists('buildpro_bill_normalize_form_data')) {
    function buildpro_bill_normalize_form_data($form_data, $wc_countries, $payment_options)
    {
        if (empty($form_data['country_label']) && !empty($form_data['country']) && isset($wc_countries[$form_data['country']])) {
            $form_data['country_label'] = $wc_countries[$form_data['country']];
        }

        if (!array_key_exists($form_data['payment'], $payment_options)) {
            $form_data['payment'] = array_key_first($payment_options);
        }

        return $form_data;
    }
}

if (!function_exists('buildpro_bill_get_initial_form_data')) {
    function buildpro_bill_get_initial_form_data($wc_base_country)
    {
        return [
            'fullname' => buildpro_bill_get_value_from_request('fullname'),
            'phone' => buildpro_bill_get_value_from_request('phone'),
            'email' => buildpro_bill_get_value_from_request('email'),
            'address' => buildpro_bill_get_value_from_request('address'),
            'city' => buildpro_bill_get_value_from_request('city'),
            'zip' => buildpro_bill_get_value_from_request('zip'),
            'country' => buildpro_bill_get_value_from_request('country', $wc_base_country),
            'country_label' => buildpro_bill_get_value_from_request('country_label'),
            'note' => buildpro_bill_get_value_from_request('note'),
            'payment' => buildpro_bill_get_value_from_request('payment', 'cod'),
        ];
    }
}

if (!function_exists('buildpro_bill_get_posted_form_data')) {
    function buildpro_bill_get_posted_form_data()
    {
        return [
            'fullname' => isset($_POST['fullname']) ? sanitize_text_field(wp_unslash($_POST['fullname'])) : '',
            'phone' => isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '',
            'email' => isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '',
            'address' => isset($_POST['address']) ? sanitize_text_field(wp_unslash($_POST['address'])) : '',
            'city' => isset($_POST['city']) ? sanitize_text_field(wp_unslash($_POST['city'])) : '',
            'zip' => isset($_POST['zip']) ? sanitize_text_field(wp_unslash($_POST['zip'])) : '',
            'country' => isset($_POST['country']) ? sanitize_text_field(wp_unslash($_POST['country'])) : '',
            'country_label' => isset($_POST['country_label']) ? sanitize_text_field(wp_unslash($_POST['country_label'])) : '',
            'note' => isset($_POST['note']) ? sanitize_textarea_field(wp_unslash($_POST['note'])) : '',
            'payment' => isset($_POST['payment']) ? sanitize_text_field(wp_unslash($_POST['payment'])) : '',
        ];
    }
}

if (!function_exists('buildpro_bill_resolve_payment_method')) {
    function buildpro_bill_resolve_payment_method($form_data, $payment_options, $bacs_enabled, $paypal_gateway_id, $paypal_gateway_title)
    {
        $payment_method_id = 'cod';
        $payment_method_title = isset($payment_options['cod']['label']) ? $payment_options['cod']['label'] : 'Cash on Delivery';

        if ($form_data['payment'] === 'bank' && $bacs_enabled) {
            $payment_method_id = 'bacs';
            $payment_method_title = isset($payment_options['bank']['label']) ? $payment_options['bank']['label'] : 'Bank Transfer';
        } elseif ($form_data['payment'] === 'paypal' && !empty($paypal_gateway_id)) {
            $payment_method_id = $paypal_gateway_id;
            $payment_method_title = $paypal_gateway_title;
        } elseif ($form_data['payment'] === 'card') {
            $payment_method_id = 'buildpro_card_manual';
            $payment_method_title = isset($payment_options['card']['label']) ? $payment_options['card']['label'] : 'Credit Card';
        }

        return [$payment_method_id, $payment_method_title];
    }
}

if (!function_exists('buildpro_bill_create_order')) {
    function buildpro_bill_create_order($form_data, $cart_items, $wc_active, $payment_options, $bacs_enabled, $paypal_gateway_id, $paypal_gateway_title)
    {
        if (empty($cart_items) || !function_exists('wc_create_order')) {
            return [false, 0, 'Cannot create order because the cart is empty.'];
        }

        $full_name = trim($form_data['fullname']);
        $name_parts = preg_split('/\\s+/', $full_name);
        $first_name = !empty($name_parts[0]) ? $name_parts[0] : $full_name;
        $last_name = count($name_parts) > 1 ? implode(' ', array_slice($name_parts, 1)) : $first_name;

        list($payment_method_id, $payment_method_title) = buildpro_bill_resolve_payment_method(
            $form_data,
            $payment_options,
            $bacs_enabled,
            $paypal_gateway_id,
            $paypal_gateway_title
        );

        try {
            $order = wc_create_order();

            foreach ($cart_items as $item) {
                $product = isset($item['data']) ? $item['data'] : null;
                $qty = isset($item['quantity']) ? intval($item['quantity']) : 0;
                if (!$product || $qty <= 0) {
                    continue;
                }
                $order->add_product($product, $qty);
            }

            $address_data = [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $form_data['email'],
                'phone' => $form_data['phone'],
                'address_1' => $form_data['address'],
                'city' => $form_data['city'],
                'postcode' => $form_data['zip'],
                'country' => $form_data['country'],
            ];

            $order->set_address($address_data, 'billing');
            $order->set_address($address_data, 'shipping');
            $order->set_payment_method($payment_method_id);
            $order->set_payment_method_title($payment_method_title);

            $order_note_text = trim($form_data['note']);
            if ($order_note_text !== '') {
                $order->set_customer_note($order_note_text);
                $order->add_order_note('Customer note: ' . $order_note_text, 0, false);
            }

            $order->calculate_totals();
            $order->update_status('pending', 'Created from Bill page confirmation.');

            $created_order_id = $order->get_id();
            $submit_success = $created_order_id > 0;

            if ($submit_success && $wc_active) {
                WC()->cart->empty_cart();
            }

            if (!$submit_success) {
                return [false, 0, 'Order creation failed. Please try again.'];
            }

            return [true, $created_order_id, ''];
        } catch (Exception $e) {
            return [false, 0, 'Unable to create WooCommerce order. Please try again.'];
        }
    }
}

if (!function_exists('buildpro_bill_process_submit')) {
    function buildpro_bill_process_submit($form_data, $wc_countries, $cart_items, $wc_active, $payment_options, $bacs_enabled, $paypal_gateway_id, $paypal_gateway_title)
    {
        $submit_success = false;
        $submit_error = '';
        $created_order_id = 0;

        if (isset($_SERVER) && isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bp_bill_confirm_submit'])) {
            if (!isset($_POST['bp_bill_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['bp_bill_nonce'])), 'bp_bill_confirm')) {
                $submit_error = 'Security validation failed. Please reload and submit again.';
            } else {
                $form_data = buildpro_bill_get_posted_form_data();
                $form_data = buildpro_bill_normalize_form_data($form_data, $wc_countries, $payment_options);

                if (!isset($_POST['bill_agree'])) {
                    $submit_error = 'Please confirm the bill information before submitting.';
                } else {
                    list($submit_success, $created_order_id, $submit_error) = buildpro_bill_create_order(
                        $form_data,
                        $cart_items,
                        $wc_active,
                        $payment_options,
                        $bacs_enabled,
                        $paypal_gateway_id,
                        $paypal_gateway_title
                    );
                }
            }
        }

        return [$form_data, $submit_success, $submit_error, $created_order_id];
    }
}

if (!function_exists('buildpro_bill_get_page_data')) {
    function buildpro_bill_get_page_data()
    {
        list($wc_active, $cart_items) = buildpro_bill_get_cart_items();
        $totals = buildpro_bill_get_totals($cart_items);
        list($wc_countries, $wc_base_country) = buildpro_bill_get_country_data();
        $gateway_data = buildpro_bill_get_gateway_data();

        $form_data = buildpro_bill_get_initial_form_data($wc_base_country);
        $form_data = buildpro_bill_normalize_form_data($form_data, $wc_countries, $gateway_data['payment_options']);

        list($form_data, $submit_success, $submit_error, $created_order_id) = buildpro_bill_process_submit(
            $form_data,
            $wc_countries,
            $cart_items,
            $wc_active,
            $gateway_data['payment_options'],
            $gateway_data['bacs_enabled'],
            $gateway_data['paypal_gateway_id'],
            $gateway_data['paypal_gateway_title']
        );

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
            'paypal_enabled' => $gateway_data['paypal_enabled'],
            'bacs_enabled' => $gateway_data['bacs_enabled'],
            'payment_options' => $gateway_data['payment_options'],
            'paypal_gateway_id' => $gateway_data['paypal_gateway_id'],
            'paypal_gateway_title' => $gateway_data['paypal_gateway_title'],
            'bp_price' => buildpro_bill_get_price_formatter(),
            'form_data' => $form_data,
            'submit_success' => $submit_success,
            'submit_error' => $submit_error,
            'created_order_id' => $created_order_id,
            'home_redirect_url' => home_url('/'),
        ];
    }
}
