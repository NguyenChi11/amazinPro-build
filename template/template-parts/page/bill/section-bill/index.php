<?php
$bill_data = function_exists('buildpro_bill_get_page_data') ? buildpro_bill_get_page_data() : [];

$wc_active = isset($bill_data['wc_active']) ? $bill_data['wc_active'] : false;
$cart_items = isset($bill_data['cart_items']) ? $bill_data['cart_items'] : [];
$regular_total_raw = isset($bill_data['regular_total_raw']) ? $bill_data['regular_total_raw'] : 0.0;
$sale_total_raw = isset($bill_data['sale_total_raw']) ? $bill_data['sale_total_raw'] : 0.0;
$you_save_raw = isset($bill_data['you_save_raw']) ? $bill_data['you_save_raw'] : 0.0;
$total = isset($bill_data['total']) ? $bill_data['total'] : 0.0;
$payment_options = isset($bill_data['payment_options']) ? $bill_data['payment_options'] : [];
$bp_price = isset($bill_data['bp_price']) ? $bill_data['bp_price'] : function ($amount) {
    return '$' . number_format((float) $amount, 2);
};
$form_data = isset($bill_data['form_data']) ? $bill_data['form_data'] : [];
$form_data = wp_parse_args($form_data, [
    'fullname' => '',
    'phone' => '',
    'email' => '',
    'address' => '',
    'city' => '',
    'zip' => '',
    'country' => '',
    'country_label' => '',
    'note' => '',
    'payment' => 'cod',
]);
$submit_success = isset($bill_data['submit_success']) ? $bill_data['submit_success'] : false;
$submit_error = isset($bill_data['submit_error']) ? $bill_data['submit_error'] : '';
$created_order_id = isset($bill_data['created_order_id']) ? $bill_data['created_order_id'] : 0;
$home_redirect_url = isset($bill_data['home_redirect_url']) ? $bill_data['home_redirect_url'] : home_url('/');

wp_enqueue_style(
    'bill-section-style',
    get_template_directory_uri() . '/template/template-parts/page/bill/section-bill/style.css',
    [],
    '1.0.0'
);
wp_enqueue_script(
    'bill-section-script',
    get_template_directory_uri() . '/template/template-parts/page/bill/section-bill/script.js',
    ['jquery'],
    '1.0.0',
    true
);
?>

<section class="bill-section">
    <div class="container">
        <div class="bill-section__header">
            <h1 class="bill-section__title">Bill Information</h1>
            <p class="bill-section__subtitle">Review your checkout information and submit confirmation.</p>
        </div>

        <?php if (!empty($submit_error)) : ?>
            <div class="bill-alert bill-alert--error"><?php echo esc_html($submit_error); ?></div>
        <?php endif; ?>

        <?php if ($submit_success) : ?>
            <div class="bill-success-popup" id="bill-success-popup"
                data-home-url="<?php echo esc_url($home_redirect_url); ?>">
                <div class="bill-success-popup__backdrop" data-popup-close="1"></div>
                <div class="bill-success-popup__dialog" role="dialog" aria-modal="true"
                    aria-labelledby="bill-success-title">
                    <h2 class="bill-success-popup__title" id="bill-success-title">order successfully</h2>
                    <p class="bill-success-popup__text">
                        order #<?php echo esc_html($created_order_id); ?> has been successfully created.
                    </p>
                    <button type="button" class="bill-success-popup__btn" id="bill-success-close-btn" data-popup-close="1">
                        Dong
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <div class="bill-layout">
            <form class="bill-card bill-card--form" id="bill-confirm-form" method="post" novalidate>
                <h2 class="bill-card__title">Customer Information</h2>

                <div class="bill-info-list">
                    <div class="bill-info-list__row">
                        <span>Full Name</span>
                        <strong><?php echo esc_html($form_data['fullname'] ?: '-'); ?></strong>
                    </div>
                    <div class="bill-info-list__row">
                        <span>Phone Number</span>
                        <strong><?php echo esc_html($form_data['phone'] ?: '-'); ?></strong>
                    </div>
                    <div class="bill-info-list__row">
                        <span>Email Address</span>
                        <strong><?php echo esc_html($form_data['email'] ?: '-'); ?></strong>
                    </div>
                    <div class="bill-info-list__row">
                        <span>Address</span>
                        <strong><?php echo esc_html($form_data['address'] ?: '-'); ?></strong>
                    </div>
                    <div class="bill-info-list__row">
                        <span>City</span>
                        <strong><?php echo esc_html($form_data['city'] ?: '-'); ?></strong>
                    </div>
                    <div class="bill-info-list__row">
                        <span>ZIP / Postal Code</span>
                        <strong><?php echo esc_html($form_data['zip'] ?: '-'); ?></strong>
                    </div>
                    <div class="bill-info-list__row">
                        <span>Country</span>
                        <strong><?php echo esc_html($form_data['country_label'] ?: '-'); ?></strong>
                    </div>
                    <div class="bill-info-list__row">
                        <span>Payment Method</span>
                        <strong><?php echo esc_html($payment_options[$form_data['payment']]['label']); ?></strong>
                    </div>
                    <div class="bill-info-list__row">
                        <span>Order Notes</span>
                        <strong><?php echo esc_html($form_data['note'] ?: '-'); ?></strong>
                    </div>
                </div>

                <input type="hidden" name="fullname" value="<?php echo esc_attr($form_data['fullname']); ?>">
                <input type="hidden" name="phone" value="<?php echo esc_attr($form_data['phone']); ?>">
                <input type="hidden" name="email" value="<?php echo esc_attr($form_data['email']); ?>">
                <input type="hidden" name="address" value="<?php echo esc_attr($form_data['address']); ?>">
                <input type="hidden" name="city" value="<?php echo esc_attr($form_data['city']); ?>">
                <input type="hidden" name="zip" value="<?php echo esc_attr($form_data['zip']); ?>">
                <input type="hidden" name="country" value="<?php echo esc_attr($form_data['country']); ?>">
                <input type="hidden" name="country_label" value="<?php echo esc_attr($form_data['country_label']); ?>">
                <input type="hidden" name="note" value="<?php echo esc_attr($form_data['note']); ?>">
                <input type="hidden" name="payment" value="<?php echo esc_attr($form_data['payment']); ?>">

                <label class="bill-agree">
                    <input type="checkbox" id="bill-agree" name="bill_agree" value="1"
                        <?php checked($submit_success); ?>>
                    <span>I confirm all bill information above is correct.</span>
                </label>
                <span class="bill-form__error" data-for="bill-agree"></span>

                <?php wp_nonce_field('bp_bill_confirm', 'bp_bill_nonce'); ?>
                <input type="hidden" name="bp_bill_confirm_submit" value="1">

                <button type="submit" class="bill-submit-btn" id="bill-submit-btn">
                    Submit Bill Confirmation
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </form>

            <aside class="bill-summary-stack">
                <div class="bill-card bill-card--summary">
                    <h2 class="bill-card__title">Order Summary</h2>

                    <div class="bill-order-items">
                        <?php if (empty($cart_items)) : ?>
                            <p class="bill-order-empty">Your cart is currently empty.</p>
                        <?php else : ?>
                            <?php foreach ($cart_items as $item) :
                                $product  = $item['data'];
                                $qty      = intval($item['quantity']);
                                $price    = floatval($product->get_price());
                                $name     = $product->get_name();
                                $img_url  = get_the_post_thumbnail_url($item['product_id'], 'thumbnail');
                            ?>
                                <div class="bill-order-item">
                                    <div class="bill-order-item__image">
                                        <?php if ($img_url) : ?>
                                            <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($name); ?>">
                                        <?php else : ?>
                                            <div class="bill-order-item__placeholder"></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="bill-order-item__meta">
                                        <p class="bill-order-item__name"><?php echo esc_html($name); ?></p>
                                        <p class="bill-order-item__qty">Qty: <?php echo esc_html($qty); ?></p>
                                    </div>
                                    <p class="bill-order-item__total"><?php echo $bp_price($price * $qty); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="bill-divider"></div>

                    <div class="bill-totals">
                        <div class="bill-totals__row">
                            <span>Regular Price</span>
                            <span><?php echo $bp_price($regular_total_raw); ?></span>
                        </div>
                        <div class="bill-totals__row">
                            <span>Sale Price</span>
                            <span><?php echo $bp_price($sale_total_raw); ?></span>
                        </div>
                        <div class="bill-totals__row">
                            <span>You Save</span>
                            <span><?php echo $bp_price($you_save_raw); ?></span>
                        </div>
                        <div class="bill-divider"></div>
                        <div class="bill-totals__row bill-totals__row--total">
                            <span>Total</span>
                            <span><?php echo $bp_price($total); ?></span>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>