<?php
function buildpro_about_leader_customize_register($wp_customize)
{
    if (!class_exists('BuildPro_About_Leader_Repeater_Control') && class_exists('WP_Customize_Control')) {
        class BuildPro_About_Leader_Repeater_Control extends WP_Customize_Control
        {
            public $type = 'buildpro_about_leader_repeater';
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
                    if ($pid > 0) {
                        $meta_items = get_post_meta($pid, 'buildpro_about_leader_items', true);
                        if (is_array($meta_items) && !empty($meta_items)) {
                            $items = array_values($meta_items);
                            foreach ($items as &$it) {
                                $iid = isset($it['icon_id']) ? (int)$it['icon_id'] : 0;
                                $url = $iid ? wp_get_attachment_image_url($iid, 'thumbnail') : '';
                                if (empty($it['icon_url']) && $url) {
                                    $it['icon_url'] = $url;
                                }
                            }
                            unset($it);
                        }
                    }
                }
                $items = is_array($items) ? $items : array();
                echo '<span class="customize-control-title">' . esc_html($this->label) . '</span>';
                if (!empty($this->description)) {
                    echo '<p class="description">' . esc_html($this->description) . '</p>';
                }
                include get_theme_file_path('template/customize/page/about-us/section-leader/index.php');
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
            if ($selected_id <= 0 && (!empty($pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => 1))) || !empty($pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-us-page.php', 'number' => 1))))) {
                return true;
            }
            return false;
        }
    }
    $wp_customize->add_section('buildpro_about_leader_section', array(
        'title' => __('About Us: Leader', 'buildpro'),
        'priority' => 32,
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
        $en_meta = get_post_meta($ed_pid, 'buildpro_about_leader_enabled', true);
        if ($en_meta !== '') {
            $enabled_default = (int) $en_meta;
        }
    }
    $wp_customize->add_setting('buildpro_about_leader_enabled', array(
        'default' => $enabled_default,
        'transport' => 'refresh',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('buildpro_about_leader_enabled', array(
        'label' => __('Enable Leader', 'buildpro'),
        'section' => 'buildpro_about_leader_section',
        'type' => 'checkbox',
    ));
    $wp_customize->add_setting('buildpro_about_leader_edit_link', array(
        'default' => '',
        'transport' => 'postMessage',
        'sanitize_callback' => 'esc_url_raw',
    ));
    if (class_exists('BuildPro_Customize_Button_Control')) {
        $wp_customize->add_control(new BuildPro_Customize_Button_Control($wp_customize, 'buildpro_about_leader_edit_link', array(
            'label' => __('Edit About Us Page', 'buildpro'),
            'description' => __('Open the About Us page to edit meta box.', 'buildpro'),
            'section' => 'buildpro_about_leader_section',
            'button_url' => $edit_url,
            'button_text' => __('Edit About Us', 'buildpro'),
        )));
    }
    $title_default = 'Meet Our Leaders';
    $text_default = '';
    $executives_default = '';
    $workforce_default = '';
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
        $t = get_post_meta($default_page_id, 'buildpro_about_leader_title', true);
        $txt = get_post_meta($default_page_id, 'buildpro_about_leader_text', true);
        $exe = get_post_meta($default_page_id, 'buildpro_about_leader_executives', true);
        $wrk = get_post_meta($default_page_id, 'buildpro_about_leader_workforce', true);
        if (is_string($t) && $t !== '') {
            $title_default = $t;
        }
        if (is_string($txt) && $txt !== '') {
            $text_default = $txt;
        }
        if (is_string($exe) && $exe !== '') {
            $executives_default = $exe;
        }
        if (is_string($wrk) && $wrk !== '') {
            $workforce_default = $wrk;
        }
    }
    $wp_customize->add_setting('buildpro_about_leader_title', array(
        'default' => $title_default,
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('buildpro_about_leader_title', array(
        'label' => __('Title', 'buildpro'),
        'section' => 'buildpro_about_leader_section',
        'type' => 'text',
    ));
    $wp_customize->add_setting('buildpro_about_leader_text', array(
        'default' => $text_default,
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('buildpro_about_leader_text', array(
        'label' => __('Text', 'buildpro'),
        'section' => 'buildpro_about_leader_section',
        'type' => 'text',
    ));
    $wp_customize->add_setting('buildpro_about_leader_executives', array(
        'default' => $executives_default,
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('buildpro_about_leader_executives', array(
        'label' => __('Core Executives', 'buildpro'),
        'section' => 'buildpro_about_leader_section',
        'type' => 'text',
    ));
    $wp_customize->add_setting('buildpro_about_leader_workforce', array(
        'default' => $workforce_default,
        'transport' => 'postMessage',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('buildpro_about_leader_workforce', array(
        'label' => __('Total Workforce', 'buildpro'),
        'section' => 'buildpro_about_leader_section',
        'type' => 'text',
    ));
    $items_default = array();
    if ($default_page_id) {
        $meta_items = get_post_meta($default_page_id, 'buildpro_about_leader_items', true);
        if (is_array($meta_items)) {
            $items_default = $meta_items;
        }
    }
    // Always sync post_meta → theme_mod so customizer reflects current meta-box data.
    if ($default_page_id > 0) {
        set_theme_mod('buildpro_about_leader_enabled', $enabled_default);
        set_theme_mod('buildpro_about_leader_title', $title_default);
        set_theme_mod('buildpro_about_leader_text', $text_default);
        set_theme_mod('buildpro_about_leader_executives', $executives_default);
        set_theme_mod('buildpro_about_leader_workforce', $workforce_default);
        set_theme_mod('buildpro_about_leader_items', $items_default);
    }
    $wp_customize->add_setting('buildpro_about_leader_items', array(
        'default' => $items_default,
        'transport' => 'postMessage',
        'sanitize_callback' => 'buildpro_about_leader_sanitize_items',
    ));
    if (class_exists('BuildPro_About_Leader_Repeater_Control')) {
        $wp_customize->add_control(new BuildPro_About_Leader_Repeater_Control($wp_customize, 'buildpro_about_leader_items', array(
            'label' => __('Leader Items', 'buildpro'),
            'description' => __('Manage leader items (image, name, position, description, URL).', 'buildpro'),
            'section' => 'buildpro_about_leader_section',
        )));
    }
    if (isset($wp_customize->selective_refresh)) {
        $wp_customize->selective_refresh->add_partial('buildpro_about_leader_partial', array(
            'selector' => '.about-leader',
            'settings' => array('buildpro_about_leader_enabled', 'buildpro_about_leader_title', 'buildpro_about_leader_text', 'buildpro_about_leader_executives', 'buildpro_about_leader_workforce', 'buildpro_about_leader_items'),
            'render_callback' => function () {
                ob_start();
                get_template_part('template/template-parts/page/about-us/section-leader/index');
                return ob_get_clean();
            },
        ));
    }
    add_action('customize_controls_enqueue_scripts', function () {
        wp_enqueue_style(
            'buildpro-about-leader-style',
            get_theme_file_uri('template/customize/page/about-us/section-leader/style.css'),
            array(),
            null
        );
        wp_enqueue_script(
            'buildpro-about-leader-script',
            get_theme_file_uri('template/customize/page/about-us/section-leader/script.js'),
            array('customize-controls', 'jquery'),
            null,
            true
        );

        if (function_exists('buildpro_about_us_add_inline_i18n')) {
            buildpro_about_us_add_inline_i18n('buildpro-about-leader-script');
        }
        $default_about = 0;
        $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => 1));
        if (!empty($pages)) {
            $default_about = (int) $pages[0]->ID;
        }
        if ($default_about <= 0) {
            $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-us-page.php', 'number' => 1));
            if (!empty($pages)) {
                $default_about = (int) $pages[0]->ID;
            }
        }
        wp_localize_script('buildpro-about-leader-script', 'BuildProAboutLeader', array(
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
            echo "<script>(function(api){try{var s=api&&api.section&&api.section('buildpro_about_leader_section');if(!s)return;s.expanded.bind(function(exp){if(!exp)return;function addCS(u){try{var uuid=api&&api.settings&&api.settings.changeset&&api.settings.changeset.uuid;if(!uuid)return u;var t=new URL(u,window.location.origin);if(!t.searchParams.get('customize_changeset_uuid')){t.searchParams.set('customize_changeset_uuid',uuid);}return t.toString();}catch(e){return u;}}var target=addCS('{$url}');var did=false;if(api&&api.previewer){if(api.previewer.previewUrl&&typeof api.previewer.previewUrl.set==='function'){api.previewer.previewUrl.set(target);did=true;}else if(typeof api.previewer.previewUrl==='function'){api.previewer.previewUrl(target);did=true;}else if(api.previewer.url&&typeof api.previewer.url.set==='function'){api.previewer.url.set(target);did=true;}if(!did){var frame=window.parent&&window.parent.document&&window.parent.document.querySelector('#customize-preview iframe');if(frame){frame.src=target;did=true;}}if(did){setTimeout(function(){try{if(api.previewer.refresh){api.previewer.refresh();}}catch(e){}},100);}try{if(api&&api.has&&api.has('buildpro_preview_page_id')){var cur=parseInt(api('buildpro_preview_page_id').get()||0,10)||0;if(!cur){api('buildpro_preview_page_id').set({$pid});}}}catch(e){}}});}catch(e){}})(wp.customize);</script>";
        });
    }
}
add_action('customize_register', 'buildpro_about_leader_customize_register');

function buildpro_about_leader_sanitize_items($value)
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
            'name' => isset($item['name']) ? sanitize_text_field($item['name']) : '',
            'position' => isset($item['position']) ? sanitize_text_field($item['position']) : '',
            'description' => isset($item['description']) ? sanitize_text_field($item['description']) : '',
            'url' => isset($item['url']) ? esc_url_raw($item['url']) : '',
        );
    }
    return array_values($clean);
}

function buildpro_about_leader_ajax_get_data()
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
        wp_send_json_success(array('enabled' => 1, 'title' => '', 'text' => '', 'executives' => '', 'workforce' => '', 'items' => array()));
    }
    $enabled = get_post_meta($page_id, 'buildpro_about_leader_enabled', true);
    $enabled = ($enabled === '' ? 1 : (int) $enabled);
    $title = get_post_meta($page_id, 'buildpro_about_leader_title', true);
    $text = get_post_meta($page_id, 'buildpro_about_leader_text', true);
    $executives = get_post_meta($page_id, 'buildpro_about_leader_executives', true);
    $workforce = get_post_meta($page_id, 'buildpro_about_leader_workforce', true);
    $items = get_post_meta($page_id, 'buildpro_about_leader_items', true);
    if (!is_array($items)) {
        $items = array();
    }
    $items = buildpro_about_leader_sanitize_items($items);
    foreach ($items as &$it) {
        $iid = isset($it['icon_id']) ? (int)$it['icon_id'] : 0;
        $url = $iid ? wp_get_attachment_image_url($iid, 'thumbnail') : '';
        if (empty($it['icon_url']) && $url) {
            $it['icon_url'] = $url;
        }
    }
    unset($it);
    wp_send_json_success(array(
        'enabled' => $enabled,
        'title' => is_string($title) ? $title : '',
        'text' => is_string($text) ? $text : '',
        'executives' => is_string($executives) ? $executives : '',
        'workforce' => is_string($workforce) ? $workforce : '',
        'items' => $items,
    ));
}
add_action('wp_ajax_buildpro_get_about_leader', 'buildpro_about_leader_ajax_get_data');

function buildpro_about_leader_sync_customizer_to_meta($wp_customize_manager)
{
    $enabled_val = null;
    $title_val = null;
    $text_val = null;
    $executives_val = null;
    $workforce_val = null;
    $items_val = null;
    if ($wp_customize_manager instanceof WP_Customize_Manager) {
        $s = $wp_customize_manager->get_setting('buildpro_about_leader_enabled');
        $enabled_val = $s ? $s->post_value() : null;
        $s = $wp_customize_manager->get_setting('buildpro_about_leader_title');
        $title_val = $s ? $s->post_value() : null;
        $s = $wp_customize_manager->get_setting('buildpro_about_leader_text');
        $text_val = $s ? $s->post_value() : null;
        $s = $wp_customize_manager->get_setting('buildpro_about_leader_executives');
        $executives_val = $s ? $s->post_value() : null;
        $s = $wp_customize_manager->get_setting('buildpro_about_leader_workforce');
        $workforce_val = $s ? $s->post_value() : null;
        $s = $wp_customize_manager->get_setting('buildpro_about_leader_items');
        $items_val = $s ? $s->post_value() : null;
    }
    if ($enabled_val === null) {
        $enabled_val = get_theme_mod('buildpro_about_leader_enabled', 1);
    }
    if ($title_val === null) {
        $title_val = get_theme_mod('buildpro_about_leader_title', '');
    }
    if ($text_val === null) {
        $text_val = get_theme_mod('buildpro_about_leader_text', '');
    }
    if ($executives_val === null) {
        $executives_val = get_theme_mod('buildpro_about_leader_executives', '');
    }
    if ($workforce_val === null) {
        $workforce_val = get_theme_mod('buildpro_about_leader_workforce', '');
    }
    if ($items_val === null) {
        $items_val = get_theme_mod('buildpro_about_leader_items', array());
    }
    $enabled = absint($enabled_val);
    $title = is_string($title_val) ? $title_val : '';
    $text = is_string($text_val) ? $text_val : '';
    $executives = is_string($executives_val) ? $executives_val : '';
    $workforce = is_string($workforce_val) ? $workforce_val : '';
    $items = buildpro_about_leader_sanitize_items($items_val);
    $page_id = 0;
    $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-us-page.php', 'number' => 1));
    if (!empty($pages)) {
        $page_id = (int) $pages[0]->ID;
    }
    if ($page_id <= 0) {
        $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => 1));
        if (!empty($pages)) {
            $page_id = (int) $pages[0]->ID;
        }
    }
    if ($page_id) {
        update_post_meta($page_id, 'buildpro_about_leader_enabled', $enabled);
        update_post_meta($page_id, 'buildpro_about_leader_title', $title);
        update_post_meta($page_id, 'buildpro_about_leader_text', $text);
        update_post_meta($page_id, 'buildpro_about_leader_executives', $executives);
        update_post_meta($page_id, 'buildpro_about_leader_workforce', $workforce);
        update_post_meta($page_id, 'buildpro_about_leader_items', $items);
    }
}
add_action('customize_save_after', 'buildpro_about_leader_sync_customizer_to_meta');
