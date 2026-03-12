<?php
$wc_active    = function_exists('WC') && WC()->cart;
$cart_items   = $wc_active ? WC()->cart->get_cart() : [];
$checkout_url = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/checkout/');
$mini_nonce   = wp_create_nonce('buildpro_mini_cart');
$cart_nonce   = wp_create_nonce('woocommerce-cart');

$shipping_cost = 120.00;
$tax_rate      = 0.08;

$subtotal_raw = 0.0;
foreach ($cart_items as $item) {
    $subtotal_raw += floatval($item['data']->get_price()) * intval($item['quantity']);
}

$wc_discount = 0.0;
if ($wc_active) {
    $wc_discount = floatval(WC()->cart->get_discount_total());
    $wc_shipping = floatval(WC()->cart->get_shipping_total());
    if ($wc_shipping > 0) {
        $shipping_cost = $wc_shipping;
    }
}

$tax_base   = $subtotal_raw + $shipping_cost - $wc_discount;
$tax_amount = $tax_base * $tax_rate;
$total      = $tax_base + $tax_amount;
?>

<section class="cart-section">
    <div class="container">
        <div class="cart-section__wrapper">

            <!-- ===== LEFT: Cart items ===== -->
            <div class="cart-section__main">
                <div class="cart-section__header">
                    <h1 class="cart-section__title">Shopping Cart</h1>
                    <p class="cart-section__subtitle">Review your construction materials and adjust quantities for your
                        project.</p>
                </div>

                <div class="cart-section__items" id="cart-items">
                    <?php if (empty($cart_items)) : ?>
                        <div class="cart-section__empty">
                            <p>Your cart is empty.</p>
                            <?php if ($wc_active) : ?>
                                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>"
                                    class="cart-section__shop-link">Continue Shopping</a>
                            <?php endif; ?>
                        </div>
                    <?php else : ?>
                        <?php foreach ($cart_items as $cart_item_key => $cart_item) :
                            $product       = $cart_item['data'];
                            $product_id    = $cart_item['product_id'];
                            $quantity      = intval($cart_item['quantity']);
                            $price         = floatval($product->get_price());
                            $line_total    = $price * $quantity;
                            $name          = $product->get_name();
                            $img           = $product->get_image('thumbnail');
                            $unit          = $product->get_attribute('unit');
                            $unit_label    = $product->get_attribute('unit_label');
                            if (!$unit_label) $unit_label = 'unit';
                            $remove_url    = wc_get_cart_remove_url($cart_item_key);
                            $short_desc    = $product->get_short_description();
                            $regular_price = floatval($product->get_regular_price());
                            $sale_price    = $product->get_sale_price();
                            $on_sale       = $product->is_on_sale() && $sale_price !== '';
                        ?>
                            <div class="cart-item" data-key="<?php echo esc_attr($cart_item_key); ?>"
                                data-price="<?php echo esc_attr($price); ?>"
                                data-regular-price="<?php echo esc_attr($regular_price); ?>">
                                <div class="cart-item__check">
                                    <input type="checkbox" class="cart-item__checkbox" checked>
                                </div>
                                <div class="cart-item__image">
                                    <?php echo $img; ?>
                                </div>
                                <div class="cart-item__info">
                                    <h4 class="cart-item__name"><?php echo esc_html($name); ?></h4>
                                    <?php if ($short_desc) : ?>
                                        <p class="cart-item__short-desc"><?php echo wp_kses_post($short_desc); ?></p>
                                    <?php endif; ?>
                                    <div class="cart-item__pricing">
                                        <?php if ($on_sale) : ?>
                                            <span
                                                class="cart-item__regular-price cart-item__regular-price--strike">$<?php echo number_format(floatval($regular_price), 2); ?></span>
                                            <span
                                                class="cart-item__sale-price">$<?php echo number_format(floatval($sale_price), 2); ?></span>
                                        <?php else : ?>
                                            <span
                                                class="cart-item__regular-price">$<?php echo number_format($regular_price, 2); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($unit) : ?>
                                        <p class="cart-item__unit">Unit: <?php echo esc_html($unit); ?></p>
                                    <?php endif; ?>
                                    <div class="cart-item__qty" data-cart-key="<?php echo esc_attr($cart_item_key); ?>"
                                        data-price="<?php echo esc_attr($price); ?>">
                                        <button class="cart-item__qty-btn cart-item__qty-minus" type="button">-</button>
                                        <input class="cart-item__qty-input" type="number"
                                            value="<?php echo esc_attr($quantity); ?>" min="1" max="9999">
                                        <button class="cart-item__qty-btn cart-item__qty-plus" type="button">+</button>
                                    </div>
                                </div>
                                <div class="cart-item__right">
                                    <span class="cart-item__price">$<?php echo number_format($line_total, 2); ?></span>
                                    <button type="button" class="cart-item__remove"
                                        data-cart-key="<?php echo esc_attr($cart_item_key); ?>"
                                        data-remove-url="<?php echo esc_attr($remove_url); ?>">
                                        <i class="fa-solid fa-trash-can"></i> Remove
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Order Notes -->
                <?php $saved_note = ($wc_active && WC()->session) ? WC()->session->get('order_comments', '') : ''; ?>
                <div class="cart-section__notes" id="cart-notes-block">
                    <div class="cart-notes__header">
                        <h3 class="cart-section__notes-title">Order Notes</h3>
                    </div>

                    <!-- Editor (always visible) -->
                    <div class="cart-notes__editor" id="notes-editor">
                        <textarea class="cart-section__notes-textarea" id="notes-textarea"
                            placeholder="Add any special instructions for delivery or packaging..."><?php echo esc_textarea($saved_note); ?></textarea>
                        <div class="cart-notes__editor-actions">
                            <button type="button" class="cart-notes__save-btn" id="notes-save-btn">
                                <i class="fa-solid fa-check"></i> Save
                            </button>
                            <button type="button" class="cart-notes__delete-btn" id="notes-delete-btn">
                                <i class="fa-solid fa-trash-can"></i> Delete
                            </button>
                        </div>
                        <p class="cart-notes__msg" id="notes-msg"></p>
                    </div>
                </div>
            </div>

            <!-- ===== RIGHT: Cart Summary ===== -->
            <div class="cart-section__sidebar">
                <div class="cart-summary">
                    <div class="cart-summary__header">
                        <i class="fa-solid fa-cart-shopping cart-summary__icon"></i>
                        <h3 class="cart-summary__title">Cart Summary</h3>
                    </div>

                    <?php
                    $summary_regular = 0.0;
                    $summary_sale    = 0.0;
                    foreach ($cart_items as $ci) {
                        $cp  = $ci['data'];
                        $qty = intval($ci['quantity']);
                        $summary_regular += floatval($cp->get_regular_price()) * $qty;
                        $summary_sale    += floatval($cp->get_price()) * $qty;
                    }
                    ?>
                    <div class="cart-summary__rows">
                        <div class="cart-summary__row">
                            <span>Regular Price</span>
                            <span class="cart-summary__val"
                                id="summary-regular-price">$<?php echo number_format($summary_regular, 2); ?></span>
                        </div>
                        <div class="cart-summary__row cart-summary__row--sale" id="summary-sale-row"
                            <?php echo ($summary_sale >= $summary_regular) ? ' style="display:none"' : ''; ?>>
                            <span>Sale Price</span>
                            <span class="cart-summary__val cart-summary__val--sale"
                                id="summary-sale-price">$<?php echo number_format($summary_sale, 2); ?></span>
                        </div>
                        <div class="cart-summary__row cart-summary__row--savings" id="summary-savings-row"
                            <?php echo ($summary_sale >= $summary_regular) ? ' style="display:none"' : ''; ?>>
                            <span>You Save</span>
                            <span class="cart-summary__val cart-summary__val--savings"
                                id="summary-savings">-$<?php echo number_format($summary_regular - $summary_sale, 2); ?></span>
                        </div>
                        <div class="cart-summary__row cart-summary__row--discount" id="summary-discount-row"
                            <?php echo $wc_discount <= 0 ? ' style="display:none"' : ''; ?>>
                            <span>Discount</span>
                            <span class="cart-summary__val cart-summary__val--discount"
                                id="summary-discount">-$<?php echo number_format($wc_discount, 2); ?></span>
                        </div>
                    </div>

                    <div class="cart-summary__divider"></div>

                    <div class="cart-summary__total-row">
                        <span class="cart-summary__total-label">Total Amount</span>
                        <span class="cart-summary__total-val"
                            id="summary-total">$<?php echo number_format($subtotal_raw - $wc_discount, 2); ?></span>
                    </div>

                    <a href="<?php echo esc_url($checkout_url); ?>" class="cart-summary__btn">
                        Place Order <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <p class="cart-summary__footnote">Prices include basic unloading at designated curb. Terms &amp;
                        Conditions apply.</p>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
    var cartSectionData = {
        shippingCost: <?php echo floatval($shipping_cost); ?>,
        taxRate: <?php echo floatval($tax_rate); ?>,
        discount: <?php echo floatval($wc_discount); ?>,
        ajaxUrl: "<?php echo esc_js(admin_url('admin-ajax.php')); ?>",
        cartNonce: "<?php echo esc_js($cart_nonce); ?>",
        miniNonce: "<?php echo esc_js($mini_nonce); ?>"
    };
</script>