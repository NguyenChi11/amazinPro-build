<?php
$pid = get_the_ID();
if (!function_exists('wc_get_product')) {
    return;
}
if (!$pid || get_post_type($pid) !== 'product') {
    return;
}

$product = call_user_func('wc_get_product', $pid);
if (!$product) {
    return;
}

$title = (string) get_the_title($pid);

$featured_id = (int) get_post_thumbnail_id($pid);
$gallery_ids = array_values(array_filter((array) $product->get_gallery_image_ids(), static function ($image_id) use ($featured_id) {
    $image_id = (int) $image_id;
    return $image_id > 0 && $image_id !== $featured_id;
}));

$slide_ids = array();
if ($featured_id > 0) {
    $slide_ids[] = $featured_id;
}
$slide_ids = array_merge($slide_ids, $gallery_ids);

$price_html = (string) $product->get_price_html();
if ($price_html === '') {
    $fallback_price = $product->get_price();
    if ($fallback_price !== '') {
        $price_html = function_exists('wc_price') ? call_user_func('wc_price', $fallback_price) : esc_html($fallback_price);
    }
}

$thumb_placeholder = function_exists('wc_placeholder_img_src') ? call_user_func('wc_placeholder_img_src', 'woocommerce_thumbnail') : '';
$main_placeholder = function_exists('wc_placeholder_img_src') ? call_user_func('wc_placeholder_img_src', 'woocommerce_single') : '';

$bedrooms = trim((string) get_post_meta($pid, 'buildpro_product_bedrooms', true));
$bathrooms = trim((string) get_post_meta($pid, 'buildpro_product_bathrooms', true));
$area = trim((string) get_post_meta($pid, 'buildpro_product_area', true));
$location = trim((string) get_post_meta($pid, 'buildpro_product_location', true));
$overview = trim((string) get_post_meta($pid, 'buildpro_product_overview', true));
$typical_range = trim((string) get_post_meta($pid, 'typical_range', true));
$lot_size = trim((string) get_post_meta($pid, 'buildpro_product_lot_size', true));
$garage = trim((string) get_post_meta($pid, 'buildpro_product_garage', true));
$year_built = trim((string) get_post_meta($pid, 'buildpro_product_year_built', true));
$floors = trim((string) get_post_meta($pid, 'buildpro_product_floors', true));

if ($overview === '') {
    $overview = trim((string) $product->get_short_description());
}
if ($overview === '') {
    $overview = trim((string) $product->get_description());
}

$normalize_list = static function ($raw_value) {
    if (is_array($raw_value)) {
        $rows = $raw_value;
    } else {
        $rows = explode("\n", str_replace(array("\r\n", "\r"), "\n", (string) $raw_value));
    }

    $clean_rows = array();
    foreach ($rows as $row) {
        $row = trim((string) $row);
        if ($row !== '') {
            $clean_rows[] = $row;
        }
    }

    return $clean_rows;
};

$feature_items = $normalize_list(get_post_meta($pid, 'buildpro_product_features', true));
$interior_feature_items = $normalize_list(get_post_meta($pid, 'buildpro_product_interior_features', true));

$details = array();
if ($lot_size !== '') {
    $details[esc_html__('Lot size', 'buildpro')] = $lot_size;
}
if ($garage !== '') {
    $details[esc_html__('Garage', 'buildpro')] = $garage;
}
if ($year_built !== '') {
    $details[esc_html__('Year built', 'buildpro')] = $year_built;
}
if ($floors !== '') {
    $details[esc_html__('Floors', 'buildpro')] = $floors;
}
if ($typical_range !== '') {
    $details[esc_html__('Typical range', 'buildpro')] = $typical_range;
}

$product_type = (string) $product->get_type();
if ($product_type !== '') {
    $details[esc_html__('Product type', 'buildpro')] = ucwords($product_type);
}

$sku = (string) $product->get_sku();
if ($sku !== '') {
    $details[esc_html__('SKU', 'buildpro')] = $sku;
}

$icon_bedroom_url = get_theme_file_uri('/assets/images/icon/icon_bedroom.png');
$icon_bathroom_url = get_theme_file_uri('/assets/images/icon/icon_bathroom.png');
$icon_ruler_url = get_theme_file_uri('/assets/images/icon/icon_ruler.png');
$icon_location_card_url = get_theme_file_uri('/assets/images/icon/icon_location_card.png');
$icon_cart_url = get_theme_file_uri('/assets/images/icon/icon_cart.png');

$related_products = array();
$related_query = new WP_Query(array(
    'post_type' => 'product',
    'post_status' => 'publish',
    'posts_per_page' => 3,
    'post__not_in' => array($pid),
    'orderby' => 'date',
    'order' => 'DESC',
    'no_found_rows' => true,
));

if ($related_query->have_posts()) {
    $currency_symbol = function_exists('get_woocommerce_currency_symbol') ? (string) call_user_func('get_woocommerce_currency_symbol') : '$';

    while ($related_query->have_posts()) {
        $related_query->the_post();

        $related_pid = get_the_ID();
        $related_product = call_user_func('wc_get_product', $related_pid);
        if (!$related_product) {
            continue;
        }

        $related_price = '';
        $raw_related_price = $related_product->get_price();
        if ($raw_related_price !== '' && is_numeric($raw_related_price)) {
            $related_price_number = (float) $raw_related_price;
            $related_price_decimals = floor($related_price_number) == $related_price_number ? 0 : 2;
            $related_price = number_format_i18n($related_price_number, $related_price_decimals);
        } elseif (is_string($raw_related_price)) {
            $related_price = $raw_related_price;
        }

        $related_area = trim((string) get_post_meta($related_pid, 'buildpro_product_area', true));
        if ($related_area !== '' && is_numeric($related_area)) {
            $related_area_number = (float) $related_area;
            $related_area_decimals = floor($related_area_number) == $related_area_number ? 0 : 2;
            $related_area = number_format_i18n($related_area_number, $related_area_decimals);
        }

        $related_image = get_the_post_thumbnail_url($related_pid, 'large');
        if ($related_image === '') {
            $related_image = $main_placeholder;
        }

        $related_products[] = array(
            'id' => $related_pid,
            'title' => (string) get_the_title($related_pid),
            'link' => (string) get_permalink($related_pid),
            'image' => (string) $related_image,
            'price' => (string) $related_price,
            'currency_symbol' => $currency_symbol,
            'bedrooms' => trim((string) get_post_meta($related_pid, 'buildpro_product_bedrooms', true)),
            'bathrooms' => trim((string) get_post_meta($related_pid, 'buildpro_product_bathrooms', true)),
            'area' => $related_area,
            'location' => trim((string) get_post_meta($related_pid, 'buildpro_product_location', true)),
        );
    }
}
wp_reset_postdata();
?>

<?php
get_template_part('template/template-parts/breadcrums/index');
?>

<article class="single-product-detail" id="product-<?php echo esc_attr($pid); ?>">
    <header class="single-product__hero" data-aos="fade-up">
        <div class="single-product__gallery">
            <div class="swiper thumbs-swiper" aria-label="Product thumbnails">
                <div class="swiper-wrapper">
                    <?php if (!empty($slide_ids)) : ?>
                    <?php foreach ($slide_ids as $image_id) :
                            $thumb_image = wp_get_attachment_image($image_id, 'thumbnail', false, array('loading' => 'lazy'));
                            if (!$thumb_image) {
                                continue;
                            }
                        ?>
                    <div class="swiper-slide"><?php echo $thumb_image; ?></div>
                    <?php endforeach; ?>
                    <?php else : ?>
                    <div class="swiper-slide">
                        <img src="<?php echo esc_url($thumb_placeholder); ?>" alt="<?php echo esc_attr($title); ?>"
                            loading="lazy">
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="swiper main-swiper" aria-label="Product gallery">
                <div class="swiper-wrapper">
                    <?php if (!empty($slide_ids)) : ?>
                    <?php foreach ($slide_ids as $index => $image_id) :
                            $main_attrs = $index === 0
                                ? array('loading' => 'eager', 'fetchpriority' => 'high')
                                : array('loading' => 'lazy');
                            $main_image = wp_get_attachment_image($image_id, 'large', false, $main_attrs);
                            if (!$main_image) {
                                continue;
                            }
                        ?>
                    <div class="swiper-slide"><?php echo $main_image; ?></div>
                    <?php endforeach; ?>
                    <?php else : ?>
                    <div class="swiper-slide">
                        <img src="<?php echo esc_url($main_placeholder); ?>" alt="<?php echo esc_attr($title); ?>"
                            loading="eager">
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="single-product__panel">
            <?php if ($price_html !== '') : ?>
            <div class="single-product__price"><?php echo wp_kses_post($price_html); ?></div>
            <?php endif; ?>

            <h1 class="single-product__title"><?php echo esc_html($title); ?></h1>

            <div class="single-product__facts">
                <?php if ($bedrooms !== '') : ?>
                <div class="single-product__fact">
                    <i class="fa-solid fa-bed" aria-hidden="true"></i>
                    <span><?php echo esc_html($bedrooms); ?></span>
                </div>
                <?php endif; ?>

                <?php if ($bathrooms !== '') : ?>
                <div class="single-product__fact">
                    <i class="fa-solid fa-bath" aria-hidden="true"></i>
                    <span><?php echo esc_html($bathrooms); ?></span>
                </div>
                <?php endif; ?>

                <?php if ($area !== '') : ?>
                <div class="single-product__fact">
                    <i class="fa-solid fa-ruler-combined" aria-hidden="true"></i>
                    <span><?php echo esc_html($area); ?> <?php esc_html_e('sq ft', 'buildpro'); ?></span>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($location !== '') : ?>
            <div class="single-product__location">
                <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                <span><?php echo esc_html($location); ?></span>
            </div>
            <?php endif; ?>

            <button type="button" class="single-product__cta btn-add-to-cart"
                data-product-id="<?php echo esc_attr($pid); ?>"
                aria-label="<?php echo esc_attr__('Contact agent', 'buildpro'); ?>">
                <?php echo esc_html__('Contact Agent', 'buildpro'); ?>
            </button>
        </div>
    </header>

    <div class="single-product__content">
        <?php if ($overview !== '') : ?>
        <section class="single-product__section single-product__section--overview" data-aos="fade-up">
            <h2 class="single-product__section-title"><?php esc_html_e('Overview', 'buildpro'); ?></h2>
            <div class="single-product__section-content"><?php echo wp_kses_post(wpautop($overview)); ?></div>
        </section>
        <?php endif; ?>

        <?php if (!empty($feature_items) || !empty($interior_feature_items)) : ?>
        <section class="single-product__section single-product__section--features" data-aos="fade-up">
            <h2 class="single-product__section-title"><?php esc_html_e('Features', 'buildpro'); ?></h2>
            <div class="single-product__feature-columns">
                <?php if (!empty($feature_items)) : ?>
                <div class="single-product__feature-block single-product__feature-block--key">
                    <ul>
                        <?php foreach ($feature_items as $feature_item) : ?>
                        <li><?php echo esc_html($feature_item); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

            </div>
        </section>
        <?php endif; ?>

        <?php if (!empty($interior_feature_items)) : ?>
        <section class="single-product__section single-product__section--interior-features" data-aos="fade-up">
            <div class="single-product__feature-block single-product__feature-block--interior">
                <h2 class="single-product__section-title"><?php esc_html_e('Interior Features', 'buildpro'); ?></h2>
                <ul>
                    <?php foreach ($interior_feature_items as $interior_item) : ?>
                    <li><?php echo esc_html($interior_item); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
        <?php endif; ?>

        <?php if (!empty($details)) : ?>
        <section class="single-product__section single-product__section--details" data-aos="fade-up">
            <h2 class="single-product__section-title"><?php esc_html_e('Key Information', 'buildpro'); ?></h2>
            <div class="single-product__details">
                <?php foreach ($details as $label => $value) : ?>
                <div class="single-product__detail-item">
                    <span class="single-product__detail-label"><?php echo esc_html($label); ?></span>
                    <span class="single-product__detail-value"><?php echo esc_html($value); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </div>

    <?php if (!empty($related_products)) : ?>
    <section class="single-product__related" data-aos="fade-up">
        <h2 class="single-product__related-title"><?php esc_html_e('Trending Listings', 'buildpro'); ?></h2>

        <div class="single-product__related-grid">
            <?php foreach ($related_products as $related_item) : ?>
            <article class="single-product__related-item">
                <a class="single-product__related-link single-product__related-link--image"
                    href="<?php echo esc_url($related_item['link']); ?>"
                    aria-label="<?php echo esc_attr($related_item['title']); ?>">
                    <?php if ($related_item['image'] !== '') : ?>
                    <img src="<?php echo esc_url($related_item['image']); ?>"
                        alt="<?php echo esc_attr($related_item['title']); ?>" loading="lazy">
                    <?php endif; ?>
                </a>

                <div class="single-product__related-content">
                    <div class="single-product__related-price-row">
                        <p class="single-product__related-price">
                            <?php if ($related_item['price'] !== '') : ?>
                            <span><?php echo esc_html($related_item['currency_symbol']); ?></span><span><?php echo esc_html($related_item['price']); ?></span>
                            <?php else : ?>
                            <?php esc_html_e('Contact', 'buildpro'); ?>
                            <?php endif; ?>
                        </p>

                        <!-- <button class="single-product__related-cta btn-add-to-cart" type="button"
                            data-product-id="<?php echo esc_attr($related_item['id']); ?>"
                            aria-label="<?php esc_attr_e('Add to cart', 'buildpro'); ?>">
                            <img src="<?php echo esc_url($icon_cart_url); ?>" alt="" aria-hidden="true">
                            <span><?php esc_html_e('Add to cart', 'buildpro'); ?></span>
                        </button> -->
                    </div>

                    <a class="single-product__related-link single-product__related-link--title"
                        href="<?php echo esc_url($related_item['link']); ?>">
                        <h3 class="single-product__related-item-title"><?php echo esc_html($related_item['title']); ?>
                        </h3>
                    </a>

                    <div class="single-product__related-meta"
                        aria-label="<?php esc_attr_e('Property details', 'buildpro'); ?>">
                        <div class="single-product__related-meta-item">
                            <img src="<?php echo esc_url($icon_bedroom_url); ?>"
                                alt="<?php esc_attr_e('Bedroom', 'buildpro'); ?>">
                            <span><?php echo esc_html($related_item['bedrooms'] !== '' ? $related_item['bedrooms'] : '-'); ?></span>
                        </div>
                        <div class="single-product__related-meta-item">
                            <img src="<?php echo esc_url($icon_bathroom_url); ?>"
                                alt="<?php esc_attr_e('Bathroom', 'buildpro'); ?>">
                            <span><?php echo esc_html($related_item['bathrooms'] !== '' ? $related_item['bathrooms'] : '-'); ?></span>
                        </div>
                        <div class="single-product__related-meta-item">
                            <img src="<?php echo esc_url($icon_ruler_url); ?>"
                                alt="<?php esc_attr_e('Area', 'buildpro'); ?>">
                            <?php if ($related_item['area'] !== '') : ?>
                            <span><?php echo esc_html($related_item['area'] . ' ' . __('sq ft', 'buildpro')); ?></span>
                            <?php else : ?>
                            <span>-</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="single-product__related-location">
                        <img src="<?php echo esc_url($icon_location_card_url); ?>"
                            alt="<?php esc_attr_e('Location', 'buildpro'); ?>">
                        <span><?php echo esc_html($related_item['location'] !== '' ? $related_item['location'] : __('Updating location', 'buildpro')); ?></span>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php get_template_part('template/template-parts/page/home/section-contact/index'); ?>
</article>