<?php
require get_template_directory() . '/inc/meta-box/post-type/post/banner/index.php';
require get_template_directory() . '/inc/meta-box/post-type/post/description/index.php';
require get_template_directory() . '/inc/meta-box/post-type/post/paragraph/index.php';
require get_template_directory() . '/inc/meta-box/post-type/post/quote/index.php';
function buildpro_post_group_meta_box_add($post_type, $post)
{
    if ($post_type !== 'post') {
        return;
    }
    add_meta_box('buildpro_post_group', 'Post Details', 'buildpro_post_group_meta_box_render', 'post', 'normal', 'high');
}
add_action('add_meta_boxes', 'buildpro_post_group_meta_box_add', 10, 2);

function buildpro_post_group_meta_box_render($post)
{
    wp_nonce_field('buildpro_post_meta_save', 'buildpro_post_meta_nonce');
    echo '<div class="buildpro-admin-tabs" style="margin:0;padding:8px 0;">'
        . '<button type="button" class="button buildpro-admin-tab is-active" data-target="buildpro_post_tab_banner">Banner</button> '
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_post_tab_desc">Description</button> '
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_post_tab_paragraph">Paragraph</button> '
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_post_tab_quote">Quote</button>'
        . '</div>';
    echo '<script>
    (function(){
        function init(){
            var tabs = document.querySelectorAll(".buildpro-admin-tab");
            function show(id){
                ["buildpro_post_tab_banner","buildpro_post_tab_desc","buildpro_post_tab_paragraph","buildpro_post_tab_quote"].forEach(function(x){
                    var el = document.getElementById(x);
                    if(el){ el.style.display = (x === id) ? "block" : "none"; }
                });
                tabs.forEach(function(b){ b.classList.toggle("is-active", b.getAttribute("data-target") === id); });
            }
            show("buildpro_post_tab_banner");
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
