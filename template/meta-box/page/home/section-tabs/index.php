<?php

/**
 * Tab Navigation for Home Page Meta Box
 */

function render_home_page_tabs()
{
    $tabs = [
        'buildpro_banner_meta' => esc_html__('Banner', 'buildpro'),
        'buildpro_data_meta' => esc_html__('Data', 'buildpro'),
        'buildpro_materials_meta' => esc_html__('Products', 'buildpro'),
        'buildpro_services_meta' => esc_html__('Services', 'buildpro'),
        'buildpro_evaluate_meta' => esc_html__('Evaluate', 'buildpro'),
        'buildpro_portfolio_meta' => esc_html__('Projects', 'buildpro'),
        'buildpro_post_section_meta' => esc_html__('Post', 'buildpro')
    ];

    echo '<div class="buildpro-admin-tabs" style="margin:0;padding:8px 0;">';

    $first = true;
    foreach ($tabs as $target => $label) {
        $active_class = $first ? ' is-active' : '';
        echo '<button type="button" class="button buildpro-admin-tab' . $active_class . '" data-target="' . esc_attr($target) . '">' . $label . '</button> ';
        $first = false;
    }

    echo '</div>';

    // Include JavaScript and CSS
    include 'script.js.php';
    include 'style.css.php';
}
