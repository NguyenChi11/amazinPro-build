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
                echo '<option value="0" data-url="">' . esc_html__('— Select Preview Page —', 'buildpro') . '</option>';
                foreach ($pages as $p) {
                    $id = (int) $p->ID;
                    $status = isset($p->post_status) ? $p->post_status : 'publish';
                    $url = ($status === 'publish' || $status === 'private') ? get_permalink($id) : get_preview_post_link($id);
                    $selected = $current === $id ? ' selected' : '';
                    echo '<option value="' . esc_attr($id) . '" data-url="' . esc_url($url) . '"' . $selected . '>' . esc_html(get_the_title($id)) . '</option>';
                }
                echo '</select>';
                $front_id = (int) get_option('page_on_front');
                $home_pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'home-page.php', 'number' => 50));
                $home_ids = array();
                foreach ($home_pages as $hp) {
                    $home_ids[] = (int) $hp->ID;
                }
                echo '<div style="margin-top:8px"><button type="button" class="button button-primary" id="buildpro-preview-confirm">Accept</button></div>';
                echo '<script>(function(){var btn=document.getElementById("buildpro-preview-confirm");var sel=document.getElementById("buildpro-preview-select");if(!btn||!sel)return;btn.addEventListener("click",function(){try{var api=window.parent&&window.parent.wp&&window.parent.wp.customize;var opt=sel.options[sel.selectedIndex];var url=opt?opt.getAttribute("data-url"):"";var id=opt&&opt.value?parseInt(opt.value,10):0;if(!url)return;function addCS(u){try{var uuid=api&&api.settings&&api.settings.changeset&&api.settings.changeset.uuid;if(!uuid)return u;var t=new URL(u,window.location.origin);if(!t.searchParams.get("customize_changeset_uuid")){t.searchParams.set("customize_changeset_uuid",uuid);}return t.toString();}catch(e){return u;}}var target=(function(){var frontId=' . json_encode($front_id) . ';var homeIds=' . json_encode($home_ids) . ';if(id&&(frontId&&id===frontId))return "buildpro_banner_section";if(id&&Array.isArray(homeIds)&&homeIds.indexOf(id)>-1)return "buildpro_banner_section";return "buildpro_header_section";})();var finalUrl=addCS(url);var did=false;if(api&&api.previewer){if(api.previewer.previewUrl&&typeof api.previewer.previewUrl.set==="function"){api.previewer.previewUrl.set(finalUrl);did=true;}else if(typeof api.previewer.previewUrl==="function"){api.previewer.previewUrl(finalUrl);did=true;}else if(api.previewer.url&&typeof api.previewer.url.set==="function"){api.previewer.url.set(finalUrl);did=true;}if(!did){var frame=window.parent&&window.parent.document&&window.parent.document.querySelector("#customize-preview iframe");if(frame){frame.src=finalUrl;did=true;}}if(did){setTimeout(function(){try{if(api.previewer.refresh){api.previewer.refresh();}}catch(e){}},100);}}} }catch(e){}});})();</script>';
                echo '<script>(function(){try{var select=document.getElementById("buildpro-preview-select");var btn=document.getElementById("buildpro-preview-confirm");if(!select)return;var api=window.parent&&window.parent.wp&&window.parent.wp.customize;var cfg={frontId:' . json_encode($front_id) . ',homeIds:' . json_encode($home_ids) . '};function isHome(id){id=parseInt(id||0,10);if(!id)return false;return (cfg.frontId&&id===cfg.frontId)||(Array.isArray(cfg.homeIds)&&cfg.homeIds.indexOf(id)>-1);}function appendChangeset(url){try{var uuid=api&&api.settings&&api.settings.changeset&&api.settings.changeset.uuid;if(!uuid)return url;var u=new URL(url,window.location.origin);if(!u.searchParams.get(\"customize_changeset_uuid\")){u.searchParams.set(\"customize_changeset_uuid\",uuid);}return u.toString();}catch(e){return url;}}function go(url){try{if(!url)return;url=appendChangeset(url);var did=false;if(api&&api.previewer){if(api.previewer.previewUrl&&typeof api.previewer.previewUrl.set===\"function\"){api.previewer.previewUrl.set(url);did=true;}else if(typeof api.previewer.previewUrl===\"function\"){api.previewer.previewUrl(url);did=true;}else if(api.previewer.url&&typeof api.previewer.url.set===\"function\"){api.previewer.url.set(url);did=true;}if(!did){var frame=window.parent&&window.parent.document&&window.parent.document.querySelector(\"#customize-preview iframe\");if(frame){frame.src=url;did=true;}}if(did){setTimeout(function(){try{if(api.previewer.refresh){api.previewer.refresh();}}catch(e){}},100);}}}catch(e){}}function setVal(id){try{if(api){var setting=api(\"buildpro_preview_page_id\");if(setting&&typeof setting.set===\"function\"){setting.set(id);}}}catch(e){}}function focus(id){try{if(!api||!api.section)return;var target=isHome(id)?\"buildpro_banner_section\":\"buildpro_header_section\";var s=api.section(target);if(s&&s.focus){s.focus();}}catch(e){}}function current(){var opt=select.options[select.selectedIndex];return{url:(opt&&opt.getAttribute(\"data-url\"))||\"\",id:opt&&opt.value?parseInt(opt.value,10):0};}function onChange(){try{var cur=current();if(!cur.url)return;setVal(cur.id);go(cur.url);}catch(e){}}select.addEventListener(\"change\",onChange);btn.addEventListener(\"click\",onChange);}catch(e){}})();</script>';
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
