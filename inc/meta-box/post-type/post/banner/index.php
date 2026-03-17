<?php
function buildpro_post_banner_add_meta_box($post_type, $post)
{
    if ($post_type !== 'post') {
        return;
    }
    add_meta_box('buildpro_post_tab_banner', esc_html__('Banner', 'buildpro'), 'buildpro_post_banner_render_meta_box', 'post', 'normal', 'default');
}
add_action('add_meta_boxes', 'buildpro_post_banner_add_meta_box', 10, 2);

function buildpro_post_banner_render_meta_box($post)
{
    $banner_id = (int) get_post_meta($post->ID, 'buildpro_post_banner_id', true);
    $thumb     = $banner_id ? wp_get_attachment_image_url($banner_id, 'medium') : '';

    $i18n = wp_json_encode([
        'selectTitle'  => __('Select banner photo', 'buildpro'),
        'usePhoto'     => __('Use photo', 'buildpro'),
        'noBanner'     => __('No banner selected', 'buildpro'),
    ]);

    echo '<style>
    .buildpro-post-block{background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,0.05);padding:16px;margin-top:8px}
    .buildpro-post-field{margin:10px 0}
    .buildpro-post-field label{display:block;font-weight:600;margin-bottom:6px;color:#374151}
    .buildpro-post-block .regular-text{width:100%;max-width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px}
    .image-banner-preview{margin-top:8px;min-height:120px;display:flex;align-items:center;justify-content:center;background:#fff;border:1px dashed #ddd;border-radius:6px}
    </style>';

    echo '<div class="buildpro-post-block">';
    echo '<p class="buildpro-post-field">';
    echo '<label>' . esc_html__('Banner Image', 'buildpro') . '</label>';
    echo '<input type="hidden" id="buildpro_post_banner_id" name="buildpro_post_banner_id" value="' . esc_attr($banner_id) . '">';
    echo ' <button type="button" class="button" id="buildpro_post_select_banner">' . esc_html__('Select photo', 'buildpro') . '</button>';
    echo ' <button type="button" class="button" id="buildpro_post_remove_banner">' . esc_html__('Remove photo', 'buildpro') . '</button>';
    echo '</p>';
    echo '<div class="image-banner-preview" id="buildpro_post_banner_preview">';
    if ($thumb) {
        echo '<img src="' . esc_url($thumb) . '">';
    } else {
        echo '<span style="color:#888">' . esc_html__('No banner selected', 'buildpro') . '</span>';
    }
    echo '</div>';
    echo '</div>';

    echo '<script>
    (function(){
        var i18n = ' . $i18n . ';
        var selectBtn = document.getElementById("buildpro_post_select_banner");
        var removeBtn = document.getElementById("buildpro_post_remove_banner");
        var input = document.getElementById("buildpro_post_banner_id");
        var preview = document.getElementById("buildpro_post_banner_preview");
        var frame;
        if(selectBtn){
            selectBtn.addEventListener("click", function(e){
                e.preventDefault();
                if(!frame){ frame = wp.media({ title: i18n.selectTitle, button: { text: i18n.usePhoto }, multiple: false, library: { type: "image" } }); }
                if(typeof frame.off === "function"){ frame.off("select"); }
                frame.on("select", function(){
                    var attachment = frame.state().get("selection").first().toJSON();
                    input.value = attachment.id;
                    var url = (attachment.sizes && attachment.sizes.medium) ? attachment.sizes.medium.url : attachment.url;
                    preview.innerHTML = "<img src=\'"+url+"\'>";
                });
                frame.open();
            });
        }
        if(removeBtn){
            removeBtn.addEventListener("click", function(e){
                e.preventDefault();
                input.value = "";
                preview.innerHTML = "<span style=\"color:#888\">" + i18n.noBanner + "</span>";
            });
        }
    })();
    </script>';
}
