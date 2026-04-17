<?php
require get_template_directory() . '/inc/meta-box/post-type/project/banner/index.php';
require get_template_directory() . '/inc/meta-box/post-type/project/location/index.php';
require get_template_directory() . '/inc/meta-box/post-type/project/about/index.php';
require get_template_directory() . '/inc/meta-box/post-type/project/overview/index.php';
require get_template_directory() . '/inc/meta-box/post-type/project/key-infomation/index.php';
require get_template_directory() . '/inc/meta-box/post-type/project/the-vision/index.php';
require get_template_directory() . '/inc/meta-box/post-type/project/architectural-design/index.php';
require get_template_directory() . '/inc/meta-box/post-type/project/highlight/index.php';
require get_template_directory() . '/inc/meta-box/post-type/project/infomations/index.php';
require get_template_directory() . '/inc/meta-box/post-type/project/price/index.php';
require get_template_directory() . '/inc/meta-box/post-type/project/datetime/index.php';
function buildpro_project_cpt_meta_box_add($post_type, $post)
{
    if ($post_type !== 'project') {
        return;
    }
    add_meta_box('buildpro_project_cpt_group', esc_html__('Project Details', 'buildpro'), 'buildpro_project_cpt_meta_box_render', 'project', 'normal', 'high');
}
add_action('add_meta_boxes', 'buildpro_project_cpt_meta_box_add', 10, 2);

function buildpro_project_cpt_meta_box_render($post)
{
    wp_nonce_field('buildpro_project_meta_save', 'buildpro_project_meta_nonce');
    echo '<div class="buildpro-admin-tabs" style="margin:0;padding:8px 0;">'
        . '<button type="button" class="button buildpro-admin-tab is-active" data-target="buildpro_project_tab_banner">' . esc_html__('Banner Image', 'buildpro') . '</button> '
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_project_tab_location">' . esc_html__('Location', 'buildpro') . '</button> '
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_project_tab_about">' . esc_html__('About Project', 'buildpro') . '</button> '
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_project_tab_overview">' . esc_html__('Project Overview', 'buildpro') . '</button> '
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_project_tab_key_infomation">' . esc_html__('Key Information', 'buildpro') . '</button> '
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_project_tab_the_vision">' . esc_html__('The Vision', 'buildpro') . '</button> '
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_project_tab_architectural_design">' . esc_html__('Architectural Design', 'buildpro') . '</button> '
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_project_tab_highlight">' . esc_html__('Highlight', 'buildpro') . '</button> '
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_project_tab_infomations">' . esc_html__('Information', 'buildpro') . '</button> '
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_project_tab_price">' . esc_html__('Price', 'buildpro') . '</button> '
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_project_tab_datetime">' . esc_html__('Date Time', 'buildpro') . '</button>'
        . '</div>';
    echo '<script>
    (function(){
        function init(){
            var tabs = document.querySelectorAll(".buildpro-admin-tab");
            function show(id){
                ["buildpro_project_tab_banner","buildpro_project_tab_location","buildpro_project_tab_about","buildpro_project_tab_overview","buildpro_project_tab_key_infomation","buildpro_project_tab_the_vision","buildpro_project_tab_architectural_design","buildpro_project_tab_highlight","buildpro_project_tab_infomations","buildpro_project_tab_price","buildpro_project_tab_datetime"].forEach(function(x){
                    var el = document.getElementById(x);
                    if(el){ el.style.display = (x === id) ? "block" : "none"; }
                });
                tabs.forEach(function(b){ b.classList.toggle("is-active", b.getAttribute("data-target") === id); });
            }
            show("buildpro_project_tab_banner");
            tabs.forEach(function(b){ b.addEventListener("click", function(){ show(b.getAttribute("data-target")); }); });
        }
        if(document.readyState === "loading"){
            document.addEventListener("DOMContentLoaded", init);
        } else {
            init();
        }
    })();
    </script>';
    echo '<style>.buildpro-admin-tabs .button{margin-right:6px;margin-top:2px;margin-bottom:2px;background:#f3f4f6;border-color:#e5e7eb}.buildpro-admin-tabs .button.is-active{background:#2563eb;color:#fff;border-color:#2563eb}</style>';
}
