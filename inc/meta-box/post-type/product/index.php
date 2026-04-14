<?php
require get_template_directory() . '/inc/meta-box/post-type/product/bedroom/index.php';
require get_template_directory() . '/inc/meta-box/post-type/product/bathroom/index.php';
require get_template_directory() . '/inc/meta-box/post-type/product/area/index.php';
require get_template_directory() . '/inc/meta-box/post-type/product/location/index.php';

function buildpro_product_group_meta_box_add($post_type, $post)
{
    if ($post_type !== 'product') {
        return;
    }
    add_meta_box('buildpro_product_group', esc_html__('Product Details', 'buildpro'), 'buildpro_product_group_meta_box_render', 'product', 'normal', 'high');
}
add_action('add_meta_boxes', 'buildpro_product_group_meta_box_add', 10, 2);

function buildpro_product_group_meta_box_render($post)
{
    wp_nonce_field('buildpro_product_meta_save', 'buildpro_product_meta_nonce');

    echo '<div class="buildpro-admin-tabs" style="margin:0;padding:8px 0;">'
        . '<button type="button" class="button buildpro-admin-tab is-active" data-target="buildpro_product_tab_bedroom">' . esc_html__('Bedroom', 'buildpro') . '</button> '
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_product_tab_bathroom">' . esc_html__('Bathroom', 'buildpro') . '</button> '
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_product_tab_area">' . esc_html__('Area', 'buildpro') . '</button> '
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_product_tab_location">' . esc_html__('Location', 'buildpro') . '</button>'
        . '</div>';

    echo '<script>
    (function(){
        function init(){
            var tabs = document.querySelectorAll(".buildpro-admin-tab");
            function show(id){
                ["buildpro_product_tab_bedroom","buildpro_product_tab_bathroom","buildpro_product_tab_area","buildpro_product_tab_location"].forEach(function(x){
                    var el = document.getElementById(x);
                    if(el){ el.style.display = (x === id) ? "block" : "none"; }
                });
                tabs.forEach(function(b){ b.classList.toggle("is-active", b.getAttribute("data-target") === id); });
            }
            show("buildpro_product_tab_bedroom");
            tabs.forEach(function(b){ b.addEventListener("click", function(){ show(b.getAttribute("data-target")); }); });
        }
        if(document.readyState === "loading"){
            document.addEventListener("DOMContentLoaded", init);
        } else {
            init();
        }
    })();
    </script>';

    echo '<div id="buildpro_product_tab_bedroom">';
    buildpro_product_bedroom_render_meta_box($post);
    echo '</div>';

    echo '<div id="buildpro_product_tab_bathroom" style="display:none;">';
    buildpro_product_bathroom_render_meta_box($post);
    echo '</div>';

    echo '<div id="buildpro_product_tab_area" style="display:none;">';
    buildpro_product_area_render_meta_box($post);
    echo '</div>';

    echo '<div id="buildpro_product_tab_location" style="display:none;">';
    buildpro_product_location_render_meta_box($post);
    echo '</div>';

    echo '<style>
        .buildpro-post-block{background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,0.05);padding:16px;margin-top:8px}
        .buildpro-post-field{margin:10px 0}
        .buildpro-post-field label{display:block;font-weight:600;margin-bottom:6px;color:#374151}
        .buildpro-post-block .regular-text{width:100%;max-width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px}
        .buildpro-admin-tabs .button{margin-right:6px;margin-top:2px;margin-bottom:2px;background:#f3f4f6;border-color:#e5e7eb}
        .buildpro-admin-tabs .button.is-active{background:#2563eb;color:#fff;border-color:#2563eb}
    </style>';
}
