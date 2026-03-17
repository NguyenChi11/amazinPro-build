<?php

/**
 * Tab Navigation for About Us Page Meta Box
 */

function render_about_us_page_tabs()
{
    $tabs = [
        'buildpro_about_banner_meta' => __('Banner', 'buildpro'),
        'buildpro_about_core_values_meta' => __('Core Values', 'buildpro'),
        'buildpro_about_leader_meta' => __('Leader', 'buildpro'),
        'buildpro_about_policy_meta' => __('Policy', 'buildpro'),
        'buildpro_about_contact_meta' => __('Contact', 'buildpro')
    ];

    echo '<div class="buildpro-admin-tabs" style="margin:0;padding:8px 0;">';

    $first = true;
    foreach ($tabs as $target => $label) {
        $active_class = $first ? ' is-active' : '';
        echo '<button type="button" class="button buildpro-admin-tab' . esc_attr($active_class) . '" data-target="' . esc_attr($target) . '">' . esc_html($label) . '</button> ';
        $first = false;
    }

    echo '</div>';

    // Include JavaScript and CSS
    include 'script.js.php';
    include 'style.css.php';
}
