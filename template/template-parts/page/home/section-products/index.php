<?php
$page_id = get_queried_object_id();
$materials_enabled = get_post_meta($page_id, 'materials_enabled', true);
$materials_enabled = $materials_enabled === '' ? 1 : (int)$materials_enabled;
$materials_title = get_post_meta($page_id, 'materials_title', true);
$materials_description = get_post_meta($page_id, 'materials_description', true);
if (is_customize_preview()) {
    $mod_title = get_theme_mod('materials_title', $materials_title);
    $mod_desc = get_theme_mod('materials_description', $materials_description);
    $mod_enabled = get_theme_mod('materials_enabled', 1);
    $materials_enabled = (int)$mod_enabled;
    if ($mod_title !== '') {
        $materials_title = $mod_title;
    }
    if ($mod_desc !== '') {
        $materials_description = $mod_desc;
    }
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
            if (function_exists('wc_get_product')) {
                $product = wc_get_product($post_id);
                if ($product) {
                    $price = $product->get_price();
                }
            }
            $items[] = array(
                'id' => $post_id,
                'title' => $title,
                'image' => $image_url,
                'price' => $price,
                'link' => get_permalink($post_id),
            );
        }
        wp_reset_postdata();
    }
}
?>
<?php $style = $materials_enabled !== 1 ? ' style="display:none"' : ''; ?>
<section class="section-product" <?php echo $style; ?>>
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
    <div class="swiper section-product__swiper">
        <div class="swiper-wrapper">
            <?php foreach ($items as $item): ?>
                <div class="swiper-slide">
                    <a class="section-product__item" href="<?php echo esc_url($item['link']); ?>">
                        <div class="section-product__item-image">
                            <?php if (!empty($item['image'])): ?>
                                <img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['title']); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="section-product__item-content">
                            <h3 class="section-product__item-title"><?php echo esc_html($item['title']); ?></h3>
                            <div class="section-product__item-bottom">
                                <p class="section-product__item-price">
                                    <span>$</span><?php echo esc_html($item['price']); ?><span>/ton</span>
                                </p>
                                <button class="section-product__item-cta btn-add-to-cart" data-product-id="<?php echo esc_attr($item['id']); ?>">Add to Cart</button>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="swiper-button-prev section-product__swiper-prev"></div>
        <div class="swiper-button-next section-product__swiper-next"></div>
    </div>
    <?php if (empty($items)): ?>
        <?php return; ?>
    <?php endif; ?>
    <?php
    $products_page_url = '';
    $products_pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'products-page.php', 'number' => 1));
    if (!empty($products_pages)) {
        $products_page_url = get_permalink($products_pages[0]->ID);
    }
    ?>
    <div class="section-portfolio__page-link">
        <a class="section-portfolio__page-link-text"
            href="<?php echo esc_url($products_page_url); ?>">
            View All Product
        </a>
        <img class="section-banner__item-button-icon"
            src="<?php echo esc_url(get_theme_file_uri('/assets/images/icon/Arrow_Right.png')); ?>" alt="Arrow Right">
    </div>
</section>