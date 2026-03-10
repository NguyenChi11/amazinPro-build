<?php
function buildpro_project_standards_add_meta_box($post_type, $post)
{
    if ($post_type !== 'project') {
        return;
    }
    add_meta_box('buildpro_project_tab_standards', 'Standards', 'buildpro_project_standards_render_meta_box', 'project', 'normal', 'default');
}
add_action('add_meta_boxes', 'buildpro_project_standards_add_meta_box', 10, 2);

function buildpro_project_standards_render_meta_box($post)
{
    wp_enqueue_media();
    $rows = get_post_meta($post->ID, 'project_standards', true);
    $rows = is_array($rows) ? $rows : array();
    echo '<style>
    .buildpro-post-block{background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,0.05);padding:16px;margin-top:8px}
    .kv-row{display:grid;grid-template-columns:120px 1fr 1fr auto;gap:8px;align-items:center;margin-top:8px}
    .thumb{width:100%;height:80px;border:1px dashed #ddd;border-radius:6px;display:flex;align-items:center;justify-content:center;background:#fff}
    </style>';
    echo '<div id="buildpro_project_tab_standards" class="buildpro-post-block">';
    echo '<div id="buildpro_project_standards_wrap">';
    $i = 0;
    foreach ($rows as $row) {
        $img_id = isset($row['image_id']) ? absint($row['image_id']) : 0;
        $title = isset($row['title']) ? sanitize_text_field($row['title']) : '';
        $desc = isset($row['description']) ? sanitize_text_field($row['description']) : '';
        $thumb = $img_id ? wp_get_attachment_image_url($img_id, 'thumbnail') : '';
        echo '<div class="kv-row" data-index="' . esc_attr($i) . '">
            <div class="thumb" id="buildpro_project_standards_thumb_' . esc_attr($i) . '">' . ($thumb ? '<img src="' . esc_url($thumb) . '" style="max-height:76px">' : '<span style="color:#888">No image</span>') . '</div>
            <input type="hidden" name="project_standards[' . esc_attr($i) . '][image_id]" id="buildpro_project_standards_image_' . esc_attr($i) . '" value="' . esc_attr($img_id) . '">
            <input type="text" name="project_standards[' . esc_attr($i) . '][title]" value="' . esc_attr($title) . '" placeholder="Title" class="regular-text">
            <input type="text" name="project_standards[' . esc_attr($i) . '][description]" value="' . esc_attr($desc) . '" placeholder="Description" class="regular-text">
            <button type="button" class="button buildpro-remove-standard">Remove</button>
            <button type="button" class="button buildpro-select-standard-image" data-index="' . esc_attr($i) . '">Add image</button>
        </div>';
        $i++;
    }
    echo '</div>';
    echo '<button type="button" class="button button-primary" id="buildpro_project_add_standard">Add row</button>';
    echo '</div>';
    echo '<script>
    (function(){
        var wrap = document.getElementById("buildpro_project_standards_wrap");
        var add = document.getElementById("buildpro_project_add_standard");
        function bindRow(row){
            var rm = row.querySelector(".buildpro-remove-standard");
            if(rm){ rm.addEventListener("click", function(e){ e.preventDefault(); row.parentNode.removeChild(row); }); }
            var sel = row.querySelector(".buildpro-select-standard-image");
            if(sel){
                sel.addEventListener("click", function(e){
                    e.preventDefault();
                    var idx = sel.getAttribute("data-index");
                    var frame = wp.media({ title: "Chọn ảnh", button: { text: "Sử dụng" }, multiple: false, library: { type: "image" } });
                    frame.on("select", function(){
                        var a = frame.state().get("selection").first().toJSON();
                        var input = document.getElementById("buildpro_project_standards_image_"+idx);
                        var thumb = document.getElementById("buildpro_project_standards_thumb_"+idx);
                        input.value = a.id;
                        var url = (a.sizes && a.sizes.thumbnail) ? a.sizes.thumbnail.url : a.url;
                        thumb.innerHTML = "<img src=\'"+url+"\' style=\'max-height:76px\'>";
                    });
                    frame.open();
                });
            }
        }
        Array.prototype.forEach.call(wrap.querySelectorAll(".kv-row"), bindRow);
        if(add){
            add.addEventListener("click", function(e){
                e.preventDefault();
                var idx = wrap.querySelectorAll(".kv-row").length;
                var temp = document.createElement("div");
                temp.className = "kv-row";
                temp.setAttribute("data-index", idx);
                temp.innerHTML = "<div class=\\"thumb\\" id=\\"buildpro_project_standards_thumb_"+idx+"\\"><span style=\\"color:#888\\">No image</span></div><input type=\\"hidden\\" name=\\"project_standards["+idx+"][image_id]\\" id=\\"buildpro_project_standards_image_"+idx+"\\"><input type=\\"text\\" name=\\"project_standards["+idx+"][title]\\" placeholder=\\"Title\\" class=\\"regular-text\\"><input type=\\"text\\" name=\\"project_standards["+idx+"][description]\\" placeholder=\\"Description\\" class=\\"regular-text\\"><button type=\\"button\\" class=\\"button buildpro-remove-standard\\">Xóa</button><button type=\\"button\\" class=\\"button buildpro-select-standard-image\\" data-index=\\""+idx+"\\">Chọn ảnh</button>";
                wrap.appendChild(temp);
                bindRow(temp);
            });
        }
    })();
    </script>';
}
