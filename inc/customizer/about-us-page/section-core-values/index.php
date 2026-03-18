<?php
if (!function_exists('buildpro_about_core_values_customize_register')) {
    function buildpro_about_core_values_customize_register($wp_customize)
    {
        if (!class_exists('BuildPro_About_CoreValues_Repeater_Control') && class_exists('WP_Customize_Control')) {
            class BuildPro_About_CoreValues_Repeater_Control extends WP_Customize_Control
            {
                public $type = 'buildpro_about_core_values_repeater';
                public function render_content()
                {
                    $items = $this->value();
                    if (!is_array($items) || empty($items)) {
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
                                $pid = (int) $pages[0]->ID;
                            }
                        }
                        if ($pid <= 0) {
                            $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-us-page.php', 'number' => 1));
                            if (!empty($pages)) {
                                $pid = (int) $pages[0]->ID;
                            }
                        }
                        if ($pid > 0) {
                            $meta_items = get_post_meta($pid, 'buildpro_about_core_values_items', true);
                            if (is_array($meta_items) && !empty($meta_items)) {
                                $items = array_values($meta_items);
                            }
                        }
                    }
                    $items = is_array($items) ? $items : array();
                    echo '<span class="customize-control-title">' . esc_html($this->label) . '</span>';
                    if (!empty($this->description)) {
                        echo '<p class="description">' . esc_html($this->description) . '</p>';
                    }
                    include get_theme_file_path('template/customize/page/about-us/section-core-values/index.php');
                    return;
                }
            }
        }
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
                if ($selected_id <= 0 && !empty($pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => 1)))) {
                    return true;
                }
                if ($selected_id <= 0 && !empty($pages2 = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-us-page.php', 'number' => 1)))) {
                    return true;
                }
                return false;
            }
        }
        $wp_customize->add_section('buildpro_about_core_values_section', array(
            'title' => __('About Us: Core Values', 'buildpro'),
            'priority' => 31,
            'active_callback' => 'buildpro_customizer_is_about_preview',
        ));
        $enabled_default = 1;
        $ed_pid = 0;
        if (isset($wp_customize) && $wp_customize instanceof WP_Customize_Manager) {
            $sel = $wp_customize->get_setting('buildpro_preview_page_id');
            if ($sel) {
                $ed_pid = absint($sel->value());
            }
        }
        if ($ed_pid <= 0) {
            $ed_pid = $about_id;
        }
        if ($ed_pid) {
            $en_meta = get_post_meta($ed_pid, 'buildpro_about_core_values_enabled', true);
            if ($en_meta !== '') {
                $enabled_default = (int) $en_meta;
            }
        }
        $wp_customize->add_setting('buildpro_about_core_values_enabled', array(
            'default' => $enabled_default,
            'transport' => 'refresh',
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control('buildpro_about_core_values_enabled', array(
            'label' => __('Enable Core Values', 'buildpro'),
            'section' => 'buildpro_about_core_values_section',
            'type' => 'checkbox',
        ));
        $wp_customize->add_setting('buildpro_about_core_values_edit_link', array(
            'default' => '',
            'transport' => 'postMessage',
            'sanitize_callback' => 'esc_url_raw',
        ));
        if (class_exists('BuildPro_Customize_Button_Control')) {
            $wp_customize->add_control(new BuildPro_Customize_Button_Control($wp_customize, 'buildpro_about_core_values_edit_link', array(
                'label' => __('Edit About Us Page', 'buildpro'),
                'description' => __('Open the About Us page to edit meta box.', 'buildpro'),
                'section' => 'buildpro_about_core_values_section',
                'button_url' => $edit_url,
                'button_text' => __('Edit About Us', 'buildpro'),
            )));
        }
        $title_default = 'CORE VALUES';
        $desc_default = '';
        $default_page_id = 0;
        if (isset($wp_customize) && $wp_customize instanceof WP_Customize_Manager) {
            $sel = $wp_customize->get_setting('buildpro_preview_page_id');
            if ($sel) {
                $default_page_id = absint($sel->value());
            }
        }
        if ($default_page_id <= 0) {
            $default_page_id = $about_id;
        }
        if ($default_page_id) {
            $t = get_post_meta($default_page_id, 'buildpro_about_core_values_title', true);
            $d = get_post_meta($default_page_id, 'buildpro_about_core_values_description', true);
            if (is_string($t) && $t !== '') {
                $title_default = $t;
            }
            if (is_string($d) && $d !== '') {
                $desc_default = $d;
            }
        }
        $wp_customize->add_setting('buildpro_about_core_values_title', array(
            'default' => $title_default,
            'transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control('buildpro_about_core_values_title', array(
            'label' => __('Title', 'buildpro'),
            'section' => 'buildpro_about_core_values_section',
            'type' => 'text',
        ));
        $wp_customize->add_setting('buildpro_about_core_values_description', array(
            'default' => $desc_default,
            'transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_textarea_field',
        ));
        $wp_customize->add_control('buildpro_about_core_values_description', array(
            'label' => __('Description', 'buildpro'),
            'section' => 'buildpro_about_core_values_section',
            'type' => 'textarea',
        ));
        $items_default = array();
        if ($default_page_id) {
            $meta_items = get_post_meta($default_page_id, 'buildpro_about_core_values_items', true);
            if (is_array($meta_items)) {
                $items_default = $meta_items;
            }
        }
        // Always sync post_meta → theme_mod so customizer reflects current meta-box data.
        // This mirrors what section-banner does for facts/title/desc.
        if ($default_page_id > 0) {
            if ($title_default !== 'CORE VALUES') {
                set_theme_mod('buildpro_about_core_values_title', $title_default);
            }
            if ($desc_default !== '') {
                set_theme_mod('buildpro_about_core_values_description', $desc_default);
            }
            if (!empty($items_default)) {
                set_theme_mod('buildpro_about_core_values_items', $items_default);
            }
        }
        $wp_customize->add_setting('buildpro_about_core_values_items', array(
            'default' => $items_default,
            'transport' => 'postMessage',
            'sanitize_callback' => 'buildpro_about_core_values_sanitize_items',
        ));
        if (class_exists('BuildPro_About_CoreValues_Repeater_Control')) {
            $wp_customize->add_control(new BuildPro_About_CoreValues_Repeater_Control($wp_customize, 'buildpro_about_core_values_items', array(
                'label' => __('Core Values Items', 'buildpro'),
                'description' => __('Manage core values items (icon, title, description, URL).', 'buildpro'),
                'section' => 'buildpro_about_core_values_section',
            )));
        }
        if (isset($wp_customize->selective_refresh)) {
            $wp_customize->selective_refresh->add_partial('buildpro_about_core_values_partial', array(
                'selector' => '.about-core-values',
                'settings' => array('buildpro_about_core_values_enabled', 'buildpro_about_core_values_title', 'buildpro_about_core_values_description', 'buildpro_about_core_values_items'),
                'render_callback' => function () {
                    ob_start();
                    get_template_part('template/template-parts/page/about-us/section-core-values/index');
                    return ob_get_clean();
                },
            ));
        }
        add_action('customize_controls_enqueue_scripts', function () {
            wp_enqueue_style(
                'buildpro-about-core-values-style',
                get_theme_file_uri('template/customize/page/about-us/section-core-values/style.css'),
                array(),
                null
            );
            wp_enqueue_script(
                'buildpro-about-core-values-script',
                get_theme_file_uri('template/customize/page/about-us/section-core-values/script.js'),
                array('customize-controls', 'jquery'),
                null,
                true
            );

            if (function_exists('buildpro_about_us_add_inline_i18n')) {
                buildpro_about_us_add_inline_i18n('buildpro-about-core-values-script');
            }
            $default_about = 0;
            $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => 1));
            if (!empty($pages)) {
                $default_about = (int) $pages[0]->ID;
            }
            if (!$default_about) {
                $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-us-page.php', 'number' => 1));
                if (!empty($pages)) {
                    $default_about = (int) $pages[0]->ID;
                }
            }
            wp_localize_script('buildpro-about-core-values-script', 'BuildProAboutCoreValues', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('buildpro_customizer_nonce'),
                'default_page_id' => $default_about,
            ));
        });
        $pages_about = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => 1));
        $about_preview_url = '';
        if (!empty($pages_about)) {
            $p = $pages_about[0];
            $about_preview_url = get_permalink($p->ID);
        }
        if ($about_preview_url && $about_id) {
            add_action('customize_controls_print_footer_scripts', function () use ($about_preview_url, $about_id) {
                $url = esc_js($about_preview_url);
                $pid = (int) $about_id;
                echo "<script>(function(api){try{var s=api&&api.section&&api.section('buildpro_about_core_values_section');if(!s)return;s.expanded.bind(function(exp){if(!exp)return;function addCS(u){try{var uuid=api&&api.settings&&api.settings.changeset&&api.settings.changeset.uuid;if(!uuid)return u;var t=new URL(u,window.location.origin);if(!t.searchParams.get('customize_changeset_uuid')){t.searchParams.set('customize_changeset_uuid',uuid);}return t.toString();}catch(e){return u;}}var target=addCS('{$url}');var did=false;if(api&&api.previewer){if(api.previewer.previewUrl&&typeof api.previewer.previewUrl.set==='function'){api.previewer.previewUrl.set(target);did=true;}else if(typeof api.previewer.previewUrl==='function'){api.previewer.previewUrl(target);did=true;}else if(api.previewer.url&&typeof api.previewer.url.set==='function'){api.previewer.url.set(target);did=true;}if(!did){var frame=window.parent&&window.parent.document&&window.parent.document.querySelector('#customize-preview iframe');if(frame){frame.src=target;did=true;}}if(did){setTimeout(function(){try{if(api.previewer.refresh){api.previewer.refresh();}}catch(e){}},100);}try{if(api&&api.has&&api.has('buildpro_preview_page_id')){var cur=parseInt(api('buildpro_preview_page_id').get()||0,10)||0;if(!cur){api('buildpro_preview_page_id').set({$pid});}}}catch(e){}}});}catch(e){}})(wp.customize);</script>";
            });
        }
    } // end if !function_exists buildpro_about_core_values_customize_register
    add_action('customize_register', 'buildpro_about_core_values_customize_register');

    if (!function_exists('buildpro_about_core_values_sanitize_items')) {
        function buildpro_about_core_values_sanitize_items($value)
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
                    'icon' => isset($item['icon']) ? sanitize_text_field($item['icon']) : '',
                    'title' => isset($item['title']) ? sanitize_text_field($item['title']) : '',
                    'description' => isset($item['description']) ? sanitize_textarea_field($item['description']) : '',
                    'url' => isset($item['url']) ? esc_url_raw($item['url']) : '',
                );
            }
            return array_values($clean);
        }
    } // end if !function_exists buildpro_about_core_values_sanitize_items

    if (!function_exists('buildpro_about_core_values_ajax_get_data')) {
        function buildpro_about_core_values_ajax_get_data()
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
                    $page_id = (int) $pages[0]->ID;
                }
            }
            if ($page_id <= 0) {
                $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-us-page.php', 'number' => 1));
                if (!empty($pages)) {
                    $page_id = (int) $pages[0]->ID;
                }
            }
            if ($page_id <= 0) {
                wp_send_json_success(array('enabled' => 1, 'title' => '', 'description' => '', 'items' => array()));
            }
            $enabled = get_post_meta($page_id, 'buildpro_about_core_values_enabled', true);
            $enabled = ($enabled === '' ? 1 : (int) $enabled);
            $title = get_post_meta($page_id, 'buildpro_about_core_values_title', true);
            $desc = get_post_meta($page_id, 'buildpro_about_core_values_description', true);
            $items = get_post_meta($page_id, 'buildpro_about_core_values_items', true);
            if (!is_array($items)) {
                $items = array();
            }
            $items = buildpro_about_core_values_sanitize_items($items);
            wp_send_json_success(array(
                'enabled' => $enabled,
                'title' => is_string($title) ? $title : '',
                'description' => is_string($desc) ? $desc : '',
                'items' => $items,
            ));
        }
    } // end if !function_exists buildpro_about_core_values_ajax_get_data
    add_action('wp_ajax_buildpro_get_about_core_values', 'buildpro_about_core_values_ajax_get_data');

    if (!function_exists('buildpro_about_core_values_sync_customizer_to_meta')) {
        function buildpro_about_core_values_sync_customizer_to_meta($wp_customize_manager)
        {
            $enabled_val = null;
            $title_val = null;
            $desc_val = null;
            $items_val = null;
            if ($wp_customize_manager instanceof WP_Customize_Manager) {
                $s = $wp_customize_manager->get_setting('buildpro_about_core_values_enabled');
                $enabled_val = $s ? $s->post_value() : null;
                $s = $wp_customize_manager->get_setting('buildpro_about_core_values_title');
                $title_val = $s ? $s->post_value() : null;
                $s = $wp_customize_manager->get_setting('buildpro_about_core_values_description');
                $desc_val = $s ? $s->post_value() : null;
                $s = $wp_customize_manager->get_setting('buildpro_about_core_values_items');
                $items_val = $s ? $s->post_value() : null;
            }
            // After customize_save, theme_mods hold the freshly saved values.
            // Use get_theme_mod() with false default so we only update post_meta for
            // fields that were actually saved (and never accidentally clear items).
            if ($enabled_val === null) {
                $enabled_val = get_theme_mod('buildpro_about_core_values_enabled');
            }
            if ($title_val === null) {
                $title_val = get_theme_mod('buildpro_about_core_values_title');
            }
            if ($desc_val === null) {
                $desc_val = get_theme_mod('buildpro_about_core_values_description');
            }
            if ($items_val === null) {
                $items_val = get_theme_mod('buildpro_about_core_values_items');
            }
            $page_id = 0;
            if ($wp_customize_manager instanceof WP_Customize_Manager) {
                $setting = $wp_customize_manager->get_setting('buildpro_preview_page_id');
                if ($setting) {
                    $page_id = absint($setting->value());
                }
            }
            if ($page_id <= 0) {
                $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => 1));
                if (!empty($pages)) {
                    $page_id = (int) $pages[0]->ID;
                }
            }
            if ($page_id <= 0) {
                $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-us-page.php', 'number' => 1));
                if (!empty($pages)) {
                    $page_id = (int) $pages[0]->ID;
                }
            }
            if ($page_id) {
                if ($enabled_val !== false && $enabled_val !== null) {
                    update_post_meta($page_id, 'buildpro_about_core_values_enabled', absint($enabled_val));
                }
                if ($title_val !== false && $title_val !== null) {
                    update_post_meta($page_id, 'buildpro_about_core_values_title', sanitize_text_field((string) $title_val));
                }
                if ($desc_val !== false && $desc_val !== null) {
                    update_post_meta($page_id, 'buildpro_about_core_values_description', sanitize_textarea_field((string) $desc_val));
                }
                if ($items_val !== false && $items_val !== null) {
                    update_post_meta($page_id, 'buildpro_about_core_values_items', buildpro_about_core_values_sanitize_items($items_val));
                }
            }
        }
    } // end if !function_exists buildpro_about_core_values_sync_customizer_to_met
}
add_action('customize_save_after', 'buildpro_about_core_values_sync_customizer_to_meta');
