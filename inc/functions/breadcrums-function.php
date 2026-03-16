<?php

/**
 * Custom Breadcrumb Function
 */

if (!function_exists('custom_breadcrumb')) {
    function custom_breadcrumb()
    {
        global $post;

        $breadcrumb = array();

        // Get current page info
        $current_url = get_permalink();
        $current_title = get_the_title();

        // Detect page type and build breadcrumb
        if (is_page()) {
            $page_slug = get_post_field('post_name', $post);

            // Cart -> Checkout -> Bill flow
            if ($page_slug === 'cart') {
                $breadcrumb[] = array(
                    'title' => 'Cart',
                    'url' => $current_url,
                    'current' => true
                );
            } elseif ($page_slug === 'checkout') {
                // Cart -> Checkout
                $cart_page = get_page_by_path('cart');
                if ($cart_page) {
                    $breadcrumb[] = array(
                        'title' => 'Cart',
                        'url' => get_permalink($cart_page),
                        'current' => false
                    );
                }
                $breadcrumb[] = array(
                    'title' => 'Checkout',
                    'url' => $current_url,
                    'current' => true
                );
            } elseif ($page_slug === 'bill') {
                // Cart -> Checkout -> Bill
                $cart_page = get_page_by_path('cart');
                $checkout_page = get_page_by_path('checkout');

                if ($cart_page) {
                    $breadcrumb[] = array(
                        'title' => 'Cart',
                        'url' => get_permalink($cart_page),
                        'current' => false
                    );
                }
                if ($checkout_page) {
                    $breadcrumb[] = array(
                        'title' => 'Checkout',
                        'url' => get_permalink($checkout_page),
                        'current' => false
                    );
                }
                $breadcrumb[] = array(
                    'title' => 'Bill',
                    'url' => $current_url,
                    'current' => true
                );
            } else {
                // Default page breadcrumb
                $breadcrumb[] = array(
                    'title' => $current_title,
                    'url' => $current_url,
                    'current' => true
                );
            }
        }
        // Single Project
        elseif (is_singular('project')) {
            $projects_page = get_page_by_path('projects');
            if ($projects_page) {
                $breadcrumb[] = array(
                    'title' => 'Projects',
                    'url' => get_permalink($projects_page),
                    'current' => false
                );
            } else {
                // Fallback if no projects page exists
                $breadcrumb[] = array(
                    'title' => 'Projects',
                    'url' => home_url('/projects/'),
                    'current' => false
                );
            }

            $breadcrumb[] = array(
                'title' => $current_title,
                'url' => $current_url,
                'current' => true
            );
        }
        // Single Product
        elseif (is_singular('product') || is_product()) {
            $products_page = get_page_by_path('products');
            if ($products_page) {
                $breadcrumb[] = array(
                    'title' => 'Products',
                    'url' => get_permalink($products_page),
                    'current' => false
                );
            } else {
                // Fallback if no products page exists
                $breadcrumb[] = array(
                    'title' => 'Products',
                    'url' => home_url('/products/'),
                    'current' => false
                );
            }

            $breadcrumb[] = array(
                'title' => $current_title,
                'url' => $current_url,
                'current' => true
            );
        }
        // Single Post
        elseif (is_singular('post')) {
            $blogs_page = get_page_by_path('blog');
            if (!$blogs_page) {
                // Try different common blog page slugs
                $blogs_page = get_page_by_path('blogs');
            }
            if (!$blogs_page) {
                $blogs_page = get_page_by_path('news');
            }

            if ($blogs_page) {
                $breadcrumb[] = array(
                    'title' => 'Blogs',
                    'url' => get_permalink($blogs_page),
                    'current' => false
                );
            } else {
                // Fallback - check if there's a blogs page template
                $blogs_page_template = get_pages([
                    'meta_key'   => '_wp_page_template',
                    'meta_value' => 'blogs-page.php'
                ]);

                if (!empty($blogs_page_template)) {
                    $breadcrumb[] = array(
                        'title' => 'Blogs',
                        'url' => get_permalink($blogs_page_template[0]->ID),
                        'current' => false
                    );
                } else {
                    // Final fallback
                    $breadcrumb[] = array(
                        'title' => 'Blogs',
                        'url' => home_url('/blog/'),
                        'current' => false
                    );
                }
            }

            $breadcrumb[] = array(
                'title' => $current_title,
                'url' => $current_url,
                'current' => true
            );
        }
        // Archive pages
        elseif (is_post_type_archive('project')) {
            $breadcrumb[] = array(
                'title' => 'Projects',
                'url' => $current_url,
                'current' => true
            );
        } elseif (is_post_type_archive('product') || is_shop()) {
            $breadcrumb[] = array(
                'title' => 'Products',
                'url' => $current_url,
                'current' => true
            );
        } elseif (is_home() || is_category() || is_tag() || is_author() || is_date()) {
            // Blog archive pages (including homepage if set as posts page)
            $breadcrumb[] = array(
                'title' => 'Blogs',
                'url' => $current_url,
                'current' => true
            );
        }
        // Default cases
        else {
            if (!is_home() && !is_front_page()) {
                $breadcrumb[] = array(
                    'title' => $current_title ?: get_the_archive_title(),
                    'url' => $current_url,
                    'current' => true
                );
            }
        }

        return apply_filters('custom_breadcrumb_items', $breadcrumb);
    }
}

/**
 * Display breadcrumb
 */
if (!function_exists('display_breadcrumb')) {
    function display_breadcrumb($separator = '/', $wrap_class = 'breadcrumb-wrapper')
    {
        $breadcrumb_items = custom_breadcrumb();

        if (empty($breadcrumb_items)) {
            return;
        }

        echo '<div class="' . esc_attr($wrap_class) . '">';
        echo '<nav class="breadcrumb-nav" aria-label="Breadcrumb">';
        echo '<ol class="breadcrumb-list">';

        foreach ($breadcrumb_items as $index => $item) {
            $is_last = ($index === count($breadcrumb_items) - 1);

            echo '<li class="breadcrumb-item' . ($item['current'] ? ' current' : '') . '">';

            if (!$item['current'] && !empty($item['url'])) {
                echo '<a href="' . esc_url($item['url']) . '" class="breadcrumb-link">';
                echo esc_html($item['title']);
                echo '</a>';
            } else {
                echo '<span class="breadcrumb-text">' . esc_html($item['title']) . '</span>';
            }

            if (!$is_last) {
                echo '<span class="breadcrumb-separator">' . esc_html($separator) . '</span>';
            }

            echo '</li>';
        }

        echo '</ol>';
        echo '</nav>';
        echo '</div>';
    }
}

/**
 * Get breadcrumb JSON-LD structured data
 */
if (!function_exists('get_breadcrumb_json_ld')) {
    function get_breadcrumb_json_ld()
    {
        $breadcrumb_items = custom_breadcrumb();

        if (empty($breadcrumb_items)) {
            return '';
        }

        $json_ld = array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array()
        );

        foreach ($breadcrumb_items as $index => $item) {
            $json_ld['itemListElement'][] = array(
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['title'],
                'item' => $item['url']
            );
        }

        return json_encode($json_ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
