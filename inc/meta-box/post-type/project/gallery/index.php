<?php
function buildpro_project_gallery_add_meta_box($post_type, $post)
{
    if ($post_type !== 'project') {
        return;
    }
    add_meta_box('buildpro_project_tab_gallery', 'Gallery', 'buildpro_project_gallery_render_meta_box', 'project', 'normal', 'default');
}
add_action('add_meta_boxes', 'buildpro_project_gallery_add_meta_box', 10, 2);

function buildpro_project_gallery_render_meta_box($post)
{
    wp_enqueue_media();
    $gallery_raw = get_post_meta($post->ID, 'project_gallery_ids', true);
    $gallery_ids = is_array($gallery_raw) ? $gallery_raw : (is_string($gallery_raw) ? array_filter(array_map('intval', explode(',', $gallery_raw))) : array());
    echo '<style>
    .buildpro-post-block{background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,0.05);padding:16px;margin-top:8px}
    .material-gallery{display:flex;gap:10px;flex-wrap:wrap}
    .material-gallery-item{position:relative;width:96px;height:96px;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,0.05)}
    .material-gallery-item img{width:100%;height:100%;object-fit:cover}
    .material-gallery-remove{position:absolute;top:4px;right:4px;background:#ef4444;color:#fff;border:none;border-radius:50%;width:22px;height:22px;line-height:22px;text-align:center;cursor:pointer;box-shadow:0 1px 2px rgba(0,0,0,0.2)}
    </style>';
    echo '<div id="buildpro_project_tab_gallery" class="buildpro-post-block">';
    echo '<p class="buildpro-post-field"><input type="hidden" id="buildpro_project_gallery_ids" name="project_gallery_ids" value="' . esc_attr(implode(',', $gallery_ids)) . '"> <button type="button" class="button button-primary" id="buildpro_project_add_gallery">Add photo</button> <button type="button" class="button" id="buildpro_project_clear_gallery">Clear gallery</button></p>';
    echo '<div class="material-gallery" id="buildpro_project_gallery_wrap">';
    foreach ($gallery_ids as $gid) {
        $gthumb = wp_get_attachment_image_url($gid, 'thumbnail');
        if ($gthumb) {
            echo '<div class="material-gallery-item" data-id="' . esc_attr($gid) . '"><img src="' . esc_url($gthumb) . '"><button type="button" class="button-link material-gallery-remove" aria-label="Remove">×</button></div>';
        }
    }
    echo '</div>';
    echo '</div>';
    echo '<script>
    (function(){
        var addBtn = document.getElementById("buildpro_project_add_gallery");
        var clearBtn = document.getElementById("buildpro_project_clear_gallery");
        var input = document.getElementById("buildpro_project_gallery_ids");
        var wrap = document.getElementById("buildpro_project_gallery_wrap");
        var frame;
        function parseIds(val){
            return (val ? val.split(",").filter(Boolean).map(function(x){ return parseInt(x,10)||0; }) : []);
        }
        function unique(arr){
            var seen = {};
            return arr.filter(function(id){ if(!id) return false; if(seen[id]) return false; seen[id]=1; return true; });
        }
        function render(ids){
            wrap.innerHTML = "";
            ids.forEach(function(id){
                var u = wp.media && wp.media.attachment ? wp.media.attachment(id) : null;
                var done = function(url){
                    var div = document.createElement("div");
                    div.className = "material-gallery-item";
                    div.setAttribute("data-id", id);
                    var img = document.createElement("img");
                    img.src = url || "";
                    var btn = document.createElement("button");
                    btn.type = "button";
                    btn.className = "button-link material-gallery-remove";
                    btn.setAttribute("aria-label","Remove");
                    btn.textContent = "×";
                    div.appendChild(img);
                    div.appendChild(btn);
                    wrap.appendChild(div);
                };
                if(u){
                    u.fetch().then(function(){
                        var url = (u.attributes && u.attributes.sizes && u.attributes.sizes.thumbnail) ? u.attributes.sizes.thumbnail.url : (u.attributes ? u.attributes.url : "");
                        done(url);
                    });
                } else {
                    done("");
                }
            });
        }
        if(addBtn){
            addBtn.addEventListener("click", function(e){
                e.preventDefault();
                if(!frame){ frame = wp.media({ frame: "select", title: "Add photo to gallery", button: { text: "Add to gallery" }, multiple: "add", library: { type: "image" } }); }
                if(typeof frame.off === "function"){ frame.off("select"); }
                frame.on("select", function(){
                    var selection = frame.state().get("selection").toArray().map(function(m){ return m.toJSON(); });
                    var ids = parseIds(input.value);
                    selection.forEach(function(a){ ids.push(a.id); });
                    ids = unique(ids);
                    input.value = ids.join(",");
                    render(ids);
                });
                frame.open();
            });
        }
        if(clearBtn){
            clearBtn.addEventListener("click", function(e){
                e.preventDefault();
                input.value = "";
                render([]);
            });
        }
        wrap.addEventListener("click", function(e){
            var btn = e.target.closest && e.target.closest(".material-gallery-remove");
            if(!btn) return;
            e.preventDefault();
            var item = btn.closest(".material-gallery-item");
            var id = parseInt(item && item.getAttribute("data-id"),10)||0;
            var ids = parseIds(input.value).filter(function(x){ return x !== id; });
            input.value = ids.join(",");
            item && item.remove();
        });
    })();
    </script>';
}
