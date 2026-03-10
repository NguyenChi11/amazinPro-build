<?php
require get_template_directory() . '/inc/meta-box/page/home-page/section-banner/index.php';
require get_template_directory() . '/inc/meta-box/page/home-page/section-option/index.php';
require get_template_directory() . '/inc/meta-box/page/home-page/section-data/index.php';
require get_template_directory() . '/inc/meta-box/page/home-page/section-products/index.php';
require get_template_directory() . '/inc/meta-box/page/home-page/section-service/index.php';
require get_template_directory() . '/inc/meta-box/page/home-page/section-evaluate/index.php';
require get_template_directory() . '/inc/meta-box/page/home-page/section-projects/index.php';
require get_template_directory() . '/inc/meta-box/page/home-page/section-post/index.php';

function buildpro_home_group_meta_box_add($post_type, $post)
{
    if ($post_type !== 'page') {
        return;
    }
    $template = get_page_template_slug($post->ID);
    $front_id = (int) get_option('page_on_front');
    if ($template !== 'home-page.php' && (int)$post->ID !== $front_id) {
        return;
    }
    add_meta_box('buildpro_home_group', 'HomePage', 'buildpro_home_group_meta_box_render', 'page', 'normal', 'high');
}
add_action('add_meta_boxes', 'buildpro_home_group_meta_box_add', 10, 2);

function buildpro_home_group_meta_box_render($post)
{
    $template = get_page_template_slug($post->ID);
    $front_id = (int) get_option('page_on_front');
    if ($template !== 'home-page.php' && (int)$post->ID !== $front_id) {
        return;
    }
    echo '<div class="buildpro-admin-tabs" style="margin:0;padding:8px 0;">'
        . '<button type="button" class="button buildpro-admin-tab is-active" data-target="buildpro_banner_meta">Banner</button> '
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_option_meta">Option</button> '
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_data_meta">Data</button> '
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_materials_meta">Products</button> '
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_services_meta">Services</button> '
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_evaluate_meta">Evaluate</button> '
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_portfolio_meta">Portfolio</button>'
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_post_section_meta">Post</button> '
        . '</div>';
    echo '<script>
    (function(){
        function init(){
            var tabs = document.querySelectorAll(".buildpro-admin-tab");
            function show(id){
                var ids = ["buildpro_banner_meta","buildpro_option_meta","buildpro_data_meta","buildpro_materials_meta","buildpro_services_meta","buildpro_evaluate_meta","buildpro_portfolio_meta","buildpro_post_section_meta"];
                ids.forEach(function(x){
                    var el = document.getElementById(x);
                    if(el){ el.style.display = (x === id) ? "block" : "none"; }
                });
                tabs.forEach(function(b){ b.classList.toggle("is-active", b.getAttribute("data-target") === id); });
            }
            show("buildpro_banner_meta");
            tabs.forEach(function(b){ b.addEventListener("click", function(){ show(b.getAttribute("data-target")); }); });
        }
        if(document.readyState === "loading"){
            document.addEventListener("DOMContentLoaded", init);
        } else {
            init();
        }
    })();
    </script>';
    echo '<style>
        .buildpro-admin-tabs .button{margin-right:6px;background:#f3f4f6;border-color:#e5e7eb}
        .buildpro-admin-tabs .button.is-active{background:#2563eb;color:#fff;border-color:#2563eb}
    </style>';
}
