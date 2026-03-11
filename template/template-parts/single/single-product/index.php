<?php
$pid = get_the_ID();
if (!(class_exists('WooCommerce') || function_exists('wc_get_product'))) {
    return;
}
if (!$pid || get_post_type($pid) !== 'product') {
    return;
}
$product = wc_get_product($pid);
$title = get_the_title($pid);
$featured_id = get_post_thumbnail_id($pid);
$featured = $featured_id ? wp_get_attachment_image($featured_id, 'large') : '';
$gallery_ids = $product->get_gallery_image_ids();
$price_html = $product->get_price_html();
$regular_price = $product->get_regular_price();
$sale_price = $product->get_sale_price();
$sku = $product->get_sku();
$in_stock = $product->is_in_stock();
$stock_status = $product->get_stock_status();
$stock_qty = $product->get_stock_quantity();
$short_desc = $product->get_short_description();
$desc = $product->get_description();
$cats = wc_get_product_category_list($pid);
$tags = wc_get_product_tag_list($pid);
$type = $product->get_type();
$avg_rating = $product->get_average_rating();
$review_count = $product->get_review_count();
$length = $product->get_length();
$width = $product->get_width();
$height = $product->get_height();
$weight = $product->get_weight();
$shipping_class = $product->get_shipping_class();
$downloads = $product->get_downloads();
$attributes = $product->get_attributes();
$typical_range = get_post_meta($pid, 'typical_range', true);
$sale_from = method_exists($product, 'get_date_on_sale_from') ? $product->get_date_on_sale_from() : null;
$sale_to = method_exists($product, 'get_date_on_sale_to') ? $product->get_date_on_sale_to() : null;
$sale_from_str = $sale_from ? wc_format_datetime($sale_from) : '';
$sale_to_str = $sale_to ? wc_format_datetime($sale_to) : '';
?>

<?php
$product_page = get_pages([
    'meta_key'   => '_wp_page_template',
    'meta_value' => 'products-page.php'
]);
?>

<article class="single-product-detail" id="product-<?php echo esc_attr($pid); ?>">
    <?php if (!empty($product_page)) : ?>
        <a href="<?php echo esc_url(get_permalink($product_page[0]->ID)); ?>" class="single-product__back">
            <img class="single-product__back-icon"
                src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/icon/Arrow_Left.png'); ?>" alt="">
            <h1 class="single-product__title_back"><?php echo esc_html(get_the_title($pid)); ?></h1>
        </a>
    <?php endif; ?>
    <header class="single-product__header">
        <div class="single-product__images-container">
            <!-- Main Swiper (ảnh lớn) -->
            <div class="swiper main-swiper">
                <div class="swiper-wrapper">
                    <?php if (!empty($featured)) : ?>
                        <div class="swiper-slide"><?php echo $featured; ?></div>
                    <?php endif; ?>

                    <?php if (!empty($gallery_ids)) : ?>
                        <?php foreach ($gallery_ids as $gid) :
                            $img = wp_get_attachment_image($gid, 'large');
                            if (!$img) continue;
                        ?>
                            <div class="swiper-slide"><?php echo $img; ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <!-- Nếu muốn thêm nút prev/next (tùy chọn) -->
                <!-- <div class="swiper-button-prev"></div> -->
                <!-- <div class="swiper-button-next"></div> -->
            </div>

            <!-- Thumbs Swiper (thumbnails nhỏ bên dưới) -->
            <div class="swiper thumbs-swiper">
                <div class="swiper-wrapper">
                    <?php if (!empty($featured)) :
                        // Lấy featured thumbnail nhỏ hơn
                        $thumb_featured = get_the_post_thumbnail($pid, 'thumbnail'); // hoặc 'medium'
                        if ($thumb_featured) : ?>
                            <div class="swiper-slide"><?php echo $thumb_featured; ?></div>
                    <?php endif;
                    endif; ?>

                    <?php if (!empty($gallery_ids)) : ?>
                        <?php foreach ($gallery_ids as $gid) :
                            $thumb_img = wp_get_attachment_image($gid, 'thumbnail'); // kích thước nhỏ cho thumbs
                            if (!$thumb_img) continue;
                        ?>
                            <div class="swiper-slide"><?php echo $thumb_img; ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="single-product__info">
            <h1 class="single-product__title"><?php echo esc_html($title); ?></h1>
            <div class="single-product__meta">
                <span class="single-product__sku"><?php echo esc_html($sku ? 'SKU: ' . $sku : ''); ?></span>
                <?php if ($stock_qty !== null) : ?>
                    <span class="single-product__stock-qty"><?php echo esc_html('SL: ' . (int)$stock_qty); ?></span>
                <?php endif; ?>
                <?php if ($avg_rating) : ?>
                    <span
                        class="single-product__rating"><?php echo esc_html($avg_rating . ' / 5 (' . (int)$review_count . ' đánh giá)'); ?></span>
                <?php endif; ?>
            </div>
            <div class="single-product__meta-info">
                <?php
                // 1. Brand (sử dụng taxonomy core 'product_brand' của WooCommerce từ 9.6+)
                if (taxonomy_exists('product_brand')) {
                    $brands = wc_get_product_terms($pid, 'product_brand', array('fields' => 'names'));
                    if (! empty($brands) && ! is_wp_error($brands)) :
                ?>
                        <div class="single-product__brand">
                            <strong>Brand:</strong> <?php echo esc_html(implode(', ', $brands)); ?>
                        </div>
                    <?php
                    endif;
                } else {
                    // Fallback nếu không dùng taxonomy core (ví dụ custom field hoặc attribute 'pa_brand')
                    $brand_custom = $product->get_attribute('brand'); // hoặc get_post_meta($pid, 'brand', true);
                    if (! empty($brand_custom)) :
                    ?>
                        <div class="single-product__brand">
                            <strong>Brand:</strong> <?php echo wp_kses_post($brand_custom); ?>
                        </div>
                <?php
                    endif;
                }
                ?>

                <?php
                // 2. Category (danh mục sản phẩm)
                $cats_html = wc_get_product_category_list($pid, ' • ', '', '');
                if (! empty($cats_html)) :
                ?>
                    <div class="single-product__categories">
                        <strong>Category:</strong> <?php echo wp_kses_post($cats_html); ?>
                    </div>
                <?php endif; ?>

                <?php
                // 3. Tags (thẻ sản phẩm)
                $tags_html = wc_get_product_tag_list($pid, ' • ', '', '');
                if (! empty($tags_html)) :
                ?>
                    <div class="single-product__tags">
                        <strong>Tags:</strong> <?php echo wp_kses_post($tags_html); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="single-product__description__container">
                <h2 class="single-product__description__title">Key Features</h2>
                <div class="single-product__description">
                    <?php if (!empty($desc)) : ?>
                        <div class="single-product__desc-content"><?php echo wp_kses_post(wpautop($desc)); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="single-product__price__container">
                <div class="single-product__price">
                    <?php
                    $low = '';
                    $high = '';
                    if ($sale_price !== '' && $regular_price !== '' && (float) $sale_price < (float) $regular_price) {
                        $low = wc_price($sale_price);
                        $high = wc_price($regular_price);
                        echo wp_kses_post($low . ' - ' . $high . '<span class="single-product__unit">/ton</span>');
                    } else {
                        $val = $product->get_price();
                        if ($val === '' && $sale_price !== '') {
                            $val = $sale_price;
                        }
                        if ($val === '' && $regular_price !== '') {
                            $val = $regular_price;
                        }
                        if ($val !== '') {
                            echo wp_kses_post(wc_price($val) . '<span class="single-product__unit">/ton</span>');
                        }
                    }
                    ?>
                </div>
                <?php
                if ($sale_price !== '' && ($sale_from_str !== '' || $sale_to_str !== '')) {
                    echo '<div class="single-product__sale-range">';
                    if ($sale_from_str !== '') {
                        echo '<span class="single-product__sale-from">Start: ' . esc_html($sale_from_str) . '</span>';
                    }
                    if ($sale_to_str !== '') {
                        echo '<span class="single-product__sale-to">End: ' . esc_html($sale_to_str) . '</span>';
                    }
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </header>
    <div class="single-product__top">
        <div class="single-product__summary">
            <?php if (!empty($short_desc)) : ?>
                <div class="single-product__short-desc"><?php echo wp_kses_post(wpautop($short_desc)); ?></div>
            <?php endif; ?>

            <?php if (!empty($typical_range)) : ?>
                <div class="single-product__typical"><?php echo esc_html('Typical Range: ' . $typical_range); ?></div>
            <?php endif; ?>
        </div>
    </div>
    <div class="single-product__specs__container">
        <?php if (!empty($attributes)) : ?>
            <section class="single-product__attributes">
                <h2>Engineering Specifications</h2>
                <div class="single-product__attr-list">
                    <?php foreach ($attributes as $attr) :
                        if ($attr->is_taxonomy()) {
                            $taxonomy = $attr->get_name();
                            $terms = wc_get_product_terms($pid, $taxonomy, array('fields' => 'names'));
                            $value = implode(', ', $terms);
                            $label = wc_attribute_label($taxonomy);
                        } else {
                            $label = $attr->get_name();
                            $options = $attr->get_options();
                            $value = implode(', ', array_map('sanitize_text_field', (array)$options));
                        }
                        if ($label === '' && $value === '') continue;
                    ?>
                        <div class="single-product__attr-item">
                            <span class="single-product__attr-name"><?php echo esc_html($label); ?></span>
                            <span class="single-product__attr-value"><?php echo esc_html($value); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
        <section class="single-product__specs">
            <h2>Specifications</h2>
            <div class="single-product__spec-list">
                <?php if ($length || $width || $height) : ?>
                    <div class="single-product__spec-item">
                        <span>Size</span><span><?php echo esc_html(trim(($length ? $length . ' × ' : '') . ($width ? $width . ' × ' : '') . ($height ? $height : ''))); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($weight) : ?>
                    <div class="single-product__spec-item"><span>Weight</span><span><?php echo esc_html($weight); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($shipping_class) : ?>
                    <div class="single-product__spec-item"><span>Shipping
                            class</span><span><?php echo esc_html($shipping_class); ?></span></div>
                <?php endif; ?>
                <div class="single-product__spec-item"><span>Product
                        Type</span><span><?php echo esc_html($type); ?></span>
                </div>
                <div class="single-product__spec-item"><span>Stock
                        Status</span><span><?php echo esc_html($stock_status); ?></span></div>
            </div>
        </section>
    </div>
    <?php if (!empty($downloads)) : ?>
        <section class="single-product__downloads">
            <h2>Downloads</h2>
            <ul class="single-product__download-list">
                <?php foreach ($downloads as $d) :
                    $name = $d->get_name();
                    $url = $d->get_file();
                ?>
                    <li><a href="<?php echo esc_url($url); ?>" target="_blank"
                            rel="noopener"><?php echo esc_html($name ?: $url); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>

    <?php
    $related_args = array(
        'post_type'           => 'product',
        'posts_per_page'      => 3,
        'post__not_in'        => array($pid),
        'post_status'         => 'publish',
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
        'orderby'             => 'rand',
    );
    $tax_filters = array();
    if (taxonomy_exists('product_brand')) {
        $brand_ids = wp_get_post_terms($pid, 'product_brand', array('fields' => 'ids'));
        if (!empty($brand_ids) && !is_wp_error($brand_ids)) {
            $tax_filters[] = array(
                'taxonomy' => 'product_brand',
                'field'    => 'term_id',
                'terms'    => $brand_ids,
            );
        }
    }
    $cat_ids = wp_get_post_terms($pid, 'product_cat', array('fields' => 'ids'));
    if (!empty($cat_ids) && !is_wp_error($cat_ids)) {
        $tax_filters[] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $cat_ids,
        );
    }
    $tag_ids = wp_get_post_terms($pid, 'product_tag', array('fields' => 'ids'));
    if (!empty($tag_ids) && !is_wp_error($tag_ids)) {
        $tax_filters[] = array(
            'taxonomy' => 'product_tag',
            'field'    => 'term_id',
            'terms'    => $tag_ids,
        );
    }
    if (!empty($tax_filters)) {
        $related_args['tax_query'] = array_merge(array('relation' => 'OR'), $tax_filters);
    }
    $related_q = new WP_Query($related_args);
    if ($related_q->have_posts()) :
    ?>
        <section class="single-product__related">
            <h2 class="single-product__related-title">You might also like</h2>
            <div class="section-product__list">
                <?php
                while ($related_q->have_posts()) :
                    $related_q->the_post();
                    $rid    = get_the_ID();
                    $rimg   = get_the_post_thumbnail_url($rid, 'large');
                    $rtitle = get_the_title($rid);
                    $rlink  = get_permalink($rid);
                    $rprice = '';
                    if (function_exists('wc_get_product')) {
                        $rp = wc_get_product($rid);
                        if ($rp) {
                            $rprice = $rp->get_price();
                        }
                    }
                ?>
                    <a class="section-product__item" href="<?php echo esc_url($rlink); ?>">
                        <div class="section-product__item-image">
                            <?php if (!empty($rimg)) : ?>
                                <img src="<?php echo esc_url($rimg); ?>" alt="<?php echo esc_attr($rtitle); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="section-product__item-content">
                            <h3 class="section-product__item-title"><?php echo esc_html($rtitle); ?></h3>
                            <div class="section-product__item-bottom">
                                <p class="section-product__item-price">
                                    <?php if ($rprice !== '') : ?>
                                        <span>$</span><?php echo esc_html($rprice); ?><span>/ton</span>
                                    <?php endif; ?>
                                </p>
                                <button class="section-product__item-cta btn-add-to-cart"
                                    data-product-id="<?php echo esc_attr($rid); ?>">Add to Cart</button>
                            </div>
                        </div>
                    </a>
                <?php endwhile;
                wp_reset_postdata(); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($product && $product->get_reviews_allowed()) : ?>
        <section class="single-product__reviews">
            <?php
            comments_template();
            ?>
        </section>
    <?php endif; ?>
    <!-- <?php if (!empty($cats)) : ?>
        <div class="single-product__cats"><?php echo wp_kses_post($cats); ?></div>
    <?php endif; ?>
    <?php if (!empty($tags)) : ?>
        <div class="single-product__tags"><?php echo wp_kses_post($tags); ?></div>
    <?php endif; ?> -->
</article>