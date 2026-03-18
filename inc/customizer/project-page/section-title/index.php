<?php
if (!function_exists('buildpro_projects_title_customize_register')) {
    function buildpro_projects_title_customize_register($wp_customize)
    {
        if (!class_exists('BuildPro_Projects_Title_Control') && class_exists('WP_Customize_Control')) {
            class BuildPro_Projects_Title_Control extends WP_Customize_Control
            {
                public $type = 'buildpro_projects_title';
                public function render_content()
                {
                    $data = $this->value();
                    $data = is_array($data) ? $data : array();
                    echo '<span class="customize-control-title">' . esc_html($this->label) . '</span>';
                    if (!empty($this->description)) {
                        echo '<p class="description">' . esc_html($this->description) . '</p>';
                    }
                    include get_theme_file_path('template/customize/page/projects/section-title/index.php');
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
                        echo '<p>' . esc_html__('Could not find a Projects page using template projects-page.php.', 'buildpro') . '</p>';
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
        if (!function_exists('buildpro_customizer_is_projects_preview')) {
            function buildpro_customizer_is_projects_preview()
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
                    if ($tpl === 'projects-page.php') {
                        return true;
                    }
                }
                return false;
            }
        }
        $edit_url = '';
        $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'projects-page.php', 'number' => 1));
        if (!empty($pages)) {
            $p = $pages[0];
            $edit_url = admin_url('post.php?post=' . $p->ID . '&action=edit');
        }
        $wp_customize->add_section('buildpro_projects_title_section', array(
            'title' => __('Title Projects', 'buildpro'),
            'priority' => 45,
            'active_callback' => 'buildpro_customizer_is_projects_preview',
        ));
        $wp_customize->add_setting('buildpro_projects_title_data', array(
            'default' => buildpro_projects_title_get_default_data(),
            'transport' => 'postMessage',
            'sanitize_callback' => 'buildpro_projects_title_sanitize_data',
        ));
        if (class_exists('BuildPro_Projects_Title_Control')) {
            $wp_customize->add_control(new BuildPro_Projects_Title_Control($wp_customize, 'buildpro_projects_title_data', array(
                'label' => __('Projects Title', 'buildpro'),
                'description' => __('Edit the title and description of the Projects page.', 'buildpro'),
                'section' => 'buildpro_projects_title_section',
            )));
        }
        $wp_customize->add_setting('buildpro_projects_title_edit_link', array(
            'default' => '',
            'transport' => 'postMessage',
            'sanitize_callback' => 'esc_url_raw',
        ));
        if (class_exists('BuildPro_Customize_Button_Control')) {
            $wp_customize->add_control(new BuildPro_Customize_Button_Control($wp_customize, 'buildpro_projects_title_edit_link', array(
                'label' => __('Edit Projects Page', 'buildpro'),
                'description' => __('Open the Projects page editor.', 'buildpro'),
                'section' => 'buildpro_projects_title_section',
                'button_url' => $edit_url,
                'button_text' => __('Edit Projects Page', 'buildpro'),
            )));
        }
        if (isset($wp_customize->selective_refresh)) {
            $wp_customize->selective_refresh->add_partial('buildpro_projects_title_data', array(
                'selector' => '.project--section-title',
                'settings' => array('buildpro_projects_title_data'),
                'container_inclusive' => true,
                'render_callback' => function () {
                    ob_start();
                    get_template_part('template/template-parts/page/projects/section-title/index');
                    return ob_get_clean();
                },
            ));
        }
    }
} // end if !function_exists buildpro_projects_title_customize_register
add_action('customize_register', 'buildpro_projects_title_customize_register');
if (!function_exists('buildpro_projects_title_enqueue_assets')) {
    function buildpro_projects_title_enqueue_assets()
    {
        wp_enqueue_style(
            'buildpro-projects-title-style',
            get_theme_file_uri('template/customize/page/projects/section-title/style.css'),
            array(),
            null
        );
        wp_enqueue_script(
            'buildpro-projects-title-script',
            get_theme_file_uri('template/customize/page/projects/section-title/script.js'),
            array('customize-controls'),
            null,
            true
        );
    }
} // end if !function_exists buildpro_projects_title_enqueue_assets
add_action('customize_controls_enqueue_scripts', 'buildpro_projects_title_enqueue_assets');
if (!function_exists('buildpro_projects_title_find_page_id')) {
    function buildpro_projects_title_find_page_id()
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
            if ($tpl === 'projects-page.php') {
                return $selected;
            }
        }
        $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'projects-page.php', 'number' => 1));
        if (!empty($pages)) {
            return (int) $pages[0]->ID;
        }
        return 0;
    }
} // end if !function_exists buildpro_projects_title_find_page_id
if (!function_exists('buildpro_projects_title_get_default_data')) {
    function buildpro_projects_title_get_default_data()
    {
        $page_id = buildpro_projects_title_find_page_id();
        if ($page_id) {
            $title = get_post_meta($page_id, 'projects_title', true);
            $desc  = get_post_meta($page_id, 'projects_description', true);
            $title = is_string($title) ? $title : '';
            $desc  = is_string($desc) ? $desc : '';
            return array('title' => $title, 'description' => $desc);
        }
        return array('title' => '', 'description' => '');
    }
} // end if !function_exists buildpro_projects_title_get_default_data
if (!function_exists('buildpro_projects_title_sanitize_data')) {
    function buildpro_projects_title_sanitize_data($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $value = $decoded;
            }
        }
        if (!is_array($value)) {
            return array('title' => '', 'description' => '');
        }
        return array(
            'title' => isset($value['title']) ? sanitize_text_field($value['title']) : '',
            'description' => isset($value['description']) ? sanitize_textarea_field($value['description']) : '',
        );
    }
} // end if !function_exists buildpro_projects_title_sanitize_data
if (!function_exists('buildpro_projects_title_sync_customizer_to_meta')) {
    function buildpro_projects_title_sync_customizer_to_meta($wp_customize_manager)
    {
        $data = get_theme_mod('buildpro_projects_title_data', array());
        $data = buildpro_projects_title_sanitize_data($data);
        $title = isset($data['title']) ? $data['title'] : '';
        $desc  = isset($data['description']) ? $data['description'] : '';
        $page_id = 0;
        if ($wp_customize_manager instanceof WP_Customize_Manager) {
            $setting = $wp_customize_manager->get_setting('buildpro_preview_page_id');
            if ($setting) {
                $page_id = absint($setting->value());
            }
        }
        if ($page_id <= 0) {
            $page_id = buildpro_projects_title_find_page_id();
        }
        if ($page_id) {
            update_post_meta($page_id, 'projects_title', $title);
            update_post_meta($page_id, 'projects_description', $desc);
            set_theme_mod('projects_title', $title);
            set_theme_mod('projects_description', $desc);
        }
    }
} // end if !function_exists buildpro_projects_title_sync_customizer_to_meta
add_action('customize_save_after', 'buildpro_projects_title_sync_customizer_to_meta');
