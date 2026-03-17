<?php

/**
 * Tab Navigation for About Us Page Meta Box
 */

function render_about_us_page_tabs()
{
    $tabs = [
        'buildpro_about_banner_meta' => 'Banner',
        'buildpro_about_core_values_meta' => 'Core Values',
        'buildpro_about_leader_meta' => 'Leader',
        'buildpro_about_policy_meta' => 'Policy',
        'buildpro_about_contact_meta' => 'Contact'
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
