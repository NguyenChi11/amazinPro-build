<?php
if (! defined('_S_VERSION')) {
    // Replace the version number of the theme on each release.
    define('_S_VERSION', '1.0.0');
}

function buildpro_setup()
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    register_nav_menus(
        array(
            'menu-1' => esc_html__('Primary', 'buildpro'),
        )
    );
}
add_action('after_setup_theme', 'buildpro_setup');

// Flush rewrite rules once to fix REST API pretty URLs (/wp-json/)
function buildpro_maybe_flush_rewrite_rules()
{
    if (get_option('buildpro_rewrite_flushed') !== '3') {
        flush_rewrite_rules();
        update_option('buildpro_rewrite_flushed', '3');
    }
}
add_action('init', 'buildpro_maybe_flush_rewrite_rules', 99);

require get_template_directory() . '/import/import-css-js.php';
require get_template_directory() . '/inc/core/buildpro-theme.php';
require get_template_directory() . '/inc/functions/header-function.php';
require get_template_directory() . '/inc/functions/demo-function.php';
require get_template_directory() . '/inc/customizer/preview-page/index.php';
require get_template_directory() . '/inc/customizer/header/index.php';
require get_template_directory() . '/inc/customizer/footer/index.php';
require get_template_directory() . '/inc/customizer/link-picker/index.php';
require get_template_directory() . '/inc/customizer/home-page/index.php';
require get_template_directory() . '/inc/customizer/project-page/index.php';
require get_template_directory() . '/inc/customizer/about-us-page/index.php';
require get_template_directory() . '/inc/meta-box/page/home-page/index.php';
require get_template_directory() . '/inc/meta-box/page/project-page/index.php';
require get_template_directory() . '/inc/meta-box/page/about-us-page/index.php';


require get_template_directory() . '/inc/core/contact-form.php';
require get_template_directory() . '/inc/core/woocomerce-function.php';
require get_template_directory() . '/inc/functions/cart-function.php';
require get_template_directory() . '/inc/functions/page/cart.php';

require get_template_directory() . '/inc/functions/post-type/project-function.php';
require get_template_directory() . '/inc/functions/post-type/post-function.php';
require get_template_directory() . '/inc/meta-box/post-type/post/index.php';
require get_template_directory() . '/inc/meta-box/post-type/project/index.php';


function buildpro_svg_icon($name, $style = 'solid', $class = '')
{
    static $icons = null;
    if ($icons === null) {
        $icons = array();
        $paths = array(
            get_theme_file_path('/assets/svg/logo-svg-icons/icons-v6-0.php'),
            get_theme_file_path('/assets/svg/logo-svg-icons/icons-v6-1.php'),
            get_theme_file_path('/assets/svg/logo-svg-icons/icons-v6-2.php'),
            get_theme_file_path('/assets/svg/logo-svg-icons/icons-v6-3.php'),
        );
        foreach ($paths as $p) {
            if (file_exists($p)) {
                $icons = array_merge($icons, require $p);
            }
        }
    }
    if (!isset($icons[$name]['svg'][$style])) {
        return '';
    }
    $def = $icons[$name]['svg'][$style];
    $w = isset($def['width']) ? (int) $def['width'] : 512;
    $h = isset($def['height']) ? (int) $def['height'] : 512;
    $path = isset($def['path']) ? $def['path'] : '';
    $cls = $class ? ' class="' . esc_attr($class) . '"' : '';
    return '<svg' . $cls . ' width="1em" height="1em" viewBox="0 0 ' . $w . ' ' . $h . '" fill="currentColor" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><path d="' . $path . '"></path></svg>';
}

function buildpro_maybe_import_wc_products()
{
    $need = get_option('buildpro_wc_do_import') === '1';
    $active = class_exists('WooCommerce') || function_exists('wc_get_product');
    if ($need && $active) {
        $wcProducts = buildpro_import_parse_js('/assets/data/woocommerce-product-data.js', 'woocommerceProductData');
        if (isset($wcProducts['items']) && is_array($wcProducts['items'])) {
            foreach ($wcProducts['items'] as $it) {
                buildpro_import_create_wc_product($it);
            }
        }
        update_option('buildpro_wc_do_import', '0');
        update_option('buildpro_wc_default_content_imported', '1');
    }
}
add_action('init', 'buildpro_maybe_import_wc_products', 20);
if (function_exists('add_action')) {
    add_action('woocommerce_init', 'buildpro_maybe_import_wc_products');
}

function buildpro_run_wc_import_now()
{
    $active = class_exists('WooCommerce') || function_exists('wc_get_product');
    if (!$active) {
        return;
    }
    $wcProducts = buildpro_import_parse_js('/assets/data/woocommerce-product-data.js', 'woocommerceProductData');
    if (isset($wcProducts['items']) && is_array($wcProducts['items'])) {
        foreach ($wcProducts['items'] as $it) {
            buildpro_import_create_wc_product($it);
        }
    }
    update_option('buildpro_wc_do_import', '0');
    update_option('buildpro_wc_default_content_imported', '1');
}

function buildpro_on_plugin_activated($plugin)
{
    if ($plugin === 'woocommerce/woocommerce.php') {
        buildpro_run_wc_import_now();
    }
}
add_action('activated_plugin', 'buildpro_on_plugin_activated', 10, 1);

function buildpro_get_post_views($post_id)
{
    $v = (int) get_post_meta($post_id, 'buildpro_post_views', true);
    return $v;
}

function buildpro_format_views($n)
{
    if ($n >= 1000000) {
        return round($n / 1000000, 1) . 'm';
    }
    if ($n >= 1000) {
        return round($n / 1000, 1) . 'k';
    }
    return (string) $n;
}
