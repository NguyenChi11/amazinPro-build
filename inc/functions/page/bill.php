<?php

if (!function_exists('buildpro_bill_get_value_from_request')) {
    function buildpro_bill_get_value_from_request($key, $default = '')
    {
        return (isset($_GET) && isset($_GET[$key])) ? sanitize_text_field(wp_unslash($_GET[$key])) : $default;
    }
}

if (!function_exists('buildpro_bill_get_order_from_request')) {
    function buildpro_bill_get_order_from_request()
    {
        if (!function_exists('wc_get_order')) {
            return null;
        }

        $order_id = (int) buildpro_bill_get_value_from_request('bp_order_id', '0');
        $order_key = buildpro_bill_get_value_from_request('key', '');
        if ($order_id <= 0 || $order_key === '') {
            return null;
        }

        $order = wc_get_order($order_id);
        if (!$order || !method_exists($order, 'get_order_key')) {
            return null;
        }

        if ((string) $order->get_order_key() !== (string) $order_key) {
            return null;
        }

        return $order;
    }
}

if (!function_exists('buildpro_bill_map_payment_method_to_bill_key')) {
    function buildpro_bill_map_payment_method_to_bill_key(string $method_id): string
    {
        $method_id = strtolower($method_id);
        if ($method_id === 'cod') {
            return 'cod';
        }
        if ($method_id === 'bacs') {
            return 'bank';
        }
        if (strpos($method_id, 'ppcp') !== false || strpos($method_id, 'paypal') !== false) {
            return 'paypal';
        }
        return '';
    }
}

if (!function_exists('buildpro_bill_build_data_from_order')) {
    function buildpro_bill_build_data_from_order($order, array $wc_countries, array $payment_options): array
    {
        $items = [];
        $regular_total_raw = 0.0;
        $sale_total_raw = 0.0;
        $total = 0.0;

        if (is_object($order) && method_exists($order, 'get_items')) {
            foreach ($order->get_items('line_item') as $item) {
                if (!is_object($item) || !method_exists($item, 'get_product')) {
                    continue;
                }
                $product = $item->get_product();
                if (!$product) {
                    continue;
                }
                $qty = method_exists($item, 'get_quantity') ? (int) $item->get_quantity() : 0;
                if ($qty <= 0) {
                    continue;
                }

                $product_id = method_exists($item, 'get_product_id') ? (int) $item->get_product_id() : (int) $product->get_id();
                $variation_id = method_exists($item, 'get_variation_id') ? (int) $item->get_variation_id() : 0;

                $items[] = [
                    'data' => $product,
                    'product_id' => $product_id,
                    'variation_id' => $variation_id,
                    'quantity' => $qty,
                ];

                if (method_exists($item, 'get_subtotal')) {
                    $regular_total_raw += (float) $item->get_subtotal();
                }
                if (method_exists($item, 'get_total')) {
                    $sale_total_raw += (float) $item->get_total();
                }
            }
        }

        if (is_object($order) && method_exists($order, 'get_total')) {
            $total = (float) $order->get_total();
        }

        $first_name = is_object($order) && method_exists($order, 'get_billing_first_name') ? (string) $order->get_billing_first_name() : '';
        $last_name = is_object($order) && method_exists($order, 'get_billing_last_name') ? (string) $order->get_billing_last_name() : '';
        $fullname = trim($first_name . ' ' . $last_name);

        $country = is_object($order) && method_exists($order, 'get_billing_country') ? (string) $order->get_billing_country() : '';
        $country_label = '';
        if ($country !== '' && !empty($wc_countries) && isset($wc_countries[$country])) {
            $country_label = (string) $wc_countries[$country];
        }

        $method_id = is_object($order) && method_exists($order, 'get_payment_method') ? (string) $order->get_payment_method() : '';
        $payment_key = buildpro_bill_map_payment_method_to_bill_key($method_id);
        if ($payment_key === '' || !isset($payment_options[$payment_key])) {
            // Fallback to any label we have.
            if (!empty($payment_options)) {
                $keys = array_keys($payment_options);
                $payment_key = (string) ($keys[0] ?? 'cod');
            } else {
                $payment_key = 'cod';
            }
        }

        $note = is_object($order) && method_exists($order, 'get_customer_note') ? (string) $order->get_customer_note() : '';

        $form_data = [
            'fullname' => $fullname,
            'phone' => is_object($order) && method_exists($order, 'get_billing_phone') ? (string) $order->get_billing_phone() : '',
            'email' => is_object($order) && method_exists($order, 'get_billing_email') ? (string) $order->get_billing_email() : '',
            'address' => is_object($order) && method_exists($order, 'get_billing_address_1') ? (string) $order->get_billing_address_1() : '',
            'city' => is_object($order) && method_exists($order, 'get_billing_city') ? (string) $order->get_billing_city() : '',
            'zip' => is_object($order) && method_exists($order, 'get_billing_postcode') ? (string) $order->get_billing_postcode() : '',
            'country' => $country,
            'country_label' => $country_label,
            'note' => $note,
            'payment' => $payment_key,
        ];

        return [
            'cart_items' => $items,
            'regular_total_raw' => $regular_total_raw,
            'sale_total_raw' => $sale_total_raw,
            'you_save_raw' => max(0, $regular_total_raw - $sale_total_raw),
            'total' => $total,
            'form_data' => $form_data,
            'order_id' => is_object($order) && method_exists($order, 'get_id') ? (int) $order->get_id() : 0,
        ];
    }
}

if (!function_exists('buildpro_bill_get_snapshot_session_key')) {
    function buildpro_bill_get_snapshot_session_key()
    {
        return 'buildpro_bill_cart_snapshot_v1';
    }
}

if (!function_exists('buildpro_bill_is_from_checkout')) {
    function buildpro_bill_is_from_checkout()
    {
        return buildpro_bill_get_value_from_request('bp_from_checkout', '') === '1';
    }
}

if (!function_exists('buildpro_bill_make_snapshot_from_cart')) {
    function buildpro_bill_make_snapshot_from_cart($cart_items)
    {
        $snapshot = [];
        foreach ($cart_items as $item) {
            $product_id = isset($item['product_id']) ? (int) $item['product_id'] : 0;
            $variation_id = isset($item['variation_id']) ? (int) $item['variation_id'] : 0;
            $qty = isset($item['quantity']) ? (int) $item['quantity'] : 0;
            if ($product_id <= 0 || $qty <= 0) {
                continue;
            }
            $snapshot[] = [
                'product_id' => $product_id,
                'variation_id' => $variation_id,
                'quantity' => $qty,
            ];
        }
        return $snapshot;
    }
}

if (!function_exists('buildpro_bill_expand_snapshot_items')) {
    function buildpro_bill_expand_snapshot_items($snapshot)
    {
        $items = [];
        if (empty($snapshot) || !is_array($snapshot)) {
            return $items;
        }

        foreach ($snapshot as $row) {
            $product_id = isset($row['product_id']) ? (int) $row['product_id'] : 0;
            $variation_id = isset($row['variation_id']) ? (int) $row['variation_id'] : 0;
            $qty = isset($row['quantity']) ? (int) $row['quantity'] : 0;
            if ($product_id <= 0 || $qty <= 0) {
                continue;
            }

            $product = null;
            if ($variation_id > 0 && function_exists('wc_get_product')) {
                $product = wc_get_product($variation_id);
            }
            if (!$product && function_exists('wc_get_product')) {
                $product = wc_get_product($product_id);
            }
            if (!$product) {
                continue;
            }

            $items[] = [
                'data' => $product,
                'product_id' => $product_id,
                'variation_id' => $variation_id,
                'quantity' => $qty,
            ];
        }

        return $items;
    }
}

if (!function_exists('buildpro_bill_get_cart_snapshot')) {
    function buildpro_bill_get_cart_snapshot()
    {
        if (!function_exists('WC') || !WC() || !isset(WC()->session)) {
            return [];
        }
        $key = buildpro_bill_get_snapshot_session_key();
        $snapshot = WC()->session->get($key);
        return is_array($snapshot) ? $snapshot : [];
    }
}

if (!function_exists('buildpro_bill_set_cart_snapshot')) {
    function buildpro_bill_set_cart_snapshot($snapshot)
    {
        if (!function_exists('WC') || !WC() || !isset(WC()->session)) {
            return;
        }
        WC()->session->set(buildpro_bill_get_snapshot_session_key(), is_array($snapshot) ? $snapshot : []);
    }
}

if (!function_exists('buildpro_bill_clear_cart_snapshot')) {
    function buildpro_bill_clear_cart_snapshot()
    {
        if (!function_exists('WC') || !WC() || !isset(WC()->session)) {
            return;
        }
        WC()->session->set(buildpro_bill_get_snapshot_session_key(), []);
    }
}

// When arriving on Bill page from Checkout, snapshot cart items into session and empty the cart
// BEFORE the header renders, so the header cart is reset while Bill can still show the snapshot.
if (!function_exists('buildpro_bill_prime_snapshot_and_empty_cart')) {
    function buildpro_bill_prime_snapshot_and_empty_cart()
    {
        if (!function_exists('is_page_template') || !is_page_template('bill-page.php')) {
            return;
        }
        if (!function_exists('WC') || !WC() || !isset(WC()->cart) || !WC()->cart) {
            return;
        }
        if (!isset(WC()->session)) {
            return;
        }

        $from_checkout = buildpro_bill_is_from_checkout();
        $snapshot = buildpro_bill_get_cart_snapshot();

        if ($from_checkout && empty($snapshot)) {
            $cart_items = WC()->cart->get_cart();
            $snapshot = buildpro_bill_make_snapshot_from_cart($cart_items);
            buildpro_bill_set_cart_snapshot($snapshot);
        }

        if (!empty($snapshot)) {
            WC()->cart->empty_cart();
        }
    }
}

add_action('template_redirect', 'buildpro_bill_prime_snapshot_and_empty_cart', 1);

if (!function_exists('buildpro_bill_get_cart_items')) {
    function buildpro_bill_get_cart_items()
    {
        $wc_active = function_exists('WC') && WC()->cart;
        if (!$wc_active) {
            return [$wc_active, []];
        }

        $snapshot = buildpro_bill_get_cart_snapshot();
        if (!empty($snapshot)) {
            return [$wc_active, buildpro_bill_expand_snapshot_items($snapshot)];
        }

        return [$wc_active, WC()->cart->get_cart()];
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
        $core = function_exists('buildpro_payment_get_gateway_data') ? buildpro_payment_get_gateway_data() : [];

        $available_gateways = isset($core['available_gateways']) && is_array($core['available_gateways']) ? $core['available_gateways'] : [];
        $paypal_enabled = !empty($core['paypal_enabled']);
        $paypal_gateway_id = isset($core['paypal_gateway_id']) ? (string) $core['paypal_gateway_id'] : '';
        $paypal_gateway_title = isset($core['paypal_title']) && $core['paypal_title'] !== '' ? (string) $core['paypal_title'] : 'PayPal';
        $bacs_enabled = !empty($core['bacs_enabled']);

        $payment_options = [
            'cod' => ['label' => 'Cash on Delivery'],
            // 'card' => ['label' => 'Credit Card'],
            'bank' => ['label' => 'Bank Transfer'],
        ];
        if ($paypal_enabled) {
            $payment_options['paypal'] = ['label' => $paypal_gateway_title];
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
                buildpro_bill_clear_cart_snapshot();
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
        list($wc_countries, $wc_base_country) = buildpro_bill_get_country_data();
        $gateway_data = buildpro_bill_get_gateway_data();

        // Order-view mode: used when redirecting here after a successful PayPal payment.
        $order = buildpro_bill_get_order_from_request();
        if ($order) {
            $order_data = buildpro_bill_build_data_from_order($order, $wc_countries, $gateway_data['payment_options']);
            return [
                'wc_active' => function_exists('WC') && WC()->cart,
                'cart_items' => $order_data['cart_items'],
                'regular_total_raw' => $order_data['regular_total_raw'],
                'sale_total_raw' => $order_data['sale_total_raw'],
                'you_save_raw' => $order_data['you_save_raw'],
                'total' => $order_data['total'],
                'wc_countries' => $wc_countries,
                'wc_base_country' => $wc_base_country,
                'available_gateways' => $gateway_data['available_gateways'],
                'paypal_enabled' => $gateway_data['paypal_enabled'],
                'bacs_enabled' => $gateway_data['bacs_enabled'],
                'payment_options' => $gateway_data['payment_options'],
                'paypal_gateway_id' => $gateway_data['paypal_gateway_id'],
                'paypal_gateway_title' => $gateway_data['paypal_gateway_title'],
                'bp_price' => buildpro_bill_get_price_formatter(),
                'form_data' => $order_data['form_data'],
                'submit_success' => true,
                'submit_error' => '',
                'created_order_id' => $order_data['order_id'],
                'home_redirect_url' => home_url('/'),
                'is_order_view' => true,
            ];
        }

        list($wc_active, $cart_items) = buildpro_bill_get_cart_items();
        $totals = buildpro_bill_get_totals($cart_items);

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
            'is_order_view' => false,
        ];
    }
}
