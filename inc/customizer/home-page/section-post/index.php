<?php
if (!function_exists('buildpro_post_customize_register')) {
    function buildpro_post_customize_register($wp_customize)
    {
        if (!class_exists('BuildPro_Post_Control') && class_exists('WP_Customize_Control')) {
            class BuildPro_Post_Control extends WP_Customize_Control
            {
                public $type = 'buildpro_post';
                public function render_content()
                {
                    $data = $this->value();
                    $data = is_array($data) ? $data : array();
                    include get_theme_file_path('template/customize/page/home/section-post/index.php');
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
                    if (empty($this->button_url)) {
                        echo '<span class="customize-control-title">' . esc_html($this->label) . '</span>';
                        echo '<p>' . esc_html__('Không tìm thấy trang Trang chủ dùng template home-page.php', 'buildpro') . '</p>';
                        return;
                    }
                    echo '<span class="customize-control-title">' . esc_html($this->label) . '</span>';
                    if (!empty($this->description)) {
                        echo '<p class="description">' . esc_html($this->description) . '</p>';
                    }
                    $text = $this->button_text ? $this->button_text : __('Mở trang chỉnh sửa', 'buildpro');
                    echo '<a class="button button-primary" href="' . esc_url($this->button_url) . '" target="_blank" rel="noopener">' . esc_html($text) . '</a>';
                }
            }
        }
        $home_id = (int) get_option('page_on_front');
        $edit_url = '';
        if ($home_id) {
            $tpl = get_page_template_slug($home_id);
            if ($tpl === 'home-page.php') {
                $edit_url = admin_url('post.php?post=' . $home_id . '&action=edit');
            }
        }
        if (!$edit_url) {
            $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'home-page.php', 'number' => 1));
            if (!empty($pages)) {
                $p = $pages[0];
                $edit_url = admin_url('post.php?post=' . $p->ID . '&action=edit');
            }
        }
        if (!function_exists('buildpro_customizer_is_home_preview')) {
            function buildpro_customizer_is_home_preview()
            {
                $selected_id = 0;
                global $wp_customize;
                if ($wp_customize && $wp_customize instanceof WP_Customize_Manager) {
                    $setting = $wp_customize->get_setting('buildpro_preview_page_id');
                    if ($setting) {
                        $val = $setting->value();
                        $selected_id = absint($val);
                    }
                }
                if ($selected_id <= 0) {
                    $selected_id = (int) get_option('page_on_front');
                }
                if ($selected_id > 0) {
                    $tpl = get_page_template_slug($selected_id);
                    if ($tpl && $tpl !== '') {
                        if ($tpl === 'home-page.php') {
                            return true;
                        }
                    }
                    $front = (int) get_option('page_on_front');
                    if ($front && $selected_id === $front) {
                        return true;
                    }
                }
                return false;
            }
        }
        $wp_customize->add_section('buildpro_post_section', array(
            'title' => __('Home Page: Post', 'buildpro'),
            'priority' => 40,
            'active_callback' => 'buildpro_customizer_is_home_preview',
        ));
        $wp_customize->add_setting('buildpro_post_data', array(
            'default' => buildpro_post_get_default_data(),
            'transport' => 'postMessage',
            'sanitize_callback' => 'buildpro_post_sanitize_data',
        ));
        $wp_customize->add_setting('buildpro_post_enabled', array(
            'default' => 1,
            'transport' => 'refresh',
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control('buildpro_post_enabled', array(
            'label' => __('Enable Post', 'buildpro'),
            'section' => 'buildpro_post_section',
            'type' => 'checkbox',
        ));
        if (class_exists('BuildPro_Post_Control')) {
            $wp_customize->add_control(new BuildPro_Post_Control($wp_customize, 'buildpro_post_data', array(
                'label' => __('Post Section Content', 'buildpro'),
                'description' => __('Edit Post section content for Front Page.', 'buildpro'),
                'section' => 'buildpro_post_section',
            )));
        }
        $wp_customize->add_setting('buildpro_post_edit_link', array(
            'default' => '',
            'transport' => 'postMessage',
            'sanitize_callback' => 'esc_url_raw',
        ));
        // if (class_exists('BuildPro_Customize_Button_Control')) {
        //     $wp_customize->add_control(new BuildPro_Customize_Button_Control($wp_customize, 'buildpro_post_edit_link', array(
        //         'label' => __('Edit Post Section', 'buildpro'),
        //         'description' => __('Open the Front Page editor.', 'buildpro'),
        //         'section' => 'buildpro_post_section',
        //         'button_url' => $edit_url,
        //         'button_text' => __('Edit Front Page', 'buildpro'),
        //     )));
        // }
        if (isset($wp_customize->selective_refresh)) {
            $wp_customize->selective_refresh->add_partial('buildpro_post_data', array(
                'selector' => '.section-post',
                'settings' => array('buildpro_post_data'),
                'container_inclusive' => true,
                'render_callback' => function () {
                    ob_start();
                    get_template_part('template/template-parts/page/home/section-post/index');
                    return ob_get_clean();
                },
            ));
        }
    }
} // end if !function_exists buildpro_post_customize_register
add_action('customize_register', 'buildpro_post_customize_register');
if (!function_exists('buildpro_post_enqueue_assets')) {
    function buildpro_post_enqueue_assets()
    {
        wp_enqueue_style('buildpro-post-style', get_theme_file_uri('template/customize/page/home/section-post/style.css'), array(), null);
        wp_enqueue_script('buildpro-post-script', get_theme_file_uri('template/customize/page/home/section-post/script.js'), array('customize-controls'), null, true);
    }
} // end if !function_exists buildpro_post_enqueue_assets
add_action('customize_controls_enqueue_scripts', 'buildpro_post_enqueue_assets');
if (!function_exists('buildpro_post_find_home_id')) {
    function buildpro_post_find_home_id()
    {
        $selected = 0;
        global $wp_customize;
        if ($wp_customize && $wp_customize instanceof WP_Customize_Manager) {
            $setting = $wp_customize->get_setting('buildpro_preview_page_id');
            if ($setting) {
                $val = $setting->value();
                $selected = absint($val);
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
        $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'home-page.php', 'number' => 1));
        if (!empty($pages)) {
            return (int) $pages[0]->ID;
        }
        return 0;
    }
} // end if !function_exists buildpro_post_find_home_id
if (!function_exists('buildpro_post_get_default_data')) {
    function buildpro_post_get_default_data()
    {
        $page_id = 0;
        global $wp_customize;
        if ($wp_customize && $wp_customize instanceof WP_Customize_Manager) {
            $setting = $wp_customize->get_setting('buildpro_preview_page_id');
            if ($setting) {
                $page_id = absint($setting->value());
            }
        }
        if ($page_id <= 0) {
            $page_id = buildpro_post_find_home_id();
        }
        $title = '';
        $desc = '';
        if ($page_id) {
            $title = get_post_meta($page_id, 'title_post', true);
            $desc = get_post_meta($page_id, 'description_post', true);
        }
        return array('title' => $title, 'desc' => $desc);
    }
} // end if !function_exists buildpro_post_get_default_data
if (!function_exists('buildpro_post_sanitize_data')) {
    function buildpro_post_sanitize_data($value)
    {
        $out = array('title' => '', 'desc' => '');
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $value = $decoded;
            }
        }
        if (!is_array($value)) {
            return $out;
        }
        $out['title'] = isset($value['title']) ? sanitize_text_field($value['title']) : '';
        $out['desc'] = isset($value['desc']) ? sanitize_textarea_field($value['desc']) : '';
        return $out;
    }
} // end if !function_exists buildpro_post_sanitize_data
if (!function_exists('buildpro_post_sync_customizer_to_meta')) {
    function buildpro_post_sync_customizer_to_meta($wp_customize_manager)
    {
        $data = get_theme_mod('buildpro_post_data', array());
        $data = buildpro_post_sanitize_data($data);
        $title = isset($data['title']) ? $data['title'] : '';
        $desc  = isset($data['desc']) ? $data['desc'] : '';
        $page_id = 0;
        if ($wp_customize_manager instanceof WP_Customize_Manager) {
            $setting = $wp_customize_manager->get_setting('buildpro_preview_page_id');
            if ($setting) {
                $page_id = absint($setting->value());
            }
        }
        $targets = array();
        if ($page_id > 0) {
            $targets[] = $page_id;
        }
        $home = buildpro_post_find_home_id();
        if ($home > 0) {
            $targets[] = $home;
        }
        if (!empty($targets)) {
            $targets = array_unique(array_filter(array_map('absint', $targets)));
            foreach ($targets as $tid) {
                update_post_meta($tid, 'title_post', $title);
                update_post_meta($tid, 'description_post', $desc);
                update_post_meta($tid, 'buildpro_post_enabled', absint(get_theme_mod('buildpro_post_enabled', 1)));
            }
            set_theme_mod('title_post', $title);
            set_theme_mod('description_post', $desc);
            set_theme_mod('buildpro_post_enabled', absint(get_theme_mod('buildpro_post_enabled', 1)));
        }
    }
} // end if !function_exists buildpro_post_sync_customizer_to_meta
add_action('customize_save_after', 'buildpro_post_sync_customizer_to_meta');
