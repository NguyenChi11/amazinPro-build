<?php
function buildpro_about_policy_customize_register($wp_customize)
{
    if (!class_exists('BuildPro_About_Policy_Repeater_Control') && class_exists('WP_Customize_Control')) {
        class BuildPro_About_Policy_Repeater_Control extends WP_Customize_Control
        {
            public $type = 'buildpro_about_policy_repeater';
            public function render_content()
            {
                $items = $this->value();
                if (!is_array($items)) {
                    $items = array();
                }
                // Nếu control ràng buộc với meta khi trống, nạp từ post meta
                $pid = 0;
                if (function_exists('wp_get_current_user')) {
                    global $wp_customize;
                    if ($wp_customize && $wp_customize instanceof WP_Customize_Manager) {
                        $setting = $wp_customize->get_setting('buildpro_preview_page_id');
                        if ($setting) {
                            $pid = absint($setting->value());
                        }
                    }
                }
                if ($pid <= 0) {
                    $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => 1));
                    if (!empty($pages)) {
                        $pid = (int)$pages[0]->ID;
                    }
                }
                if ($pid > 0 && empty($items)) {
                    if ($this->id === 'buildpro_about_policy_certifications') {
                        $meta = get_post_meta($pid, 'buildpro_about_policy_certifications', true);
                        if (is_array($meta) && !empty($meta)) {
                            foreach ($meta as &$it) {
                                $iid = isset($it['image_id']) ? (int)$it['image_id'] : 0;
                                $url = $iid ? wp_get_attachment_image_url($iid, 'thumbnail') : '';
                                if (empty($it['image_url']) && $url) {
                                    $it['image_url'] = $url;
                                }
                            }
                            unset($it);
                            $items = array_values($meta);
                        }
                    } elseif ($this->id === 'buildpro_about_policy_items') {
                        $meta = get_post_meta($pid, 'buildpro_about_policy_items', true);
                        if (is_array($meta) && !empty($meta)) {
                            foreach ($meta as &$it) {
                                $iid = isset($it['icon_id']) ? (int)$it['icon_id'] : 0;
                                $url = $iid ? wp_get_attachment_image_url($iid, 'thumbnail') : '';
                                if (empty($it['icon_url']) && $url) {
                                    $it['icon_url'] = $url;
                                }
                            }
                            unset($it);
                            $items = array_values($meta);
                        }
                    }
                }
                echo '<span class="customize-control-title">' . esc_html($this->label) . '</span>';
                if (!empty($this->description)) {
                    echo '<p class="description">' . esc_html($this->description) . '</p>';
                }
                include get_theme_file_path('template/customize/page/about-us/section-policy/index.php');
                return;
            }
        }
    }
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
    $wp_customize->add_section('buildpro_about_policy_section', array(
        'title' => __('About Us: Policy', 'buildpro'),
        'priority' => 33,
        'active_callback' => 'buildpro_customizer_is_about_preview',
    ));
    // Defaults lấy từ meta nếu có
    $about_id = 0;
    $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => 1));
    if (!empty($pages)) {
        $about_id = (int)$pages[0]->ID;
    }
    if ($about_id <= 0) {
        $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-us-page.php', 'number' => 1));
        if (!empty($pages)) {
            $about_id = (int)$pages[0]->ID;
        }
    }
    $edit_url = $about_id ? admin_url('post.php?post=' . $about_id . '&action=edit') : '';
    $enabled_default = 1;
    if ($about_id) {
        $en_meta = get_post_meta($about_id, 'buildpro_about_policy_enabled', true);
        if ($en_meta !== '') {
            $enabled_default = (int)$en_meta;
        }
    }
    $wp_customize->add_setting('buildpro_about_policy_enabled', array(
        'default' => $enabled_default,
        'transport' => 'refresh',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('buildpro_about_policy_enabled', array(
        'label' => __('Enable Policy', 'buildpro'),
        'section' => 'buildpro_about_policy_section',
        'type' => 'checkbox',
    ));
    $fields = array(
        'buildpro_about_policy_title_left' => array('label' => __('Left Title', 'buildpro')),
        'buildpro_about_policy_business_registration' => array('label' => __('Business Registration', 'buildpro')),
        'buildpro_about_policy_general_contractor' => array('label' => __('General Contractor', 'buildpro')),
        'buildpro_about_policy_duns_number' => array('label' => __('DUNS Number', 'buildpro')),
        'buildpro_about_policy_title_right' => array('label' => __('Right Title', 'buildpro')),
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
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control($key, array(
            'label' => $cfg['label'],
            'section' => 'buildpro_about_policy_section',
            'type' => 'text',
        ));
    }
    $warranty_default = '';
    if ($about_id) {
        $w = get_post_meta($about_id, 'buildpro_about_policy_warranty_desc', true);
        if (is_string($w) && $w !== '') {
            $warranty_default = $w;
        }
    }
    $wp_customize->add_setting('buildpro_about_policy_warranty_desc', array(
        'default' => $warranty_default,
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('buildpro_about_policy_warranty_desc', array(
        'label' => __('Warranty Description', 'buildpro'),
        'section' => 'buildpro_about_policy_section',
        'type' => 'textarea',
    ));
    $items_default = array();
    if ($about_id) {
        $meta_items = get_post_meta($about_id, 'buildpro_about_policy_items', true);
        if (is_array($meta_items)) {
            $items_default = $meta_items;
        }
    }
    $certs_default = array();
    if ($about_id) {
        $meta_certs = get_post_meta($about_id, 'buildpro_about_policy_certifications', true);
        if (is_array($meta_certs)) {
            $certs_default = $meta_certs;
        }
    }
    // Always sync post_meta → theme_mod so customizer reflects current meta-box data.
    if ($about_id > 0) {
        set_theme_mod('buildpro_about_policy_enabled', $enabled_default);
        foreach ($fields as $key => $cfg) {
            $def_val = '';
            $m = get_post_meta($about_id, $key, true);
            if (is_string($m)) {
                $def_val = $m;
            }
            set_theme_mod($key, $def_val);
        }
        set_theme_mod('buildpro_about_policy_warranty_desc', $warranty_default);
        set_theme_mod('buildpro_about_policy_items', $items_default);
        set_theme_mod('buildpro_about_policy_certifications', $certs_default);
    }
    $wp_customize->add_setting('buildpro_about_policy_items', array(
        'default' => $items_default,
        'transport' => 'postMessage',
        'sanitize_callback' => 'buildpro_about_policy_sanitize_items',
    ));
    $wp_customize->add_setting('buildpro_about_policy_certifications', array(
        'default' => $certs_default,
        'transport' => 'postMessage',
        'sanitize_callback' => 'buildpro_about_policy_sanitize_certs',
    ));
    if (class_exists('BuildPro_About_Policy_Repeater_Control')) {
        $wp_customize->add_control(new BuildPro_About_Policy_Repeater_Control($wp_customize, 'buildpro_about_policy_certifications', array(
            'label' => __('Certifications', 'buildpro'),
            'description' => __('Manage certifications (image/url/title/desc).', 'buildpro'),
            'section' => 'buildpro_about_policy_section',
        )));
        $wp_customize->add_control(new BuildPro_About_Policy_Repeater_Control($wp_customize, 'buildpro_about_policy_items', array(
            'label' => __('Warranty Items', 'buildpro'),
            'description' => __('Manage warranty items (icon/title/desc).', 'buildpro'),
            'section' => 'buildpro_about_policy_section',
        )));
    }
    if (isset($wp_customize->selective_refresh)) {
        $wp_customize->selective_refresh->add_partial('buildpro_about_policy_partial', array(
            'selector' => '.about-policy',
            'settings' => array(
                'buildpro_about_policy_enabled',
                'buildpro_about_policy_title_left',
                'buildpro_about_policy_business_registration',
                'buildpro_about_policy_general_contractor',
                'buildpro_about_policy_duns_number',
                'buildpro_about_policy_certifications',
                'buildpro_about_policy_title_right',
                'buildpro_about_policy_warranty_desc',
                'buildpro_about_policy_items'
            ),
            'render_callback' => function () {
                ob_start();
                get_template_part('template/template-parts/page/about-us/section-policy/index');
                return ob_get_clean();
            },
        ));
    }
    add_action('customize_controls_enqueue_scripts', function () {
        wp_enqueue_style(
            'buildpro-about-policy-style',
            get_theme_file_uri('template/customize/page/about-us/section-policy/style.css'),
            array(),
            null
        );
        wp_enqueue_script(
            'buildpro-about-policy-script',
            get_theme_file_uri('template/customize/page/about-us/section-policy/script.js'),
            array('customize-controls', 'jquery'),
            null,
            true
        );
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
        wp_localize_script('buildpro-about-policy-script', 'BuildProAboutPolicy', array(
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
            echo "<script>(function(api){try{var s=api&&api.section&&api.section('buildpro_about_policy_section');if(!s)return;s.expanded.bind(function(exp){if(!exp)return;function addCS(u){try{var uuid=api&&api.settings&&api.settings.changeset&&api.settings.changeset.uuid;if(!uuid)return u;var t=new URL(u,window.location.origin);if(!t.searchParams.get('customize_changeset_uuid')){t.searchParams.set('customize_changeset_uuid',uuid);}return t.toString();}catch(e){return u;}}var target=addCS('{$url}');var did=false;if(api&&api.previewer){if(api.previewer.previewUrl&&typeof api.previewer.previewUrl.set==='function'){api.previewer.previewUrl.set(target);did=true;}else if(typeof api.previewer.previewUrl==='function'){api.previewer.previewUrl(target);did=true;}else if(api.previewer.url&&typeof api.previewer.url.set==='function'){api.previewer.url.set(target);did=true;}if(!did){var frame=window.parent&&window.parent.document&&window.parent.document.querySelector('#customize-preview iframe');if(frame){frame.src=target;did=true;}}if(did){setTimeout(function(){try{if(api.previewer.refresh){api.previewer.refresh();}}catch(e){}},100);}try{if(api&&api.has&&api.has('buildpro_preview_page_id')){var cur=parseInt(api('buildpro_preview_page_id').get()||0,10)||0;if(!cur){api('buildpro_preview_page_id').set({$pid});}}}catch(e){}}});}catch(e){}})(wp.customize);</script>";
        });
    }
}
add_action('customize_register', 'buildpro_about_policy_customize_register');

function buildpro_about_policy_sanitize_items($value)
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
            'icon_url' => isset($item['icon_url']) ? esc_url_raw($item['icon_url']) : '',
            'title' => isset($item['title']) ? sanitize_text_field($item['title']) : '',
            'desc' => isset($item['desc']) ? sanitize_textarea_field($item['desc']) : '',
        );
    }
    return array_values($clean);
}
function buildpro_about_policy_sanitize_certs($value)
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
            'image_id' => isset($item['image_id']) ? absint($item['image_id']) : 0,
            'image_url' => isset($item['image_url']) ? esc_url_raw($item['image_url']) : '',
            'url' => isset($item['url']) ? esc_url_raw($item['url']) : '',
            'title' => isset($item['title']) ? sanitize_text_field($item['title']) : '',
            'desc' => isset($item['desc']) ? sanitize_textarea_field($item['desc']) : '',
        );
    }
    return array_values($clean);
}
function buildpro_about_policy_ajax_get_data()
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
            'title_left' => '',
            'business_registration' => '',
            'general_contractor' => '',
            'duns_number' => '',
            'certifications' => array(),
            'title_right' => '',
            'warranty_desc' => '',
            'items' => array(),
        ));
    }
    $enabled = get_post_meta($page_id, 'buildpro_about_policy_enabled', true);
    $enabled = ($enabled === '' ? 1 : (int)$enabled);
    $title_left = get_post_meta($page_id, 'buildpro_about_policy_title_left', true);
    $business_registration = get_post_meta($page_id, 'buildpro_about_policy_business_registration', true);
    $general_contractor = get_post_meta($page_id, 'buildpro_about_policy_general_contractor', true);
    $duns_number = get_post_meta($page_id, 'buildpro_about_policy_duns_number', true);
    $title_right = get_post_meta($page_id, 'buildpro_about_policy_title_right', true);
    $warranty_desc = get_post_meta($page_id, 'buildpro_about_policy_warranty_desc', true);
    $items = get_post_meta($page_id, 'buildpro_about_policy_items', true);
    $items = buildpro_about_policy_sanitize_items(is_array($items) ? $items : array());
    $certs = get_post_meta($page_id, 'buildpro_about_policy_certifications', true);
    $certs = buildpro_about_policy_sanitize_certs(is_array($certs) ? $certs : array());
    foreach ($items as &$it) {
        $iid = isset($it['icon_id']) ? (int)$it['icon_id'] : 0;
        $url = $iid ? wp_get_attachment_image_url($iid, 'thumbnail') : '';
        if (empty($it['icon_url']) && $url) {
            $it['icon_url'] = $url;
        }
    }
    unset($it);
    foreach ($certs as &$c) {
        $iid = isset($c['image_id']) ? (int)$c['image_id'] : 0;
        $url = $iid ? wp_get_attachment_image_url($iid, 'thumbnail') : '';
        if (empty($c['image_url']) && $url) {
            $c['image_url'] = $url;
        }
    }
    unset($c);
    wp_send_json_success(array(
        'enabled' => $enabled,
        'title_left' => is_string($title_left) ? $title_left : '',
        'business_registration' => is_string($business_registration) ? $business_registration : '',
        'general_contractor' => is_string($general_contractor) ? $general_contractor : '',
        'duns_number' => is_string($duns_number) ? $duns_number : '',
        'certifications' => $certs,
        'title_right' => is_string($title_right) ? $title_right : '',
        'warranty_desc' => is_string($warranty_desc) ? $warranty_desc : '',
        'items' => $items,
    ));
}
add_action('wp_ajax_buildpro_get_about_policy', 'buildpro_about_policy_ajax_get_data');

function buildpro_about_policy_sync_customizer_to_meta($wp_customize_manager)
{
    $keys_text = array(
        'buildpro_about_policy_title_left',
        'buildpro_about_policy_business_registration',
        'buildpro_about_policy_general_contractor',
        'buildpro_about_policy_duns_number',
        'buildpro_about_policy_title_right',
        'buildpro_about_policy_warranty_desc',
    );
    $enabled_val = null;
    $vals_text = array();
    $items_val = null;
    $certs_val = null;
    if ($wp_customize_manager instanceof WP_Customize_Manager) {
        $s = $wp_customize_manager->get_setting('buildpro_about_policy_enabled');
        $enabled_val = $s ? $s->post_value() : null;
        foreach ($keys_text as $k) {
            $s = $wp_customize_manager->get_setting($k);
            $vals_text[$k] = $s ? $s->post_value() : null;
        }
        $s = $wp_customize_manager->get_setting('buildpro_about_policy_items');
        $items_val = $s ? $s->post_value() : null;
        $s = $wp_customize_manager->get_setting('buildpro_about_policy_certifications');
        $certs_val = $s ? $s->post_value() : null;
    }
    if ($enabled_val === null) {
        $enabled_val = get_theme_mod('buildpro_about_policy_enabled', 1);
    }
    foreach ($keys_text as $k) {
        if ($vals_text[$k] === null) {
            $vals_text[$k] = get_theme_mod($k, '');
        }
    }
    if ($items_val === null) {
        $items_val = get_theme_mod('buildpro_about_policy_items', array());
    }
    if ($certs_val === null) {
        $certs_val = get_theme_mod('buildpro_about_policy_certifications', array());
    }
    $enabled = absint($enabled_val);
    $items = buildpro_about_policy_sanitize_items($items_val);
    $certs = buildpro_about_policy_sanitize_certs($certs_val);
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
        update_post_meta($page_id, 'buildpro_about_policy_enabled', $enabled);
        foreach ($keys_text as $k) {
            update_post_meta($page_id, $k, is_string($vals_text[$k]) ? $vals_text[$k] : '');
        }
        update_post_meta($page_id, 'buildpro_about_policy_items', $items);
        update_post_meta($page_id, 'buildpro_about_policy_certifications', $certs);
    }
}
add_action('customize_save_after', 'buildpro_about_policy_sync_customizer_to_meta');
