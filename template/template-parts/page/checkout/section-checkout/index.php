<?php
$checkout_data = function_exists('buildpro_checkout_get_page_data') ? buildpro_checkout_get_page_data() : [];

$wc_active = isset($checkout_data['wc_active']) ? $checkout_data['wc_active'] : false;
$cart_items = isset($checkout_data['cart_items']) ? $checkout_data['cart_items'] : [];
$regular_total_raw = isset($checkout_data['regular_total_raw']) ? $checkout_data['regular_total_raw'] : 0.0;
$sale_total_raw = isset($checkout_data['sale_total_raw']) ? $checkout_data['sale_total_raw'] : 0.0;
$you_save_raw = isset($checkout_data['you_save_raw']) ? $checkout_data['you_save_raw'] : 0.0;
$total = isset($checkout_data['total']) ? $checkout_data['total'] : 0.0;
$wc_countries = isset($checkout_data['wc_countries']) ? $checkout_data['wc_countries'] : [];
$wc_base_country = isset($checkout_data['wc_base_country']) ? $checkout_data['wc_base_country'] : '';
$paypal_enabled = isset($checkout_data['paypal_enabled']) ? $checkout_data['paypal_enabled'] : false;
$ppcp_available = isset($checkout_data['ppcp_available']) ? (bool) $checkout_data['ppcp_available'] : false;
$ppcp_gateway_id = isset($checkout_data['ppcp_gateway_id']) ? $checkout_data['ppcp_gateway_id'] : 'ppcp-gateway';
$paypal_title = isset($checkout_data['paypal_title']) ? $checkout_data['paypal_title'] : 'PayPal';
$paypal_description = isset($checkout_data['paypal_description']) ? $checkout_data['paypal_description'] : '';
$wcpay_gateway = isset($checkout_data['wcpay_gateway']) ? $checkout_data['wcpay_gateway'] : null;
$wcpay_gateway_id = isset($checkout_data['wcpay_gateway_id']) ? $checkout_data['wcpay_gateway_id'] : 'woocommerce_payments';
$wcpay_enabled = isset($checkout_data['wcpay_enabled']) ? (bool) $checkout_data['wcpay_enabled'] : false;
$wcpay_title = isset($checkout_data['wcpay_title']) ? $checkout_data['wcpay_title'] : 'Credit Card';
$cod_enabled = isset($checkout_data['cod_enabled']) ? (bool) $checkout_data['cod_enabled'] : true;
$cod_title = isset($checkout_data['cod_title']) ? $checkout_data['cod_title'] : 'Cash on Delivery';
$bacs_enabled = isset($checkout_data['bacs_enabled']) ? (bool) $checkout_data['bacs_enabled'] : false;
$bacs_title = isset($checkout_data['bacs_title']) ? $checkout_data['bacs_title'] : 'Bank Transfer';
$bacs_desc = isset($checkout_data['bacs_desc']) ? $checkout_data['bacs_desc'] : '';
$bacs_accounts = isset($checkout_data['bacs_accounts']) ? $checkout_data['bacs_accounts'] : [];
$paypal_gateway_id = isset($checkout_data['paypal_gateway_id']) ? $checkout_data['paypal_gateway_id'] : '';
$bill_page_url = isset($checkout_data['bill_page_url']) ? $checkout_data['bill_page_url'] : home_url('/bill-page/');

$paypal_tab_enabled = $paypal_enabled && $ppcp_available;

$enabled_payment_tabs = [];
if ($cod_enabled) {
    $enabled_payment_tabs[] = 'tab-cod';
}
if ($paypal_tab_enabled) {
    $enabled_payment_tabs[] = 'tab-paypal';
}
if ($wcpay_enabled) {
    $enabled_payment_tabs[] = 'tab-card';
}
if ($bacs_enabled) {
    $enabled_payment_tabs[] = 'tab-bank';
}

$payment_tab_count = count($enabled_payment_tabs) > 0 ? count($enabled_payment_tabs) : 1;
$active_payment_tab = $enabled_payment_tabs[0] ?? 'tab-cod';

$bp_price = isset($checkout_data['bp_price']) ? $checkout_data['bp_price'] : function ($amount) {
    return '$' . number_format((float) $amount, 2);
};
$checkout_localize = isset($checkout_data['checkout_localize']) ? $checkout_data['checkout_localize'] : [
    'ajaxUrl' => esc_url_raw(add_query_arg('wc-ajax', 'checkout', home_url('/'))),
    'nonce' => wp_create_nonce('woocommerce-process_checkout'),
    'referer' => esc_url_raw('/'),
    'billUrl' => esc_url_raw($bill_page_url),
    'paypalEnabled' => $paypal_tab_enabled,
    'paypalMethodId' => $paypal_gateway_id,
    'paypalTitle' => $paypal_title,
    'wcpayEnabled' => $wcpay_enabled,
    'wcpayMethodId' => $wcpay_gateway_id,
    'codMethodId' => 'cod',
    'bankMethodId' => 'bacs',
];

$bp_ppcp_debug_enabled = function_exists('buildpro_ppcp_is_debug_enabled')
    ? buildpro_ppcp_is_debug_enabled()
    : (is_user_logged_in()
        && (current_user_can('manage_woocommerce') || current_user_can('administrator'))
        && isset($_GET['bp_ppcp_debug']));
$bp_ppcp_force_style = $bp_ppcp_debug_enabled && isset($_GET['bp_ppcp_force_style']);

// Pass debug flags to JS (no secrets).
$checkout_localize['ppcpDebug'] = [
    'enabled' => $bp_ppcp_debug_enabled,
    'forceStyle' => $bp_ppcp_force_style,
];

wp_enqueue_style(
    'checkout-section-style',
    get_template_directory_uri() . '/template/template-parts/page/checkout/section-checkout/style.css',
    [],
    '1.0.0'
);
wp_enqueue_script(
    'checkout-section-script',
    get_template_directory_uri() . '/template/template-parts/page/checkout/section-checkout/script.js',
    ['jquery'],
    '1.0.0',
    true
);
wp_localize_script(
    'checkout-section-script',
    'bpCheckout',
    $checkout_localize
);
?>

<section class="checkout-section" data-aos="fade-up">
    <div class="container">

        <div class="checkout-section__header">
            <h1 class="checkout-section__title"><?php esc_html_e('Checkout', 'buildpro'); ?></h1>
            <p class="checkout-section__subtitle">
                <?php esc_html_e('Complete your details to place the order.', 'buildpro'); ?></p>
        </div>

        <div class="checkout-section__wrapper">

            <!-- ===== LEFT: Form ===== -->
            <div class="checkout-section__left">

                <!-- Billing info -->
                <div class="checkout-card">
                    <h2 class="checkout-card__title"><?php esc_html_e('Shipping Information', 'buildpro'); ?></h2>
                    <form class="checkout woocommerce-checkout" id="checkout-form" method="post"
                        novalidate>

                        <?php do_action('woocommerce_checkout_before_customer_details'); ?>

                        <!-- Hidden WooCommerce fields (used by WooCommerce PayPal Payments Smart Buttons) -->
                        <input type="hidden" id="billing_first_name" name="billing_first_name" value="">
                        <input type="hidden" id="billing_last_name" name="billing_last_name" value="">
                        <input type="hidden" id="billing_email" name="billing_email" value="">
                        <input type="hidden" id="billing_phone" name="billing_phone" value="">
                        <input type="hidden" id="billing_address_1" name="billing_address_1" value="">
                        <input type="hidden" id="billing_city" name="billing_city" value="">
                        <input type="hidden" id="billing_postcode" name="billing_postcode" value="">
                        <input type="hidden" id="billing_country" name="billing_country" value="">
                        <input type="hidden" id="shipping_first_name" name="shipping_first_name" value="">
                        <input type="hidden" id="shipping_last_name" name="shipping_last_name" value="">
                        <input type="hidden" id="shipping_address_1" name="shipping_address_1" value="">
                        <input type="hidden" id="shipping_city" name="shipping_city" value="">
                        <input type="hidden" id="shipping_postcode" name="shipping_postcode" value="">
                        <input type="hidden" id="shipping_country" name="shipping_country" value="">
                        <input type="hidden" name="ship_to_different_address" value="0">

                        <input type="hidden" name="woocommerce-process-checkout-nonce"
                            value="<?php echo esc_attr($checkout_localize['nonce']); ?>">
                        <input type="hidden" name="_wpnonce"
                            value="<?php echo esc_attr($checkout_localize['nonce']); ?>">
                        <input type="hidden" name="_wp_http_referer"
                            value="<?php echo esc_attr(parse_url($checkout_localize['referer'], PHP_URL_PATH) ?: '/'); ?>">
                        <input type="hidden" name="terms" value="on">
                        <input type="hidden" name="terms-field" value="1">
                        <input type="hidden" id="bp-checkout-flow" name="bp_checkout_flow" value="0">

                        <div style="display:none">
                            <?php if ($cod_enabled) : ?>
                                <input type="radio" class="input-radio" id="payment_method_cod" name="payment_method"
                                    value="cod" <?php checked($active_payment_tab, 'tab-cod'); ?>>
                            <?php endif; ?>
                            <?php if ($bacs_enabled) : ?>
                                <input type="radio" class="input-radio" id="payment_method_bacs" name="payment_method"
                                    value="bacs" <?php checked($active_payment_tab, 'tab-bank'); ?>>
                            <?php endif; ?>
                            <?php if ($paypal_tab_enabled && !empty($paypal_gateway_id)) : ?>
                                <input type="radio" class="input-radio"
                                    id="payment_method_<?php echo esc_attr($paypal_gateway_id); ?>" name="payment_method"
                                    value="<?php echo esc_attr($paypal_gateway_id); ?>"
                                    <?php checked($active_payment_tab, 'tab-paypal'); ?>>
                            <?php endif; ?>
                            <?php if ($wcpay_enabled && !empty($wcpay_gateway_id)) : ?>
                                <input type="radio" class="input-radio"
                                    id="payment_method_<?php echo esc_attr($wcpay_gateway_id); ?>" name="payment_method"
                                    value="<?php echo esc_attr($wcpay_gateway_id); ?>"
                                    <?php checked($active_payment_tab, 'tab-card'); ?>>
                            <?php endif; ?>
                        </div>

                        <div class="checkout-form__row">
                            <div class="checkout-form__group">
                                <label class="checkout-form__label"
                                    for="co-fullname"><?php esc_html_e('Full Name', 'buildpro'); ?>
                                    <span>*</span></label>
                                <input class="checkout-form__input" type="text" id="co-fullname" name="fullname"
                                    placeholder="John Doe" required>
                                <span class="checkout-form__error" data-for="co-fullname"></span>
                            </div>
                            <div class="checkout-form__group">
                                <label class="checkout-form__label"
                                    for="co-phone"><?php esc_html_e('Phone Number', 'buildpro'); ?>
                                    <span>*</span></label>
                                <input class="checkout-form__input" type="tel" id="co-phone" name="phone"
                                    placeholder="+1 234 567 8900" required>
                                <span class="checkout-form__error" data-for="co-phone"></span>
                            </div>
                        </div>

                        <div class="checkout-form__group">
                            <label class="checkout-form__label"
                                for="co-email"><?php esc_html_e('Email Address', 'buildpro'); ?> <span>*</span></label>
                            <input class="checkout-form__input" type="email" id="co-email" name="email"
                                placeholder="example@email.com" required>
                            <span class="checkout-form__error" data-for="co-email"></span>
                        </div>

                        <div class="checkout-form__group">
                            <label class="checkout-form__label"
                                for="co-address"><?php esc_html_e('Address', 'buildpro'); ?> <span>*</span></label>
                            <input class="checkout-form__input" type="text" id="co-address" name="address"
                                placeholder="House number, street name..." required>
                            <span class="checkout-form__error" data-for="co-address"></span>
                        </div>

                        <div class="checkout-form__row">
                            <div class="checkout-form__group">
                                <label class="checkout-form__label"
                                    for="co-city"><?php esc_html_e('City', 'buildpro'); ?> <span>*</span></label>
                                <input class="checkout-form__input" type="text" id="co-city" name="city"
                                    placeholder="New York" required>
                                <span class="checkout-form__error" data-for="co-city"></span>
                            </div>
                            <div class="checkout-form__group">
                                <label class="checkout-form__label"
                                    for="co-zip"><?php esc_html_e('ZIP / Postal Code', 'buildpro'); ?></label>
                                <input class="checkout-form__input" type="text" id="co-zip" name="zip"
                                    placeholder="10001">
                            </div>
                        </div>

                        <div class="checkout-form__group">
                            <label class="checkout-form__label"
                                for="co-country"><?php esc_html_e('Country', 'buildpro'); ?> <span>*</span></label>
                            <select class="checkout-form__input checkout-form__select" id="co-country" name="country"
                                required>
                                <option value=""><?php esc_html_e('-- Select country --', 'buildpro'); ?></option>
                                <?php foreach ($wc_countries as $code => $name) : ?>
                                    <option value="<?php echo esc_attr($code); ?>"
                                        <?php selected($code, $wc_base_country); ?>>
                                        <?php echo esc_html($name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="checkout-form__error" data-for="co-country"></span>
                        </div>

                        <div class="checkout-form__group">
                            <label class="checkout-form__label"
                                for="co-note"><?php esc_html_e('Order Notes', 'buildpro'); ?></label>
                            <textarea class="checkout-form__input checkout-form__textarea" id="co-note"
                                name="order_comments"
                                placeholder="Any special instructions for delivery or packaging..." rows="3"></textarea>
                        </div>

                        <?php do_action('woocommerce_checkout_after_customer_details'); ?>
                </div>

                <!-- Payment tabs -->
                <div class="checkout-card checkout-card--payment">
                    <h2 class="checkout-card__title"><?php esc_html_e('Payment Method', 'buildpro'); ?></h2>

                    <div class="payment-tabs payment-tabs--count-<?php echo esc_attr($payment_tab_count); ?>"
                        role="tablist">

                        <!-- Tab buttons -->
                        <?php if ($cod_enabled) : ?>
                            <button type="button"
                                class="payment-tab<?php echo $active_payment_tab === 'tab-cod' ? ' payment-tab--active' : ''; ?>"
                                role="tab" data-target="tab-cod"
                                aria-selected="<?php echo $active_payment_tab === 'tab-cod' ? 'true' : 'false'; ?>">
                                <span class="payment-tab__icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="6" width="18" height="13" rx="2" />
                                        <path d="M3 10h18" />
                                        <circle cx="12" cy="15" r="2" />
                                    </svg>
                                </span>
                                <span class="payment-tab__label"><?php echo esc_html($cod_title); ?></span>
                            </button>
                        <?php endif; ?>

                        <?php if ($paypal_tab_enabled) : ?>
                            <button type="button"
                                class="payment-tab<?php echo $active_payment_tab === 'tab-paypal' ? ' payment-tab--active' : ''; ?>"
                                role="tab" data-target="tab-paypal"
                                aria-selected="<?php echo $active_payment_tab === 'tab-paypal' ? 'true' : 'false'; ?>">
                                <span class="payment-tab__icon payment-tab__icon--paypal">
                                    <img class="paypal__image"
                                        src="<?php echo get_template_directory_uri(); ?>/assets/images/icon/paypal.png"
                                        alt="PayPal">
                                </span>
                                <span class="payment-tab__label"><?php echo esc_html($paypal_title); ?></span>
                            </button>
                        <?php endif; ?>

                        <?php if ($wcpay_enabled) : ?>
                            <button type="button"
                                class="payment-tab<?php echo $active_payment_tab === 'tab-card' ? ' payment-tab--active' : ''; ?>"
                                role="tab" data-target="tab-card"
                                aria-selected="<?php echo $active_payment_tab === 'tab-card' ? 'true' : 'false'; ?>">
                                <span class="payment-tab__icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="2" y="5" width="20" height="14" rx="2" />
                                        <path d="M2 10h20" />
                                        <path d="M6 15h3M14 15h4" />
                                    </svg>
                                </span>
                                <span class="payment-tab__label"><?php echo esc_html($wcpay_title); ?></span>
                            </button>
                        <?php endif; ?>

                        <?php if ($bacs_enabled) : ?>
                            <button type="button"
                                class="payment-tab<?php echo $active_payment_tab === 'tab-bank' ? ' payment-tab--active' : ''; ?>"
                                role="tab" data-target="tab-bank"
                                aria-selected="<?php echo $active_payment_tab === 'tab-bank' ? 'true' : 'false'; ?>">
                                <span class="payment-tab__icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                        <polyline points="9 22 9 12 15 12 15 22" />
                                    </svg>
                                </span>
                                <span class="payment-tab__label"><?php echo esc_html($bacs_title); ?></span>
                            </button>
                        <?php endif; ?>

                    </div><!-- /payment-tabs -->

                    <!-- Tab panels -->
                    <div class="payment-panels">

                        <!-- COD -->
                        <?php if ($cod_enabled) : ?>
                            <div class="payment-panel<?php echo $active_payment_tab === 'tab-cod' ? ' payment-panel--active' : ''; ?>"
                                id="tab-cod" role="tabpanel">
                                <div class="payment-panel__icon-wrap">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <rect x="3" y="6" width="18" height="13" rx="2" />
                                        <path d="M3 10h18" />
                                        <circle cx="12" cy="15" r="2" />
                                    </svg>
                                </div>
                                <p class="payment-panel__desc">
                                    <?php esc_html_e('Pay in cash upon delivery. Our delivery staff will collect payment directly at your shipping address.', 'buildpro'); ?>
                                </p>
                                <div class="payment-panel__note">
                                    <svg viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <?php esc_html_e('Please prepare the exact amount to make the delivery process smoother.', 'buildpro'); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($paypal_tab_enabled) : ?>
                            <div class="payment-panel<?php echo $active_payment_tab === 'tab-paypal' ? ' payment-panel--active' : ''; ?>"
                                id="tab-paypal" role="tabpanel">

                                <!-- PayPal header -->
                                <div class="pp-panel__header">
                                    <div class="pp-panel__logo">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/icon/paypal.png"
                                            alt="PayPal" class="pp-panel__logo-img">
                                        <span class="pp-panel__logo-label">PayPal</span>
                                    </div>
                                    <span class="pp-panel__badge">
                                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <?php esc_html_e('Secure', 'buildpro'); ?>
                                    </span>
                                </div>

                                <!-- WooCommerce notices -->
                                <div class="woocommerce-notices-wrapper"></div>

                                <!-- PPCP Smart Buttons area -->
                                <div class="pp-panel__buttons">
                                    <?php if ($ppcp_available) : ?>
                                        <?php
                                        /**
                                         * Trigger PPCP renderer.
                                         * Plugin outputs:
                                         *   <div class="ppc-button-wrapper">
                                         *     <div id="ppc-button-ppcp-gateway"></div>
                                         *   </div>
                                         * Height / shape / color / label are injected by the PayPal SDK
                                         * based on WooCommerce > Payments > PayPal > Styling settings.
                                         */
                                        $ppcp_renderer_hook = apply_filters(
                                            'woocommerce_paypal_payments_checkout_button_renderer_hook',
                                            'woocommerce_review_order_after_payment'
                                        );
                                        if (is_string($ppcp_renderer_hook) && $ppcp_renderer_hook !== '') {
                                            do_action($ppcp_renderer_hook);
                                        }
                                        ?>
                                    <?php else : ?>
                                        <div class="pp-panel__error">
                                            <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd"
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <?php esc_html_e('PayPal is currently unavailable. Please try another payment method.', 'buildpro'); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Divider + trust tagline -->
                                <div class="pp-panel__footer">
                                    <span class="pp-panel__divider"></span>
                                    <span class="pp-panel__trust">
                                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <?php esc_html_e('Protected by PayPal Buyer Protection', 'buildpro'); ?>
                                    </span>
                                    <span class="pp-panel__divider"></span>
                                </div>

                                <?php if ($bp_ppcp_debug_enabled) : ?>
                                    <?php
                                    $ppcp_settings = (array) get_option('woocommerce-ppcp-settings', []);
                                    $ppcp_styling = (array) get_option('woocommerce-ppcp-data-styling', []);
                                    $classic_checkout = isset($ppcp_styling['classic_checkout']) ? $ppcp_styling['classic_checkout'] : null;
                                    if (is_object($classic_checkout) && method_exists($classic_checkout, 'to_array')) {
                                        $classic_checkout = $classic_checkout->to_array();
                                    }
                                    if (!is_array($classic_checkout)) {
                                        $classic_checkout = [];
                                    }

                                    $style_is_enqueued = function_exists('wp_style_is') ? wp_style_is('gateway', 'enqueued') : false;
                                    $script_is_enqueued = function_exists('wp_script_is') ? wp_script_is('ppcp-smart-button', 'enqueued') : false;

                                    $selected_method = '';
                                    if (!empty($_POST['payment_method'])) {
                                        $selected_method = (string) $_POST['payment_method'];
                                    } elseif (!empty($ppcp_gateway_id) && $ppcp_available) {
                                        $selected_method = $ppcp_gateway_id;
                                    } else {
                                        $selected_method = $paypal_gateway_id;
                                    }

                                    $safe_settings = [
                                        'smart_button_locations' => $ppcp_settings['smart_button_locations'] ?? null,
                                        'button_checkout_layout' => $ppcp_settings['button_checkout_layout'] ?? null,
                                        'button_checkout_color' => $ppcp_settings['button_checkout_color'] ?? null,
                                        'button_checkout_shape' => $ppcp_settings['button_checkout_shape'] ?? null,
                                        'button_checkout_label' => $ppcp_settings['button_checkout_label'] ?? null,
                                        'button_checkout_tagline' => $ppcp_settings['button_checkout_tagline'] ?? null,
                                        'button_checkout_height' => $ppcp_settings['button_checkout_height'] ?? null,
                                    ];

                                    $bp_is_custom_checkout_template = function_exists('buildpro_ppcp_is_custom_checkout_template')
                                        ? (buildpro_ppcp_is_custom_checkout_template() ? 'true' : 'false')
                                        : '(missing helper)';
                                    ?>
                                    <pre class="bp-ppcp-debug">
PPCP Debug
- ppcp_available: <?php echo $ppcp_available ? 'true' : 'false'; ?>

- ppcp_gateway_id: <?php echo esc_html($ppcp_gateway_id ?: '(empty)'); ?>

- is_checkout(): <?php echo function_exists('is_checkout') && is_checkout() ? 'true' : 'false'; ?>

- is_custom_checkout: <?php echo esc_html($bp_is_custom_checkout_template); ?>

- ppcp_context(filter): <?php echo esc_html((string) apply_filters('woocommerce_paypal_payments_context', '')); ?>

- style[gateway]: <?php echo $style_is_enqueued ? 'enqueued' : 'not enqueued'; ?>

- script[ppcp-smart-button]: <?php echo $script_is_enqueued ? 'enqueued' : 'not enqueued'; ?>


Checkout button settings:
<?php echo esc_html(wp_json_encode($safe_settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?>

classic_checkout styling:
<?php echo esc_html(wp_json_encode($classic_checkout, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?>
                                    </pre>
                                <?php endif; ?>

                            </div>
                        <?php endif; ?>



                        <!-- Credit card -->
                        <?php if ($wcpay_enabled) : ?>
                            <div class="payment-panel<?php echo $active_payment_tab === 'tab-card' ? ' payment-panel--active' : ''; ?>"
                                id="tab-card" role="tabpanel">
                                <?php if (is_object($wcpay_gateway) && method_exists($wcpay_gateway, 'payment_fields')) : ?>
                                    <div class="woocommerce-notices-wrapper"></div>
                                    <div
                                        class="wc_payment_method payment_method_<?php echo esc_attr($wcpay_gateway_id); ?> bp-wcpay-method">
                                        <label class="screen-reader-text"
                                            for="payment_method_<?php echo esc_attr($wcpay_gateway_id); ?>">
                                            <?php
                                            $wcpay_label_text = method_exists($wcpay_gateway, 'get_title') ? $wcpay_gateway->get_title() : $wcpay_title;
                                            $wcpay_label_icon = method_exists($wcpay_gateway, 'get_icon') ? $wcpay_gateway->get_icon() : '';
                                            echo wp_kses_post(trim($wcpay_label_text . ' ' . $wcpay_label_icon));
                                            ?>
                                        </label>
                                        <div
                                            class="payment_box payment_method_<?php echo esc_attr($wcpay_gateway_id); ?> bp-wcpay-fields">
                                            <?php $wcpay_gateway->payment_fields(); ?>
                                        </div>
                                    </div>
                                    <div class="payment-panel__secure">
                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <?php esc_html_e('Card payment is handled securely by WooPayments.', 'buildpro'); ?>
                                    </div>
                                <?php else : ?>
                                    <div class="payment-panel__note">
                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <?php esc_html_e('WooPayments credit card gateway is unavailable. Please enable WooPayments in WooCommerce settings.', 'buildpro'); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Bank transfer -->
                        <?php if ($bacs_enabled) : ?>
                            <div class="payment-panel<?php echo $active_payment_tab === 'tab-bank' ? ' payment-panel--active' : ''; ?>"
                                id="tab-bank" role="tabpanel">
                                <p class="payment-panel__desc"><?php echo wp_kses_post(wpautop(wptexturize($bacs_desc))); ?>
                                </p>

                                <?php if (!empty($bacs_accounts)) : ?>
                                    <?php foreach ($bacs_accounts as $i => $account) :
                                        $acct_name   = isset($account['account_name'])   ? trim($account['account_name'])   : '';
                                        $acct_number = isset($account['account_number']) ? trim($account['account_number']) : '';
                                        $bank_name   = isset($account['bank_name'])      ? trim($account['bank_name'])      : '';
                                        $sort_code   = isset($account['sort_code'])      ? trim($account['sort_code'])      : '';
                                        $iban        = isset($account['iban'])           ? trim($account['iban'])           : '';
                                        $bic         = isset($account['bic'])            ? trim($account['bic'])            : '';
                                        $copy_id     = 'bank-acct-' . $i;
                                    ?>
                                        <div class="bank-info<?php echo $i > 0 ? ' bank-info--extra' : ''; ?>">
                                            <?php if ($bank_name) : ?>
                                                <div class="bank-info__row">
                                                    <span class="bank-info__label"><?php esc_html_e('Bank', 'buildpro'); ?></span>
                                                    <span class="bank-info__value"><?php echo esc_html($bank_name); ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($acct_name) : ?>
                                                <div class="bank-info__row">
                                                    <span
                                                        class="bank-info__label"><?php esc_html_e('Account Name', 'buildpro'); ?></span>
                                                    <span class="bank-info__value"><?php echo esc_html($acct_name); ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($acct_number) : ?>
                                                <div class="bank-info__row">
                                                    <span
                                                        class="bank-info__label"><?php esc_html_e('Account No.', 'buildpro'); ?></span>
                                                    <span class="bank-info__value bank-info__value--copy"
                                                        id="<?php echo esc_attr($copy_id); ?>"><?php echo esc_html($acct_number); ?></span>
                                                    <button type="button" class="bank-info__copy-btn"
                                                        data-copy="<?php echo esc_attr($copy_id); ?>"
                                                        title="<?php esc_attr_e('Copy', 'buildpro'); ?>">
                                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />
                                                            <path
                                                                d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($sort_code) : ?>
                                                <div class="bank-info__row">
                                                    <span class="bank-info__label"><?php esc_html_e('Sort Code', 'buildpro'); ?></span>
                                                    <span class="bank-info__value bank-info__value--copy"
                                                        id="<?php echo esc_attr($copy_id . '-sort'); ?>"><?php echo esc_html($sort_code); ?></span>
                                                    <button type="button" class="bank-info__copy-btn"
                                                        data-copy="<?php echo esc_attr($copy_id . '-sort'); ?>"
                                                        title="<?php esc_attr_e('Copy', 'buildpro'); ?>">
                                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />
                                                            <path
                                                                d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($iban) : ?>
                                                <div class="bank-info__row">
                                                    <span class="bank-info__label"><?php esc_html_e('IBAN', 'buildpro'); ?></span>
                                                    <span class="bank-info__value bank-info__value--copy"
                                                        id="<?php echo esc_attr($copy_id . '-iban'); ?>"><?php echo esc_html($iban); ?></span>
                                                    <button type="button" class="bank-info__copy-btn"
                                                        data-copy="<?php echo esc_attr($copy_id . '-iban'); ?>"
                                                        title="<?php esc_attr_e('Copy', 'buildpro'); ?>">
                                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />
                                                            <path
                                                                d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($bic) : ?>
                                                <div class="bank-info__row">
                                                    <span
                                                        class="bank-info__label"><?php esc_html_e('BIC / Swift', 'buildpro'); ?></span>
                                                    <span class="bank-info__value bank-info__value--copy"
                                                        id="<?php echo esc_attr($copy_id . '-bic'); ?>"><?php echo esc_html($bic); ?></span>
                                                    <button type="button" class="bank-info__copy-btn"
                                                        data-copy="<?php echo esc_attr($copy_id . '-bic'); ?>"
                                                        title="<?php esc_attr_e('Copy', 'buildpro'); ?>">
                                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />
                                                            <path
                                                                d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <div class="payment-panel__note">
                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <?php esc_html_e('No bank account details have been configured yet. Please contact us for payment instructions.', 'buildpro'); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="payment-panel__note">
                                    <svg viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <?php esc_html_e('Please use the exact reference code so your order can be processed quickly.', 'buildpro'); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div><!-- /payment-panels -->

                    <div class="form-row place-order" style="display:none;">
                        <button type="submit" class="button alt wp-element-button"
                            name="woocommerce_checkout_place_order" id="place_order"
                            value="<?php echo esc_attr__('Place order', 'woocommerce'); ?>"
                            data-value="<?php echo esc_attr__('Place order', 'woocommerce'); ?>">
                            <?php echo esc_html__('Place order', 'woocommerce'); ?>
                        </button>
                    </div>

                </div><!-- /checkout-card--payment -->

                </form>

            </div><!-- /left -->

            <!-- ===== RIGHT: Order summary ===== -->
            <div class="checkout-section__right">
                <div class="checkout-card checkout-card--summary">
                    <h2 class="checkout-card__title"><?php esc_html_e('Order Summary', 'buildpro'); ?></h2>

                    <div class="order-items" id="order-items">
                        <?php if (empty($cart_items)) : ?>
                            <p class="order-items__empty"><?php esc_html_e('Your cart is empty.', 'buildpro'); ?></p>
                        <?php else : ?>
                            <?php foreach ($cart_items as $item) :
                                $product  = $item['data'];
                                $qty      = intval($item['quantity']);
                                $price    = floatval($product->get_price());
                                $regular_item_price = floatval($product->get_regular_price());
                                $sale_item_price    = $product->get_sale_price() !== '' ? floatval($product->get_sale_price()) : $price;
                                if ($regular_item_price <= 0) {
                                    $regular_item_price = $price;
                                }
                                $regular_item_total = $regular_item_price * $qty;
                                $sale_item_total    = $sale_item_price * $qty;
                                $item_save_total    = max(0, $regular_item_total - $sale_item_total);
                                $name     = $product->get_name();
                                $img_url  = get_the_post_thumbnail_url($item['product_id'], 'thumbnail');
                            ?>
                                <div class="order-item">
                                    <div class="order-item__image">
                                        <?php if ($img_url) : ?>
                                            <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($name); ?>">
                                        <?php else : ?>
                                            <div class="order-item__image-placeholder"></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="order-item__info">
                                        <p class="order-item__name"><?php echo esc_html($name); ?></p>
                                        <p class="order-item__meta">
                                            <?php printf(esc_html__('Qty: %s', 'buildpro'), esc_html($qty)); ?></p>
                                        <p class="order-item__price-line order-item__price-line--regular">
                                            <span><?php esc_html_e('Regular:', 'buildpro'); ?></span>
                                            <span><?php echo $bp_price($regular_item_total); ?></span>
                                        </p>
                                        <p class="order-item__price-line order-item__price-line--sale">
                                            <span><?php esc_html_e('Sale:', 'buildpro'); ?></span>
                                            <span><?php echo $bp_price($sale_item_total); ?></span>
                                        </p>
                                        <p class="order-item__price-line order-item__price-line--save">
                                            <span><?php esc_html_e('You Save:', 'buildpro'); ?></span>
                                            <span><?php echo $bp_price($item_save_total); ?></span>
                                        </p>
                                    </div>

                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="order-divider"></div>

                    <div class="order-totals">
                        <div class="order-totals__row">
                            <span><?php esc_html_e('Regular Price', 'buildpro'); ?></span>
                            <span id="ot-regular"><?php echo $bp_price($regular_total_raw); ?></span>
                        </div>
                        <div class="order-totals__row">
                            <span><?php esc_html_e('Sale Price', 'buildpro'); ?></span>
                            <span id="ot-sale"><?php echo $bp_price($sale_total_raw); ?></span>
                        </div>
                        <div class="order-totals__row">
                            <span><?php esc_html_e('You Save', 'buildpro'); ?></span>
                            <span id="ot-save"><?php echo $bp_price($you_save_raw); ?></span>
                        </div>
                        <div class="order-divider"></div>
                        <div class="order-totals__row order-totals__row--total">
                            <span><?php esc_html_e('Total', 'buildpro'); ?></span>
                            <span id="ot-total"><?php echo $bp_price($total); ?></span>
                        </div>
                    </div>

                    <button type="button" class="checkout-submit-btn" id="checkout-submit-btn">
                        <?php esc_html_e('Bill Order', 'buildpro'); ?>
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    <p class="checkout-secure-note">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <?php esc_html_e('Transaction secured &amp; encrypted', 'buildpro'); ?>
                    </p>
                </div>
            </div><!-- /right -->

        </div><!-- /wrapper -->
    </div><!-- /container -->
</section>