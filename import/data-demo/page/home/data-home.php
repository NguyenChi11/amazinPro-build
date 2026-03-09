<?php
function buildpro_import_data_demo()
{
    $home_id = 0;
    if (function_exists('buildpro_data_find_home_id')) {
        $home_id = buildpro_data_find_home_id();
    }
    if ($home_id <= 0) {
        $home_id = (int) get_option('page_on_front');
    }
    if ($home_id <= 0) {
        $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'home-page.php', 'number' => 1));
        if (!empty($pages)) {
            $home_id = (int) $pages[0]->ID;
        }
    }
    if ($home_id <= 0) {
        return;
    }
    $existing = get_post_meta($home_id, 'buildpro_data_items', true);
    if (is_array($existing) && !empty($existing)) {
        return;
    }
    $path = get_theme_file_path('/assets/data/data-items.js');
    if (!file_exists($path)) {
        return;
    }
    $src = file_get_contents($path);
    if (!is_string($src) || $src === '') {
        return;
    }
    $m = array();
    if (!preg_match('/const\s+dataItems\s*=\s*(\[[\s\S]*?\]);/m', $src, $m)) {
        return;
    }
    $arr = rtrim($m[1], ';');
    $json = preg_replace('/([\\{\\s,])([A-Za-z_][A-Za-z0-9_]*)\\s*:/', '$1"$2":', $arr);
    $json = preg_replace('/,\\s*]/', ']', $json);
    $json = preg_replace('/,\\s*}/', '}', $json);
    $items = json_decode($json, true);
    if (!is_array($items)) {
        return;
    }
    $prepared = array();
    foreach ($items as $it) {
        $prepared[] = array(
            'number' => isset($it['number']) ? (string)$it['number'] : '',
            'text' => isset($it['text']) ? (string)$it['text'] : '',
        );
    }
    if (function_exists('buildpro_data_sanitize_items')) {
        $prepared = buildpro_data_sanitize_items($prepared);
    }
    update_post_meta($home_id, 'buildpro_data_items', $prepared);
    update_post_meta($home_id, 'buildpro_data_enabled', 1);
    set_theme_mod('buildpro_data_items', $prepared);
    set_theme_mod('buildpro_data_enabled', 1);
}
