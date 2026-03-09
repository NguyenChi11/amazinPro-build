<?php
function buildpro_about_group_meta_box_add($post_type, $post)
{
    if ($post_type !== 'page') {
        return;
    }
    $template = get_page_template_slug($post->ID);
    if ($template !== 'about-page.php') {
        return;
    }
    add_meta_box('buildpro_about_group', 'About Us', 'buildpro_about_group_meta_box_render', 'page', 'normal', 'high');
}
add_action('add_meta_boxes', 'buildpro_about_group_meta_box_add', 10, 2);

function buildpro_about_group_meta_box_render($post)
{
    $template = get_page_template_slug($post->ID);
    if ($template !== 'about-page.php') {
        return;
    }
    echo '<div class="buildpro-admin-tabs" style="margin:0;padding:8px 0;">'
        . '<button type="button" class="button buildpro-admin-tab is-active" data-target="buildpro_about_banner_meta">Banner</button> '
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_about_core_values_meta">Core Values</button>'
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_about_leader_meta">Leader</button>'
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_about_policy_meta">Policy</button>'
        . '<button type="button" class="button buildpro-admin-tab" data-target="buildpro_about_contact_meta">Contact</button>'
        . '</div>';
    echo '<script>
    (function(){
        function init(){
            var tabs = document.querySelectorAll(".buildpro-admin-tab");
            function show(id){
                var ids = ["buildpro_about_banner_meta","buildpro_about_core_values_meta","buildpro_about_leader_meta","buildpro_about_policy_meta","buildpro_about_contact_meta"];
                ids.forEach(function(x){
                    var el = document.getElementById(x);
                    if(el){ el.style.display = (x === id) ? "block" : "none"; }
                });
                tabs.forEach(function(b){ b.classList.toggle("is-active", b.getAttribute("data-target") === id); });
            }
            show("buildpro_about_banner_meta");
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
