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

    $is_aos_context = (
        is_front_page()
        || is_page_template('home-page.php')
        || is_page_template('about-us-page.php')
        || is_page_template('about-page.php')
        || is_page_template('blogs-page.php')
        || is_page_template('projects-page.php')
        || is_page_template('products-page.php')
        || is_page_template('cart-page.php')
        || is_page_template('checkout-page.php')
        || is_page_template('bill-page.php')
        || is_singular('post')
        || is_singular('product')
        || is_singular('project')
        || is_404()
    );

    $wp_enqueue_mapping = [
        [
            'type' => 'style',
            'handle' => 'buildpro-aos',
            'src' => 'https://unpkg.com/aos@2.3.4/dist/aos.css',
            'deps' => [],
            'ver' => '2.3.4',
            'in_footer' => false,
            'condition' => $is_aos_context,
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-aos',
            'src' => 'https://unpkg.com/aos@2.3.4/dist/aos.js',
            'deps' => [],
            'ver' => '2.3.4',
            'in_footer' => true,
            'condition' => $is_aos_context,
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-aos-init',
            'src' => get_theme_file_uri('assets/js/aos-init.js'),
            'deps' => ['buildpro-aos'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => $is_aos_context && file_exists(get_theme_file_path('assets/js/aos-init.js')),
        ],
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
            'handle' => 'buildpro-comment-product-style',
            'src' => get_theme_file_uri('template/template-parts/comment-product/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => is_singular() && file_exists(get_theme_file_path('template/template-parts/comment-product/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-comment-product-script',
            'src' => get_theme_file_uri('template/template-parts/comment-product/script.js'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => true,
            'condition' => is_singular() && file_exists(get_theme_file_path('template/template-parts/comment-product/script.js'))
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
            'deps' => ['swiper'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/home/section-banner/script.js'))
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
            'type' => 'style',
            'handle' => 'buildpro-section-contact-style',
            'src' => get_theme_file_uri('template/template-parts/page/home/section-contact/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/home/section-contact/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-section-post-script',
            'src' => get_theme_file_uri('template/template-parts/page/home/section-post/script.js'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/home/section-post/script.js'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-section-contact-script',
            'src' => get_theme_file_uri('template/template-parts/page/home/section-contact/script.js'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/home/section-contact/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-section-projects-list-style',
            'src' => get_theme_file_uri('template/template-parts/page/projects/section-list/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/projects/section-list/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-section-projects-list-script',
            'src' => get_theme_file_uri('template/template-parts/page/projects/section-list/script.js'),
            'deps' => ['swiper'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/projects/section-list/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-section-projects-title-style',
            'src' => get_theme_file_uri('template/template-parts/page/projects/section-title/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/projects/section-title/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-section-projects-title-script',
            'src' => get_theme_file_uri('template/template-parts/page/projects/section-title/script.js'),
            'deps' => ['swiper'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/projects/section-title/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-section-about-us-banner-style',
            'src' => get_theme_file_uri('template/template-parts/page/about-us/section-banner/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/about-us/section-banner/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-section-about-us-banner-script',
            'src' => get_theme_file_uri('template/template-parts/page/about-us/section-banner/script.js'),
            'deps' => ['swiper'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/about-us/section-banner/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-section-about-us-core-values-style',
            'src' => get_theme_file_uri('template/template-parts/page/about-us/section-core-values/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/about-us/section-core-values/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-section-about-us-core-values-script',
            'src' => get_theme_file_uri('template/template-parts/page/about-us/section-core-values/script.js'),
            'deps' => ['swiper'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/about-us/section-core-values/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-section-about-us-leader-style',
            'src' => get_theme_file_uri('template/template-parts/page/about-us/section-leader/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/about-us/section-leader/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-section-about-us-leader-script',
            'src' => get_theme_file_uri('template/template-parts/page/about-us/section-leader/script.js'),
            'deps' => ['swiper'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/about-us/section-leader/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-section-about-us-policy-style',
            'src' => get_theme_file_uri('template/template-parts/page/about-us/section-policy/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/about-us/section-policy/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-section-about-us-policy-script',
            'src' => get_theme_file_uri('template/template-parts/page/about-us/section-policy/script.js'),
            'deps' => ['swiper'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/about-us/section-policy/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-section-about-us-contact-style',
            'src' => get_theme_file_uri('template/template-parts/page/about-us/section-contact/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/about-us/section-contact/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-section-about-us-contact-script',
            'src' => get_theme_file_uri('template/template-parts/page/about-us/section-contact/script.js'),
            'deps' => ['swiper'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/about-us/section-contact/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-section-about-us-contact-form-style',
            'src' => get_theme_file_uri('template/template-parts/page/about-us/section-contact-form/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/about-us/section-contact-form/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-section-about-us-contact-form-script',
            'src' => get_theme_file_uri('template/template-parts/page/about-us/section-contact-form/script.js'),
            'deps' => ['swiper'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/about-us/section-contact-form/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-section-blog-style',
            'src' => get_theme_file_uri('template/template-parts/page/blog/section-blog/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/blog/section-blog/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-section-blog-script',
            'src' => get_theme_file_uri('template/template-parts/page/blog/section-blog/script.js'),
            'deps' => ['swiper'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/blog/section-blog/script.js'))
        ],

        [
            'type' => 'style',
            'handle' => 'buildpro-section-product-title-style',
            'src' => get_theme_file_uri('template/template-parts/page/product/section-title/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/product/section-title/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-section-product-title-script',
            'src' => get_theme_file_uri('template/template-parts/page/product/section-title/script.js'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/product/section-title/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-section-product-style',
            'src' => get_theme_file_uri('template/template-parts/page/product/section-products/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/product/section-products/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-section-product-script',
            'src' => get_theme_file_uri('template/template-parts/page/product/section-products/script.js'),
            'deps' => ['swiper'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/product/section-products/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-comment-product-contact-form-style',
            'src' => get_theme_file_uri('template/template-parts/comment-product/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/comment-product/style.css'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-single-post-style',
            'src' => get_theme_file_uri('template/template-parts/single/single-post/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/single/single-post/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-single-post-script',
            'src' => get_theme_file_uri('template/template-parts/single/single-post/script.js'),
            'deps' => ['swiper'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/single/single-post/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-single-product-style',
            'src' => get_theme_file_uri('template/template-parts/single/single-product/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/single/single-product/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-single-product-script',
            'src' => get_theme_file_uri('template/template-parts/single/single-product/script.js'),
            'deps' => ['swiper'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/single/single-product/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-single-project-style',
            'src' => get_theme_file_uri('template/template-parts/single/single-project/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/single/single-project/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-single-project-script',
            'src' => get_theme_file_uri('template/template-parts/single/single-project/script.js'),
            'deps' => ['swiper'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/single/single-project/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-section-cart-style',
            'src' => get_theme_file_uri('template/template-parts/page/cart/section-cart/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/cart/section-cart/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-section-cart-script',
            'src' => get_theme_file_uri('template/template-parts/page/cart/section-cart/script.js'),
            'deps' => ['swiper'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/cart/section-cart/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-section-checkout-style',
            'src' => get_theme_file_uri('template/template-parts/page/checkout/section-checkout/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/checkout/section-checkout/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-section-checkout-script',
            'src' => get_theme_file_uri('template/template-parts/page/checkout/section-checkout/script.js'),
            'deps' => ['swiper'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/checkout/section-checkout/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-section-bill-style',
            'src' => get_theme_file_uri('template/template-parts/page/bill/section-bill/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/bill/section-bill/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-section-bill-script',
            'src' => get_theme_file_uri('template/template-parts/page/bill/section-bill/script.js'),
            'deps' => ['swiper'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/bill/section-bill/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-privacy-policy-style',
            'src' => get_theme_file_uri('template/template-parts/page/privacy-policy/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/privacy-policy/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-privacy-policy-script',
            'src' => get_theme_file_uri('template/template-parts/page/privacy-policy/script.js'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/privacy-policy/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-terms-of-service-style',
            'src' => get_theme_file_uri('template/template-parts/page/terms-of-service/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/terms-of-service/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-terms-of-service-script',
            'src' => get_theme_file_uri('template/template-parts/page/terms-of-service/script.js'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/terms-of-service/script.js'))
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-404-style',
            'src' => get_theme_file_uri('template/template-parts/page/404/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/404/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-404-script',
            'src' => get_theme_file_uri('template/template-parts/page/404/script.js'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/page/404/script.js'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-cart',
            'src' => get_theme_file_uri('assets/js/cart.js'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => true,
            'condition' => class_exists('WooCommerce'),
        ],
        [
            'type' => 'style',
            'handle' => 'buildpro-cart-dropdown-style',
            'src' => get_theme_file_uri('template/template-parts/header/cart/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => class_exists('WooCommerce'),
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-cart-dropdown-script',
            'src' => get_theme_file_uri('template/template-parts/header/cart/script.js'),
            'deps' => ['buildpro-cart'],
            'ver' => $version,
            'in_footer' => true,
            'condition' => class_exists('WooCommerce'),
        ],

        // [
        //     'type' => 'style',
        //     'handle' => 'buildpro-post-style',
        //     'src' => get_theme_file_uri('template/meta-box/post-type/post/style.css'),
        //     'deps' => [],
        //     'ver' => $version,
        //     'in_footer' => false,
        //     'condition' => file_exists(get_theme_file_path('template/meta-box/post-type/post/style.css'))
        // ],
        // [
        //     'type' => 'script',
        //     'handle' => 'buildpro-post-script',
        //     'src' => get_theme_file_uri('template/meta-box/post-type/post/script.js'),
        //     'deps' => ['swiper'],
        //     'ver' => $version,
        //     'in_footer' => true,
        //     'condition' => file_exists(get_theme_file_path('template/meta-box/post-type/post/script.js'))
        // ],

        // Breadcrumb assets
        [
            'type' => 'style',
            'handle' => 'buildpro-breadcrumb-style',
            'src' => get_theme_file_uri('template/template-parts/breadcrums/style.css'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => false,
            'condition' => file_exists(get_theme_file_path('template/template-parts/breadcrums/style.css'))
        ],
        [
            'type' => 'script',
            'handle' => 'buildpro-breadcrumb-script',
            'src' => get_theme_file_uri('template/template-parts/breadcrums/script.js'),
            'deps' => [],
            'ver' => $version,
            'in_footer' => true,
            'condition' => file_exists(get_theme_file_path('template/template-parts/breadcrums/script.js'))
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
