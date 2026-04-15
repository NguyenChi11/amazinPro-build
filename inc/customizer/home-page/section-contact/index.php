<?php

if (!function_exists('buildpro_contact_find_home_id')) {
    function buildpro_contact_find_home_id()
    {
        if (function_exists('buildpro_banner_find_home_id')) {
            return (int) buildpro_banner_find_home_id();
        }

        $selected = 0;
        if (function_exists('wp_get_current_user')) {
            global $wp_customize;
            if ($wp_customize && $wp_customize instanceof WP_Customize_Manager) {
                $setting = $wp_customize->get_setting('buildpro_preview_page_id');
                if ($setting) {
                    $selected = absint($setting->value());
                }
            }
        }

        if ($selected > 0) {
            $tpl = get_page_template_slug($selected);
            if ($tpl === 'home-page.php') {
                return (int) $selected;
            }
        }

        $home_id = (int) get_option('page_on_front');
        if ($home_id > 0) {
            $tpl = get_page_template_slug($home_id);
            if ($tpl === 'home-page.php') {
                return (int) $home_id;
            }
        }

        $pages = get_pages(array(
            'meta_key' => '_wp_page_template',
            'meta_value' => 'home-page.php',
            'number' => 1,
        ));
        if (!empty($pages)) {
            return (int) $pages[0]->ID;
        }

        return 0;
    }
}

if (!function_exists('buildpro_contact_default_values')) {
    function buildpro_contact_default_values()
    {
        return array(
            'enabled' => 1,
            'title' => __('Get Expert Advice for Your Dream Home', 'buildpro'),
            'description' => __('Leave your email and our construction experts will contact you with personalized solutions.', 'buildpro'),
            'placeholder' => __('Enter your email', 'buildpro'),
            'image_url' => get_theme_file_uri('/assets/images/image_contact.jpg'),
        );
    }
}

if (!function_exists('buildpro_contact_meta_default')) {
    function buildpro_contact_meta_default($page_id, $meta_key, $default)
    {
        if ($page_id <= 0) {
            return $default;
        }

        $value = get_post_meta($page_id, $meta_key, true);
        if ($value === '' || $value === null) {
            return $default;
        }

        return $value;
    }
}

if (!class_exists('BuildPro_Home_Contact_Info_Control') && class_exists('WP_Customize_Control')) {
    class BuildPro_Home_Contact_Info_Control extends WP_Customize_Control
    {
        public $type = 'buildpro_home_contact_info';

        public function render_content()
        {
            include get_theme_file_path('template/customize/page/home/section-contact/index.php');
        }
    }
}

if (!function_exists('buildpro_contact_customize_register')) {
    function buildpro_contact_customize_register($wp_customize)
    {
        if (!($wp_customize instanceof WP_Customize_Manager)) {
            return;
        }

        $defaults = buildpro_contact_default_values();
        $home_id = buildpro_contact_find_home_id();

        $enabled_default = absint(buildpro_contact_meta_default($home_id, 'buildpro_contact_enabled', $defaults['enabled']));
        $title_default = (string) buildpro_contact_meta_default($home_id, 'buildpro_contact_title', $defaults['title']);
        $description_default = (string) buildpro_contact_meta_default($home_id, 'buildpro_contact_description', $defaults['description']);
        $placeholder_default = (string) buildpro_contact_meta_default($home_id, 'buildpro_contact_placeholder', $defaults['placeholder']);
        $image_id_default = absint(buildpro_contact_meta_default($home_id, 'buildpro_contact_image_id', 0));

        $wp_customize->add_section('buildpro_contact_section', array(
            'title' => __('Home Page: Contact', 'buildpro'),
            'priority' => 33,
            'active_callback' => 'buildpro_customizer_is_home_preview',
        ));

        $wp_customize->add_setting('buildpro_contact_info', array(
            'default' => '',
            'transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        if (class_exists('BuildPro_Home_Contact_Info_Control')) {
            $wp_customize->add_control(new BuildPro_Home_Contact_Info_Control($wp_customize, 'buildpro_contact_info', array(
                'section' => 'buildpro_contact_section',
                'priority' => 1,
            )));
        }

        $wp_customize->add_setting('buildpro_contact_enabled', array(
            'default' => $enabled_default,
            'transport' => 'postMessage',
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control('buildpro_contact_enabled', array(
            'label' => __('Enable Contact', 'buildpro'),
            'section' => 'buildpro_contact_section',
            'type' => 'checkbox',
            'priority' => 5,
        ));

        $wp_customize->add_setting('buildpro_contact_title', array(
            'default' => $title_default,
            'transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control('buildpro_contact_title', array(
            'label' => __('Title', 'buildpro'),
            'section' => 'buildpro_contact_section',
            'type' => 'text',
            'priority' => 10,
        ));

        $wp_customize->add_setting('buildpro_contact_description', array(
            'default' => $description_default,
            'transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_textarea_field',
        ));
        $wp_customize->add_control('buildpro_contact_description', array(
            'label' => __('Description', 'buildpro'),
            'section' => 'buildpro_contact_section',
            'type' => 'textarea',
            'priority' => 20,
        ));

        $wp_customize->add_setting('buildpro_contact_placeholder', array(
            'default' => $placeholder_default,
            'transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control('buildpro_contact_placeholder', array(
            'label' => __('Input Placeholder', 'buildpro'),
            'section' => 'buildpro_contact_section',
            'type' => 'text',
            'priority' => 30,
        ));

        $wp_customize->add_setting('buildpro_contact_image_id', array(
            'default' => $image_id_default,
            'transport' => 'postMessage',
            'sanitize_callback' => 'absint',
        ));
        if (class_exists('WP_Customize_Media_Control')) {
            $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'buildpro_contact_image_id', array(
                'label' => __('Section Image', 'buildpro'),
                'section' => 'buildpro_contact_section',
                'mime_type' => 'image',
                'priority' => 50,
            )));
        } else {
            $wp_customize->add_control('buildpro_contact_image_id', array(
                'label' => __('Section Image ID', 'buildpro'),
                'section' => 'buildpro_contact_section',
                'type' => 'number',
                'priority' => 50,
            ));
        }

        if (isset($wp_customize->selective_refresh)) {
            $wp_customize->selective_refresh->add_partial('buildpro_contact_partial', array(
                'selector' => '.section-contact',
                'settings' => array(
                    'buildpro_contact_enabled',
                    'buildpro_contact_title',
                    'buildpro_contact_description',
                    'buildpro_contact_placeholder',
                    'buildpro_contact_image_id',
                ),
                'container_inclusive' => true,
                'render_callback' => function () {
                    ob_start();
                    get_template_part('template/template-parts/page/home/section-contact/index');
                    return ob_get_clean();
                },
            ));
        }
    }
}
add_action('customize_register', 'buildpro_contact_customize_register');

if (!function_exists('buildpro_contact_sync_customizer_to_meta')) {
    function buildpro_contact_sync_customizer_to_meta($wp_customize_manager)
    {
        $defaults = buildpro_contact_default_values();

        $enabled_val = null;
        $title_val = null;
        $description_val = null;
        $placeholder_val = null;
        $image_id_val = null;

        if ($wp_customize_manager instanceof WP_Customize_Manager) {
            $setting = $wp_customize_manager->get_setting('buildpro_contact_enabled');
            $enabled_val = $setting ? $setting->post_value() : null;

            $setting = $wp_customize_manager->get_setting('buildpro_contact_title');
            $title_val = $setting ? $setting->post_value() : null;

            $setting = $wp_customize_manager->get_setting('buildpro_contact_description');
            $description_val = $setting ? $setting->post_value() : null;

            $setting = $wp_customize_manager->get_setting('buildpro_contact_placeholder');
            $placeholder_val = $setting ? $setting->post_value() : null;

            $setting = $wp_customize_manager->get_setting('buildpro_contact_image_id');
            $image_id_val = $setting ? $setting->post_value() : null;
        }

        if ($enabled_val === null) {
            $enabled_val = get_theme_mod('buildpro_contact_enabled', $defaults['enabled']);
        }
        if ($title_val === null) {
            $title_val = get_theme_mod('buildpro_contact_title', $defaults['title']);
        }
        if ($description_val === null) {
            $description_val = get_theme_mod('buildpro_contact_description', $defaults['description']);
        }
        if ($placeholder_val === null) {
            $placeholder_val = get_theme_mod('buildpro_contact_placeholder', $defaults['placeholder']);
        }
        if ($image_id_val === null) {
            $image_id_val = get_theme_mod('buildpro_contact_image_id', 0);
        }

        $enabled = absint($enabled_val) ? 1 : 0;
        $title = sanitize_text_field((string) $title_val);
        $description = sanitize_textarea_field((string) $description_val);
        $placeholder = sanitize_text_field((string) $placeholder_val);
        $image_id = absint($image_id_val);
        $image_url = '';
        if ($image_id > 0) {
            $image_url = (string) wp_get_attachment_image_url($image_id, 'full');
        }

        $page_id = buildpro_contact_find_home_id();
        if ($page_id > 0) {
            update_post_meta($page_id, 'buildpro_contact_enabled', $enabled);
            update_post_meta($page_id, 'buildpro_contact_title', $title);
            update_post_meta($page_id, 'buildpro_contact_description', $description);
            update_post_meta($page_id, 'buildpro_contact_placeholder', $placeholder);
            update_post_meta($page_id, 'buildpro_contact_image_id', $image_id);
            update_post_meta($page_id, 'buildpro_contact_image_url', $image_url);
        }

        set_theme_mod('buildpro_contact_enabled', $enabled);
        set_theme_mod('buildpro_contact_title', $title);
        set_theme_mod('buildpro_contact_description', $description);
        set_theme_mod('buildpro_contact_placeholder', $placeholder);
        set_theme_mod('buildpro_contact_image_id', $image_id);
        set_theme_mod('buildpro_contact_image_url', $image_url);
    }
}
add_action('customize_save_after', 'buildpro_contact_sync_customizer_to_meta');

if (!function_exists('buildpro_contact_runtime_page_id')) {
    function buildpro_contact_runtime_page_id()
    {
        $page_id = (int) get_queried_object_id();
        if ($page_id > 0) {
            $front_id = (int) get_option('page_on_front');
            $tpl = get_page_template_slug($page_id);
            if ($tpl === 'home-page.php' || $page_id === $front_id) {
                return $page_id;
            }
        }

        return buildpro_contact_find_home_id();
    }
}

if (!function_exists('buildpro_contact_runtime_value')) {
    function buildpro_contact_runtime_value($meta_key, $mod_key, $default)
    {
        $page_id = buildpro_contact_runtime_page_id();
        $value = $default;

        if ($page_id > 0) {
            $meta_value = get_post_meta($page_id, $meta_key, true);
            if ($meta_value !== '' && $meta_value !== null) {
                $value = $meta_value;
            }
        }

        if (is_customize_preview()) {
            $value = get_theme_mod($mod_key, $value);
        }

        return $value;
    }
}

if (!function_exists('buildpro_contact_filter_enabled')) {
    function buildpro_contact_filter_enabled($enabled)
    {
        $value = buildpro_contact_runtime_value('buildpro_contact_enabled', 'buildpro_contact_enabled', $enabled ? 1 : 0);
        return absint($value) === 1;
    }
}
add_filter('buildpro_home_contact_enabled', 'buildpro_contact_filter_enabled');

if (!function_exists('buildpro_contact_filter_title')) {
    function buildpro_contact_filter_title($title)
    {
        $value = buildpro_contact_runtime_value('buildpro_contact_title', 'buildpro_contact_title', $title);
        return sanitize_text_field((string) $value);
    }
}
add_filter('buildpro_home_contact_title', 'buildpro_contact_filter_title');

if (!function_exists('buildpro_contact_filter_description')) {
    function buildpro_contact_filter_description($description)
    {
        $value = buildpro_contact_runtime_value('buildpro_contact_description', 'buildpro_contact_description', $description);
        return sanitize_textarea_field((string) $value);
    }
}
add_filter('buildpro_home_contact_description', 'buildpro_contact_filter_description');

if (!function_exists('buildpro_contact_filter_placeholder')) {
    function buildpro_contact_filter_placeholder($placeholder)
    {
        $value = buildpro_contact_runtime_value('buildpro_contact_placeholder', 'buildpro_contact_placeholder', $placeholder);
        return sanitize_text_field((string) $value);
    }
}
add_filter('buildpro_home_contact_placeholder', 'buildpro_contact_filter_placeholder');

if (!function_exists('buildpro_contact_filter_image_url')) {
    function buildpro_contact_filter_image_url($default_url)
    {
        $page_id = buildpro_contact_runtime_page_id();

        $image_id = 0;
        $image_url = '';

        if ($page_id > 0) {
            $image_id = absint(get_post_meta($page_id, 'buildpro_contact_image_id', true));
            $image_url = (string) get_post_meta($page_id, 'buildpro_contact_image_url', true);
        }

        if (is_customize_preview()) {
            $image_id = absint(get_theme_mod('buildpro_contact_image_id', $image_id));
            $image_url = (string) get_theme_mod('buildpro_contact_image_url', $image_url);
        }

        if ($image_id > 0) {
            $candidate = wp_get_attachment_image_url($image_id, 'full');
            if (is_string($candidate) && $candidate !== '') {
                return $candidate;
            }
        }

        if (is_string($image_url) && $image_url !== '') {
            return $image_url;
        }

        return (string) $default_url;
    }
}
add_filter('buildpro_home_contact_image_url', 'buildpro_contact_filter_image_url');

if (!function_exists('buildpro_contact_customize_enqueue_assets')) {
    function buildpro_contact_customize_enqueue_assets()
    {
        wp_enqueue_style(
            'buildpro-contact-customize-style',
            get_theme_file_uri('template/customize/page/home/section-contact/style.css'),
            array(),
            null
        );

        wp_enqueue_script(
            'buildpro-contact-customize-script',
            get_theme_file_uri('template/customize/page/home/section-contact/script.js'),
            array('customize-controls'),
            null,
            true
        );

        if (function_exists('buildpro_home_add_inline_i18n')) {
            buildpro_home_add_inline_i18n('buildpro-contact-customize-script');
        }
    }
}
add_action('customize_controls_enqueue_scripts', 'buildpro_contact_customize_enqueue_assets');
