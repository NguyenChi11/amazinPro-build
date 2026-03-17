<?php
function buildpro_project_about_add_meta_box($post_type, $post)
{
    if ($post_type !== 'project') {
        return;
    }
    add_meta_box('buildpro_project_tab_about', esc_html__('About Project', 'buildpro'), 'buildpro_project_about_render_meta_box', 'project', 'normal', 'default');
}
add_action('add_meta_boxes', 'buildpro_project_about_add_meta_box', 10, 2);

function buildpro_project_about_render_meta_box($post)
{
    wp_enqueue_media();
    $about = get_post_meta($post->ID, 'about_project', true);
    $image_id = (int) get_post_meta($post->ID, 'about_image_project', true);
    $thumb = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';

    $i18n = wp_json_encode([
        'selectImage' => __('Select image', 'buildpro'),
        'usePhoto'    => __('Use photo', 'buildpro'),
        'empty'       => __('No banner selected', 'buildpro'),
    ]);

    echo '<div id="buildpro_project_tab_about" class="buildpro-post-block">';
    ob_start();
    wp_editor($about, 'buildpro_project_about_editor', array('textarea_name' => 'about_project', 'textarea_rows' => 8, 'media_buttons' => true));
    $editor_html = ob_get_clean();
    echo $editor_html;
    echo '<style>
    .buildpro-post-field{margin:10px 0}
    .about-image-preview{margin-top:8px;min-height:120px;display:flex;align-items:center;justify-content:center;background:#fff;border:1px dashed #ddd;border-radius:6px}
    </style>';
    echo '<p class="buildpro-post-field"><label>' . esc_html__('About Image', 'buildpro') . '</label><input type="hidden" id="buildpro_project_about_image_id" name="about_image_project" value="' . esc_attr($image_id) . '"> <button type="button" class="button" id="buildpro_project_select_about_image">' . esc_html__('Select photo', 'buildpro') . '</button> <button type="button" class="button" id="buildpro_project_remove_about_image">' . esc_html__('Remove photo', 'buildpro') . '</button></p>';
    echo '<div class="about-image-preview" id="buildpro_project_about_image_preview">' . ($thumb ? '<img src="' . esc_url($thumb) . '">' : '<span style="color:#888">' . esc_html__('No banner selected', 'buildpro') . '</span>') . '</div>';
    echo '<script>
    (function(){
        var i18n = ' . $i18n . ';
        var selectBtn = document.getElementById("buildpro_project_select_about_image");
        var removeBtn = document.getElementById("buildpro_project_remove_about_image");
        var input = document.getElementById("buildpro_project_about_image_id");
        var preview = document.getElementById("buildpro_project_about_image_preview");
        var frame;
        function setImage(id, url){
            input.value = id || "";
            preview.innerHTML = url ? "<img src=\\"" + url + "\\">" : "<span style=\\"color:#888\\">" + i18n.empty + "</span>";
        }
        if(selectBtn){
            selectBtn.addEventListener("click", function(e){
                e.preventDefault();
                if(frame){ frame.open(); return; }
                frame = wp.media({
                    title: i18n.selectImage,
                    button: { text: i18n.usePhoto },
                    multiple: false
                });
                frame.on("select", function(){
                    var attachment = frame.state().get("selection").first().toJSON();
                    setImage(attachment.id, attachment.url);
                });
                frame.open();
            });
        }
        if(removeBtn){
            removeBtn.addEventListener("click", function(e){
                e.preventDefault();
                setImage("", "");
            });
        }
    })();
    </script>';
    echo '</div>';
}
