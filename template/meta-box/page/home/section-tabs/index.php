<?php

/**
 * Tab Navigation for Home Page Meta Box
 */

function render_home_page_tabs()
{
    $tabs = [
        'buildpro_banner_meta' => 'Banner',
        'buildpro_option_meta' => 'Option',
        'buildpro_data_meta' => 'Data',
        'buildpro_materials_meta' => 'Products',
        'buildpro_services_meta' => 'Services',
        'buildpro_evaluate_meta' => 'Evaluate',
        'buildpro_portfolio_meta' => 'Portfolio',
        'buildpro_post_section_meta' => 'Post'
    ];

    echo '<div class="buildpro-admin-tabs" style="margin:0;padding:8px 0;">';

    $first = true;
    foreach ($tabs as $target => $label) {
        $active_class = $first ? ' is-active' : '';
        echo '<button type="button" class="button buildpro-admin-tab' . $active_class . '" data-target="' . $target . '">' . $label . '</button> ';
        $first = false;
    }

    echo '</div>';

    // Include JavaScript and CSS
    include 'script.js.php';
    include 'style.css.php';
}
