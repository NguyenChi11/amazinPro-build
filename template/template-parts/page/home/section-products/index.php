<?php
$page_id = get_queried_object_id();
$materials_enabled = get_post_meta($page_id, 'materials_enabled', true);
$materials_enabled = $materials_enabled === '' ? 1 : (int)$materials_enabled;
$materials_title = get_post_meta($page_id, 'materials_title', true);
$materials_description = get_post_meta($page_id, 'materials_description', true);
$materials_view_all_text = get_post_meta($page_id, 'materials_view_all_text', true);
if (is_customize_preview()) {
    $mod_title = get_theme_mod('materials_title', $materials_title);
    $mod_desc = get_theme_mod('materials_description', $materials_description);
    $mod_view_all_text = get_theme_mod('materials_view_all_text', $materials_view_all_text);
    $mod_enabled = get_theme_mod('materials_enabled', 1);
    $materials_enabled = (int)$mod_enabled;
    if ($mod_title !== '') {
        $materials_title = $mod_title;
    }
    if ($mod_desc !== '') {
        $materials_description = $mod_desc;
    }
    if ($mod_view_all_text !== '') {
        $materials_view_all_text = $mod_view_all_text;
    }
}
if (!is_string($materials_view_all_text) || $materials_view_all_text === '') {
    $materials_view_all_text = __('View All Products', 'buildpro');
}

$items = [];
if (class_exists('WooCommerce') || function_exists('wc_get_product')) {
    $query = new WP_Query(array(
        'post_type' => 'product',
        'posts_per_page' => 6,
        'orderby' => 'date',
        'order' => 'DESC',
        'post_status' => 'publish',
        'no_found_rows' => true,
    ));
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $image_url = get_the_post_thumbnail_url($post_id, 'large');
            $title = get_the_title($post_id);
            $price = '';
            $currency_symbol = '';
            $gallery_images = array();
            if ($image_url !== '') {
                $gallery_images[] = $image_url;
            }
            $product = null;
            if (function_exists('wc_get_product')) {
                $product = call_user_func('wc_get_product', $post_id);
                if ($product) {
                    $raw_price = $product->get_price();
                    if ($raw_price !== '' && is_numeric($raw_price)) {
                        $price_number = (float) $raw_price;
                        $price_decimals = floor($price_number) == $price_number ? 0 : 2;
                        $price = number_format_i18n($price_number, $price_decimals);
                    } elseif (is_string($raw_price)) {
                        $price = $raw_price;
                    }
                    if (function_exists('get_woocommerce_currency_symbol')) {
                        $currency_symbol = get_woocommerce_currency_symbol();
                    }

                    if (method_exists($product, 'get_gallery_image_ids')) {
                        $gallery_ids = $product->get_gallery_image_ids();
                        if (is_array($gallery_ids) && !empty($gallery_ids)) {
                            foreach ($gallery_ids as $gallery_id) {
                                $gallery_url = wp_get_attachment_image_url((int) $gallery_id, 'large');
                                if (is_string($gallery_url) && $gallery_url !== '' && !in_array($gallery_url, $gallery_images, true)) {
                                    $gallery_images[] = $gallery_url;
                                }
                            }
                        }
                    }
                }
            }
            $bedrooms = (string) get_post_meta($post_id, 'buildpro_product_bedrooms', true);
            $bathrooms = (string) get_post_meta($post_id, 'buildpro_product_bathrooms', true);
            $area = (string) get_post_meta($post_id, 'buildpro_product_area', true);
            if ($area !== '' && is_numeric($area)) {
                $area_number = (float) $area;
                $area_decimals = floor($area_number) == $area_number ? 0 : 2;
                $area = number_format_i18n($area_number, $area_decimals);
            }
            $location = (string) get_post_meta($post_id, 'buildpro_product_location', true);
            $items[] = array(
                'id' => $post_id,
                'title' => $title,
                'image' => $image_url,
                'price' => $price,
                'currency_symbol' => $currency_symbol,
                'link' => get_permalink($post_id),
                'gallery' => $gallery_images,
                'bedrooms' => $bedrooms,
                'bathrooms' => $bathrooms,
                'area' => $area,
                'location' => $location,
            );
        }
        wp_reset_postdata();
    }
}

// Avoid rendering broken/empty markup on the frontend.
// Keep section available in Customizer preview for live editing.
if ($materials_enabled !== 1 && !is_customize_preview()) {
    return;
}

if (empty($items) && !is_customize_preview()) {
    return;
}

$icon_bedroom_url = get_theme_file_uri('/assets/images/icon/icon_bedroom.png');
$icon_bathroom_url = get_theme_file_uri('/assets/images/icon/icon_bathroom.png');
$icon_ruler_url = get_theme_file_uri('/assets/images/icon/icon_ruler.png');
$icon_location_card_url = get_theme_file_uri('/assets/images/icon/icon_location_card.png');
$icon_cart_url = get_theme_file_uri('/assets/images/icon/icon_cart.png');
?>
<?php $style = $materials_enabled !== 1 ? ' style="display:none"' : ''; ?>
<section class="section-product" data-aos="fade-up" <?php echo $style; ?>>
    <?php if (is_customize_preview()): ?>
        <div class="section-product__hover-outline"></div>

        <script>
            (function() {
                var btn = document.querySelector('.section-product__customize-button');
                if (btn && window.parent && window.parent.wp && window.parent.wp.customize) {
                    btn.addEventListener('click', function() {
                        window.parent.wp.customize.section('buildpro_product_section').focus();
                    });
                }
            })();
        </script>
    <?php endif; ?>
    <div class="section-product__header">
        <?php if ($materials_title !== ''): ?>
            <h2 class="section-product__title"><?php echo esc_html($materials_title); ?></h2>
        <?php endif; ?>
        <?php if ($materials_description !== ''): ?>
            <p class="section-product__description"><?php echo esc_html($materials_description); ?></p>
        <?php endif; ?>
    </div>
    <div class="section-product__grid">
        <?php foreach ($items as $item): ?>
            <div class="section-product__grid-item">
                <div class="section-product__item">
                    <div class="section-product__item-image swiper section-product__item-image-swiper">
                        <div class="swiper-wrapper">
                            <?php if (!empty($item['gallery'])): ?>
                                <?php foreach ($item['gallery'] as $gallery_image_url): ?>
                                    <div class="swiper-slide">
                                        <a class="section-product__item-link" href="<?php echo esc_url($item['link']); ?>"
                                            aria-label="<?php echo esc_attr($item['title']); ?>">
                                            <img src="<?php echo esc_url($gallery_image_url); ?>"
                                                alt="<?php echo esc_attr($item['title']); ?>">
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($item['gallery']) && count($item['gallery']) > 1): ?>
                            <div class="swiper-button-prev section-product__item-image-prev"
                                aria-label="<?php esc_attr_e('Previous image', 'buildpro'); ?>"></div>
                            <div class="swiper-button-next section-product__item-image-next"
                                aria-label="<?php esc_attr_e('Next image', 'buildpro'); ?>"></div>
                        <?php endif; ?>
                    </div>
                    <div class="section-product__item-content">
                        <div class="section-product__item-price-row">
                            <p class="section-product__item-price">
                                <?php if ($item['price'] !== ''): ?>
                                    <span><?php echo esc_html($item['currency_symbol'] !== '' ? $item['currency_symbol'] : '$'); ?></span><span><?php echo esc_html($item['price']); ?>
                                    <?php else: ?></span>
                                    <?php esc_html_e('Contact', 'buildpro'); ?>
                                <?php endif; ?>
                            </p>
                            <!-- <button class="section-product__item-cta btn-add-to-cart" type="button"
                            data-product-id="<?php echo esc_attr($item['id']); ?>"
                            aria-label="<?php esc_attr_e('Add to Cart', 'buildpro'); ?>">
                            <img src="<?php echo esc_url($icon_cart_url); ?>" alt="" aria-hidden="true">
                            <span><?php esc_html_e('Add to Cart', 'buildpro'); ?></span>
                        </button> -->
                        </div>
                        <a class="section-product__item-title-link" href="<?php echo esc_url($item['link']); ?>">
                            <h3 class="section-product__item-title"><?php echo esc_html($item['title']); ?></h3>
                        </a>
                        <div class="section-product__item-meta"
                            aria-label="<?php esc_attr_e('Property details', 'buildpro'); ?>">
                            <div class="section-product__item-meta-item">
                                <img src="<?php echo esc_url($icon_bedroom_url); ?>"
                                    alt="<?php esc_attr_e('Bedroom', 'buildpro'); ?>">
                                <span><?php echo esc_html($item['bedrooms'] !== '' ? $item['bedrooms'] : '-'); ?></span>
                            </div>
                            <div class="section-product__item-meta-item">
                                <img src="<?php echo esc_url($icon_bathroom_url); ?>"
                                    alt="<?php esc_attr_e('Bathroom', 'buildpro'); ?>">
                                <span><?php echo esc_html($item['bathrooms'] !== '' ? $item['bathrooms'] : '-'); ?></span>
                            </div>
                            <div class="section-product__item-meta-item">
                                <img src="<?php echo esc_url($icon_ruler_url); ?>"
                                    alt="<?php esc_attr_e('Area', 'buildpro'); ?>">
                                <span>
                                    <?php
                                    if ($item['area'] !== '') {
                                        echo esc_html($item['area'] . ' ' . __('sq ft', 'buildpro'));
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                        <div class="section-product__item-location">
                            <img src="<?php echo esc_url($icon_location_card_url); ?>"
                                alt="<?php esc_attr_e('Location', 'buildpro'); ?>">
                            <span><?php echo esc_html($item['location'] !== '' ? $item['location'] : __('Updating location', 'buildpro')); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    $products_page_url = '';
    $products_pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'products-page.php', 'number' => 1));
    if (!empty($products_pages)) {
        $products_page_url = get_permalink($products_pages[0]->ID);
    }
    ?>
    <div class="section-portfolio__page-link">
        <a class="section-portfolio__page-link-text" href="<?php echo esc_url($products_page_url); ?>">
            <?php echo esc_html($materials_view_all_text); ?>
        </a>
        <svg class="section-banner__item-button-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
            aria-hidden="true" focusable="false">
            <path
                d="M566.6 342.6C579.1 330.1 579.1 309.8 566.6 297.3L406.6 137.3C394.1 124.8 373.8 124.8 361.3 137.3C348.8 149.8 348.8 170.1 361.3 182.6L466.7 288L96 288C78.3 288 64 302.3 64 320C64 337.7 78.3 352 96 352L466.7 352L361.3 457.4C348.8 469.9 348.8 490.2 361.3 502.7C373.8 515.2 394.1 515.2 406.6 502.7L566.6 342.7z" />
        </svg>
    </div>
</section>