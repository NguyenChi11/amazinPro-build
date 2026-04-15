<?php
function buildpro_import_products_title_demo()
{
    $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'products-page.php', 'number' => 1));
    if (empty($pages)) {
        return;
    }

    $page_id = (int) $pages[0]->ID;
    $title = get_post_meta($page_id, 'products_title', true);
    $desc = get_post_meta($page_id, 'products_description', true);
    if ($title !== '' || $desc !== '') {
        return;
    }

    $path = get_theme_file_path('/assets/data/product-title-data.js');
    if (!file_exists($path)) {
        return;
    }

    $src = file_get_contents($path);
    if (!is_string($src) || $src === '') {
        return;
    }

    $m = array();
    if (!preg_match('/window\\s*\\.\\s*buildproProductTitleData\\s*=\\s*(\\{[\\s\\S]*?\\});/m', $src, $m)) {
        return;
    }

    $obj = rtrim($m[1], ';');
    $json = preg_replace('/([,{]\\s*)([A-Za-z_][A-Za-z0-9_]*)\\s*:/', '$1"$2":', $obj);
    $json = preg_replace('/,\\s*]/', ']', $json);
    $json = preg_replace('/,\\s*}/', '}', $json);
    if (!is_string($json)) {
        return;
    }
    $data = json_decode($json, true);
    if (!is_array($data)) {
        return;
    }

    $title = isset($data['title']) ? (string) $data['title'] : '';
    $desc = isset($data['description']) ? (string) $data['description'] : '';

    update_post_meta($page_id, 'products_title', $title);
    update_post_meta($page_id, 'products_description', $desc);
    set_theme_mod('products_title', $title);
    set_theme_mod('products_description', $desc);
}
