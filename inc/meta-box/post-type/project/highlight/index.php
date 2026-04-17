<?php
function buildpro_project_highlight_add_meta_box($post_type, $post)
{
    if ($post_type !== 'project') {
        return;
    }
    add_meta_box('buildpro_project_tab_highlight', esc_html__('Highlight', 'buildpro'), 'buildpro_project_highlight_render_meta_box', 'project', 'normal', 'default');
}
add_action('add_meta_boxes', 'buildpro_project_highlight_add_meta_box', 10, 2);

function buildpro_project_highlight_render_meta_box($post)
{
    $rows = get_post_meta($post->ID, 'project_highlight_options', true);
    $rows = is_array($rows) ? $rows : array();

    $i18n = wp_json_encode(array(
        'option' => __('Option', 'buildpro'),
        'addRow' => __('Add option', 'buildpro'),
        'remove' => __('Remove', 'buildpro'),
    ));

    echo '<style>
    .buildpro-project-highlight-row{display:grid;grid-template-columns:1fr auto;gap:8px;align-items:center;margin-top:8px}
    </style>';

    echo '<div id="buildpro_project_tab_highlight" class="buildpro-post-block">';
    echo '<div id="buildpro_project_highlight_wrap">';

    $i = 0;
    foreach ($rows as $option) {
        $text = sanitize_text_field($option);
        echo '<div class="buildpro-project-highlight-row" data-index="' . esc_attr($i) . '">'
            . '<input type="text" class="regular-text" name="project_highlight_options[' . esc_attr($i) . ']" value="' . esc_attr($text) . '" placeholder="' . esc_attr__('Option', 'buildpro') . '">'
            . '<button type="button" class="button buildpro-project-highlight-remove">' . esc_html__('Remove', 'buildpro') . '</button>'
            . '</div>';
        $i++;
    }

    echo '</div>';
    echo '<button type="button" class="button button-primary" id="buildpro_project_highlight_add">' . esc_html__('Add option', 'buildpro') . '</button>';
    echo '</div>';

    echo '<script>
    (function(){
        var i18n = ' . $i18n . ';
        var wrap = document.getElementById("buildpro_project_highlight_wrap");
        var add = document.getElementById("buildpro_project_highlight_add");

        function bindRow(row){
            var rm = row.querySelector(".buildpro-project-highlight-remove");
            if(rm){
                rm.addEventListener("click", function(e){
                    e.preventDefault();
                    row.parentNode.removeChild(row);
                });
            }
        }

        Array.prototype.forEach.call(wrap.querySelectorAll(".buildpro-project-highlight-row"), bindRow);

        if(add){
            add.addEventListener("click", function(e){
                e.preventDefault();
                var idx = wrap.querySelectorAll(".buildpro-project-highlight-row").length;
                var temp = document.createElement("div");
                temp.className = "buildpro-project-highlight-row";
                temp.setAttribute("data-index", idx);
                temp.innerHTML = "<input type=\"text\" class=\"regular-text\" name=\"project_highlight_options["+idx+"]\" placeholder=\""+i18n.option+"\"><button type=\"button\" class=\"button buildpro-project-highlight-remove\">"+i18n.remove+"</button>";
                wrap.appendChild(temp);
                bindRow(temp);
            });
        }
    })();
    </script>';
}
