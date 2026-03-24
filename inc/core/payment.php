<?php

// Payment & PayPal (PPCP) integration helpers.

if (!function_exists('buildpro_payment_get_gateway_data')) {
    function buildpro_payment_get_gateway_data(): array
    {
        $available_gateways = [];
        if (function_exists('WC') && WC()->payment_gateways()) {
            $available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
        }

        $ppcp_gateway_id = 'ppcp-gateway';
        $ppcp_available = !empty($available_gateways[$ppcp_gateway_id]);

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
            'ppcp_available' => $ppcp_available,
            'ppcp_gateway_id' => $ppcp_gateway_id,
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

// Back-compat: existing checkout code calls this.
if (!function_exists('buildpro_checkout_get_gateway_data')) {
    function buildpro_checkout_get_gateway_data()
    {
        return buildpro_payment_get_gateway_data();
    }
}

if (!function_exists('buildpro_ppcp_is_custom_checkout_template')) {
    function buildpro_ppcp_is_custom_checkout_template(): bool
    {
        return function_exists('is_page_template') && is_page_template('checkout-page.php');
    }
}

if (!function_exists('buildpro_ppcp_is_debug_enabled')) {
    function buildpro_ppcp_is_debug_enabled(): bool
    {
        return is_user_logged_in()
            && (current_user_can('manage_woocommerce') || current_user_can('administrator'))
            && isset($_GET['bp_ppcp_debug']);
    }
}

if (!function_exists('buildpro_ppcp_context_checkout')) {
    function buildpro_ppcp_context_checkout($context)
    {
        if (!buildpro_ppcp_is_custom_checkout_template()) {
            return $context;
        }
        return 'checkout';
    }
}

if (!function_exists('buildpro_ppcp_adjust_localized_script_data_for_pay_later')) {
    function buildpro_ppcp_adjust_localized_script_data_for_pay_later(array $data): array
    {
        if (!buildpro_ppcp_is_custom_checkout_template()) {
            return $data;
        }

        $settings = (array) get_option('woocommerce-ppcp-settings', array());

        $pay_later_enabled = $settings['pay_later_button_enabled'] ?? null;
        $pay_later_enabled = ($pay_later_enabled === true || $pay_later_enabled === 'yes' || $pay_later_enabled === '1' || $pay_later_enabled === 1);

        $pay_later_locations = $settings['pay_later_button_locations'] ?? array();
        if (!is_array($pay_later_locations)) {
            $pay_later_locations = array();
        }

        // If locations are not stored (older/migrated settings), treat it as enabled on checkout.
        $pay_later_for_checkout = $pay_later_enabled && (empty($pay_later_locations) || in_array('checkout', $pay_later_locations, true));
        if (!$pay_later_for_checkout) {
            return $data;
        }

        if (!isset($data['url_params']) || !is_array($data['url_params'])) {
            return $data;
        }

        $normalize_list = static function ($value): array {
            if (!is_string($value) || $value === '') {
                return array();
            }
            $items = array_map('trim', explode(',', $value));
            $items = array_filter($items, static fn($v) => is_string($v) && $v !== '');
            return array_values(array_unique($items));
        };

        $stringify_list = static function (array $items): string {
            $items = array_values(array_unique(array_filter($items, static fn($v) => is_string($v) && $v !== '')));
            return implode(',', $items);
        };

        $add_item = static function (string $csv, string $item) use ($normalize_list, $stringify_list): string {
            $list = $normalize_list($csv);
            if (!in_array($item, $list, true)) {
                $list[] = $item;
            }
            return $stringify_list($list);
        };

        $remove_item = static function (string $csv, string $item) use ($normalize_list, $stringify_list): string {
            $list = array_values(array_filter($normalize_list($csv), static fn($v) => $v !== $item));
            return $stringify_list($list);
        };

        $enable_funding = isset($data['url_params']['enable-funding']) ? (string) $data['url_params']['enable-funding'] : '';
        $disable_funding = isset($data['url_params']['disable-funding']) ? (string) $data['url_params']['disable-funding'] : '';

        // Prefer Pay Later over Card in the PayPal Smart Buttons area.
        $enable_funding = $add_item($enable_funding, 'paylater');
        $disable_funding = $remove_item($disable_funding, 'paylater');
        $disable_funding = $add_item($disable_funding, 'card');

        $data['url_params']['enable-funding'] = $enable_funding;
        $data['url_params']['disable-funding'] = $disable_funding;

        return $data;
    }
}

if (!function_exists('buildpro_ppcp_force_button_style_from_settings')) {
    function buildpro_ppcp_force_button_style_from_settings(array $data): array
    {
        if (!buildpro_ppcp_is_custom_checkout_template()) {
            return $data;
        }

        if (!buildpro_ppcp_is_debug_enabled() || !isset($_GET['bp_ppcp_force_style'])) {
            return $data;
        }

        $styling = (array) get_option('woocommerce-ppcp-data-styling', array());
        $classic = $styling['classic_checkout'] ?? null;

        if (is_object($classic) && method_exists($classic, 'to_array')) {
            $classic = $classic->to_array();
        }
        if (!is_array($classic)) {
            return $data;
        }

        if (!isset($data['button']) || !is_array($data['button'])) {
            return $data;
        }

        if (!isset($data['button']['style']) || !is_array($data['button']['style'])) {
            $data['button']['style'] = array();
        }

        foreach (array('layout', 'color', 'shape', 'label', 'tagline') as $key) {
            if (array_key_exists($key, $classic) && $classic[$key] !== null && $classic[$key] !== '') {
                $data['button']['style'][$key] = $classic[$key];
            }
        }

        return $data;
    }
}

if (!function_exists('buildpro_ppcp_adjust_for_custom_checkout_template')) {
    // Ensure WooCommerce PayPal Payments smart buttons treat our custom Checkout Page template
    // as a checkout context, so its JS reads from `form.checkout`.
    function buildpro_ppcp_adjust_for_custom_checkout_template()
    {
        if (!buildpro_ppcp_is_custom_checkout_template()) {
            return;
        }

        if (!has_filter('woocommerce_is_checkout', '__return_true')) {
            add_filter('woocommerce_is_checkout', '__return_true', 99);
        }

        if (!has_filter('woocommerce_paypal_payments_context', 'buildpro_ppcp_context_checkout')) {
            add_filter('woocommerce_paypal_payments_context', 'buildpro_ppcp_context_checkout', 99);
        }

        if (!has_filter('woocommerce_paypal_payments_localized_script_data', 'buildpro_ppcp_adjust_localized_script_data_for_pay_later')) {
            add_filter('woocommerce_paypal_payments_localized_script_data', 'buildpro_ppcp_adjust_localized_script_data_for_pay_later', 120);
        }

        if (!has_filter('woocommerce_paypal_payments_localized_script_data', 'buildpro_ppcp_force_button_style_from_settings')) {
            add_filter('woocommerce_paypal_payments_localized_script_data', 'buildpro_ppcp_force_button_style_from_settings', 999);
        }
    }
}

add_action('wp', 'buildpro_ppcp_adjust_for_custom_checkout_template', 20);

if (!function_exists('buildpro_payment_get_bill_page_url')) {
    function buildpro_payment_get_bill_page_url(): string
    {
        $bill_page_url = home_url('/bill-page/');
        if (function_exists('get_pages') && function_exists('get_permalink')) {
            $bill_pages = get_pages([
                'meta_key'   => '_wp_page_template',
                'meta_value' => 'bill-page.php',
                'number'     => 1,
            ]);
            if (!empty($bill_pages) && !empty($bill_pages[0]->ID)) {
                $bill_page_url = get_permalink($bill_pages[0]->ID);
            }
        }
        return (string) $bill_page_url;
    }
}

if (!function_exists('buildpro_payment_is_paypal_method_id')) {
    function buildpro_payment_is_paypal_method_id(string $method_id): bool
    {
        $method_id = strtolower($method_id);
        return $method_id !== '' && (strpos($method_id, 'ppcp') !== false || strpos($method_id, 'paypal') !== false);
    }
}

if (!function_exists('buildpro_payment_mark_order_from_custom_checkout')) {
    function buildpro_payment_mark_order_from_custom_checkout($order, array $data): void
    {
        if (!is_object($order) || !method_exists($order, 'update_meta_data')) {
            return;
        }
        // This flag is posted by our custom checkout JS to the WC AJAX checkout endpoint.
        if (!isset($_POST['bp_checkout_flow']) || sanitize_text_field(wp_unslash($_POST['bp_checkout_flow'])) !== '1') {
            return;
        }
        $method = '';
        if (method_exists($order, 'get_payment_method')) {
            $method = (string) $order->get_payment_method();
        }
        if (!$method && isset($data['payment_method'])) {
            $method = (string) $data['payment_method'];
        }
        if (!buildpro_payment_is_paypal_method_id($method)) {
            return;
        }
        $order->update_meta_data('_buildpro_redirect_bill_after_payment', '1');
    }
}

add_action('woocommerce_checkout_create_order', 'buildpro_payment_mark_order_from_custom_checkout', 20, 2);

if (!function_exists('buildpro_payment_success_redirect_to_bill')) {
    function buildpro_payment_success_redirect_to_bill(array $result, $order_id): array
    {
        if (!function_exists('wc_get_order')) {
            return $result;
        }
        $order = wc_get_order($order_id);
        if (!$order) {
            return $result;
        }
        $method = method_exists($order, 'get_payment_method') ? (string) $order->get_payment_method() : '';
        if (!buildpro_payment_is_paypal_method_id($method)) {
            return $result;
        }
        $flag = method_exists($order, 'get_meta') ? (string) $order->get_meta('_buildpro_redirect_bill_after_payment', true) : '';
        if ($flag !== '1') {
            return $result;
        }
        $key = method_exists($order, 'get_order_key') ? (string) $order->get_order_key() : '';
        if ($key === '') {
            return $result;
        }

        $bill_url = buildpro_payment_get_bill_page_url();
        $result['redirect'] = add_query_arg([
            'bp_order_id' => (int) $order_id,
            'key' => $key,
            'bp_from_checkout' => '1',
        ], $bill_url);
        return $result;
    }
}

add_filter('woocommerce_payment_successful_result', 'buildpro_payment_success_redirect_to_bill', 20, 2);

if (!function_exists('buildpro_payment_return_url_to_bill')) {
    function buildpro_payment_return_url_to_bill(string $return_url, $order): string
    {
        if (!is_object($order) || !method_exists($order, 'get_id')) {
            return $return_url;
        }
        $method = method_exists($order, 'get_payment_method') ? (string) $order->get_payment_method() : '';
        if (!buildpro_payment_is_paypal_method_id($method)) {
            return $return_url;
        }
        $flag = method_exists($order, 'get_meta') ? (string) $order->get_meta('_buildpro_redirect_bill_after_payment', true) : '';
        if ($flag !== '1') {
            return $return_url;
        }
        $key = method_exists($order, 'get_order_key') ? (string) $order->get_order_key() : '';
        if ($key === '') {
            return $return_url;
        }
        $bill_url = buildpro_payment_get_bill_page_url();
        return (string) add_query_arg([
            'bp_order_id' => (int) $order->get_id(),
            'key' => $key,
            'bp_from_checkout' => '1',
        ], $bill_url);
    }
}

add_filter('woocommerce_get_return_url', 'buildpro_payment_return_url_to_bill', 20, 2);
