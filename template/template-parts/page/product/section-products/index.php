<?php
$current_url = function_exists('get_permalink') ? get_permalink() : home_url('/');
$sel_brand = isset($_GET['brand']) ? sanitize_text_field(wp_unslash($_GET['brand'])) : '';
$sel_cat   = isset($_GET['category']) ? sanitize_text_field(wp_unslash($_GET['category'])) : '';
$sel_tag   = isset($_GET['tag']) ? sanitize_text_field(wp_unslash($_GET['tag'])) : '';
$keyword   = isset($_GET['q']) ? sanitize_text_field(wp_unslash($_GET['q'])) : '';

$brands_icon = get_theme_file_uri('/assets/images/icon/materials-svgrepo-com 1.png');
$cats_icon   = get_theme_file_uri('/assets/images/icon/paint-bucket-svgrepo-com 1.png');
$tags_icon   = get_theme_file_uri('/assets/images/icon/tools-svgrepo-com 1.png');
$maps = array(
    'product_brand' => array(
        'icon' => $brands_icon,
        'label' => __('Brands', 'buildpro'),
        'query_key' => 'brand',
    ),
    'product_cat'   => array(
        'icon' => $cats_icon,
        'label' => __('Categories', 'buildpro'),
        'query_key' => 'category',
    ),
    'product_tag'   => array(
        'icon' => $tags_icon,
        'label' => __('Tags', 'buildpro'),
        'query_key' => 'tag',
    ),
);
$psp_terms_max = (int) apply_filters('buildpro_product_filters_terms_max', 50);
if ($psp_terms_max <= 0) {
    $psp_terms_max = 50;
}
?>
<section class="product--section-products product-section-products" data-aos="fade-up">
    <div class="product-section-products__top">
        <div class="product-section-products__top_right">


            <div class="product-section-products__product-search">
                <form class="psp-search psp-filter-form" role="search" method="get"
                    action="<?php echo esc_url($current_url); ?>">
                    <label class="screen-reader-text"
                        for="psp-search-input"><?php esc_html_e('Search products', 'buildpro'); ?></label>
                    <span class="psp-search__icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M21 21L16.65 16.65M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z"
                                stroke="#6B7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <input id="psp-search-input" class="psp-search__input" type="search" name="q"
                        placeholder="<?php esc_attr_e('Search materials, tools, or brands ...', 'buildpro'); ?>"
                        value="<?php echo esc_attr($keyword); ?>" />

                    <!-- <div class="product-section-products__category">
                        <div class="psp-filters-grid">
                            <?php
                            foreach ($maps as $tax => $cfg) {
                                if (!taxonomy_exists($tax)) {
                                    continue;
                                }

                                $terms = get_terms(array(
                                    'taxonomy' => $tax,
                                    'hide_empty' => true,
                                    'number' => $psp_terms_max,
                                    'orderby' => 'count',
                                    'order' => 'DESC',
                                ));
                                if (is_wp_error($terms)) {
                                    continue;
                                }

                                $selected_value = '';
                                if ($tax === 'product_brand') {
                                    $selected_value = $sel_brand;
                                } elseif ($tax === 'product_cat') {
                                    $selected_value = $sel_cat;
                                } elseif ($tax === 'product_tag') {
                                    $selected_value = $sel_tag;
                                }
                            ?>
                                <label class="psp-filter-field psp-filter-field--<?php echo esc_attr($tax); ?>"
                                    for="psp-filter-<?php echo esc_attr($tax); ?>">
                                    <span class="psp-filter-field__meta">
                                        <img class="psp-filter-field__icon" src="<?php echo esc_url($cfg['icon']); ?>"
                                            alt="<?php echo esc_attr($cfg['label']); ?>">
                                        <span class="psp-filter-field__label"><?php echo esc_html($cfg['label']); ?></span>
                                    </span>
                                    <span class="psp-filter-field__control-wrap">
                                        <select id="psp-filter-<?php echo esc_attr($tax); ?>"
                                            class="psp-filter-field__select"
                                            name="<?php echo esc_attr($cfg['query_key']); ?>">
                                            <option value="">
                                                <?php
                                                echo esc_html(
                                                    sprintf(
                                                        __('All %s', 'buildpro'),
                                                        $cfg['label']
                                                    )
                                                );
                                                ?>
                                            </option>
                                            <?php
                                            if (!empty($terms) && is_array($terms)) {
                                                foreach ($terms as $t) {
                                            ?>
                                                    <option value="<?php echo esc_attr($t->slug); ?>"
                                                        <?php selected($selected_value, $t->slug); ?>>
                                                        <?php echo esc_html($t->name); ?>
                                                    </option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </span>
                                </label>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                    <button type="submit"
                        class="psp-filter-submit"><?php esc_html_e('Apply Filters', 'buildpro'); ?></button> -->
                </form>
            </div>
        </div>
    </div>
    <div class="product-section-products__bottom">

        <div class="product-section-products__product--list">
            <?php
            $paged = max(1, !empty($_GET['prod_p']) ? (int) $_GET['prod_p'] : 1);
            $ppp = 6;
            $items = array();
            $icon_bedroom_url = get_theme_file_uri('/assets/images/icon/icon_bedroom.png');
            $icon_bathroom_url = get_theme_file_uri('/assets/images/icon/icon_bathroom.png');
            $icon_ruler_url = get_theme_file_uri('/assets/images/icon/icon_ruler.png');
            $icon_location_card_url = get_theme_file_uri('/assets/images/icon/icon_location_card.png');
            $icon_cart_url = get_theme_file_uri('/assets/images/icon/icon_cart.png');
            if (class_exists('WooCommerce') || function_exists('wc_get_product')) {
                $sel_brand = isset($_GET['brand']) ? sanitize_text_field(wp_unslash($_GET['brand'])) : '';
                $sel_cat   = isset($_GET['category']) ? sanitize_text_field(wp_unslash($_GET['category'])) : '';
                $sel_tag   = isset($_GET['tag']) ? sanitize_text_field(wp_unslash($_GET['tag'])) : '';
                $keyword   = isset($_GET['q']) ? sanitize_text_field(wp_unslash($_GET['q'])) : '';
                $args = array(
                    'post_type' => 'product',
                    'posts_per_page' => $ppp,
                    'paged' => $paged,
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'post_status' => 'publish',
                );
                $tax_query = array('relation' => 'AND');
                if ($sel_brand !== '') {
                    $tax_query[] = array(
                        'taxonomy' => 'product_brand',
                        'field' => 'slug',
                        'terms' => $sel_brand,
                    );
                }
                if ($sel_cat !== '') {
                    $tax_query[] = array(
                        'taxonomy' => 'product_cat',
                        'field' => 'slug',
                        'terms' => $sel_cat,
                    );
                }
                if ($sel_tag !== '') {
                    $tax_query[] = array(
                        'taxonomy' => 'product_tag',
                        'field' => 'slug',
                        'terms' => $sel_tag,
                    );
                }
                if (count($tax_query) > 1) {
                    $args['tax_query'] = $tax_query;
                }
                if ($keyword !== '') {
                    $args['s'] = $keyword;
                }
                $q = new WP_Query($args);
                if ($q->have_posts()) {
            ?>
                    <div class="section-product__list">
                        <?php
                        while ($q->have_posts()) {
                            $q->the_post();
                            $pid = get_the_ID();
                            $img = get_the_post_thumbnail_url($pid, 'large');
                            $title = get_the_title($pid);
                            $price = '';
                            $currency_symbol = '';
                            $bedrooms = (string) get_post_meta($pid, 'buildpro_product_bedrooms', true);
                            $bathrooms = (string) get_post_meta($pid, 'buildpro_product_bathrooms', true);
                            $area = (string) get_post_meta($pid, 'buildpro_product_area', true);
                            if ($area !== '' && is_numeric($area)) {
                                $area_number = (float) $area;
                                $area_decimals = floor($area_number) == $area_number ? 0 : 2;
                                $area = number_format_i18n($area_number, $area_decimals);
                            }
                            $location = (string) get_post_meta($pid, 'buildpro_product_location', true);
                            if (function_exists('wc_get_product')) {
                                $p = call_user_func('wc_get_product', $pid);
                                if ($p) {
                                    $raw_price = $p->get_price();
                                    if ($raw_price !== '' && is_numeric($raw_price)) {
                                        $price_number = (float) $raw_price;
                                        $price_decimals = floor($price_number) == $price_number ? 0 : 2;
                                        $price = number_format_i18n($price_number, $price_decimals);
                                    } elseif (is_string($raw_price)) {
                                        $price = $raw_price;
                                    }
                                    if (function_exists('get_woocommerce_currency_symbol')) {
                                        $currency_symbol = call_user_func('get_woocommerce_currency_symbol');
                                    }
                                }
                            }
                            if ($currency_symbol === '') {
                                $currency_symbol = '$';
                            }
                            $link = get_permalink($pid);
                        ?>
                            <div class="section-product__grid-item">
                                <div class="section-product__item">
                                    <div class="section-product__item-image">
                                        <a class="section-product__item-link" href="<?php echo esc_url($link); ?>"
                                            aria-label="<?php echo esc_attr($title); ?>">
                                            <?php if (!empty($img)) : ?>
                                                <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($title); ?>">
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <div class="section-product__item-content">
                                        <div class="section-product__item-price-row">
                                            <p class="section-product__item-price">
                                                <?php if ($price !== '') : ?>
                                                    <span><?php echo esc_html($currency_symbol); ?></span><span><?php echo esc_html($price); ?></span>
                                                <?php else : ?>
                                                    <?php esc_html_e('Contact', 'buildpro'); ?>
                                                <?php endif; ?>
                                            </p>
                                            <!-- <button class="section-product__item-cta btn-add-to-cart" type="button"
                                    data-product-id="<?php echo esc_attr($pid); ?>"
                                    aria-label="<?php esc_attr_e('Add to Cart', 'buildpro'); ?>">
                                    <img src="<?php echo esc_url($icon_cart_url); ?>" alt="" aria-hidden="true">
                                    <span><?php esc_html_e('Add to Cart', 'buildpro'); ?></span>
                                </button> -->
                                        </div>
                                        <a class="section-product__item-title-link" href="<?php echo esc_url($link); ?>">
                                            <h3 class="section-product__item-title"><?php echo esc_html($title); ?></h3>
                                        </a>
                                        <div class="section-product__item-meta"
                                            aria-label="<?php esc_attr_e('Property details', 'buildpro'); ?>">
                                            <div class="section-product__item-meta-item">
                                                <img src="<?php echo esc_url($icon_bedroom_url); ?>"
                                                    alt="<?php esc_attr_e('Bedroom', 'buildpro'); ?>">
                                                <span><?php echo esc_html($bedrooms !== '' ? $bedrooms : '-'); ?></span>
                                            </div>
                                            <div class="section-product__item-meta-item">
                                                <img src="<?php echo esc_url($icon_bathroom_url); ?>"
                                                    alt="<?php esc_attr_e('Bathroom', 'buildpro'); ?>">
                                                <span><?php echo esc_html($bathrooms !== '' ? $bathrooms : '-'); ?></span>
                                            </div>
                                            <div class="section-product__item-meta-item">
                                                <img src="<?php echo esc_url($icon_ruler_url); ?>"
                                                    alt="<?php esc_attr_e('Area', 'buildpro'); ?>">
                                                <?php if ($area !== '') : ?>
                                                    <span><?php echo esc_html($area . ' ' . __('sq ft', 'buildpro')); ?></span>
                                                <?php else : ?>
                                                    <span>-</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="section-product__item-location">
                                            <img src="<?php echo esc_url($icon_location_card_url); ?>"
                                                alt="<?php esc_attr_e('Location', 'buildpro'); ?>">
                                            <span><?php echo esc_html($location !== '' ? $location : __('Updating location', 'buildpro')); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                    <?php
                    $preserve = array();
                    if ($keyword !== '') $preserve['q'] = $keyword;
                    if ($sel_brand !== '') $preserve['brand'] = $sel_brand;
                    if ($sel_cat !== '') $preserve['category'] = $sel_cat;
                    if ($sel_tag !== '') $preserve['tag'] = $sel_tag;
                    wp_reset_postdata();
                    $base_pg_url = remove_query_arg(array('prod_p', 'paged', 'page'), empty($preserve) ? $current_url : add_query_arg($preserve, $current_url));
                    $sep = (strpos($base_pg_url, '?') !== false) ? '&' : '?';
                    $links = paginate_links(array(
                        'base' => $base_pg_url . $sep . 'prod_p=%#%',
                        'format' => '',
                        'current' => max(1, $paged),
                        'total' => (int) $q->max_num_pages,
                        'type' => 'array',
                        'prev_next' => false,
                    ));
                    if (!empty($links) && is_array($links)) {
                    ?>
                        <nav class="product--pagination">
                            <ul class="page-numbers">
                                <?php if ($paged > 1) : ?>
                                    <li><a class="page-numbers prev"
                                            href="<?php echo esc_url(add_query_arg(array_merge($preserve, array('prod_p' => $paged - 1)), $current_url)); ?>">&lsaquo;</a>
                                    </li>
                                <?php else : ?>
                                    <li><span class="page-numbers prev disabled">&lsaquo;</span></li>
                                <?php endif; ?>

                                <?php foreach ($links as $lnk) : ?>
                                    <li><?php echo $lnk; ?></li>
                                <?php endforeach; ?>

                                <?php if ($paged < (int) $q->max_num_pages) : ?>
                                    <li><a class="page-numbers next"
                                            href="<?php echo esc_url(add_query_arg(array_merge($preserve, array('prod_p' => $paged + 1)), $current_url)); ?>">&rsaquo;</a>
                                    </li>
                                <?php else : ?>
                                    <li><span class="page-numbers next disabled">&rsaquo;</span></li>
                                <?php endif; ?>
                            </ul>
                        </nav>
            <?php
                    }
                }
            }
            ?>
        </div>
    </div>
</section>