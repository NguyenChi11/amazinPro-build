<?php
if (!function_exists('buildpro_services_customize_register')) {
    function buildpro_services_customize_register($wp_customize)
    {
        if (!class_exists('BuildPro_Services_Repeater_Control') && class_exists('WP_Customize_Control')) {
            class BuildPro_Services_Repeater_Control extends WP_Customize_Control
            {
                public $type = 'buildpro_services_repeater';
                public function render_content()
                {
                    $items = $this->value();
                    $items = is_array($items) ? $items : array();
                    echo '<span class="customize-control-title">' . esc_html($this->label) . '</span>';
                    if (!empty($this->description)) {
                        echo '<p class="description">' . esc_html($this->description) . '</p>';
                    }
                    include get_theme_file_path('template/customize/page/home/section-service/index.php');
                    return;
                }
            }
        }
        if (!function_exists('buildpro_services_find_home_id')) {
            function buildpro_services_find_home_id()
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
        if (!function_exists('buildpro_services_get_default_title')) {
            function buildpro_services_get_default_title()
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
                    $page_id = buildpro_services_find_home_id();
                }
                if ($page_id) {
                    $title = get_post_meta($page_id, 'buildpro_service_title', true);
                    return is_string($title) ? $title : '';
                }
                return '';
            }
        }
        if (!function_exists('buildpro_services_get_default_desc')) {
            function buildpro_services_get_default_desc()
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
                    $page_id = buildpro_services_find_home_id();
                }
                if ($page_id) {
                    $desc = get_post_meta($page_id, 'buildpro_service_desc', true);
                    return is_string($desc) ? $desc : '';
                }
                return '';
            }
        }
        if (!function_exists('buildpro_services_get_default_items')) {
            function buildpro_services_get_default_items()
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
                    $page_id = buildpro_services_find_home_id();
                }
                if ($page_id) {
                    $items = get_post_meta($page_id, 'buildpro_service_items', true);
                    return is_array($items) ? $items : array();
                }
                return array();
            }
        }
        if (!function_exists('buildpro_services_get_default_enabled')) {
            function buildpro_services_get_default_enabled()
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
                    $page_id = buildpro_services_find_home_id();
                }
                if ($page_id) {
                    $enabled = get_post_meta($page_id, 'buildpro_service_enabled', true);
                    $enabled = ($enabled === '' ? 1 : (int)$enabled);
                    return $enabled;
                }
                return 1;
            }
        }
        if (!function_exists('buildpro_services_sanitize_items')) {
            function buildpro_services_sanitize_items($value)
            {
                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    if (is_array($decoded)) {
                        $value = $decoded;
                    }
                }
                if (!is_array($value)) {
                    return array();
                }
                $clean = array();
                foreach ($value as $item) {
                    $clean[] = array(
                        'icon_id' => isset($item['icon_id']) ? absint($item['icon_id']) : 0,
                        'title' => isset($item['title']) ? sanitize_text_field($item['title']) : '',
                        'description' => isset($item['description']) ? sanitize_textarea_field($item['description']) : '',
                        'link_url' => isset($item['link_url']) ? esc_url_raw($item['link_url']) : '',
                        'link_title' => isset($item['link_title']) ? sanitize_text_field($item['link_title']) : '',
                        'link_target' => isset($item['link_target']) ? sanitize_text_field($item['link_target']) : '',
                    );
                }
                return $clean;
            }
        }

        $wp_customize->add_section('buildpro_services_section', array(
            'title' => __('Home Page: Services', 'buildpro'),
            'priority' => 30,
            'active_callback' => 'buildpro_customizer_is_home_preview',
        ));

        $wp_customize->add_setting('buildpro_service_enabled', array(
            'default' => buildpro_services_get_default_enabled(),
            'transport' => 'postMessage',
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control('buildpro_service_enabled', array(
            'label' => __('Enable Services', 'buildpro'),
            'section' => 'buildpro_services_section',
            'type' => 'checkbox',
        ));

        $wp_customize->add_setting('buildpro_service_title', array(
            'default' => buildpro_services_get_default_title(),
            'transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control('buildpro_service_title', array(
            'label' => __('Services Title', 'buildpro'),
            'section' => 'buildpro_services_section',
            'type' => 'text',
        ));

        $wp_customize->add_setting('buildpro_service_desc', array(
            'default' => buildpro_services_get_default_desc(),
            'transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_textarea_field',
        ));
        $wp_customize->add_control('buildpro_service_desc', array(
            'label' => __('Services Description', 'buildpro'),
            'section' => 'buildpro_services_section',
            'type' => 'textarea',
        ));

        $wp_customize->add_setting('buildpro_service_items', array(
            'default' => buildpro_services_get_default_items(),
            'transport' => 'postMessage',
            'sanitize_callback' => 'buildpro_services_sanitize_items',
        ));
        if (class_exists('BuildPro_Services_Repeater_Control')) {
            $wp_customize->add_control(new BuildPro_Services_Repeater_Control($wp_customize, 'buildpro_service_items', array(
                'label' => __('Services Items', 'buildpro'),
                'description' => __('Add/Edit Services Items to display on the home page.', 'buildpro'),
                'section' => 'buildpro_services_section',
            )));
        }

        if (isset($wp_customize->selective_refresh)) {
            $wp_customize->selective_refresh->add_partial('buildpro_service_title', array(
                'selector' => '.section-services',
                'settings' => array('buildpro_service_title'),
                'render_callback' => function () {
                    ob_start();
                    get_template_part('template/template-parts/page/home/section-services/index');
                    return ob_get_clean();
                },
            ));
            $wp_customize->selective_refresh->add_partial('buildpro_service_desc', array(
                'selector' => '.section-services',
                'settings' => array('buildpro_service_desc'),
                'container_inclusive' => true,
                'render_callback' => function () {
                    ob_start();
                    get_template_part('template/template-parts/page/home/section-services/index');
                    return ob_get_clean();
                },
            ));
            $wp_customize->selective_refresh->add_partial('buildpro_service_items', array(
                'selector' => '.section-services',
                'settings' => array('buildpro_service_items'),
                'render_callback' => function () {
                    ob_start();
                    get_template_part('template/template-parts/page/home/section-services/index');
                    return ob_get_clean();
                },
            ));
            $wp_customize->selective_refresh->add_partial('buildpro_service_enabled', array(
                'selector' => '.section-services',
                'settings' => array('buildpro_service_enabled'),
                'container_inclusive' => true,
                'render_callback' => function () {
                    ob_start();
                    get_template_part('template/template-parts/page/home/section-services/index');
                    return ob_get_clean();
                },
            ));
        }
    }
} // end if !function_exists buildpro_services_customize_register
add_action('customize_register', 'buildpro_services_customize_register');

if (!function_exists('buildpro_services_enqueue_assets')) {
    function buildpro_services_enqueue_assets()
    {
        wp_enqueue_style(
            'buildpro-services-style',
            get_theme_file_uri('template/customize/page/home/section-service/style.css'),
            array(),
            null
        );
        wp_enqueue_script(
            'buildpro-services-script',
            get_theme_file_uri('template/customize/page/home/section-service/script.js'),
            array('customize-controls'),
            null,
            true
        );

        if (function_exists('buildpro_home_add_inline_i18n')) {
            buildpro_home_add_inline_i18n('buildpro-services-script');
        }
    }
} // end if !function_exists buildpro_services_enqueue_assets
add_action('customize_controls_enqueue_scripts', 'buildpro_services_enqueue_assets');

if (!function_exists('buildpro_services_sync_customizer_to_meta')) {
    function buildpro_services_sync_customizer_to_meta()
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
            $page_id = buildpro_services_find_home_id();
        }
        if ($page_id) {
            $title = get_theme_mod('buildpro_service_title', '');
            $desc = get_theme_mod('buildpro_service_desc', '');
            $items = get_theme_mod('buildpro_service_items', array());
            $items = is_array($items) ? $items : array();
            $clean = array();
            foreach ($items as $item) {
                $clean[] = array(
                    'icon_id' => isset($item['icon_id']) ? absint($item['icon_id']) : 0,
                    'title' => isset($item['title']) ? sanitize_text_field($item['title']) : '',
                    'description' => isset($item['description']) ? sanitize_textarea_field($item['description']) : '',
                    'link_url' => isset($item['link_url']) ? esc_url_raw($item['link_url']) : '',
                    'link_title' => isset($item['link_title']) ? sanitize_text_field($item['link_title']) : '',
                    'link_target' => isset($item['link_target']) ? sanitize_text_field($item['link_target']) : '',
                );
            }
            $enabled = absint(get_theme_mod('buildpro_service_enabled', 1));
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
                update_post_meta($tid, 'buildpro_service_title', is_string($title) ? $title : '');
                update_post_meta($tid, 'buildpro_service_desc', is_string($desc) ? $desc : '');
                update_post_meta($tid, 'buildpro_service_items', $clean);
                update_post_meta($tid, 'buildpro_service_enabled', $enabled);
            }
        }
    }
}
add_action('customize_save_after', 'buildpro_services_sync_customizer_to_meta');
