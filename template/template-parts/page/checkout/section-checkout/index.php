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
$payment_tab_count = isset($checkout_data['payment_tab_count']) ? $checkout_data['payment_tab_count'] : 3;
$bacs_desc = isset($checkout_data['bacs_desc']) ? $checkout_data['bacs_desc'] : '';
$bacs_accounts = isset($checkout_data['bacs_accounts']) ? $checkout_data['bacs_accounts'] : [];
$paypal_gateway_id = isset($checkout_data['paypal_gateway_id']) ? $checkout_data['paypal_gateway_id'] : '';
$bill_page_url = isset($checkout_data['bill_page_url']) ? $checkout_data['bill_page_url'] : home_url('/bill-page/');
$bp_price = isset($checkout_data['bp_price']) ? $checkout_data['bp_price'] : function ($amount) {
    return '$' . number_format((float) $amount, 2);
};
$checkout_localize = isset($checkout_data['checkout_localize']) ? $checkout_data['checkout_localize'] : [
    'ajaxUrl' => esc_url_raw(add_query_arg('wc-ajax', 'checkout', home_url('/'))),
    'nonce' => wp_create_nonce('woocommerce-process_checkout'),
    'referer' => esc_url_raw('/'),
    'billUrl' => esc_url_raw($bill_page_url),
    'paypalEnabled' => $paypal_enabled,
    'paypalMethodId' => $paypal_gateway_id,
    'paypalTitle' => $paypal_title,
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
                    <form class="checkout-form checkout woocommerce-checkout" id="checkout-form" method="post"
                        novalidate>

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

                        <?php if ($ppcp_available) : ?>
                            <input type="radio" name="payment_method" value="<?php echo esc_attr($ppcp_gateway_id); ?>"
                                checked style="display:none">
                        <?php elseif (!empty($paypal_gateway_id)) : ?>
                            <input type="radio" name="payment_method" value="<?php echo esc_attr($paypal_gateway_id); ?>"
                                checked style="display:none">
                        <?php endif; ?>

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
                            <textarea class="checkout-form__input checkout-form__textarea" id="co-note" name="note"
                                placeholder="Any special instructions for delivery or packaging..." rows="3"></textarea>
                        </div>

                    </form>
                </div>

                <!-- Payment tabs -->
                <div class="checkout-card checkout-card--payment">
                    <h2 class="checkout-card__title"><?php esc_html_e('Payment Method', 'buildpro'); ?></h2>

                    <div class="payment-tabs payment-tabs--count-<?php echo esc_attr($payment_tab_count); ?>"
                        role="tablist">

                        <!-- Tab buttons -->
                        <button class="payment-tab payment-tab--active" role="tab" data-target="tab-cod"
                            aria-selected="true">
                            <span class="payment-tab__icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="6" width="18" height="13" rx="2" />
                                    <path d="M3 10h18" />
                                    <circle cx="12" cy="15" r="2" />
                                </svg>
                            </span>
                            <span class="payment-tab__label"><?php esc_html_e('Cash on Delivery', 'buildpro'); ?></span>
                        </button>

                        <?php if ($paypal_enabled) : ?>
                            <button class="payment-tab" role="tab" data-target="tab-paypal" aria-selected="false">
                                <span class="payment-tab__icon payment-tab__icon--paypal">
                                    <img class="paypal__image"
                                        src="<?php echo get_template_directory_uri(); ?>/assets/images/icon/paypal.png"
                                        alt="PayPal">
                                </span>
                                <span class="payment-tab__label"><?php echo esc_html($paypal_title); ?></span>
                            </button>
                        <?php endif; ?>

                        <button class="payment-tab" role="tab" data-target="tab-card" aria-selected="false">
                            <span class="payment-tab__icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="5" width="20" height="14" rx="2" />
                                    <path d="M2 10h20" />
                                    <path d="M6 15h3M14 15h4" />
                                </svg>
                            </span>
                            <span class="payment-tab__label"><?php esc_html_e('Credit Card', 'buildpro'); ?></span>
                        </button>

                        <button class="payment-tab" role="tab" data-target="tab-bank" aria-selected="false">
                            <span class="payment-tab__icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                    <polyline points="9 22 9 12 15 12 15 22" />
                                </svg>
                            </span>
                            <span class="payment-tab__label"><?php esc_html_e('Bank Transfer', 'buildpro'); ?></span>
                        </button>

                    </div><!-- /payment-tabs -->

                    <!-- Tab panels -->
                    <div class="payment-panels">

                        <!-- COD -->
                        <div class="payment-panel payment-panel--active" id="tab-cod" role="tabpanel">
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

                        <!-- PayPal -->
                        <?php if ($paypal_enabled) : ?>
                            <div class="payment-panel" id="tab-paypal" role="tabpanel">
                                <div class="payment-panel__icon-wrap payment-panel__icon-wrap--paypal">
                                    <img class="paypal__image"
                                        src="<?php echo get_template_directory_uri(); ?>/assets/images/icon/paypal.png"
                                        alt="PayPal">
                                </div>
                                <div class="payment-panel__desc payment-panel__desc--left">
                                    <?php echo $paypal_description; ?></div>

                                <div class="woocommerce-notices-wrapper"></div>

                                <?php if ($ppcp_available) : ?>
                                    <div class="bp-paypal-smart-buttons">
                                        <?php
                                        // WooCommerce PayPal Payments renders both the PayPal Smart Buttons and the
                                        // optional "Standard Card Button" (Debit/Credit) on this checkout hook.
                                        // In our custom checkout UI, the PayPal tab should show PayPal (+ Pay Later)
                                        // only, so we strip the card-button wrapper from the rendered HTML.
                                        ob_start();
                                        do_action('woocommerce_review_order_after_payment');
                                        $ppcp_html = (string) ob_get_clean();

                                        // Remove only the "Standard Card Button" wrapper.
                                        // Regex here can easily get greedy and wipe both wrappers, so we prefer DOM.
                                        if (class_exists('DOMDocument')) {
                                            $dom = new DOMDocument();
                                            $previous_use_errors = libxml_use_internal_errors(true);

                                            $dom->loadHTML(
                                                '<!doctype html><html><head><meta charset="utf-8"></head><body>' . $ppcp_html . '</body></html>',
                                                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
                                            );

                                            $card_target = $dom->getElementById('ppc-button-ppcp-card-button-gateway');
                                            if ($card_target instanceof DOMElement) {
                                                $node = $card_target;
                                                while ($node && $node instanceof DOMElement) {
                                                    $class_attr = (string) $node->getAttribute('class');
                                                    if (strpos(' ' . $class_attr . ' ', ' ppc-button-wrapper ') !== false) {
                                                        if ($node->parentNode) {
                                                            $node->parentNode->removeChild($node);
                                                        }
                                                        break;
                                                    }
                                                    $node = $node->parentNode;
                                                }
                                            }

                                            $body = $dom->getElementsByTagName('body')->item(0);
                                            if ($body) {
                                                $ppcp_html = '';
                                                foreach ($body->childNodes as $child) {
                                                    $ppcp_html .= $dom->saveHTML($child);
                                                }
                                            }

                                            libxml_clear_errors();
                                            libxml_use_internal_errors($previous_use_errors);
                                        } else {
                                            // Fallback: match a wrapper that contains the card target, without crossing into other wrappers.
                                            $ppcp_html = preg_replace(
                                                '#<div\s+class="ppc-button-wrapper"[^>]*>(?:(?!<div\s+class="ppc-button-wrapper").)*<div\s+id="ppc-button-ppcp-card-button-gateway"[^>]*></div>(?:(?!<div\s+class="ppc-button-wrapper").)*</div>#s',
                                                '',
                                                $ppcp_html
                                            );
                                        }

                                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                        echo $ppcp_html;
                                        ?>
                                    </div>

                                    <!-- Required by WooCommerce PayPal Payments JS: it clicks #place_order after approval -->
                                    <button type="button" id="place_order" style="display:none"></button>
                                <?php else : ?>
                                    <button type="button" id="bp-paypal-pay-btn" class="payment-panel__paypal-btn">
                                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                                            <path
                                                d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944 3.217a.77.77 0 0 1 .761-.645h6.747c2.37 0 4.062.643 4.859 1.863.378.578.521 1.183.44 1.851a5.14 5.14 0 0 1-.031.382c-.583 3.147-2.633 4.553-5.987 4.553H9.79a.77.77 0 0 0-.76.652l-.876 5.55a.641.641 0 0 1-.633.541zm9.415-13.48c-.01.06-.019.121-.03.182-.71 3.651-3.169 5.357-7.264 5.357h-1.87a.64.64 0 0 0-.633.541l-1.05 6.652h3.303a.641.641 0 0 0 .633-.54l.877-5.551a.77.77 0 0 1 .76-.652h1.944c3.061 0 4.967-1.234 5.508-3.988.252-1.294-.003-2.233-.678-2.9a4.066 4.066 0 0 0-.5-.101z" />
                                        </svg>
                                        <?php echo esc_html(sprintf(__('Continue with %s', 'buildpro'), $paypal_title)); ?>
                                    </button>
                                <?php endif; ?>

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
                                        'smart_button_enable_styling_per_location' => $ppcp_settings['smart_button_enable_styling_per_location'] ?? null,
                                        'smart_button_locations' => $ppcp_settings['smart_button_locations'] ?? null,
                                        'pay_later_button_enabled' => $ppcp_settings['pay_later_button_enabled'] ?? null,
                                        'pay_later_button_locations' => $ppcp_settings['pay_later_button_locations'] ?? null,
                                        // These may or may not exist depending on plugin version/migration.
                                        'button_general_layout' => $ppcp_settings['button_general_layout'] ?? null,
                                        'button_general_color' => $ppcp_settings['button_general_color'] ?? null,
                                        'button_general_shape' => $ppcp_settings['button_general_shape'] ?? null,
                                        'button_general_label' => $ppcp_settings['button_general_label'] ?? null,
                                        'button_general_tagline' => $ppcp_settings['button_general_tagline'] ?? null,
                                        'button_checkout_layout' => $ppcp_settings['button_checkout_layout'] ?? null,
                                        'button_checkout_color' => $ppcp_settings['button_checkout_color'] ?? null,
                                        'button_checkout_shape' => $ppcp_settings['button_checkout_shape'] ?? null,
                                        'button_checkout_label' => $ppcp_settings['button_checkout_label'] ?? null,
                                        'button_checkout_tagline' => $ppcp_settings['button_checkout_tagline'] ?? null,
                                    ];
                                    ?>
                                    <pre class="bp-ppcp-debug"
                                        style="margin-top:12px; padding:10px; border:1px solid #ddd; background:#fff; max-width:100%; overflow:auto; font-size:12px; line-height:1.4;">
                                        PPCP Debug (temporary)
                                        - ppcp_available: <?php echo $ppcp_available ? 'true' : 'false'; ?>
                                        - paypal_gateway_id (detected): <?php echo esc_html($paypal_gateway_id ?: '(empty)'); ?>
                                        - ppcp_gateway_id: <?php echo esc_html($ppcp_gateway_id ?: '(empty)'); ?>
                                        - selected_method (theme hidden radio): <?php echo esc_html($selected_method ?: '(empty)'); ?>
                                        - is_checkout(): <?php echo function_exists('is_checkout') && is_checkout() ? 'true' : 'false'; ?>
                                        - woocommerce_paypal_payments_context(filter): <?php echo esc_html((string) apply_filters('woocommerce_paypal_payments_context', '')); ?>
                                        - assets enqueued: style[gateway]=<?php echo $style_is_enqueued ? 'yes' : 'no'; ?>, script[ppcp-smart-button]=<?php echo $script_is_enqueued ? 'yes' : 'no'; ?>
                                        - force style param: <?php echo $bp_ppcp_force_style ? 'on' : 'off'; ?>

                                        WooCommerce PayPal Payments settings (sanitized):
                                        <?php echo esc_html(wp_json_encode($safe_settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?>

                                        woocommerce-ppcp-data-styling.classic_checkout:
                                        <?php echo esc_html(wp_json_encode($classic_checkout, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?>
                                    </pre>
                                <?php endif; ?>

                                <div class="payment-panel__note">
                                    <svg viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <?php esc_html_e('You will be redirected to PayPal and returned here after authorization.', 'buildpro'); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Credit card -->
                        <div class="payment-panel" id="tab-card" role="tabpanel">
                            <div class="payment-panel__card-brands">
                                <span class="card-brand card-brand--visa">VISA</span>
                                <span class="card-brand card-brand--mc">MC</span>
                                <span class="card-brand card-brand--amex">AMEX</span>
                                <span class="card-brand card-brand--jcb">JCB</span>
                            </div>
                            <div class="checkout-form__group">
                                <label class="checkout-form__label"
                                    for="card-number"><?php esc_html_e('Card Number', 'buildpro'); ?>
                                    <span>*</span></label>
                                <input class="checkout-form__input" type="text" id="card-number" name="card_number"
                                    placeholder="1234 5678 9012 3456" maxlength="19" autocomplete="cc-number">
                            </div>
                            <div class="checkout-form__row">
                                <div class="checkout-form__group">
                                    <label class="checkout-form__label"
                                        for="card-expiry"><?php esc_html_e('Expiry Date', 'buildpro'); ?>
                                        <span>*</span></label>
                                    <input class="checkout-form__input" type="text" id="card-expiry" name="card_expiry"
                                        placeholder="MM / YY" maxlength="7" autocomplete="cc-exp">
                                </div>
                                <div class="checkout-form__group">
                                    <label class="checkout-form__label"
                                        for="card-cvc"><?php esc_html_e('CVV Code', 'buildpro'); ?>
                                        <span>*</span></label>
                                    <input class="checkout-form__input" type="text" id="card-cvc" name="card_cvc"
                                        placeholder="123" maxlength="4" autocomplete="cc-csc">
                                </div>
                            </div>
                            <div class="checkout-form__group">
                                <label class="checkout-form__label"
                                    for="card-name"><?php esc_html_e('Name on Card', 'buildpro'); ?>
                                    <span>*</span></label>
                                <input class="checkout-form__input" type="text" id="card-name" name="card_name"
                                    placeholder="JOHN DOE" autocomplete="cc-name">
                            </div>
                            <div class="payment-panel__secure">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                <?php esc_html_e('Card details are encrypted with 256-bit SSL', 'buildpro'); ?>
                            </div>
                        </div>

                        <!-- Bank transfer -->
                        <div class="payment-panel" id="tab-bank" role="tabpanel">
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
                                                <button class="bank-info__copy-btn" data-copy="<?php echo esc_attr($copy_id); ?>"
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
                                                <button class="bank-info__copy-btn"
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
                                                <button class="bank-info__copy-btn"
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
                                                <button class="bank-info__copy-btn"
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

                    </div><!-- /payment-panels -->

                </div><!-- /checkout-card--payment -->

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