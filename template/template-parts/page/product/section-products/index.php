<section class="product-section-products">
    <div class="product-section-products__left">
        <div class="product-section-products__title">
            <h2 class="product-section-products__title-text">
                Catalog
            </h2>
            <p class="product-section-products__title-desc">
                Construction Supplies
            </p>
        </div>
        <div class="product-section-products__category">
            <?php
            $brands_icon = get_theme_file_uri('/assets/images/icon/materials-svgrepo-com 1.png');
            $cats_icon   = get_theme_file_uri('/assets/images/icon/paint-bucket-svgrepo-com 1.png');
            $tags_icon   = get_theme_file_uri('/assets/images/icon/tools-svgrepo-com 1.png');
            $maps = array(
                'product_brand' => array('icon' => $brands_icon, 'label' => 'Brands'),
                'product_cat'   => array('icon' => $cats_icon, 'label' => 'Categories'),
                'product_tag'   => array('icon' => $tags_icon, 'label' => 'Tags'),
            );
            $current_url = function_exists('get_permalink') ? get_permalink() : home_url('/');
            $current_paged = max(1, get_query_var('paged') ? (int) get_query_var('paged') : (get_query_var('page') ? (int) get_query_var('page') : 1));
            $pagination_key = get_query_var('paged') ? 'paged' : (get_query_var('page') ? 'page' : 'paged');
            $sel_brand = isset($_GET['brand']) ? sanitize_text_field(wp_unslash($_GET['brand'])) : '';
            $sel_cat   = isset($_GET['category']) ? sanitize_text_field(wp_unslash($_GET['category'])) : '';
            $sel_tag   = isset($_GET['tag']) ? sanitize_text_field(wp_unslash($_GET['tag'])) : '';
            $keyword   = isset($_GET['q']) ? sanitize_text_field(wp_unslash($_GET['q'])) : '';
            foreach ($maps as $tax => $cfg) {
                if (!taxonomy_exists($tax)) {
                    continue;
                }
                $terms = get_terms(array(
                    'taxonomy' => $tax,
                    'hide_empty' => true,
                    'number' => 8,
                ));
                if (is_wp_error($terms) || empty($terms)) {
                    continue;
                }
                echo '<div class="psp-cat-group psp-cat-group--' . esc_attr($tax) . '">';
                foreach ($terms as $t) {
                    $is_active = ($tax === 'product_brand' && $sel_brand === $t->slug)
                        || ($tax === 'product_cat' && $sel_cat === $t->slug)
                        || ($tax === 'product_tag' && $sel_tag === $t->slug);

                    // Preserve current selections and keyword
                    $args_out = array();
                    if ($keyword !== '') {
                        $args_out['q'] = $keyword;
                    }
                    if ($sel_brand !== '' && $tax !== 'product_brand') {
                        $args_out['brand'] = $sel_brand;
                    }
                    if ($sel_cat !== '' && $tax !== 'product_cat') {
                        $args_out['category'] = $sel_cat;
                    }
                    if ($sel_tag !== '' && $tax !== 'product_tag') {
                        $args_out['tag'] = $sel_tag;
                    }
                    // Toggle behavior: clicking active chip removes it; clicking inactive sets it
                    if (!$is_active) {
                        if ($tax === 'product_brand') {
                            $args_out['brand'] = $t->slug;
                        } elseif ($tax === 'product_cat') {
                            $args_out['category'] = $t->slug;
                        } else {
                            $args_out['tag'] = $t->slug;
                        }
                    }
                    // Preserve current page to avoid losing pagination on toggle (supports both 'paged' and 'page')
                    if ($current_paged > 1) {
                        $args_out[$pagination_key] = $current_paged;
                    }
                    $link = add_query_arg($args_out, $current_url);
                    $cls = 'psp-chip' . ($is_active ? ' psp-chip--active' : '');
                    echo '<a class="' . esc_attr($cls) . '" href="' . esc_url($link) . '">';
                    echo '<img class="psp-chip__icon" src="' . esc_url($cfg['icon']) . '" alt="' . esc_attr($cfg['label']) . '">';
                    echo '<span class="psp-chip__text">' . esc_html($t->name) . '</span>';
                    echo '</a>';
                }
                echo '</div>';
            }
            ?>
        </div>
    </div>
    <div class="product-section-products__right">
        <div class="product-section-products__product-search">
            <form class="psp-search" role="search" method="get" action="<?php echo esc_url(get_permalink()); ?>">
                <label class="screen-reader-text" for="psp-search-input">Search products</label>
                <span class="psp-search__icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M21 21L16.65 16.65M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z"
                            stroke="#6B7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
                <input id="psp-search-input" class="psp-search__input" type="search" name="q"
                    placeholder="Search materials, tools, or brands ..."
                    value="<?php echo isset($_GET['q']) ? esc_attr(wp_unslash($_GET['q'])) : ''; ?>" />
                <?php if (!empty($sel_brand)): ?>
                <input type="hidden" name="brand" value="<?php echo esc_attr($sel_brand); ?>" />
                <?php endif; ?>
                <?php if (!empty($sel_cat)): ?>
                <input type="hidden" name="category" value="<?php echo esc_attr($sel_cat); ?>" />
                <?php endif; ?>
                <?php if (!empty($sel_tag)): ?>
                <input type="hidden" name="tag" value="<?php echo esc_attr($sel_tag); ?>" />
                <?php endif; ?>
            </form>
        </div>
        <div class="product-section-products__product--list">
            <?php
            $paged = max(1, get_query_var('paged') ? (int) get_query_var('paged') : (get_query_var('page') ? (int) get_query_var('page') : 1));
            $ppp = 9;
            $items = array();
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
                    echo '<div class="section-product__list">';
                    while ($q->have_posts()) {
                        $q->the_post();
                        $pid = get_the_ID();
                        $img = get_the_post_thumbnail_url($pid, 'large');
                        $title = get_the_title($pid);
                        $price = '';
                        if (function_exists('wc_get_product')) {
                            $p = wc_get_product($pid);
                            if ($p) {
                                $price = $p->get_price();
                            }
                        }
                        $link = get_permalink($pid);
                        echo '<a class="section-product__item" href="' . esc_url($link) . '">';
                        echo '  <div class="section-product__item-image">';
                        if (!empty($img)) {
                            echo '    <img src="' . esc_url($img) . '" alt="' . esc_attr($title) . '">';
                        }
                        echo '  </div>';
                        echo '  <div class="section-product__item-content">';
                        echo '    <h3 class="section-product__item-title">' . esc_html($title) . '</h3>';
                        echo '    <div class="section-product__item-bottom">';
                        echo '      <p class="section-product__item-price"><span>$</span>' . esc_html($price) . '<span>/ton</span></p>';
                        echo '      <button class="section-product__item-cta">Request a Quote</button>';
                        echo '    </div>';
                        echo '  </div>';
                        echo '</a>';
                    }
                    echo '</div>';
                    $big = 999999999;
                    $preserve = array();
                    if ($keyword !== '') $preserve['q'] = $keyword;
                    if ($sel_brand !== '') $preserve['brand'] = $sel_brand;
                    if ($sel_cat !== '') $preserve['category'] = $sel_cat;
                    if ($sel_tag !== '') $preserve['tag'] = $sel_tag;
                    $links = paginate_links(array(
                        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                        'format' => '?paged=%#%',
                        'current' => max(1, $paged),
                        'total' => (int) $q->max_num_pages,
                        'type' => 'array',
                        'prev_next' => false,
                        'add_args' => $preserve,
                    ));
                    if (!empty($links) && is_array($links)) {
                        echo '<nav class="product--pagination"><ul class="page-numbers">';
                        if ($paged > 1) {
                            echo '<li><a class="page-numbers prev" href="' . esc_url(add_query_arg($preserve, get_pagenum_link($paged - 1))) . '">&lsaquo;</a></li>';
                        } else {
                            echo '<li><span class="page-numbers prev disabled">&lsaquo;</span></li>';
                        }
                        foreach ($links as $lnk) {
                            echo '<li>' . $lnk . '</li>';
                        }
                        if ($paged < (int) $q->max_num_pages) {
                            echo '<li><a class="page-numbers next" href="' . esc_url(add_query_arg($preserve, get_pagenum_link($paged + 1))) . '">&rsaquo;</a></li>';
                        } else {
                            echo '<li><span class="page-numbers next disabled">&rsaquo;</span></li>';
                        }
                        echo '</ul></nav>';
                    }
                    wp_reset_postdata();
                }
            }
            ?>
        </div>
    </div>
</section>