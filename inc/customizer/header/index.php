<?php
function buildpro_customize_register($wp_customize)
{
    $wp_customize->add_section('buildpro_header_section', array(
        'title' => __('Header', 'buildpro'),
        'priority' => 30,
    ));
    $wp_customize->add_setting('header_logo', array(
        'default' => 0,
        'transport' => 'postMessage',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'header_logo', array(
        'label' => __('Logo', 'buildpro'),
        'section' => 'buildpro_header_section',
        'description' => __('Please use a square logo', 'buildpro'),
        'mime_type' => 'image',
    )));
    $wp_customize->add_setting('buildpro_header_quote_text', array(
        'default' => __('Request a Quote', 'buildpro'),
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('buildpro_header_quote_text', array(
        'label' => __('Quote Button Text', 'buildpro'),
        'section' => 'buildpro_header_section',
        'type' => 'text',
    ));
    $wp_customize->add_setting('buildpro_header_quote_url', array(
        'default' => '',
        'transport' => 'postMessage',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('buildpro_header_quote_url', array(
        'label' => __('Quote Button URL', 'buildpro'),
        'description' => __('Leave blank to use the About page contact anchor automatically.', 'buildpro'),
        'section' => 'buildpro_header_section',
        'type' => 'url',
    ));
    if (isset($wp_customize->selective_refresh)) {
        $wp_customize->selective_refresh->add_partial('header_logo', array(
            'selector' => '.header-logo',
            'settings' => array('header_logo'),
            'render_callback' => function () {
                $logo_id = get_theme_mod('header_logo', 0);
                if ($logo_id) {
                    return wp_get_attachment_image($logo_id, 'full', false, array('class' => ''));
                }
                return '<img src="' . esc_url(get_theme_file_uri('/assets/images/Logo.png')) . '" alt="Logo" />';
            },
        ));
    }
}
add_action('customize_register', 'buildpro_customize_register');

function buildpro_header_print_i18n()
{
    $i18n = array(
        'mediaTitle' => __('Select Header Logo', 'buildpro'),
        'useImage'   => __('Use Image', 'buildpro'),
        'chooseLink' => __('Choose Link', 'buildpro'),
    );

    wp_add_inline_script(
        'buildpro-header',
        'window.buildproHeaderI18n = ' . wp_json_encode($i18n) . ';',
        'before'
    );
}

function buildpro_header_customize_preview_js()
{
    $script_path = get_theme_file_path('template/customize/header/script.js');
    $script_ver = file_exists($script_path) ? filemtime($script_path) : null;

    wp_enqueue_script(
        'buildpro-header',
        get_theme_file_uri('template/customize/header/script.js'),
        array('customize-preview'),
        $script_ver,
        true
    );

    buildpro_header_print_i18n();
}
add_action('customize_preview_init', 'buildpro_header_customize_preview_js');

function buildpro_header_customize_controls_js()
{
    $script_path = get_theme_file_path('template/customize/header/script.js');
    $script_ver = file_exists($script_path) ? filemtime($script_path) : null;

    wp_enqueue_script(
        'buildpro-header',
        get_theme_file_uri('template/customize/header/script.js'),
        array('customize-controls'),
        $script_ver,
        true
    );

    buildpro_header_print_i18n();
}
add_action('customize_controls_enqueue_scripts', 'buildpro_header_customize_controls_js');

function buildpro_header_admin_menu()
{
    add_theme_page(__('Header', 'buildpro'), __('Header', 'buildpro'), 'edit_theme_options', 'buildpro-header', 'buildpro_header_admin_page');
}
add_action('admin_menu', 'buildpro_header_admin_menu');

function buildpro_header_admin_enqueue($hook)
{
    if ($hook !== 'appearance_page_buildpro-header') {
        return;
    }
    wp_enqueue_media();
    $script_path = get_theme_file_path('template/customize/header/script.js');
    $script_ver = file_exists($script_path) ? filemtime($script_path) : null;

    wp_enqueue_style(
        'buildpro-header-style',
        get_theme_file_uri('template/customize/header/style.css'),
        array(),
        null
    );
    wp_enqueue_script(
        'buildpro-header',
        get_theme_file_uri('template/customize/header/script.js'),
        array('jquery'),
        $script_ver,
        true
    );

    buildpro_header_print_i18n();
}
add_action('admin_enqueue_scripts', 'buildpro_header_admin_enqueue');

function buildpro_header_admin_page()
{
    $logo_id = get_theme_mod('header_logo', 0);
    $quote_text = get_theme_mod('buildpro_header_quote_text', '');
    $quote_url = get_theme_mod('buildpro_header_quote_url', '');
    if (!is_scalar($quote_text) || trim((string) $quote_text) === '') {
        $quote_text = __('Request a Quote', 'buildpro');
    }
    $logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'thumbnail') : '';

    include get_theme_file_path('template/customize/header/index.php');
}

function buildpro_handle_header_save()
{
    if (!current_user_can('edit_theme_options')) {
        wp_die(esc_html__('Not allowed', 'buildpro'));
    }
    check_admin_referer('buildpro_header_save');
    $logo_raw = isset($_POST['header_logo']) ? $_POST['header_logo'] : "";
    $logo = absint($logo_raw);
    $quote_text = isset($_POST['buildpro_header_quote_text']) ? sanitize_text_field($_POST['buildpro_header_quote_text']) : '';
    $quote_url = isset($_POST['buildpro_header_quote_url']) ? esc_url_raw($_POST['buildpro_header_quote_url']) : '';
    if ($logo_raw === "" || $logo === 0) {
        remove_theme_mod('header_logo');
    } else {
        set_theme_mod('header_logo', $logo);
    }
    remove_theme_mod('buildpro_header_title');
    remove_theme_mod('header_text');
    remove_theme_mod('buildpro_header_description');
    remove_theme_mod('header_description');
    if ($quote_text === '') {
        remove_theme_mod('buildpro_header_quote_text');
    } else {
        set_theme_mod('buildpro_header_quote_text', $quote_text);
    }
    if ($quote_url === '') {
        remove_theme_mod('buildpro_header_quote_url');
    } else {
        set_theme_mod('buildpro_header_quote_url', $quote_url);
    }
    wp_redirect(admin_url('themes.php?page=buildpro-header&updated=1'));
    exit;
}
add_action('admin_post_buildpro_save_header', 'buildpro_handle_header_save');
