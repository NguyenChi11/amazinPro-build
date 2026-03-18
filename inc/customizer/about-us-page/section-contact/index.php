<?php
function buildpro_about_contact_customize_register($wp_customize)
{
    if (!class_exists('BuildPro_Customize_Button_Control') && class_exists('WP_Customize_Control')) {
        class BuildPro_Customize_Button_Control extends WP_Customize_Control
        {
            public $type = 'buildpro_button';
            public $button_url = '';
            public $button_text = '';
            public function render_content()
            {
                echo '<span class="customize-control-title">' . esc_html($this->label) . '</span>';
                if (!empty($this->description)) {
                    echo '<p class="description">' . esc_html($this->description) . '</p>';
                }
                if (empty($this->button_url)) {
                    echo '<p>' . esc_html__('Could not find an About Us page using template about-us-page.php or about-page.php.', 'buildpro') . '</p>';
                    return;
                }
                $text = $this->button_text ? $this->button_text : __('Open edit page', 'buildpro');
                echo '<a class="button button-primary" href="' . esc_url($this->button_url) . '" target="_blank" rel="noopener">' . esc_html($text) . '</a>';
            }
        }
    }
    $about_id = 0;
    $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => 1));
    if (!empty($pages)) {
        $about_id = (int) $pages[0]->ID;
    }
    if ($about_id <= 0) {
        $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-us-page.php', 'number' => 1));
        if (!empty($pages)) {
            $about_id = (int) $pages[0]->ID;
        }
    }
    $edit_url = $about_id ? admin_url('post.php?post=' . $about_id . '&action=edit') : '';
    if (!function_exists('buildpro_customizer_is_about_preview')) {
        function buildpro_customizer_is_about_preview()
        {
            $selected_id = 0;
            if (function_exists('wp_get_current_user')) {
                global $wp_customize;
                if ($wp_customize && $wp_customize instanceof WP_Customize_Manager) {
                    $setting = $wp_customize->get_setting('buildpro_preview_page_id');
                    if ($setting) {
                        $val = $setting->value();
                        $selected_id = absint($val);
                    }
                }
            }
            if ($selected_id > 0) {
                $tpl = get_page_template_slug($selected_id);
                if ($tpl === 'about-page.php' || $tpl === 'about-us-page.php') {
                    return true;
                }
            }
            if ($selected_id <= 0 && (!empty($pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => 1))) || !empty($pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-us-page.php', 'number' => 1))))) {
                return true;
            }
            return false;
        }
    }
    $wp_customize->add_section('buildpro_about_contact_section', array(
        'title' => __('About Us: Contact', 'buildpro'),
        'priority' => 34,
        'active_callback' => 'buildpro_customizer_is_about_preview',
    ));
    // Enabled
    $enabled_default = 1;
    if ($about_id) {
        $en_meta = get_post_meta($about_id, 'buildpro_about_contact_enabled', true);
        if ($en_meta !== '') {
            $enabled_default = (int) $en_meta;
        }
    }
    $wp_customize->add_setting('buildpro_about_contact_enabled', array(
        'default' => $enabled_default,
        'transport' => 'refresh',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('buildpro_about_contact_enabled', array(
        'label' => __('Enable Contact', 'buildpro'),
        'section' => 'buildpro_about_contact_section',
        'type' => 'checkbox',
    ));
    // Edit button
    if (class_exists('BuildPro_Customize_Button_Control')) {
        $wp_customize->add_setting('buildpro_about_contact_edit_link', array(
            'default' => '',
            'transport' => 'postMessage',
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control(new BuildPro_Customize_Button_Control($wp_customize, 'buildpro_about_contact_edit_link', array(
            'label' => __('Edit About Us Page', 'buildpro'),
            'description' => __('Open the About Us page to edit meta box.', 'buildpro'),
            'section' => 'buildpro_about_contact_section',
            'button_url' => $edit_url,
            'button_text' => __('Edit About Us', 'buildpro'),
        )));
    }
    // Text fields defaults from meta
    $fields = array(
        'buildpro_about_contact_title' => array('label' => __('Title', 'buildpro'), 'sanitize' => 'sanitize_text_field'),
        'buildpro_about_contact_text' => array('label' => __('Description', 'buildpro'), 'sanitize' => 'sanitize_textarea_field', 'type' => 'textarea'),
        'buildpro_about_contact_address' => array('label' => __('Address', 'buildpro'), 'sanitize' => 'sanitize_text_field'),
        'buildpro_about_contact_phone' => array('label' => __('Phone', 'buildpro'), 'sanitize' => 'sanitize_text_field'),
        'buildpro_about_contact_email' => array('label' => __('Email', 'buildpro'), 'sanitize' => 'sanitize_email'),
    );
    foreach ($fields as $key => $cfg) {
        $def = '';
        if ($about_id) {
            $m = get_post_meta($about_id, $key, true);
            if (is_string($m) && $m !== '') {
                $def = $m;
            }
        }
        $wp_customize->add_setting($key, array(
            'default' => $def,
            'transport' => 'postMessage',
            'sanitize_callback' => $cfg['sanitize'],
        ));
        $wp_customize->add_control($key, array(
            'label' => $cfg['label'],
            'section' => 'buildpro_about_contact_section',
            'type' => isset($cfg['type']) ? $cfg['type'] : 'text',
        ));
    }
    // Map image for contact-form aside
    $map_def = 0;
    if ($about_id) {
        $m = get_post_meta($about_id, 'buildpro_about_contact_form_map_image_id', true);
        if ($m !== '') {
            $map_def = absint($m);
        }
    }
    $wp_customize->add_setting('buildpro_about_contact_form_map_image_id', array(
        'default' => $map_def,
        'transport' => 'postMessage',
        'sanitize_callback' => 'absint',
    ));
    if (class_exists('WP_Customize_Media_Control')) {
        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'buildpro_about_contact_form_map_image_id', array(
            'label' => __('Map Image', 'buildpro'),
            'section' => 'buildpro_about_contact_section',
            'mime_type' => 'image',
        )));
    } else {
        $wp_customize->add_control('buildpro_about_contact_form_map_image_id', array(
            'label' => __('Map Image ID', 'buildpro'),
            'section' => 'buildpro_about_contact_section',
            'type' => 'number',
        ));
    }
    // Always sync post_meta → theme_mod so customizer reflects current meta-box data.
    if ($about_id > 0) {
        set_theme_mod('buildpro_about_contact_enabled', $enabled_default);
        foreach ($fields as $key => $cfg) {
            $def_val = '';
            $m = get_post_meta($about_id, $key, true);
            if (is_string($m)) {
                $def_val = $m;
            }
            set_theme_mod($key, $def_val);
        }
        set_theme_mod('buildpro_about_contact_form_map_image_id', $map_def);
    }
    // Selective refresh (optional template)
    if (isset($wp_customize->selective_refresh)) {
        $wp_customize->selective_refresh->add_partial('buildpro_about_contact_partial', array(
            'selector' => '.about-contact',
            'settings' => array('buildpro_about_contact_enabled', 'buildpro_about_contact_title', 'buildpro_about_contact_text', 'buildpro_about_contact_address', 'buildpro_about_contact_phone', 'buildpro_about_contact_email'),
            'render_callback' => function () {
                ob_start();
                get_template_part('template/template-parts/page/about-us/section-contact/index');
                return ob_get_clean();
            },
        ));
    }
    add_action('customize_controls_enqueue_scripts', function () {
        wp_enqueue_style(
            'buildpro-about-contact-style',
            get_theme_file_uri('template/customize/page/about-us/section-contact/style.css'),
            array(),
            null
        );
        wp_enqueue_script(
            'buildpro-about-contact-script',
            get_theme_file_uri('template/customize/page/about-us/section-contact/script.js'),
            array('customize-controls', 'jquery'),
            null,
            true
        );

        if (function_exists('buildpro_about_us_add_inline_i18n')) {
            buildpro_about_us_add_inline_i18n('buildpro-about-contact-script');
        }
        $default_about = 0;
        $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => 1));
        if (!empty($pages)) {
            $default_about = (int)$pages[0]->ID;
        }
        if ($default_about <= 0) {
            $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-us-page.php', 'number' => 1));
            if (!empty($pages)) {
                $default_about = (int)$pages[0]->ID;
            }
        }
        wp_localize_script('buildpro-about-contact-script', 'BuildProAboutContact', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('buildpro_customizer_nonce'),
            'default_page_id' => $default_about,
        ));
    });
    $pages_about = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => 1));
    $about_preview_url = '';
    if (!empty($pages_about)) {
        $about_preview_url = get_permalink($pages_about[0]->ID);
    }
    if (empty($about_preview_url)) {
        $pages_about = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-us-page.php', 'number' => 1));
        if (!empty($pages_about)) {
            $about_preview_url = get_permalink($pages_about[0]->ID);
        }
    }
    if ($about_preview_url && $about_id) {
        add_action('customize_controls_print_footer_scripts', function () use ($about_preview_url, $about_id) {
            $url = esc_js($about_preview_url);
            $pid = (int) $about_id;
            echo "<script>(function(api){try{var s=api&&api.section&&api.section('buildpro_about_contact_section');if(!s)return;s.expanded.bind(function(exp){if(!exp)return;function addCS(u){try{var uuid=api&&api.settings&&api.settings.changeset&&api.settings.changeset.uuid;if(!uuid)return u;var t=new URL(u,window.location.origin);if(!t.searchParams.get('customize_changeset_uuid')){t.searchParams.set('customize_changeset_uuid',uuid);}return t.toString();}catch(e){return u;}}var target=addCS('{$url}');var did=false;if(api&&api.previewer){if(api.previewer.previewUrl&&typeof api.previewer.previewUrl.set==='function'){api.previewer.previewUrl.set(target);did=true;}else if(typeof api.previewer.previewUrl==='function'){api.previewer.previewUrl(target);did=true;}else if(api.previewer.url&&typeof api.previewer.url.set==='function'){api.previewer.url.set(target);did=true;}if(!did){var frame=window.parent&&window.parent.document&&window.parent.document.querySelector('#customize-preview iframe');if(frame){frame.src=target;did=true;}}if(did){setTimeout(function(){try{if(api.previewer.refresh){api.previewer.refresh();}}catch(e){}},100);}try{if(api&&api.has&&api.has('buildpro_preview_page_id')){var cur=parseInt(api('buildpro_preview_page_id').get()||0,10)||0;if(!cur){api('buildpro_preview_page_id').set({$pid});}}}catch(e){}}});}catch(e){}})(wp.customize);</script>";
        });
    }
}
add_action('customize_register', 'buildpro_about_contact_customize_register');

function buildpro_about_contact_ajax_get_data()
{
    if (!current_user_can('edit_theme_options')) {
        wp_send_json_error(array('message' => 'forbidden'), 403);
    }
    $nonce = isset($_REQUEST['nonce']) ? $_REQUEST['nonce'] : '';
    if (!wp_verify_nonce($nonce, 'buildpro_customizer_nonce')) {
        wp_send_json_error(array('message' => 'invalid_nonce'), 400);
    }
    $page_id = isset($_REQUEST['page_id']) ? absint($_REQUEST['page_id']) : 0;
    if ($page_id <= 0) {
        $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => 1));
        if (!empty($pages)) {
            $page_id = (int)$pages[0]->ID;
        }
    }
    if ($page_id <= 0) {
        $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-us-page.php', 'number' => 1));
        if (!empty($pages)) {
            $page_id = (int)$pages[0]->ID;
        }
    }
    if ($page_id <= 0) {
        wp_send_json_success(array(
            'enabled' => 1,
            'title' => '',
            'text' => '',
            'address' => '',
            'phone' => '',
            'email' => '',
        ));
    }
    $enabled = get_post_meta($page_id, 'buildpro_about_contact_enabled', true);
    $enabled = ($enabled === '' ? 1 : (int)$enabled);
    $title = get_post_meta($page_id, 'buildpro_about_contact_title', true);
    $text = get_post_meta($page_id, 'buildpro_about_contact_text', true);
    $address = get_post_meta($page_id, 'buildpro_about_contact_address', true);
    $phone = get_post_meta($page_id, 'buildpro_about_contact_phone', true);
    $email = get_post_meta($page_id, 'buildpro_about_contact_email', true);
    wp_send_json_success(array(
        'enabled' => $enabled,
        'title' => is_string($title) ? $title : '',
        'text' => is_string($text) ? $text : '',
        'address' => is_string($address) ? $address : '',
        'phone' => is_string($phone) ? $phone : '',
        'email' => is_string($email) ? $email : '',
    ));
}
add_action('wp_ajax_buildpro_get_about_contact', 'buildpro_about_contact_ajax_get_data');

function buildpro_about_contact_sync_customizer_to_meta($wp_customize_manager)
{
    $enabled_val = null;
    $title_val = null;
    $text_val = null;
    $address_val = null;
    $phone_val = null;
    $email_val = null;
    $map_id_val = null;
    if ($wp_customize_manager instanceof WP_Customize_Manager) {
        $s = $wp_customize_manager->get_setting('buildpro_about_contact_enabled');
        $enabled_val = $s ? $s->post_value() : null;
        $s = $wp_customize_manager->get_setting('buildpro_about_contact_title');
        $title_val = $s ? $s->post_value() : null;
        $s = $wp_customize_manager->get_setting('buildpro_about_contact_text');
        $text_val = $s ? $s->post_value() : null;
        $s = $wp_customize_manager->get_setting('buildpro_about_contact_address');
        $address_val = $s ? $s->post_value() : null;
        $s = $wp_customize_manager->get_setting('buildpro_about_contact_phone');
        $phone_val = $s ? $s->post_value() : null;
        $s = $wp_customize_manager->get_setting('buildpro_about_contact_email');
        $email_val = $s ? $s->post_value() : null;
        $s = $wp_customize_manager->get_setting('buildpro_about_contact_form_map_image_id');
        $map_id_val = $s ? $s->post_value() : null;
    }
    if ($enabled_val === null) {
        $enabled_val = get_theme_mod('buildpro_about_contact_enabled', 1);
    }
    if ($title_val === null) {
        $title_val = get_theme_mod('buildpro_about_contact_title', '');
    }
    if ($text_val === null) {
        $text_val = get_theme_mod('buildpro_about_contact_text', '');
    }
    if ($address_val === null) {
        $address_val = get_theme_mod('buildpro_about_contact_address', '');
    }
    if ($phone_val === null) {
        $phone_val = get_theme_mod('buildpro_about_contact_phone', '');
    }
    if ($email_val === null) {
        $email_val = get_theme_mod('buildpro_about_contact_email', '');
    }
    if ($map_id_val === null) {
        $map_id_val = get_theme_mod('buildpro_about_contact_form_map_image_id', 0);
    }
    $enabled = absint($enabled_val);
    $title = is_string($title_val) ? $title_val : '';
    $text = is_string($text_val) ? $text_val : '';
    $address = is_string($address_val) ? $address_val : '';
    $phone = is_string($phone_val) ? $phone_val : '';
    $email = is_string($email_val) ? $email_val : '';
    $map_id = absint($map_id_val);
    $page_id = 0;
    $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-us-page.php', 'number' => 1));
    if (!empty($pages)) {
        $page_id = (int)$pages[0]->ID;
    }
    if ($page_id <= 0) {
        $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => 1));
        if (!empty($pages)) {
            $page_id = (int)$pages[0]->ID;
        }
    }
    if ($page_id) {
        update_post_meta($page_id, 'buildpro_about_contact_enabled', $enabled);
        update_post_meta($page_id, 'buildpro_about_contact_title', $title);
        update_post_meta($page_id, 'buildpro_about_contact_text', $text);
        update_post_meta($page_id, 'buildpro_about_contact_address', $address);
        update_post_meta($page_id, 'buildpro_about_contact_phone', $phone);
        update_post_meta($page_id, 'buildpro_about_contact_email', $email);
        update_post_meta($page_id, 'buildpro_about_contact_form_map_image_id', $map_id);
    }
    set_theme_mod('buildpro_about_contact_form_map_image_id', $map_id);
}
add_action('customize_save_after', 'buildpro_about_contact_sync_customizer_to_meta');
