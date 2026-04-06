<?php
if (!function_exists('buildpro_portfolio_customize_register')) {
    function buildpro_portfolio_customize_register($wp_customize)
    {
        if (!class_exists('BuildPro_Portfolio_Control') && class_exists('WP_Customize_Control')) {
            class BuildPro_Portfolio_Control extends WP_Customize_Control
            {
                public $type = 'buildpro_portfolio';
                public function render_content()
                {
                    $data = $this->value();
                    $data = is_array($data) ? $data : array();
                    echo '<span class="customize-control-title">' . esc_html($this->label) . '</span>';
                    if (!empty($this->description)) {
                        echo '<p class="description">' . esc_html($this->description) . '</p>';
                    }
                    include get_theme_file_path('template/customize/page/home/section-projects/index.php');
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
                        echo '<p>' . esc_html__('Could not find a Home page using template home-page.php.', 'buildpro') . '</p>';
                        return;
                    }
                    echo '<span class="customize-control-title">' . esc_html($this->label) . '</span>';
                    if (!empty($this->description)) {
                        echo '<p class="description">' . esc_html($this->description) . '</p>';
                    }
                    $text = $this->button_text ? $this->button_text : __('Open edit page', 'buildpro');
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
        $wp_customize->add_section('buildpro_portfolio_section', array(
            'title' => __('Home Page: Portfolio', 'buildpro'),
            'priority' => 35,
            'active_callback' => 'buildpro_customizer_is_home_preview',
        ));
        $wp_customize->add_setting('buildpro_portfolio_enabled', array(
            'default' => 1,
            'transport' => 'refresh',
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control('buildpro_portfolio_enabled', array(
            'label' => __('Enable Portfolio', 'buildpro'),
            'section' => 'buildpro_portfolio_section',
            'type' => 'checkbox',
        ));
        $wp_customize->add_setting('buildpro_portfolio_data', array(
            'default' => buildpro_portfolio_get_default_data(),
            'transport' => 'postMessage',
            'sanitize_callback' => 'buildpro_portfolio_sanitize_data',
        ));
        if (class_exists('BuildPro_Portfolio_Control')) {
            $wp_customize->add_control(new BuildPro_Portfolio_Control($wp_customize, 'buildpro_portfolio_data', array(
                'label' => __('Portfolio Content', 'buildpro'),
                'description' => __('Edit Portfolio title and description for Front Page.', 'buildpro'),
                'section' => 'buildpro_portfolio_section',
            )));
        }
        $wp_customize->add_setting('buildpro_portfolio_edit_link', array(
            'default' => '',
            'transport' => 'postMessage',
            'sanitize_callback' => 'esc_url_raw',
        ));
        // if (class_exists('BuildPro_Customize_Button_Control')) {
        //     $wp_customize->add_control(new BuildPro_Customize_Button_Control($wp_customize, 'buildpro_portfolio_edit_link', array(
        //         'label' => __('Edit Portfolio Section', 'buildpro'),
        //         'description' => __('Open the Front Page editor.', 'buildpro'),
        //         'section' => 'buildpro_portfolio_section',
        //         'button_url' => $edit_url,
        //         'button_text' => __('Edit Front Page', 'buildpro'),
        //     )));
        // }
        if (isset($wp_customize->selective_refresh)) {
            $wp_customize->selective_refresh->add_partial('buildpro_portfolio_data', array(
                'selector' => '.section-portfolio',
                'settings' => array('buildpro_portfolio_data'),
                'render_callback' => function () {
                    ob_start();
                    get_template_part('template/template-parts/page/home/section-projects/index');
                    return ob_get_clean();
                },
            ));
        }
    }
} // end if !function_exists buildpro_portfolio_customize_register
add_action('customize_register', 'buildpro_portfolio_customize_register');
if (!function_exists('buildpro_portfolio_enqueue_assets')) {
    function buildpro_portfolio_enqueue_assets()
    {
        wp_enqueue_style(
            'buildpro-portfolio-style',
            get_theme_file_uri('template/customize/page/home/section-projects/style.css'),
            array(),
            null
        );
        wp_enqueue_script(
            'buildpro-portfolio-script',
            get_theme_file_uri('template/customize/page/home/section-projects/script.js'),
            array('customize-controls'),
            null,
            true
        );

        if (function_exists('buildpro_home_add_inline_i18n')) {
            buildpro_home_add_inline_i18n('buildpro-portfolio-script');
        }
    }
} // end if !function_exists buildpro_portfolio_enqueue_assets
add_action('customize_controls_enqueue_scripts', 'buildpro_portfolio_enqueue_assets');
if (!function_exists('buildpro_portfolio_find_home_id')) {
    function buildpro_portfolio_find_home_id()
    {
        $selected = 0;
        if (function_exists('wp_get_current_user')) {
            global $wp_customize;
            if ($wp_customize && $wp_customize instanceof WP_Customize_Manager) {
                $setting = $wp_customize->get_setting('buildpro_preview_page_id');
                if ($setting) {
                    $val = $setting->value();
                    $selected = absint($val);
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
        $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'home-page.php', 'number' => 1));
        if (!empty($pages)) {
            return (int) $pages[0]->ID;
        }
        return 0;
    }
} // end if !function_exists buildpro_portfolio_find_home_id
if (!function_exists('buildpro_portfolio_get_default_data')) {
    function buildpro_portfolio_get_default_data()
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
            $page_id = buildpro_portfolio_find_home_id();
        }
        if ($page_id) {
            $title = get_post_meta($page_id, 'projects_title', true);
            $desc  = get_post_meta($page_id, 'projects_description', true);
            $view_all_text = get_post_meta($page_id, 'projects_view_all_text', true);
            $title = is_string($title) ? $title : '';
            $desc  = is_string($desc) ? $desc : '';
            $view_all_text = is_string($view_all_text) ? $view_all_text : '';
            if ($view_all_text === '') {
                $view_all_text = __('View All Projects', 'buildpro');
            }
            return array('title' => $title, 'description' => $desc, 'view_all_text' => $view_all_text);
        }
        return array('title' => '', 'description' => '', 'view_all_text' => __('View All Projects', 'buildpro'));
    }
} // end if !function_exists buildpro_portfolio_get_default_data
if (!function_exists('buildpro_portfolio_sanitize_data')) {
    function buildpro_portfolio_sanitize_data($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $value = $decoded;
            }
        }
        if (!is_array($value)) {
            return array('title' => '', 'description' => '', 'view_all_text' => '');
        }
        return array(
            'title' => isset($value['title']) ? sanitize_text_field($value['title']) : '',
            'description' => isset($value['description']) ? sanitize_textarea_field($value['description']) : '',
            'view_all_text' => isset($value['view_all_text']) ? sanitize_text_field($value['view_all_text']) : '',
        );
    }
} // end if !function_exists buildpro_portfolio_sanitize_data
if (!function_exists('buildpro_portfolio_sync_customizer_to_meta')) {
    function buildpro_portfolio_sync_customizer_to_meta($wp_customize_manager)
    {
        $data = get_theme_mod('buildpro_portfolio_data', array());
        $data = buildpro_portfolio_sanitize_data($data);
        $title = isset($data['title']) ? $data['title'] : '';
        $desc  = isset($data['description']) ? $data['description'] : '';
        $view_all_text  = isset($data['view_all_text']) ? $data['view_all_text'] : '';
        $enabled = absint(get_theme_mod('buildpro_portfolio_enabled', 1));
        $page_id = 0;
        if ($wp_customize_manager instanceof WP_Customize_Manager) {
            $setting = $wp_customize_manager->get_setting('buildpro_preview_page_id');
            if ($setting) {
                $page_id = absint($setting->value());
            }
        }
        if ($page_id <= 0) {
            $page_id = buildpro_portfolio_find_home_id();
        }
        if ($page_id) {
            $targets = array();
            $targets[] = $page_id;
            $front_id = (int) get_option('page_on_front');
            if ($front_id > 0) {
                $targets[] = $front_id;
            }
            $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'home-page.php', 'number' => 1));
            if (!empty($pages)) {
                $targets[] = (int) $pages[0]->ID;
            }
            $targets = array_unique(array_filter(array_map('absint', $targets)));
            foreach ($targets as $tid) {
                update_post_meta($tid, 'projects_title', $title);
                update_post_meta($tid, 'projects_description', $desc);
                update_post_meta($tid, 'projects_view_all_text', $view_all_text);
                update_post_meta($tid, 'buildpro_portfolio_enabled', $enabled);
            }
            set_theme_mod('projects_title', $title);
            set_theme_mod('projects_description', $desc);
            set_theme_mod('projects_view_all_text', $view_all_text);
            set_theme_mod('buildpro_portfolio_enabled', $enabled);
        }
    }
} // end if !function_exists buildpro_portfolio_sync_customizer_to_meta
add_action('customize_save_after', 'buildpro_portfolio_sync_customizer_to_meta');
