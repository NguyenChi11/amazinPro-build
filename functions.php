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

require get_template_directory() . '/import/import-css-js.php';
require get_template_directory() . '/inc/core/buildpro-theme.php';
require get_template_directory() . '/inc/functions/header-function.php';
require get_template_directory() . '/inc/customizer/header/index.php';
require get_template_directory() . '/inc/customizer/footer/index.php';
require get_template_directory() . '/inc/customizer/link-picker/index.php';
require get_template_directory() . '/inc/customizer/home-page/index.php';
require get_template_directory() . '/inc/meta-box/home-page/index.php';



function buildpro_maybe_import_default_content()
{
    if ((defined('REST_REQUEST') && REST_REQUEST) || (defined('DOING_AJAX') && DOING_AJAX)) {
        return;
    }
    $footer_demo_file = get_theme_file_path('/import/data-demo/footer-demo.php');
    if (file_exists($footer_demo_file)) {
        require_once $footer_demo_file;
        if (function_exists('buildpro_import_footer_demo')) {
            buildpro_import_footer_demo();
        }
    }
}

add_action('init', 'buildpro_maybe_import_default_content');
