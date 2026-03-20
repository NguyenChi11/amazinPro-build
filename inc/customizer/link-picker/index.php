<?php

/**
 * BuildPro – Link Picker Customizer
 *
 * Registers the Link Picker section, settings, control,
 * and related enqueue hooks.
 */

// ─── Control Classes ──────────────────────────────────────────────────────────

if (!class_exists('BuildPro_Customize_Button_Control') && class_exists('WP_Customize_Control')) {
    class BuildPro_Customize_Button_Control extends WP_Customize_Control
    {
        public $type        = 'buildpro_button';
        public $button_url  = '';
        public $button_text = '';

        public function render_content()
        {
            if (empty($this->button_url)) {
                echo '<span class="customize-control-title">' . esc_html($this->label) . '</span>';
                echo '<p>' . esc_html__('Could not find a Home page using template home-page.php.', 'buildpro') . '</p>';
                return;
            }

            echo '<span class="customize-control-title">' . esc_html($this->label) . '</span>';
            if (!empty($this->description)) {
                echo '<p class="description">' . esc_html($this->description) . '</p>';
            }

            $text = $this->button_text
                ? $this->button_text
                : __('Open edit page', 'buildpro');

            echo '<a class="button button-primary" href="' . esc_url($this->button_url) . '" target="_blank" rel="noopener">'
                . esc_html($text)
                . '</a>';
        }
    }
}

if (!class_exists('BuildPro_Link_List_Control') && class_exists('WP_Customize_Control')) {
    class BuildPro_Link_List_Control extends WP_Customize_Control
    {
        public $type = 'buildpro_link_list';

        public function render_content()
        {
            echo '<span class="customize-control-title">' . esc_html($this->label) . '</span>';
            if (!empty($this->description)) {
                echo '<p class="description">' . esc_html($this->description) . '</p>';
            }

            include get_theme_file_path('template/customize/link-picker/index.php');
        }
    }
}

// ─── Customize Register ───────────────────────────────────────────────────────

function buildpro_link_picker_customize_register($wp_customize)
{
    $wp_customize->add_section('buildpro_link_picker_section', array(
        'title'    => __('Link Picker', 'buildpro'),
        'priority' => 26,
        // Available for all templates – no active_callback restriction
    ));

    $wp_customize->add_setting('buildpro_link_picker_dummy', array(
        'default'           => '',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    if (class_exists('BuildPro_Link_List_Control')) {
        $wp_customize->add_control(new BuildPro_Link_List_Control($wp_customize, 'buildpro_link_picker_dummy', array(
            'label'       => __('Link Picker', 'buildpro'),
            'description' => __('List of pages and posts to select as links.', 'buildpro'),
            'section'     => 'buildpro_link_picker_section',
        )));
    }
}
add_action('customize_register', 'buildpro_link_picker_customize_register');

// ─── Enqueue Assets ───────────────────────────────────────────────────────────

function buildpro_link_picker_enqueue_assets()
{
    wp_enqueue_style(
        'buildpro-link-picker-style',
        get_theme_file_uri('template/customize/link-picker/style.css'),
        array(),
        null
    );
    wp_enqueue_script(
        'buildpro-link-picker-script',
        get_theme_file_uri('template/customize/link-picker/script.js'),
        array('customize-controls'),
        null,
        true
    );

    $i18n = array(
        'noResults' => __('No results found.', 'buildpro'),
        'select' => __('Select', 'buildpro'),
        'loading' => __('Loading...', 'buildpro'),
        'directOpenNotice' => __('Link Picker is used from other sections. Please use the “Choose Link” button in the relevant tab to pick a link.', 'buildpro'),
    );
    wp_add_inline_script(
        'buildpro-link-picker-script',
        'window.buildproLinkPickerI18n = ' . wp_json_encode($i18n) . ';',
        'before'
    );
}
add_action('customize_controls_enqueue_scripts', 'buildpro_link_picker_enqueue_assets');
