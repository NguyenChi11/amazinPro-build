<?php
function buildpro_import_product_demo($target_id = 0)
{
    $home_id = (int) $target_id;
    if ($home_id <= 0 && function_exists('buildpro_banner_find_home_id')) {
        $home_id = buildpro_banner_find_home_id();
    }
    if ($home_id <= 0) {
        $home_id = (int) get_option('page_on_front');
    }
    if ($home_id <= 0) {
        $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'home-page.php', 'number' => 1));
        if (!empty($pages)) {
            $home_id = (int) $pages[0]->ID;
        }
    }
    if ($home_id <= 0) {
        return;
    }
    $wc_active = class_exists('WooCommerce') || function_exists('wc_get_product');
    // Always read title/description from product-data.js (WooCommerce-independent)
    if (function_exists('buildpro_import_parse_js')) {
        $product_data = buildpro_import_parse_js('/assets/data/product-data.js', 'ProductsData');
        $pd_title = isset($product_data['productsTitle']) ? (string)$product_data['productsTitle'] : '';
        $pd_desc  = isset($product_data['productsDescription']) ? (string)$product_data['productsDescription'] : '';
        $pd_view_all_text = isset($product_data['productsViewAllText']) ? (string)$product_data['productsViewAllText'] : '';
        if ($pd_title !== '') {
            update_post_meta($home_id, 'materials_title', $pd_title);
            set_theme_mod('materials_title', $pd_title);
        }
        if ($pd_desc !== '') {
            update_post_meta($home_id, 'materials_description', $pd_desc);
            set_theme_mod('materials_description', $pd_desc);
        }
        if ($pd_view_all_text !== '') {
            update_post_meta($home_id, 'materials_view_all_text', $pd_view_all_text);
            set_theme_mod('materials_view_all_text', $pd_view_all_text);
        }
    }

    if (!$wc_active) {
        update_post_meta($home_id, 'materials_enabled', 1);
        set_theme_mod('materials_enabled', 1);
        return;
    }
    // Always process full demo dataset.
    // Existing products are skipped safely inside buildpro_import_create_wc_product() by slug.
    if (function_exists('buildpro_import_get_wc_products_data')) {
        $data = buildpro_import_get_wc_products_data();
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $it) {
                if (function_exists('buildpro_import_create_wc_product')) {
                    buildpro_import_create_wc_product($it);
                }
            }
        }
    } elseif (function_exists('buildpro_import_parse_js')) {
        $data = buildpro_import_parse_js('/assets/data/woocommerce-product-data.js', 'woocommerceProductData');
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $it) {
                if (function_exists('buildpro_import_create_wc_product')) {
                    buildpro_import_create_wc_product($it);
                }
            }
        }
    }
    update_post_meta($home_id, 'materials_enabled', 1);
    set_theme_mod('materials_enabled', 1);
}
