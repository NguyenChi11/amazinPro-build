<?php
function buildpro_preview_page_customize_register($wp_customize)
{
    if (!class_exists('BuildPro_Preview_Page_Control') && class_exists('WP_Customize_Control')) {
        class BuildPro_Preview_Page_Control extends WP_Customize_Control
        {
            public $type = 'buildpro_preview_page';
            public function render_content()
            {
                $current = (int) $this->value();
                $pages = get_pages(array(
                    'post_status' => array('publish', 'private', 'draft'),
                    'number'      => 200,
                ));
                echo '<span class="customize-control-title">' . esc_html($this->label) . '</span>';
                if (!empty($this->description)) {
                    echo '<p class="description">' . esc_html($this->description) . '</p>';
                }
                echo '<select id="buildpro-preview-select" ';
                $this->link();
                echo '>';
                echo '<option value="0" data-url="" data-template="">' . esc_html__('— Select Preview Page —', 'buildpro') . '</option>';
                foreach ($pages as $p) {
                    $id = (int) $p->ID;
                    $status = isset($p->post_status) ? $p->post_status : 'publish';
                    $url = ($status === 'publish' || $status === 'private') ? get_permalink($id) : get_preview_post_link($id);
                    $tpl = get_page_template_slug($id);
                    $selected = $current === $id ? ' selected' : '';
                    echo '<option value="' . esc_attr($id) . '" data-url="' . esc_url($url) . '" data-template="' . esc_attr($tpl) . '"' . $selected . '>' . esc_html(get_the_title($id)) . '</option>';
                }
                echo '</select>';
                $front_id = (int) get_option('page_on_front');
                $home_pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'home-page.php', 'number' => 50));
                $home_ids = array();
                foreach ($home_pages as $hp) {
                    $home_ids[] = (int) $hp->ID;
                }
                echo '<div style="margin-top:8px"><button type="button" class="button button-primary" id="buildpro-preview-confirm">' . esc_html__('Accept', 'buildpro') . '</button></div>';
                echo '<script>(function(){try{var select=document.getElementById("buildpro-preview-select");var btn=document.getElementById("buildpro-preview-confirm");if(!select||!btn)return;var api=window.wp&&window.wp.customize;var cfg={frontId:' . json_encode($front_id) . ',homeIds:' . json_encode($home_ids) . '};function isHomeId(id){id=parseInt(id||0,10);if(!id)return false;return(cfg.frontId&&id===cfg.frontId)||(Array.isArray(cfg.homeIds)&&cfg.homeIds.indexOf(id)>-1);}function go(url){try{if(!url)return;if(api&&api.previewer&&api.previewer.previewUrl&&typeof api.previewer.previewUrl.set==="function"){api.previewer.previewUrl.set(url);}else{var frame=document.querySelector("#customize-preview iframe");if(frame){frame.src=url;}}}catch(e){}}function setVal(id){try{if(api){var setting=api("buildpro_preview_page_id");if(setting&&typeof setting.set==="function"){setting.set(id);}}}catch(e){}}function updateSections(tpl,id){try{if(!api)return;var home=tpl==="home-page.php"||isHomeId(id);var about=tpl==="about-us-page.php"||tpl==="about-page.php";var projects=tpl==="projects-page.php";var map=[["buildpro_banner_section","buildpro_data_section","buildpro_evaluate_section","buildpro_post_section","buildpro_product_section","buildpro_portfolio_section"],["buildpro_about_banner_section","buildpro_about_core_values_section","buildpro_about_leader_section","buildpro_about_policy_section","buildpro_about_contact_section"],["buildpro_projects_title_section"]];map.forEach(function(group,i){var show=i===0?home:i===1?about:projects;group.forEach(function(n){try{if(api.section&&api.section(n)){api.section(n).active.set(show);}}catch(e){}});});}catch(e){}}function current(){var opt=select.options[select.selectedIndex];return{url:(opt&&opt.getAttribute("data-url"))||"",id:opt&&opt.value?parseInt(opt.value,10):0,tpl:(opt&&opt.getAttribute("data-template"))||""}}function onChange(){try{var cur=current();if(!cur.url)return;setVal(cur.id);updateSections(cur.tpl,cur.id);go(cur.url);}catch(e){}}select.addEventListener("change",onChange);btn.addEventListener("click",onChange);}catch(e){}})();</script>';
            }
        }
    }
    $default_page = (int) get_option('page_on_front');
    $wp_customize->add_section('buildpro_preview_page_section', array(
        'title'    => __('Preview Page', 'buildpro'),
        'priority' => 20,
    ));
    $wp_customize->add_setting('buildpro_preview_page_id', array(
        'default'           => $default_page,
        'transport'         => 'postMessage',
        'sanitize_callback' => 'absint',
    ));
    if (class_exists('BuildPro_Preview_Page_Control')) {
        $wp_customize->add_control(new BuildPro_Preview_Page_Control($wp_customize, 'buildpro_preview_page_id', array(
            'label'       => __('Preview Page', 'buildpro'),
            'description' => __('Select the page to preview in the Customizer. The sidebar will update accordingly.', 'buildpro'),
            'section'     => 'buildpro_preview_page_section',
        )));
    }
}
add_action('customize_register', 'buildpro_preview_page_customize_register');
