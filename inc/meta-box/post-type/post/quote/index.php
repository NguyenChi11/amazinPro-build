<?php
function buildpro_post_quote_add_meta_box($post_type, $post)
{
    if ($post_type !== 'post') {
        return;
    }
    add_meta_box('buildpro_post_tab_quote', esc_html__('Quote', 'buildpro'), 'buildpro_post_quote_render_meta_box', 'post', 'normal', 'default');
}
add_action('add_meta_boxes', 'buildpro_post_quote_add_meta_box', 10, 2);

function buildpro_post_quote_render_meta_box($post)
{
    $quote_title       = get_post_meta($post->ID, 'buildpro_post_quote_title', true);
    $quote_desc        = get_post_meta($post->ID, 'buildpro_post_quote_description', true);
    $quote_gallery     = get_post_meta($post->ID, 'buildpro_post_quote_gallery', true);
    $quote_gallery     = is_array($quote_gallery) ? $quote_gallery : array();
    $quote_kv          = get_post_meta($post->ID, 'buildpro_post_quote_kv', true);
    $quote_kv          = is_array($quote_kv) ? $quote_kv : array();
    $quote_desc_img_id = (int) get_post_meta($post->ID, 'buildpro_post_quote_desc_image_id', true);
    $quote_desc_img_desc = get_post_meta($post->ID, 'buildpro_post_quote_desc_image_desc', true);
    $quote_desc_img_thumb = $quote_desc_img_id ? wp_get_attachment_image_url($quote_desc_img_id, 'thumbnail') : '';

    $i18n = wp_json_encode([
        'selectImage' => __('Select image', 'buildpro'),
        'add'         => __('Add', 'buildpro'),
        'remove'      => __('Remove', 'buildpro'),
        'key'         => __('Key', 'buildpro'),
        'value'       => __('Value', 'buildpro'),
    ]);

    echo '<style>
    .buildpro-post-block{background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,0.05);padding:16px;margin-top:8px}
    .buildpro-post-field{margin:10px 0}
    .buildpro-post-block .regular-text{width:100%;max-width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px}
    .buildpro-post-block .large-text{width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px}
    .quote-gallery{display:flex;flex-wrap:wrap;gap:10px;margin-top:10px}
    .quote-thumb{width:90px;height:90px;display:flex;align-items:center;justify-content:center;background:#fff;border:1px solid #e5e7eb;border-radius:6px;position:relative;overflow:hidden}
    .quote-thumb img{max-width:100%;max-height:100%}
    .quote-thumb .remove{position:absolute;top:2px;right:2px;background:#ef4444;color:#fff;border:none;border-radius:4px;padding:2px 6px;cursor:pointer}
    .kv-row{display:grid;grid-template-columns:1fr 1fr auto;gap:8px;align-items:center;margin-top:8px}
    </style>';

    echo '<div class="buildpro-post-block">';
    echo '<p class="buildpro-post-field"><label>' . esc_html__('Title', 'buildpro') . '</label><input type="text" name="buildpro_post_quote_title" class="regular-text" value="' . esc_attr($quote_title) . '"></p>';
    echo '<p class="buildpro-post-field"><label>' . esc_html__('Description', 'buildpro') . '</label><textarea name="buildpro_post_quote_description" rows="4" class="large-text">' . esc_textarea($quote_desc) . '</textarea></p>';
    echo '<p class="buildpro-post-field"><label>' . esc_html__('Image Description', 'buildpro') . '</label><textarea name="buildpro_post_quote_desc_image_desc" rows="3" class="large-text">' . esc_textarea($quote_desc_img_desc) . '</textarea></p>';
    echo '<div class="buildpro-post-field"><label>' . esc_html__('Gallery', 'buildpro') . '</label><div class="quote-gallery" id="buildpro_post_quote_gallery">';
    foreach ($quote_gallery as $img_id) {
        $u = wp_get_attachment_image_url((int)$img_id, 'thumbnail');
        if ($u) {
            echo '<div class="quote-thumb" data-id="' . esc_attr($img_id) . '"><img src="' . esc_url($u) . '"><button type="button" class="remove">x</button><input type="hidden" name="buildpro_post_quote_gallery[]" value="' . esc_attr($img_id) . '"></div>';
        }
    }
    echo '</div><button type="button" class="button" id="buildpro_post_add_gallery">' . esc_html__('Add image', 'buildpro') . '</button></div>';
    echo '<div class="buildpro-post-field"><label>' . esc_html__('Quote Repeater', 'buildpro') . '</label><div id="buildpro_post_quote_kv">';
    $i = 0;
    foreach ($quote_kv as $row) {
        $k = isset($row['key']) ? sanitize_text_field($row['key']) : '';
        $v = isset($row['value']) ? sanitize_text_field($row['value']) : '';
        echo '<div class="kv-row" data-index="' . esc_attr($i) . '"><input type="text" name="buildpro_post_quote_kv[' . esc_attr($i) . '][key]" value="' . esc_attr($k) . '" placeholder="' . esc_attr__('Key', 'buildpro') . '" class="regular-text"><input type="text" name="buildpro_post_quote_kv[' . esc_attr($i) . '][value]" value="' . esc_attr($v) . '" placeholder="' . esc_attr__('Value', 'buildpro') . '" class="regular-text"><button type="button" class="button remove-kv">' . esc_html__('Remove', 'buildpro') . '</button></div>';
        $i++;
    }
    echo '</div><button type="button" class="button button-primary" id="buildpro_post_add_kv">' . esc_html__('Add row', 'buildpro') . '</button></div>';
    echo '</div>';

    echo '<script>
    (function(){
        var i18n = ' . $i18n . ';
        var addBtn = document.getElementById("buildpro_post_add_gallery");
        var box = document.getElementById("buildpro_post_quote_gallery");
        var frame;
        if(addBtn){
            addBtn.addEventListener("click", function(e){
                e.preventDefault();
                if(!frame){ frame = wp.media({ title: i18n.selectImage, button: { text: i18n.add }, multiple: true, library: { type: "image" } }); }
                if(typeof frame.off === "function"){ frame.off("select"); }
                frame.on("select", function(){
                    var selection = frame.state().get("selection");
                    selection.each(function(att){
                        var a = att.toJSON();
                        var url = (a.sizes && a.sizes.thumbnail) ? a.sizes.thumbnail.url : a.url;
                        var div = document.createElement("div");
                        div.className = "quote-thumb";
                        div.setAttribute("data-id", a.id);
                        div.innerHTML = "<img src=\'"+url+"\'><button type=\\"button\\" class=\\"remove\\">x</button><input type=\\"hidden\\" name=\\"buildpro_post_quote_gallery[]\\" value=\\""+a.id+"\\">";
                        box.appendChild(div);
                        var rm = div.querySelector(".remove");
                        rm.addEventListener("click", function(ev){ ev.preventDefault(); box.removeChild(div); });
                    });
                });
                frame.open();
            });
        }
        box.addEventListener("click", function(e){
            if(e.target && e.target.classList.contains("remove")){
                e.preventDefault();
                var parent = e.target.closest(".quote-thumb");
                if(parent){ parent.parentNode.removeChild(parent); }
            }
        });
        var wrap = document.getElementById("buildpro_post_quote_kv");
        var add = document.getElementById("buildpro_post_add_kv");
        function bindRow(row){
            var rm = row.querySelector(".remove-kv");
            if(rm){ rm.addEventListener("click", function(e){ e.preventDefault(); row.parentNode.removeChild(row); }); }
        }
        Array.prototype.forEach.call(wrap.querySelectorAll(".kv-row"), bindRow);
        if(add){
            add.addEventListener("click", function(e){
                e.preventDefault();
                var idx = wrap.querySelectorAll(".kv-row").length;
                var temp = document.createElement("div");
                temp.className = "kv-row";
                temp.setAttribute("data-index", idx);
                temp.innerHTML = "<input type=\\"text\\" name=\\"buildpro_post_quote_kv["+idx+"][key]\\" placeholder=\\""+i18n.key+"\\" class=\\"regular-text\\"><input type=\\"text\\" name=\\"buildpro_post_quote_kv["+idx+"][value]\\" placeholder=\\""+i18n.value+"\\" class=\\"regular-text\\"><button type=\\"button\\" class=\\"button remove-kv\\">"+i18n.remove+"</button>";
                wrap.appendChild(temp);
                bindRow(temp);
            });
        }
    })();
    </script>';
}
