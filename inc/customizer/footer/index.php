<?php
if (!class_exists('BuildPro_Footer_Single_Link_Control') && class_exists('WP_Customize_Control')) {
    class BuildPro_Footer_Single_Link_Control extends WP_Customize_Control
    {
        public $type = 'buildpro_footer_single_link';

        public function render_content()
        {
            $item = $this->value();
            if (is_string($item)) {
                $decoded = json_decode($item, true);
                if (is_array($decoded)) {
                    $item = $decoded;
                }
            }
            $item = is_array($item) ? $item : array();

            $label = $this->label;
            $description = $this->description;
            $link_attr = $this->get_link();
            $buildpro_control_type = 'footer-single-link';
            $buildpro_single_link_id = $this->id;

            include get_theme_file_path('template/customize/footer/controls.php');
        }
    }
}

if (!class_exists('BuildPro_Footer_Contact_Links_Control') && class_exists('WP_Customize_Control')) {
    class BuildPro_Footer_Contact_Links_Control extends WP_Customize_Control
    {
        public $type = 'buildpro_footer_contact_links';
        public function render_content()
        {
            $items                 = $this->value();
            $items                 = is_array($items) ? $items : array();
            $label                 = $this->label;
            $description           = $this->description;
            $link_attr             = $this->get_link();
            $buildpro_control_type = 'footer-contact-links';
            include get_theme_file_path('template/customize/footer/controls.php');
        }
    }
}
if (!function_exists('buildpro_footer_sanitize_contact_links')) {
    function buildpro_footer_sanitize_contact_links($value)
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
        foreach ($value as $cl) {
            $clean[] = array(
                'icon_id' => isset($cl['icon_id']) ? absint($cl['icon_id']) : 0,
                'url' => isset($cl['url']) ? esc_url_raw($cl['url']) : '',
                'title' => isset($cl['title']) ? sanitize_text_field($cl['title']) : '',
                'target' => isset($cl['target']) ? sanitize_text_field($cl['target']) : '',
            );
        }
        return $clean;
    }
}
if (!function_exists('buildpro_footer_sanitize_link')) {
    function buildpro_footer_sanitize_link($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $value = $decoded;
            }
        }
        if (!is_array($value)) {
            $value = array();
        }
        return array(
            'url' => isset($value['url']) ? esc_url_raw($value['url']) : '',
            'title' => isset($value['title']) ? sanitize_text_field($value['title']) : '',
            'target' => isset($value['target']) ? sanitize_text_field($value['target']) : '',
        );
    }
}
function buildpro_footer_customize_register($wp_customize)
{
    $wp_customize->add_section('buildpro_footer_section', array(
        'title' => __('Footer', 'buildpro'),
        'priority' => 40,
    ));
    $wp_customize->add_setting('footer_banner_image_id', array(
        'default' => 0,
        'transport' => 'postMessage',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'footer_banner_image_id', array(
        'label' => __('Banner Background', 'buildpro'),
        'section' => 'buildpro_footer_section',
        'mime_type' => 'image',
    )));
    $wp_customize->add_setting('footer_information_description', array(
        'default' => '',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('footer_information_description', array(
        'label' => __('Description', 'buildpro'),
        'section' => 'buildpro_footer_section',
        'type' => 'textarea',
    ));

    $wp_customize->add_setting('footer_contact_location', array(
        'default' => '',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('footer_contact_location', array(
        'label' => __('Contact Location', 'buildpro'),
        'section' => 'buildpro_footer_section',
        'type' => 'text',
    ));
    $wp_customize->add_setting('footer_contact_phone', array(
        'default' => '',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('footer_contact_phone', array(
        'label' => __('Contact Phone', 'buildpro'),
        'section' => 'buildpro_footer_section',
        'type' => 'text',
    ));
    $wp_customize->add_setting('footer_contact_email', array(
        'default' => '',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('footer_contact_email', array(
        'label' => __('Contact Email', 'buildpro'),
        'section' => 'buildpro_footer_section',
        'type' => 'email',
    ));
    $wp_customize->add_setting('footer_contact_time', array(
        'default' => '',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('footer_contact_time', array(
        'label' => __('Contact Time', 'buildpro'),
        'section' => 'buildpro_footer_section',
        'type' => 'text',
    ));
    $wp_customize->add_setting('footer_contact_links', array(
        'default' => array(),
        'transport' => 'postMessage',
        'sanitize_callback' => 'buildpro_footer_sanitize_contact_links',
    ));
    if (class_exists('BuildPro_Footer_Contact_Links_Control')) {
        $wp_customize->add_control(new BuildPro_Footer_Contact_Links_Control($wp_customize, 'footer_contact_links', array(
            'label' => __('Social Linkss', 'buildpro'),
            'description' => __('Add/Edit contact links with icons.', 'buildpro'),
            'section' => 'buildpro_footer_section',
        )));
    }
    $wp_customize->add_setting('footer_create_build_text', array(
        'default' => '',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('footer_create_build_text', array(
        'label' => __('Copyright Text', 'buildpro'),
        'section' => 'buildpro_footer_section',
        'type' => 'text',
    ));
    $wp_customize->add_setting('footer_policy_text', array(
        'default' => '',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('footer_policy_text', array(
        'label' => __('Policy Text', 'buildpro'),
        'section' => 'buildpro_footer_section',
        'type' => 'text',
    ));
    $wp_customize->add_setting('footer_policy_link', array(
        'default' => array('url' => '', 'title' => '', 'target' => ''),
        'transport' => 'postMessage',
        'sanitize_callback' => 'buildpro_footer_sanitize_link',
    ));
    if (class_exists('BuildPro_Footer_Single_Link_Control')) {
        $wp_customize->add_control(new BuildPro_Footer_Single_Link_Control($wp_customize, 'footer_policy_link', array(
            'label' => __('Policy Link', 'buildpro'),
            'description' => __('Choose link for Policy.', 'buildpro'),
            'section' => 'buildpro_footer_section',
        )));
    }
    $wp_customize->add_setting('footer_servicer_text', array(
        'default' => '',
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('footer_servicer_text', array(
        'label' => __('Service Text', 'buildpro'),
        'section' => 'buildpro_footer_section',
        'type' => 'text',
    ));
    $wp_customize->add_setting('footer_servicer_link', array(
        'default' => array('url' => '', 'title' => '', 'target' => ''),
        'transport' => 'postMessage',
        'sanitize_callback' => 'buildpro_footer_sanitize_link',
    ));
    if (class_exists('BuildPro_Footer_Single_Link_Control')) {
        $wp_customize->add_control(new BuildPro_Footer_Single_Link_Control($wp_customize, 'footer_servicer_link', array(
            'label' => __('Service Link', 'buildpro'),
            'description' => __('Choose link for Servicer.', 'buildpro'),
            'section' => 'buildpro_footer_section',
        )));
    }
    if (isset($wp_customize->selective_refresh)) {
        $wp_customize->selective_refresh->add_partial('buildpro_footer_all', array(
            'selector' => '.site-footer',
            'settings' => array(
                'footer_banner_image_id',
                'footer_information_description',
                'footer_contact_location',
                'footer_contact_phone',
                'footer_contact_email',
                'footer_contact_time',
                'footer_contact_links',
                'footer_create_build_text',
                'footer_policy_text',
                'footer_policy_link',
                'footer_servicer_text',
                'footer_servicer_link'
            ),
            'render_callback' => function () {
                ob_start();
                get_template_part('template/template-parts/footer/footer');
                return ob_get_clean();
            },
        ));
    }
}
add_action('customize_register', 'buildpro_footer_customize_register');

function buildpro_footer_customize_preview_js()
{
    wp_enqueue_script(
        'buildpro-footer-preview',
        get_theme_file_uri('template/customize/footer/script.js'),
        array('customize-preview'),
        filemtime(get_theme_file_path('template/customize/footer/script.js')),
        true
    );

    buildpro_footer_add_inline_i18n('buildpro-footer-preview');
}
add_action('customize_preview_init', 'buildpro_footer_customize_preview_js');

function buildpro_footer_add_inline_i18n(string $handle)
{
    $i18n = array(
        'linkUrl'         => __('Link URL', 'buildpro'),
        'chooseLink'      => __('Choose Link', 'buildpro'),
        'linkTitle'       => __('Button Label', 'buildpro'),
        'linkTarget'      => __('Link Target', 'buildpro'),
        'sameTab'         => __('Same Tab', 'buildpro'),
        'openInNewTab'    => __('Open in new tab', 'buildpro'),
        'remove'          => __('Remove', 'buildpro'),
        'addItem'         => __('Add Item', 'buildpro'),
        'icon'            => __('Icon', 'buildpro'),
        'selectPhoto'     => __('Select photo', 'buildpro'),
        'removePhoto'     => __('Remove photo', 'buildpro'),
        'noImageSelected' => __('No image selected', 'buildpro'),
        'chooseImage'     => __('Choose Image', 'buildpro'),
        'selectImage'     => __('Choose Image', 'buildpro'),
        'useImage'        => __('Use Image', 'buildpro'),
        'selectIconTitle' => __('Select photo', 'buildpro'),
        'page'            => __('Page', 'buildpro'),
        'post'            => __('Post', 'buildpro'),
    );

    wp_add_inline_script(
        $handle,
        'window.buildproFooterI18n = ' . wp_json_encode($i18n) . ';',
        'before'
    );
}

function buildpro_footer_customize_controls_enqueue()
{
    wp_enqueue_media();
    wp_enqueue_script('wplink');
    wp_enqueue_style('wp-link');
    wp_enqueue_script('wp-api-fetch');
    wp_enqueue_style(
        'buildpro-customize-style',
        get_theme_file_uri('template/customize/footer/style.css'),
        array(),
        null
    );
    wp_enqueue_script(
        'buildpro-customize-script',
        get_theme_file_uri('template/customize/footer/script.js'),
        array('customize-controls', 'media-editor'),
        filemtime(get_theme_file_path('template/customize/footer/script.js')),
        true
    );

    buildpro_footer_add_inline_i18n('buildpro-customize-script');
}
add_action('customize_controls_enqueue_scripts', 'buildpro_footer_customize_controls_enqueue');

function buildpro_footer_admin_menu()
{
    add_theme_page(__('Footer', 'buildpro'), __('Footer', 'buildpro'), 'edit_theme_options', 'buildpro-footer', 'buildpro_footer_admin_page');
}
add_action('admin_menu', 'buildpro_footer_admin_menu');

function buildpro_footer_admin_enqueue($hook)
{
    if ($hook !== 'appearance_page_buildpro-footer') {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_script('wplink');
    wp_enqueue_style('wp-link');
    wp_enqueue_script('wp-api-fetch');
    wp_enqueue_style(
        'buildpro-customize-style',
        get_theme_file_uri('template/customize/footer/style.css'),
        array(),
        null
    );
    wp_enqueue_script(
        'buildpro-customize-script',
        get_theme_file_uri('template/customize/footer/script.js'),
        array('jquery', 'media-editor'),
        filemtime(get_theme_file_path('template/customize/footer/script.js')),
        true
    );

    buildpro_footer_add_inline_i18n('buildpro-customize-script');
}
add_action('admin_enqueue_scripts', 'buildpro_footer_admin_enqueue');

function buildpro_footer_admin_page()
{
    $banner_image_id = get_theme_mod('footer_banner_image_id', 0);
    $banner_thumb = $banner_image_id ? wp_get_attachment_image_url($banner_image_id, 'thumbnail') : '';
    $info_description = get_theme_mod('footer_information_description', '');
    $contact_location = get_theme_mod('footer_contact_location', '');
    $contact_phone = get_theme_mod('footer_contact_phone', '');
    $contact_email = get_theme_mod('footer_contact_email', '');
    $contact_time = get_theme_mod('footer_contact_time', '');
    $contact_links = get_theme_mod('footer_contact_links', array());
    $contact_links = is_array($contact_links) ? $contact_links : array();
    $create_build_text = get_theme_mod('footer_create_build_text', '');
    $policy_text = get_theme_mod('footer_policy_text', '');
    $policy_link = get_theme_mod('footer_policy_link', array('url' => '', 'title' => '', 'target' => ''));
    $policy_link = is_array($policy_link) ? $policy_link : array('url' => '', 'title' => '', 'target' => '');
    $servicer_text = get_theme_mod('footer_servicer_text', '');
    $servicer_link = get_theme_mod('footer_servicer_link', array('url' => '', 'title' => '', 'target' => ''));
    $servicer_link = is_array($servicer_link) ? $servicer_link : array('url' => '', 'title' => '', 'target' => '');

    include get_theme_file_path('template/customize/footer/index.php');
}

function buildpro_handle_footer_save()
{
    if (!current_user_can('edit_theme_options')) {
        wp_die(esc_html__('Not allowed', 'buildpro'));
    }
    check_admin_referer('buildpro_footer_save');
    $banner_image_id = isset($_POST['footer_banner_image_id']) ? absint($_POST['footer_banner_image_id']) : 0;
    $info_description = isset($_POST['footer_information_description']) ? sanitize_textarea_field($_POST['footer_information_description']) : '';
    $contact_location = isset($_POST['footer_contact_location']) ? sanitize_text_field($_POST['footer_contact_location']) : '';
    $contact_phone = isset($_POST['footer_contact_phone']) ? sanitize_text_field($_POST['footer_contact_phone']) : '';
    $contact_email = isset($_POST['footer_contact_email']) ? sanitize_email($_POST['footer_contact_email']) : '';
    $contact_time = isset($_POST['footer_contact_time']) ? sanitize_text_field($_POST['footer_contact_time']) : '';
    $contact_links = isset($_POST['footer_contact_links']) && is_array($_POST['footer_contact_links']) ? $_POST['footer_contact_links'] : array();
    $clean_cl = array();
    foreach ($contact_links as $cl) {
        $clean_cl[] = array(
            'icon_id' => isset($cl['icon_id']) ? absint($cl['icon_id']) : 0,
            'url' => isset($cl['url']) ? esc_url_raw($cl['url']) : '',
            'title' => isset($cl['title']) ? sanitize_text_field($cl['title']) : '',
            'target' => isset($cl['target']) ? sanitize_text_field($cl['target']) : '',
        );
    }
    $create_build_text = isset($_POST['footer_create_build_text']) ? sanitize_text_field($_POST['footer_create_build_text']) : '';
    $policy_text = isset($_POST['footer_policy_text']) ? sanitize_text_field($_POST['footer_policy_text']) : '';
    $policy_link = isset($_POST['footer_policy_link']) && is_array($_POST['footer_policy_link']) ? $_POST['footer_policy_link'] : array();
    $clean_policy_link = array(
        'url' => isset($policy_link['url']) ? esc_url_raw($policy_link['url']) : '',
        'title' => isset($policy_link['title']) ? sanitize_text_field($policy_link['title']) : '',
        'target' => isset($policy_link['target']) ? sanitize_text_field($policy_link['target']) : '',
    );
    $servicer_text = isset($_POST['footer_servicer_text']) ? sanitize_text_field($_POST['footer_servicer_text']) : '';
    $servicer_link = isset($_POST['footer_servicer_link']) && is_array($_POST['footer_servicer_link']) ? $_POST['footer_servicer_link'] : array();
    $clean_servicer_link = array(
        'url' => isset($servicer_link['url']) ? esc_url_raw($servicer_link['url']) : '',
        'title' => isset($servicer_link['title']) ? sanitize_text_field($servicer_link['title']) : '',
        'target' => isset($servicer_link['target']) ? sanitize_text_field($servicer_link['target']) : '',
    );
    set_theme_mod('footer_banner_image_id', $banner_image_id);
    set_theme_mod('footer_information_description', $info_description);
    set_theme_mod('footer_contact_location', $contact_location);
    set_theme_mod('footer_contact_phone', $contact_phone);
    set_theme_mod('footer_contact_email', $contact_email);
    set_theme_mod('footer_contact_time', $contact_time);
    set_theme_mod('footer_contact_links', $clean_cl);
    set_theme_mod('footer_create_build_text', $create_build_text);
    set_theme_mod('footer_policy_text', $policy_text);
    set_theme_mod('footer_policy_link', $clean_policy_link);
    set_theme_mod('footer_servicer_text', $servicer_text);
    set_theme_mod('footer_servicer_link', $clean_servicer_link);
    wp_redirect(admin_url('themes.php?page=buildpro-footer&updated=1'));
    exit;
}
add_action('admin_post_buildpro_save_footer', 'buildpro_handle_footer_save');