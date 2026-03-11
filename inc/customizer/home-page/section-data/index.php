<?php
function buildpro_data_customize_register($wp_customize)
{
    if (!class_exists('BuildPro_Data_Repeater_Control') && class_exists('WP_Customize_Control')) {
        class BuildPro_Data_Repeater_Control extends WP_Customize_Control
        {
            public $type = 'buildpro_data_repeater';
            public function render_content()
            {
                $items = $this->value();
                $items = is_array($items) ? $items : array();
                echo '<span class="customize-control-title">' . esc_html($this->label) . '</span>';
                if (!empty($this->description)) {
                    echo '<p class="description">' . esc_html($this->description) . '</p>';
                }
                include get_theme_file_path('template/customize/page/home/section-data/index.php');
                return;
            }
        }
    }
    if (!function_exists('buildpro_data_find_home_id')) {
        function buildpro_data_find_home_id()
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
                return $selected;
            }
            $home_id = (int) get_option('page_on_front');
            if ($home_id) {
                return $home_id;
            }
            $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'home-page.php', 'number' => 1));
            if (!empty($pages)) {
                return (int) $pages[0]->ID;
            }
            return 0;
        }
    }
    if (!function_exists('buildpro_data_get_default_items')) {
        function buildpro_data_get_default_items()
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
                $page_id = buildpro_data_find_home_id();
            }
            if ($page_id) {
                $items = get_post_meta($page_id, 'buildpro_data_items', true);
                return is_array($items) ? $items : array();
            }
            return array();
        }
    }
    if (!function_exists('buildpro_data_get_default_enabled')) {
        function buildpro_data_get_default_enabled()
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
                $page_id = buildpro_data_find_home_id();
            }
            if ($page_id) {
                $enabled = get_post_meta($page_id, 'buildpro_data_enabled', true);
                $enabled = ($enabled === '' ? 1 : (int) $enabled);
                return $enabled;
            }
            return 1;
        }
    }
    if (!function_exists('buildpro_data_sanitize_items')) {
        function buildpro_data_sanitize_items($value)
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
                    'number' => isset($item['number']) ? sanitize_text_field($item['number']) : '',
                    'text'   => isset($item['text']) ? sanitize_text_field($item['text']) : '',
                );
            }
            return $clean;
        }
    }
    $wp_customize->add_section('buildpro_data_section', array(
        'title' => __('Home Page: Data', 'buildpro'),
        'priority' => 28,
        'active_callback' => 'buildpro_customizer_is_home_preview',
    ));
    $wp_customize->add_setting('buildpro_data_enabled', array(
        'default' => buildpro_data_get_default_enabled(),
        'transport' => 'postMessage',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('buildpro_data_enabled', array(
        'label' => __('Enable Section Data', 'buildpro'),
        'section' => 'buildpro_data_section',
        'type' => 'checkbox',
    ));
    $wp_customize->add_setting('buildpro_data_items', array(
        'default' => buildpro_data_get_default_items(),
        'transport' => 'postMessage',
        'sanitize_callback' => 'buildpro_data_sanitize_items',
    ));
    if (class_exists('BuildPro_Data_Repeater_Control')) {
        $wp_customize->add_control(new BuildPro_Data_Repeater_Control($wp_customize, 'buildpro_data_items', array(
            'label' => __('Data Items', 'buildpro'),
            'description' => __('Add/Edit Data items to display on the home page.', 'buildpro'),
            'section' => 'buildpro_data_section',
        )));
    }
    if (isset($wp_customize->selective_refresh)) {
        $wp_customize->selective_refresh->add_partial('buildpro_data_items', array(
            'selector' => '.section-data',
            'settings' => array('buildpro_data_items'),
            'container_inclusive' => true,
            'render_callback' => function () {
                ob_start();
                get_template_part('template/template-parts/page/home/section-data/index');
                return ob_get_clean();
            },
        ));
        $wp_customize->selective_refresh->add_partial('buildpro_data_enabled', array(
            'selector' => '.section-data',
            'settings' => array('buildpro_data_enabled'),
            'container_inclusive' => true,
            'render_callback' => function () {
                ob_start();
                get_template_part('template/template-parts/page/home/section-data/index');
                return ob_get_clean();
            },
        ));
    }
}
add_action('customize_register', 'buildpro_data_customize_register');
function buildpro_data_enqueue_assets()
{
    wp_enqueue_style(
        'buildpro-data-style',
        get_theme_file_uri('template/customize/page/home/section-data/style.css'),
        array(),
        null
    );
    wp_enqueue_script(
        'buildpro-data-script',
        get_theme_file_uri('template/customize/page/home/section-data/script.js'),
        array('customize-controls'),
        null,
        true
    );
}
add_action('customize_controls_enqueue_scripts', 'buildpro_data_enqueue_assets');
if (!function_exists('buildpro_data_sync_customizer_to_meta')) {
    function buildpro_data_sync_customizer_to_meta($wp_customize_manager)
    {
        $page_id = 0;
        if ($wp_customize_manager instanceof WP_Customize_Manager) {
            $setting = $wp_customize_manager->get_setting('buildpro_preview_page_id');
            if ($setting) {
                $page_id = absint($setting->value());
            }
        }
        if ($page_id <= 0) {
            $page_id = buildpro_data_find_home_id();
        }
        if ($page_id) {
            $items = array();
            $enabled = 1;
            if ($wp_customize_manager instanceof WP_Customize_Manager) {
                $items_setting = $wp_customize_manager->get_setting('buildpro_data_items');
                if ($items_setting) {
                    $items = $items_setting->value();
                } else {
                    $items = get_theme_mod('buildpro_data_items', array());
                }
                $enabled_setting = $wp_customize_manager->get_setting('buildpro_data_enabled');
                if ($enabled_setting) {
                    $enabled = absint($enabled_setting->value());
                } else {
                    $enabled = absint(get_theme_mod('buildpro_data_enabled', 1));
                }
            } else {
                $items = get_theme_mod('buildpro_data_items', array());
                $enabled = absint(get_theme_mod('buildpro_data_enabled', 1));
            }
            $items = is_array($items) ? $items : array();
            $clean = array();
            foreach ($items as $item) {
                $clean[] = array(
                    'number' => isset($item['number']) ? sanitize_text_field($item['number']) : '',
                    'text'   => isset($item['text']) ? sanitize_text_field($item['text']) : '',
                );
            }
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
                update_post_meta($tid, 'buildpro_data_items', $clean);
                update_post_meta($tid, 'buildpro_data_enabled', $enabled);
            }
        }
    }
    add_action('customize_save_after', 'buildpro_data_sync_customizer_to_meta', 10, 1);
    add_action('customize_save', 'buildpro_data_sync_customizer_to_meta', 10, 1);
}
