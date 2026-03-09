<?php
function buildpro_import_service_demo($target_id = 0)
{
    $home_id = (int) $target_id;
    if (function_exists('buildpro_services_find_home_id')) {
        $home_id = $home_id > 0 ? $home_id : buildpro_services_find_home_id();
    }
    if ($home_id <= 0 && function_exists('buildpro_banner_find_home_id')) {
        $home_id = buildpro_banner_find_home_id();
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
    $existing = get_post_meta($home_id, 'buildpro_service_items', true);
    if (is_array($existing) && !empty($existing)) {
        return;
    }
    $service_title = '';
    $service_desc = '';
    $items = array();
    if (function_exists('buildpro_import_parse_js')) {
        $data = buildpro_import_parse_js('/assets/data/service-data.js', 'servicesData');
        if (is_array($data)) {
            $service_title = isset($data['serviceTitle']) ? (string)$data['serviceTitle'] : '';
            $service_desc = isset($data['serviceDescription']) ? (string)$data['serviceDescription'] : '';
            $items = isset($data['items']) && is_array($data['items']) ? $data['items'] : array();
        }
    }
    if (empty($items)) {
        $path = get_theme_file_path('/assets/data/service-data.js');
        if (file_exists($path)) {
            $src = file_get_contents($path);
            if (is_string($src) && $src !== '') {
                $m = array();
                if (preg_match('/const\s+servicesData\s*=\s*(\{[\s\S]*?\});/m', $src, $m)) {
                    $obj = rtrim($m[1], ';');
                    $json = preg_replace('/([,{]\s*)([A-Za-z_][A-Za-z0-9_]*)\s*:/', '$1"$2":', $obj);
                    $json = preg_replace('/,\s*]/', ']', $json);
                    $json = preg_replace('/,\s*}/', '}', $json);
                    $data = json_decode($json, true);
                    if (is_array($data)) {
                        $service_title = isset($data['serviceTitle']) ? (string)$data['serviceTitle'] : $service_title;
                        $service_desc = isset($data['serviceDescription']) ? (string)$data['serviceDescription'] : $service_desc;
                        $items = isset($data['items']) && is_array($data['items']) ? $data['items'] : $items;
                    }
                }
                if (empty($items)) {
                    $m2 = array();
                    if (preg_match('/items\s*:\s*(\[[\s\S]*?\])/m', $src, $m2)) {
                        $arr = rtrim($m2[1], ';');
                        $json = preg_replace('/([\\{\\s,])([A-Za-z_][A-Za-z0-9_]*)\\s*:/', '$1"$2":', $arr);
                        $json = preg_replace('/,\\s*]/', ']', $json);
                        $json = preg_replace('/,\\s*}/', '}', $json);
                        $decoded = json_decode($json, true);
                        if (is_array($decoded)) {
                            $items = $decoded;
                        }
                    }
                }
            }
        }
    }
    $prepared = array();
    foreach ($items as $it) {
        $icon_id = 0;
        if (isset($it['icon_url']) && function_exists('buildpro_import_image_id')) {
            $icon_id = buildpro_import_image_id($it['icon_url']);
        }
        $prepared[] = array(
            'icon_id' => (int)$icon_id,
            'title' => isset($it['title']) ? (string)$it['title'] : '',
            'description' => isset($it['description']) ? (string)$it['description'] : '',
            'link_url' => isset($it['link_url']) ? (string)$it['link_url'] : '',
            'link_title' => isset($it['link_title']) ? (string)$it['link_title'] : '',
            'link_target' => isset($it['link_target']) ? (string)$it['link_target'] : '',
        );
    }
    if (function_exists('buildpro_services_sanitize_items')) {
        $prepared = buildpro_services_sanitize_items($prepared);
    }
    update_post_meta($home_id, 'buildpro_service_title', $service_title);
    update_post_meta($home_id, 'buildpro_service_desc', $service_desc);
    update_post_meta($home_id, 'buildpro_service_items', $prepared);
    update_post_meta($home_id, 'buildpro_service_enabled', 1);
    set_theme_mod('buildpro_service_title', $service_title);
    set_theme_mod('buildpro_service_desc', $service_desc);
    set_theme_mod('buildpro_service_items', $prepared);
    set_theme_mod('buildpro_service_enabled', 1);
}
