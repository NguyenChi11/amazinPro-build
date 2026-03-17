<?php

/**
 * Tab Navigation for Projects Page Meta Box
 */

function render_projects_page_tabs()
{
    $tabs = [
        'buildpro_projects_title_meta' => 'Title'
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
