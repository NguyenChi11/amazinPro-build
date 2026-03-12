<?php
if (!function_exists('WC') || !WC()->cart) {
    return;
}
$cart_items = WC()->cart->get_cart();
$cart_url   = function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/');
?>
<div class="hcd__header">
    <span class="hcd__title">Your Cart</span>
</div>
<?php if (empty($cart_items)) : ?>
    <div class="hcd__empty">
        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" stroke="#d1d5db" stroke-width="1.5"
                stroke-linecap="round" stroke-linejoin="round" />
            <line x1="3" y1="6" x2="21" y2="6" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" />
            <path d="M16 10a4 4 0 0 1-8 0" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round"
                stroke-linejoin="round" />
        </svg>
        <p>Your cart is empty</p>
    </div>
<?php else : ?>
    <ul class="hcd__list">
        <?php foreach ($cart_items as $cart_key => $item) :
            $product    = $item['data'];
            $pid        = $item['product_id'];
            $qty        = (int) $item['quantity'];
            $name       = $product->get_name();
            $img        = $product->get_image('thumbnail');
            $unit       = $product->get_attribute('unit');
            $unit_label = $product->get_attribute('unit_label');
            if (!$unit_label) $unit_label = 'ton';
            $remove_url = wc_get_cart_remove_url($cart_key);
            $price      = (float) $product->get_price();
        ?>
            <li class="hcd__item">
                <a href="<?php echo esc_url(get_permalink($pid)); ?>" class="hcd__item-img">
                    <?php echo $img; ?>
                </a>
                <div class="hcd__item-body">
                    <div class="hcd__item-top">
                        <a href="<?php echo esc_url(get_permalink($pid)); ?>"
                            class="hcd__item-name"><?php echo esc_html($name); ?></a>
                        <button type="button" class="hcd__item-delete" data-cart-key="<?php echo esc_attr($cart_key); ?>"
                            data-remove-url="<?php echo esc_attr(wc_get_cart_remove_url($cart_key)); ?>" aria-label="Remove">
                            <svg width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M1 3.5h13M5.5 3.5V2h4v1.5M2.5 3.5l.75 9.5a1 1 0 0 0 1 .987h5.5a1 1 0 0 0 1-.987l.75-9.5"
                                    stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                    <?php if (!empty($unit)) : ?>
                        <span class="hcd__item-unit">Unit: <?php echo esc_html($unit); ?></span>
                    <?php endif; ?>
                    <div class="hcd__item-bottom">
                        <div class="hcd__qty" data-cart-key="<?php echo esc_attr($cart_key); ?>"
                            data-price="<?php echo esc_attr($price); ?>" data-unit-label="<?php echo esc_attr($unit_label); ?>">
                            <button class="hcd__qty-btn hcd__qty-minus" type="button" aria-label="Decrease">&#8722;</button>
                            <span class="hcd__qty-val"><?php echo esc_html($qty); ?></span>
                            <button class="hcd__qty-btn hcd__qty-plus" type="button" aria-label="Increase">+</button>
                        </div>
                        <?php if ($price) : ?>
                            <span class="hcd__item-price" data-unit-price="<?php echo esc_attr($price); ?>">
                                $<?php echo esc_html(number_format($price * $qty, 1)); ?>
                                <span class="hcd__item-unit-label">/<?php echo esc_html($unit_label); ?></span>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php
    $total = 0;
    foreach ($cart_items as $k => $v) {
        $total += (float) $v['data']->get_price() * (int) $v['quantity'];
    }
    ?>
    <div class="hcd__total">
        <span class="hcd__total-label">Order total</span>
        <span class="hcd__total-value">$<?php echo number_format($total, 1); ?></span>
    </div>
    <div class="hcd__footer">
        <a href="<?php echo esc_url($cart_url); ?>" class="hcd__view-btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true">
                <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
            View my shopping cart
        </a>
    </div>
<?php endif; ?>