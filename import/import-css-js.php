<?php
$VERSION = WP_DEBUG ? time() : wp_get_theme()->get('Version');
if (!defined('THEME_VERSION')) {
    define('THEME_VERSION', $VERSION);
}

// ============================== start wp_enqueue lib =====================//
// Add preconnect for Google Fonts
function my_add_preconnects($hints, $relation_type)
{
    if ('preconnect' === $relation_type) {
        $hints[] = [
            'href' => 'https://fonts.googleapis.com',
            'crossorigin' => 'anonymous',
        ];
        $hints[] = [
            'href' => 'https://fonts.gstatic.com',
            'crossorigin' => 'anonymous',
        ];
    }
    return $hints;
}
add_filter('wp_resource_hints', 'my_add_preconnects', 10, 2);

function wp_enqueue_lib()
{
    // Fonts
    wp_enqueue_style('font-Quicksand', 'https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap', [], THEME_VERSION);
    wp_enqueue_style('font-Inter', 'https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap', [], THEME_VERSION);
    wp_enqueue_style('font-Barlow', 'https://fonts.googleapis.com/css2?family=Barlow:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap', [], THEME_VERSION);
    wp_enqueue_style('font-Poppins', 'https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap', [], THEME_VERSION);
    wp_enqueue_style('font-Montserrat', 'https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap', [], THEME_VERSION);
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', [], THEME_VERSION);

    // Swiper
    wp_enqueue_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', [], THEME_VERSION);
    wp_enqueue_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], THEME_VERSION, true);

    // GSAP
    wp_enqueue_script('gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js', [], '3.12.5', true);
}
add_action('wp_enqueue_scripts', 'wp_enqueue_lib', 1000);

// ============================== end wp_enqueue lib =====================//

// ============================== wp_enqueue custom assets =====================//
function wp_enqueue_custom_assets()
{
    $version = WP_DEBUG ? time() : wp_get_theme()->get('Version');

    $wp_enqueue_mapping = [
        [
            'type' => 'style',
            'handle' => 'buildpro-style',
            'src' => get_stylesheet_uri(),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => true,
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-global',
            'src' => get_theme_file_uri('/assets/css/global.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('/assets/css/global.css'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-header-style',
            'src' => get_theme_file_uri('template/template-parts/header/styles.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => true
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-header-script',
            'src' => get_theme_file_uri('template/template-parts/header/scripts.js'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => true,
            'condition' => true
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-footer-style',
            'src' => get_theme_file_uri('template/template-parts/footer/styles.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/footer/styles.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-footer-script',
            'src' => get_theme_file_uri('template/template-parts/footer/scripts.js'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/footer/scripts.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-section-banner-style',
            'src' => get_theme_file_uri('template/template-parts/page/home/section-banner/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/home/section-banner/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-section-banner-script',
            'src' => get_theme_file_uri('template/template-parts/page/home/section-banner/script.js'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/home/section-banner/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-section-option-style',
            'src' => get_theme_file_uri('template/template-parts/page/home/section-option/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/home/section-option/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-section-option-script',
            'src' => get_theme_file_uri('template/template-parts/page/home/section-option/script.js'),
            'deps' => ['swiper'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/home/section-option/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-section-data-style',
            'src' => get_theme_file_uri('template/template-parts/page/home/section-data/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/home/section-data/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-section-data-script',
            'src' => get_theme_file_uri('template/template-parts/page/home/section-data/script.js'),
            'deps' => ['swiper'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/home/section-data/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-section-products-style',
            'src' => get_theme_file_uri('template/template-parts/page/home/section-products/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/home/section-products/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-section-products-script',
            'src' => get_theme_file_uri('template/template-parts/page/home/section-products/script.js'),
            'deps' => ['swiper'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/home/section-products/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-section-services-style',
            'src' => get_theme_file_uri('template/template-parts/page/home/section-services/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/home/section-services/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-section-services-script',
            'src' => get_theme_file_uri('template/template-parts/page/home/section-services/script.js'),
            'deps' => ['swiper'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/home/section-services/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-section-evaluate-style',
            'src' => get_theme_file_uri('template/template-parts/page/home/section-evaluate/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/home/section-evaluate/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-section-evaluate-script',
            'src' => get_theme_file_uri('template/template-parts/page/home/section-evaluate/script.js'),
            'deps' => ['swiper'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/home/section-evaluate/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-section-projects-style',
            'src' => get_theme_file_uri('template/template-parts/page/home/section-projects/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/home/section-projects/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-section-projects-script',
            'src' => get_theme_file_uri('template/template-parts/page/home/section-projects/script.js'),
            'deps' => ['swiper'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/home/section-projects/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-section-post-style',
            'src' => get_theme_file_uri('template/template-parts/page/home/section-post/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/home/section-post/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-section-post-script',
            'src' => get_theme_file_uri('template/template-parts/page/home/section-post/script.js'),
            'deps' => ['swiper'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/home/section-post/script.js'))
        ],
    ];
    foreach ($wp_enqueue_mapping as $asset) {
        if (isset($asset['condition']) && ! $asset['condition']) {
            continue;
        }

        $deps = isset($asset['deps']) ? $asset['deps'] : [];
        $ver  = isset($asset['ver']) ? $asset['ver'] : $version;
        $in_footer = isset($asset['in_footer']) ? $asset['in_footer'] : false;

        if ($asset['type'] === 'style') {
            wp_enqueue_style($asset['handle'], $asset['src'], $deps, $ver);
        } elseif ($asset['type'] === 'script') {
            wp_enqueue_script($asset['handle'], $asset['src'], $deps, $ver, $in_footer);
        }
    }
}
add_action('wp_enqueue_scripts', 'wp_enqueue_custom_assets', 1001);

// ============================== Customizer scripts =====================//

/**
 * Enqueued in the CONTROL PANE (sidebar).
 * Sends postMessages to the preview when a section is expanded/collapsed.
 */
function buildpro_customize_controls_scripts()
{
    $version = WP_DEBUG ? time() : wp_get_theme()->get('Version');
    wp_enqueue_script(
        'buildpro-customizer-section-focus',
        get_theme_file_uri('/assets/js/customizer-section-focus.js'),
        array('customize-controls'),
        $version,
        true
    );
}
add_action('customize_controls_enqueue_scripts', 'buildpro_customize_controls_scripts');

/**
 * Enqueued inside the PREVIEW IFRAME.
 * Receives postMessages, highlights the active section, and scrolls to it.
 */
function buildpro_customize_preview_scripts()
{
    $version = WP_DEBUG ? time() : wp_get_theme()->get('Version');
    wp_enqueue_script(
        'buildpro-customizer-preview-outline',
        get_theme_file_uri('/assets/js/customizer-preview-outline.js'),
        array('customize-preview'),
        $version,
        true
    );
}
add_action('customize_preview_init', 'buildpro_customize_preview_scripts');
