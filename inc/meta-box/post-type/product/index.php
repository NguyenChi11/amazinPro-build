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

    $overview = (string) get_post_meta($post->ID, 'buildpro_product_overview', true);
    $area = (string) get_post_meta($post->ID, 'buildpro_product_area', true);
    $location = (string) get_post_meta($post->ID, 'buildpro_product_location', true);
    $typical_range = (string) get_post_meta($post->ID, 'typical_range', true);
    $lot_size = (string) get_post_meta($post->ID, 'buildpro_product_lot_size', true);
    $bedrooms = (string) get_post_meta($post->ID, 'buildpro_product_bedrooms', true);
    $bathrooms = (string) get_post_meta($post->ID, 'buildpro_product_bathrooms', true);
    $garage = (string) get_post_meta($post->ID, 'buildpro_product_garage', true);
    $year_built = (string) get_post_meta($post->ID, 'buildpro_product_year_built', true);
    $floors = (string) get_post_meta($post->ID, 'buildpro_product_floors', true);
    $features_meta = get_post_meta($post->ID, 'buildpro_product_features', true);
    $interior_features_meta = get_post_meta($post->ID, 'buildpro_product_interior_features', true);

    $features_items = array();
    if (is_array($features_meta)) {
        $features_items = $features_meta;
    } else {
        $features_items = explode("\n", str_replace(array("\r\n", "\r"), "\n", (string) $features_meta));
    }
    $features_items = array_values(array_filter(array_map('trim', (array) $features_items), function ($item) {
        return $item !== '';
    }));
    if (empty($features_items)) {
        $features_items = array('');
    }

    $interior_feature_items = array();
    if (is_array($interior_features_meta)) {
        $interior_feature_items = $interior_features_meta;
    } else {
        $interior_feature_items = explode("\n", str_replace(array("\r\n", "\r"), "\n", (string) $interior_features_meta));
    }
    $interior_feature_items = array_values(array_filter(array_map('trim', (array) $interior_feature_items), function ($item) {
        return $item !== '';
    }));
    if (empty($interior_feature_items)) {
        $interior_feature_items = array('');
    }

    echo '<div class="buildpro-product-meta-grid">';

    echo '<div class="buildpro-product-meta-col">';
    echo '<div class="buildpro-post-block">';
    echo '<h3 class="buildpro-post-title">' . esc_html__('Overview', 'buildpro') . '</h3>';
    echo '<p class="buildpro-post-field">';
    echo '<label for="buildpro_product_overview">' . esc_html__('Summary Description', 'buildpro') . '</label>';
    echo '<textarea id="buildpro_product_overview" name="buildpro_product_overview" rows="5" class="large-text" placeholder="' . esc_attr__('Write overview text shown in product details section.', 'buildpro') . '">' . esc_textarea($overview) . '</textarea>';
    echo '</p>';
    echo '</div>';

    echo '<div class="buildpro-post-block">';
    echo '<h3 class="buildpro-post-title">' . esc_html__('Key Information', 'buildpro') . '</h3>';
    echo '<div class="buildpro-product-fields-grid">';

    echo '<p class="buildpro-post-field"><label for="buildpro_product_area">' . esc_html__('Area', 'buildpro') . '</label><input id="buildpro_product_area" type="text" name="buildpro_product_area" class="regular-text" placeholder="' . esc_attr__('e.g. 2,350 sqft', 'buildpro') . '" value="' . esc_attr($area) . '"></p>';
    echo '<p class="buildpro-post-field"><label for="buildpro_product_location">' . esc_html__('Location', 'buildpro') . '</label><input id="buildpro_product_location" type="text" name="buildpro_product_location" class="regular-text" placeholder="' . esc_attr__('e.g. New York, USA', 'buildpro') . '" value="' . esc_attr($location) . '"></p>';
    echo '<p class="buildpro-post-field"><label for="typical_range">' . esc_html__('Typical Range', 'buildpro') . '</label><input id="typical_range" type="text" name="typical_range" class="regular-text" placeholder="' . esc_attr__('e.g. C25-C40', 'buildpro') . '" value="' . esc_attr($typical_range) . '"></p>';
    echo '<p class="buildpro-post-field"><label for="buildpro_product_lot_size">' . esc_html__('Lot Size', 'buildpro') . '</label><input id="buildpro_product_lot_size" type="text" name="buildpro_product_lot_size" class="regular-text" placeholder="' . esc_attr__('e.g. 4,500 sqft', 'buildpro') . '" value="' . esc_attr($lot_size) . '"></p>';
    echo '<p class="buildpro-post-field"><label for="buildpro_product_bedrooms">' . esc_html__('Bedrooms', 'buildpro') . '</label><input id="buildpro_product_bedrooms" type="number" min="0" step="1" name="buildpro_product_bedrooms" class="regular-text" value="' . esc_attr($bedrooms) . '"></p>';
    echo '<p class="buildpro-post-field"><label for="buildpro_product_bathrooms">' . esc_html__('Bathrooms', 'buildpro') . '</label><input id="buildpro_product_bathrooms" type="number" min="0" step="0.5" name="buildpro_product_bathrooms" class="regular-text" value="' . esc_attr($bathrooms) . '"></p>';
    echo '<p class="buildpro-post-field"><label for="buildpro_product_garage">' . esc_html__('Garage', 'buildpro') . '</label><input id="buildpro_product_garage" type="text" name="buildpro_product_garage" class="regular-text" placeholder="' . esc_attr__('e.g. 2 cars', 'buildpro') . '" value="' . esc_attr($garage) . '"></p>';
    echo '<p class="buildpro-post-field"><label for="buildpro_product_year_built">' . esc_html__('Year Built', 'buildpro') . '</label><input id="buildpro_product_year_built" type="number" min="0" step="1" name="buildpro_product_year_built" class="regular-text" placeholder="' . esc_attr__('e.g. 2018', 'buildpro') . '" value="' . esc_attr($year_built) . '"></p>';
    echo '<p class="buildpro-post-field"><label for="buildpro_product_floors">' . esc_html__('Floors', 'buildpro') . '</label><input id="buildpro_product_floors" type="number" min="0" step="1" name="buildpro_product_floors" class="regular-text" placeholder="' . esc_attr__('e.g. 2', 'buildpro') . '" value="' . esc_attr($floors) . '"></p>';

    echo '</div>';
    echo '</div>';
    echo '</div>';

    echo '<div class="buildpro-product-meta-col">';
    echo '<div class="buildpro-post-block">';
    echo '<h3 class="buildpro-post-title">' . esc_html__('Features', 'buildpro') . '</h3>';
    echo '<p class="buildpro-post-field">';
    echo '<label>' . esc_html__('Feature List', 'buildpro') . '</label>';
    echo '<div class="buildpro-list-editor" data-field-name="buildpro_product_features_items">';
    echo '<div class="buildpro-list-items">';
    foreach ($features_items as $item) {
        echo '<div class="buildpro-list-item">';
        echo '<input type="text" name="buildpro_product_features_items[]" class="regular-text" value="' . esc_attr($item) . '" placeholder="' . esc_attr__('e.g. Hardwood Floors', 'buildpro') . '">';
        echo '<button type="button" class="button-link-delete buildpro-remove-item">' . esc_html__('Remove', 'buildpro') . '</button>';
        echo '</div>';
    }
    echo '</div>';
    echo '<button type="button" class="button button-secondary buildpro-add-item">' . esc_html__('Add Item', 'buildpro') . '</button>';
    echo '</div>';
    echo '<span class="description">' . esc_html__('Add each feature as a separate item.', 'buildpro') . '</span>';
    echo '</p>';
    echo '</div>';

    echo '<div class="buildpro-post-block">';
    echo '<h3 class="buildpro-post-title">' . esc_html__('Interior Features', 'buildpro') . '</h3>';
    echo '<p class="buildpro-post-field">';
    echo '<label>' . esc_html__('Interior Feature List', 'buildpro') . '</label>';
    echo '<div class="buildpro-list-editor" data-field-name="buildpro_product_interior_features_items">';
    echo '<div class="buildpro-list-items">';
    foreach ($interior_feature_items as $item) {
        echo '<div class="buildpro-list-item">';
        echo '<input type="text" name="buildpro_product_interior_features_items[]" class="regular-text" value="' . esc_attr($item) . '" placeholder="' . esc_attr__('e.g. Open floor plan', 'buildpro') . '">';
        echo '<button type="button" class="button-link-delete buildpro-remove-item">' . esc_html__('Remove', 'buildpro') . '</button>';
        echo '</div>';
    }
    echo '</div>';
    echo '<button type="button" class="button button-secondary buildpro-add-item">' . esc_html__('Add Item', 'buildpro') . '</button>';
    echo '</div>';
    echo '<span class="description">' . esc_html__('Add each interior feature as a separate item.', 'buildpro') . '</span>';
    echo '</p>';
    echo '</div>';
    echo '</div>';

    echo '</div>';

    echo '<style>
        .buildpro-product-meta-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;align-items:start;margin-top:6px}
        .buildpro-product-meta-col{min-width:0}
        .buildpro-post-block{background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,0.05);padding:16px}
        .buildpro-post-block + .buildpro-post-block{margin-top:12px}
        .buildpro-post-title{margin:0 0 12px;color:#111827;font-size:18px;line-height:1.3;font-weight:700}
        .buildpro-post-field{margin:10px 0}
        .buildpro-post-field label{display:block;font-weight:600;margin-bottom:6px;color:#374151}
        .buildpro-post-block .regular-text,.buildpro-post-block .large-text{width:100%;max-width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px}
        .buildpro-post-block textarea.large-text{min-height:120px}
        .buildpro-product-fields-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));column-gap:12px}
        .buildpro-list-editor{border:1px solid #e5e7eb;border-radius:8px;padding:10px;background:#f9fafb}
        .buildpro-list-items{display:grid;gap:8px}
        .buildpro-list-item{display:flex;align-items:center;gap:8px}
        .buildpro-list-item .regular-text{flex:1 1 auto}
        .buildpro-list-item .button-link-delete{color:#b91c1c;text-decoration:none}
        .buildpro-list-item .button-link-delete:hover{color:#991b1b}
        .buildpro-add-item{margin-top:10px}
        @media (max-width: 1200px){
            .buildpro-product-meta-grid{grid-template-columns:1fr}
        }
        @media (max-width: 782px){
            .buildpro-product-fields-grid{grid-template-columns:1fr}
            .buildpro-list-item{flex-direction:column;align-items:stretch}
        }
    </style>';

    echo '<script>
        (function(){
            var root = document.currentScript ? document.currentScript.parentElement : document;
            if (!root) {
                root = document;
            }
            var editors = root.querySelectorAll(".buildpro-list-editor");
            editors.forEach(function(editor){
                var addBtn = editor.querySelector(".buildpro-add-item");
                var list = editor.querySelector(".buildpro-list-items");
                var fieldName = editor.getAttribute("data-field-name") || "buildpro_items";

                function createItem(value){
                    var row = document.createElement("div");
                    row.className = "buildpro-list-item";

                    var input = document.createElement("input");
                    input.type = "text";
                    input.name = fieldName + "[]";
                    input.className = "regular-text";
                    input.value = value || "";

                    var remove = document.createElement("button");
                    remove.type = "button";
                    remove.className = "button-link-delete buildpro-remove-item";
                    remove.textContent = "' . esc_js(__('Remove', 'buildpro')) . '";

                    row.appendChild(input);
                    row.appendChild(remove);
                    return row;
                }

                addBtn.addEventListener("click", function(){
                    list.appendChild(createItem(""));
                });

                list.addEventListener("click", function(event){
                    var target = event.target;
                    if (!target.classList.contains("buildpro-remove-item")) {
                        return;
                    }
                    var rows = list.querySelectorAll(".buildpro-list-item");
                    if (rows.length <= 1) {
                        var input = rows[0].querySelector("input");
                        if (input) {
                            input.value = "";
                        }
                        return;
                    }
                    var row = target.closest(".buildpro-list-item");
                    if (row) {
                        row.remove();
                    }
                });
            });
        })();
    </script>';
}
