<?php
function buildpro_project_key_infomation_add_meta_box($post_type, $post)
{
    if ($post_type !== 'project') {
        return;
    }
    add_meta_box('buildpro_project_tab_key_infomation', esc_html__('Key Information', 'buildpro'), 'buildpro_project_key_infomation_render_meta_box', 'project', 'normal', 'default');
}
add_action('add_meta_boxes', 'buildpro_project_key_infomation_add_meta_box', 10, 2);

function buildpro_project_key_infomation_render_meta_box($post)
{
    $rows = get_post_meta($post->ID, 'project_key_infomation', true);
    $rows = is_array($rows) ? $rows : array();

    $i18n = wp_json_encode(array(
        'key' => __('Key', 'buildpro'),
        'value' => __('Value', 'buildpro'),
        'addRow' => __('Add row', 'buildpro'),
        'remove' => __('Remove', 'buildpro'),
    ));

    echo '<style>
    .buildpro-project-kv-row{display:grid;grid-template-columns:1fr 1fr auto;gap:8px;align-items:center;margin-top:8px}
    </style>';

    echo '<div id="buildpro_project_tab_key_infomation" class="buildpro-post-block">';
    echo '<div id="buildpro_project_key_infomation_wrap">';

    $i = 0;
    foreach ($rows as $row) {
        $key = isset($row['key']) ? sanitize_text_field($row['key']) : '';
        $value = isset($row['value']) ? sanitize_text_field($row['value']) : '';
        echo '<div class="buildpro-project-kv-row" data-index="' . esc_attr($i) . '">'
            . '<input type="text" class="regular-text" name="project_key_infomation[' . esc_attr($i) . '][key]" value="' . esc_attr($key) . '" placeholder="' . esc_attr__('Key', 'buildpro') . '">'
            . '<input type="text" class="regular-text" name="project_key_infomation[' . esc_attr($i) . '][value]" value="' . esc_attr($value) . '" placeholder="' . esc_attr__('Value', 'buildpro') . '">'
            . '<button type="button" class="button buildpro-project-kv-remove">' . esc_html__('Remove', 'buildpro') . '</button>'
            . '</div>';
        $i++;
    }

    echo '</div>';
    echo '<button type="button" class="button button-primary" id="buildpro_project_key_infomation_add">' . esc_html__('Add row', 'buildpro') . '</button>';
    echo '</div>';

    echo '<script>
    (function(){
        var i18n = ' . $i18n . ';
        var wrap = document.getElementById("buildpro_project_key_infomation_wrap");
        var add = document.getElementById("buildpro_project_key_infomation_add");

        function bindRow(row){
            var rm = row.querySelector(".buildpro-project-kv-remove");
            if(rm){
                rm.addEventListener("click", function(e){
                    e.preventDefault();
                    row.parentNode.removeChild(row);
                });
            }
        }

        Array.prototype.forEach.call(wrap.querySelectorAll(".buildpro-project-kv-row"), bindRow);

        if(add){
            add.addEventListener("click", function(e){
                e.preventDefault();
                var idx = wrap.querySelectorAll(".buildpro-project-kv-row").length;
                var temp = document.createElement("div");
                temp.className = "buildpro-project-kv-row";
                temp.setAttribute("data-index", idx);
                temp.innerHTML = "<input type=\"text\" class=\"regular-text\" name=\"project_key_infomation["+idx+"][key]\" placeholder=\""+i18n.key+"\"><input type=\"text\" class=\"regular-text\" name=\"project_key_infomation["+idx+"][value]\" placeholder=\""+i18n.value+"\"><button type=\"button\" class=\"button buildpro-project-kv-remove\">"+i18n.remove+"</button>";
                wrap.appendChild(temp);
                bindRow(temp);
            });
        }
    })();
    </script>';
}
