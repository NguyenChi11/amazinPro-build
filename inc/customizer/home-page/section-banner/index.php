<?php

/**
 * BuildPro – Banner Home Customizer
 *
 * Registers the Banner Home section, settings, controls,
 * selective refresh, and related hooks.
 */

// ─── Control Class ────────────────────────────────────────────────────────────

if (!class_exists('BuildPro_Banner_Repeater_Control') && class_exists('WP_Customize_Control')) {
    class BuildPro_Banner_Repeater_Control extends WP_Customize_Control
    {
        public $type = 'buildpro_banner_repeater';

        public function render_content()
        {
            $items = $this->value();
            $items = is_array($items) ? $items : array();

            echo '<span class="customize-control-title">' . esc_html($this->label) . '</span>';
            if (!empty($this->description)) {
                echo '<p class="description">' . esc_html($this->description) . '</p>';
            }

            include get_theme_file_path('template/customize/page/home/section-banner/index.php');
        }
    }
}

// ─── Helper: Is Home Preview? ─────────────────────────────────────────────────

if (!function_exists('buildpro_customizer_is_home_preview')) {
    function buildpro_customizer_is_home_preview()
    {
        $selected_id = 0;

        if (function_exists('wp_get_current_user')) {
            global $wp_customize;
            if ($wp_customize && $wp_customize instanceof WP_Customize_Manager) {
                $setting = $wp_customize->get_setting('buildpro_preview_page_id');
                if ($setting) {
                    $selected_id = absint($setting->value());
                }
            }
        }

        if ($selected_id <= 0) {
            $selected_id = (int) get_option('page_on_front');
        }

        if ($selected_id > 0) {
            $tpl = get_page_template_slug($selected_id);
            if ($tpl === 'home-page.php') {
                return true;
            }
            $front = (int) get_option('page_on_front');
            if ($front && $selected_id === $front) {
                return true;
            }
        }

        return false;
    }
}

// ─── Helper: Find Home Page ID ────────────────────────────────────────────────

function buildpro_banner_find_home_id()
{
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

    $pages = get_pages(array(
        'meta_key'   => '_wp_page_template',
        'meta_value' => 'home-page.php',
        'number'     => 1,
    ));
    if (!empty($pages)) {
        return (int) $pages[0]->ID;
    }

    return 0;
}

// ─── Helper: Default Items ────────────────────────────────────────────────────

function buildpro_banner_get_default_items()
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
        $page_id = buildpro_banner_find_home_id();
    }

    if ($page_id) {
        $items = get_post_meta($page_id, 'buildpro_banner_items', true);
        return is_array($items) ? $items : array();
    }

    return array();
}

// ─── Sanitize ─────────────────────────────────────────────────────────────────

function buildpro_banner_sanitize_items($value)
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
            'image_id'    => isset($item['image_id'])    ? absint($item['image_id'])                        : 0,
            'type'        => isset($item['type'])        ? sanitize_text_field($item['type'])               : '',
            'text'        => isset($item['text'])        ? sanitize_text_field($item['text'])               : '',
            'description' => isset($item['description']) ? sanitize_textarea_field($item['description'])    : '',
            'link_url'    => isset($item['link_url'])    ? esc_url_raw($item['link_url'])                   : '',
            'link_title'  => isset($item['link_title'])  ? sanitize_text_field($item['link_title'])         : '',
            'link_target' => isset($item['link_target']) ? sanitize_text_field($item['link_target'])        : '',
        );
    }

    return $clean;
}

// ─── Customize Register ───────────────────────────────────────────────────────

function buildpro_banner_customize_register($wp_customize)
{
    // Resolve edit URL for the home-page template
    $home_id  = (int) get_option('page_on_front');
    $edit_url = '';

    if ($home_id && get_page_template_slug($home_id) === 'home-page.php') {
        $edit_url = admin_url('post.php?post=' . $home_id . '&action=edit');
    }

    if (!$edit_url) {
        $pages = get_pages(array(
            'meta_key'   => '_wp_page_template',
            'meta_value' => 'home-page.php',
            'number'     => 1,
        ));
        if (!empty($pages)) {
            $edit_url = admin_url('post.php?post=' . $pages[0]->ID . '&action=edit');
        }
    }

    // Section
    $wp_customize->add_section('buildpro_banner_section', array(
        'title'           => __('Home Page: Banner', 'buildpro'),
        'priority'        => 25,
        'active_callback' => 'buildpro_customizer_is_home_preview',
    ));

    // Enable Banner
    $wp_customize->add_setting('buildpro_banner_enabled', array(
        'default'           => 1,
        'transport'         => 'refresh',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('buildpro_banner_enabled', array(
        'label'   => __('Enable Banner', 'buildpro'),
        'section' => 'buildpro_banner_section',
        'type'    => 'checkbox',
    ));

    // Banner Items
    $wp_customize->add_setting('buildpro_banner_items', array(
        'default'           => buildpro_banner_get_default_items(),
        'transport'         => 'postMessage',
        'sanitize_callback' => 'buildpro_banner_sanitize_items',
    ));
    if (class_exists('BuildPro_Banner_Repeater_Control')) {
        $wp_customize->add_control(new BuildPro_Banner_Repeater_Control($wp_customize, 'buildpro_banner_items', array(
            'label'       => __('Banner Items', 'buildpro'),
            'description' => __('Add/Edit Banner items to display on the Front Page.', 'buildpro'),
            'section'     => 'buildpro_banner_section',
        )));
    }

    // Selective Refresh
    if (isset($wp_customize->selective_refresh)) {
        $wp_customize->selective_refresh->add_partial('buildpro_banner_items', array(
            'selector'        => '.section-banner',
            'settings'        => array('buildpro_banner_items'),
            'render_callback' => function () {
                ob_start();
                get_template_part('template/template-parts/page/home/section-banner/index');
                return ob_get_clean();
            },
        ));
    }

    // Show Navigation Buttons
    $wp_customize->add_setting('buildpro_banner_show_nav', array(
        'default'           => 1,
        'transport'         => 'refresh',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('buildpro_banner_show_nav', array(
        'label'   => __('Show Navigation Buttons', 'buildpro'),
        'section' => 'buildpro_banner_section',
        'type'    => 'checkbox',
    ));
}
add_action('customize_register', 'buildpro_banner_customize_register');

// ─── Sync Customizer → Post Meta on Save ─────────────────────────────────────

function buildpro_banner_sync_customizer_to_meta($wp_customize_manager)
{
    $items   = buildpro_banner_sanitize_items(get_theme_mod('buildpro_banner_items', array()));
    $enabled = absint(get_theme_mod('buildpro_banner_enabled', 1));

    $page_id = 0;
    if ($wp_customize_manager instanceof WP_Customize_Manager) {
        $setting = $wp_customize_manager->get_setting('buildpro_preview_page_id');
        if ($setting) {
            $page_id = absint($setting->value());
        }
    }

    if ($page_id <= 0) {
        $page_id = buildpro_banner_find_home_id();
    }

    if ($page_id) {
        $targets = array($page_id);

        $front_id = (int) get_option('page_on_front');
        if ($front_id > 0) {
            $targets[] = $front_id;
        }

        $pages = get_pages(array(
            'meta_key'   => '_wp_page_template',
            'meta_value' => 'home-page.php',
            'number'     => 1,
        ));
        if (!empty($pages)) {
            $targets[] = (int) $pages[0]->ID;
        }

        $targets = array_unique(array_filter(array_map('absint', $targets)));

        foreach ($targets as $tid) {
            update_post_meta($tid, 'buildpro_banner_items',   $items);
            update_post_meta($tid, 'buildpro_banner_enabled', $enabled);
        }
    }
}
add_action('customize_save_after', 'buildpro_banner_sync_customizer_to_meta');

// ─── Enqueue Assets ───────────────────────────────────────────────────────────

function buildpro_banner_customize_controls_enqueue()
{
    wp_enqueue_media();
    wp_enqueue_script('wplink');
    wp_enqueue_style('wp-link');
}
add_action('customize_controls_enqueue_scripts', 'buildpro_banner_customize_controls_enqueue');

function buildpro_banner_enqueue_assets()
{
    wp_enqueue_style(
        'buildpro-banner-style',
        get_theme_file_uri('template/customize/page/home/section-banner/style.css'),
        array(),
        null
    );
    wp_enqueue_script(
        'buildpro-banner-script',
        get_theme_file_uri('template/customize/page/home/section-banner/script.js'),
        array('customize-controls'),
        null,
        true
    );

    if (function_exists('buildpro_home_add_inline_i18n')) {
        buildpro_home_add_inline_i18n('buildpro-banner-script');
    }
}
add_action('customize_controls_enqueue_scripts', 'buildpro_banner_enqueue_assets');
