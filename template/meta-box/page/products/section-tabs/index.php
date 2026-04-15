<?php
function render_products_page_tabs()
{
    $tabs = array(
        'buildpro_products_title_meta' => __('Title', 'buildpro')
    );

    echo '<div class="buildpro-admin-tabs" style="margin:0;padding:8px 0;">';

    $first = true;
    foreach ($tabs as $target => $label) {
        $active_class = $first ? ' is-active' : '';
        echo '<button type="button" class="button buildpro-admin-tab' . esc_attr($active_class) . '" data-target="' . esc_attr($target) . '">' . esc_html($label) . '</button> ';
        $first = false;
    }

    echo '</div>';

    $script_path = __DIR__ . '/script.js';
    if (file_exists($script_path)) {
        echo '<script>' . file_get_contents($script_path) . '</script>';
    }

    $style_path = __DIR__ . '/style.css';
    if (file_exists($style_path)) {
        echo '<style>' . file_get_contents($style_path) . '</style>';
    }
}
