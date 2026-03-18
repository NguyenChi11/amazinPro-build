<?php
function buildpro_product_customize_register($wp_customize)
{
    if (!function_exists('buildpro_product_find_home_id')) {
        function buildpro_product_find_home_id()
        {
            if (function_exists('buildpro_banner_find_home_id')) {
                return buildpro_banner_find_home_id();
            }
            $selected = 0;
            if (function_exists('wp_get_current_user')) {
                global $wp_customize;
                if ($wp_customize && $wp_customize instanceof WP_Customize_Manager) {
                    $setting = $wp_customize->get_setting('buildpro_preview_page_id');
                    if ($setting) {
                        $val = $setting->value();
                        $selected = absint($val);
                    }
                }
            }
            if ($selected > 0) {
                $tpl = get_page_template_slug($selected);
                if ($tpl === 'home-page.php') {
                    return $selected;
                }
            }
            $home_id = (int) get_option('page_on_front');
            if ($home_id) {
                $tpl = get_page_template_slug($home_id);
                if ($tpl === 'home-page.php') {
                    return $home_id;
                }
            }
            $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'home-page.php', 'number' => 1));
            if (!empty($pages)) {
                return (int) $pages[0]->ID;
            }
            return 0;
        }
    }
    if (!function_exists('buildpro_product_get_default_title')) {
        function buildpro_product_get_default_title()
        {
            $page_id = 0;
            if (function_exists('wp_get_current_user')) {
                global $wp_customize;
                if ($wp_customize && $wp_customize instanceof WP_Customize_Manager) {
                    $setting = $wp_customize->get_setting('buildpro_preview_page_id');
                    if ($setting) {
                        $page_id = absint($setting->value());
                    }
                }
            }
            if ($page_id <= 0) {
                $page_id = buildpro_product_find_home_id();
            }
            if ($page_id) {
                $title = get_post_meta($page_id, 'materials_title', true);
                return is_string($title) ? $title : '';
            }
            return '';
        }
    }
    if (!function_exists('buildpro_product_get_default_desc')) {
        function buildpro_product_get_default_desc()
        {
            $page_id = 0;
            if (function_exists('wp_get_current_user')) {
                global $wp_customize;
                if ($wp_customize && $wp_customize instanceof WP_Customize_Manager) {
                    $setting = $wp_customize->get_setting('buildpro_preview_page_id');
                    if ($setting) {
                        $page_id = absint($setting->value());
                    }
                }
            }
            if ($page_id <= 0) {
                $page_id = buildpro_product_find_home_id();
            }
            if ($page_id) {
                $desc = get_post_meta($page_id, 'materials_description', true);
                return is_string($desc) ? $desc : '';
            }
            return '';
        }
    }
    if (!function_exists('buildpro_product_get_default_enabled')) {
        function buildpro_product_get_default_enabled()
        {
            $page_id = 0;
            if (function_exists('wp_get_current_user')) {
                global $wp_customize;
                if ($wp_customize && $wp_customize instanceof WP_Customize_Manager) {
                    $setting = $wp_customize->get_setting('buildpro_preview_page_id');
                    if ($setting) {
                        $page_id = absint($setting->value());
                    }
                }
            }
            if ($page_id <= 0) {
                $page_id = buildpro_product_find_home_id();
            }
            if ($page_id) {
                $enabled = get_post_meta($page_id, 'materials_enabled', true);
                $enabled = ($enabled === '' ? 1 : (int)$enabled);
                return $enabled;
            }
            return 1;
        }
    }

    $wp_customize->add_section('buildpro_product_section', array(
        'title' => __('Home Page: Product', 'buildpro'),
        'priority' => 29,
        'active_callback' => 'buildpro_customizer_is_home_preview',
    ));

    $wp_customize->add_setting('materials_enabled', array(
        'default' => buildpro_product_get_default_enabled(),
        'transport' => 'postMessage',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('materials_enabled', array(
        'label' => __('Enable Product', 'buildpro'),
        'section' => 'buildpro_product_section',
        'type' => 'checkbox',
    ));

    $wp_customize->add_setting('materials_title', array(
        'default' => buildpro_product_get_default_title(),
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('materials_title', array(
        'label' => __('Materials Title', 'buildpro'),
        'section' => 'buildpro_product_section',
        'type' => 'text',
    ));

    $wp_customize->add_setting('materials_description', array(
        'default' => buildpro_product_get_default_desc(),
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('materials_description', array(
        'label' => __('Materials Description', 'buildpro'),
        'section' => 'buildpro_product_section',
        'type' => 'textarea',
    ));

    if (isset($wp_customize->selective_refresh)) {
        $wp_customize->selective_refresh->add_partial('materials_title', array(
            'selector' => '.section-product',
            'settings' => array('materials_title'),
            'render_callback' => function () {
                ob_start();
                get_template_part('template/template-parts/page/home/section-products/index');
                return ob_get_clean();
            },
        ));
        $wp_customize->selective_refresh->add_partial('materials_description', array(
            'selector' => '.section-product',
            'settings' => array('materials_description'),
            'container_inclusive' => true,
            'render_callback' => function () {
                ob_start();
                get_template_part('template/template-parts/page/home/section-products/index');
                return ob_get_clean();
            },
        ));
        $wp_customize->selective_refresh->add_partial('materials_enabled', array(
            'selector' => '.section-product',
            'settings' => array('materials_enabled'),
            'container_inclusive' => true,
            'render_callback' => function () {
                ob_start();
                get_template_part('template/template-parts/page/home/section-products/index');
                return ob_get_clean();
            },
        ));
    }
}
add_action('customize_register', 'buildpro_product_customize_register');

function buildpro_product_enqueue_assets()
{
    wp_enqueue_style(
        'buildpro-product-style',
        get_theme_file_uri('template/customize/page/home/section-products/style.css'),
        array(),
        null
    );
    wp_enqueue_script(
        'buildpro-product-script',
        get_theme_file_uri('template/customize/page/home/section-products/script.js'),
        array('customize-controls'),
        null,
        true
    );

    if (function_exists('buildpro_home_add_inline_i18n')) {
        buildpro_home_add_inline_i18n('buildpro-product-script');
    }
}
add_action('customize_controls_enqueue_scripts', 'buildpro_product_enqueue_assets');

if (!function_exists('buildpro_product_sync_customizer_to_meta')) {
    function buildpro_product_sync_customizer_to_meta()
    {
        $page_id = 0;
        if (function_exists('wp_get_current_user')) {
            global $wp_customize;
            if ($wp_customize && $wp_customize instanceof WP_Customize_Manager) {
                $setting = $wp_customize->get_setting('buildpro_preview_page_id');
                if ($setting) {
                    $page_id = absint($setting->value());
                }
            }
        }
        if ($page_id <= 0) {
            $page_id = buildpro_product_find_home_id();
        }
        if ($page_id) {
            $title = get_theme_mod('materials_title', '');
            $desc = get_theme_mod('materials_description', '');
            $enabled = absint(get_theme_mod('materials_enabled', 1));
            $targets = array();
            $targets[] = $page_id;
            $front_id = (int) get_option('page_on_front');
            if ($front_id > 0) {
                $targets[] = $front_id;
            }
            $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'home-page.php', 'number' => 1));
            if (!empty($pages)) {
                $targets[] = (int) $pages[0]->ID;
            }
            $targets = array_unique(array_filter(array_map('absint', $targets)));
            foreach ($targets as $tid) {
                update_post_meta($tid, 'materials_title', is_string($title) ? $title : '');
                update_post_meta($tid, 'materials_description', is_string($desc) ? $desc : '');
                update_post_meta($tid, 'materials_enabled', $enabled);
            }
        }
    }
}
add_action('customize_save_after', 'buildpro_product_sync_customizer_to_meta');
