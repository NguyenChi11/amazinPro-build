<?php
function buildpro_import_evaluate_demo($target_id = 0)
{
    $home_id = (int) $target_id;
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
    $existing = get_post_meta($home_id, 'buildpro_evaluate_items', true);
    if (is_array($existing) && !empty($existing)) {
        return;
    }
    $title = '';
    $text = '';
    $desc = '';
    $items = array();
    if (function_exists('buildpro_import_parse_js')) {
        $data = buildpro_import_parse_js('/assets/data/evaluate-date.js', 'evaluateData');
        if (is_array($data)) {
            $title = isset($data['evaluateTitle']) ? (string)$data['evaluateTitle'] : '';
            $text = isset($data['evaluateText']) ? (string)$data['evaluateText'] : '';
            $desc = isset($data['evaluateDescription']) ? (string)$data['evaluateDescription'] : '';
            $items = isset($data['items']) && is_array($data['items']) ? $data['items'] : array();
        }
    }
    if (empty($items)) {
        $path = get_theme_file_path('/assets/data/evaluate-date.js');
        if (file_exists($path)) {
            $src = file_get_contents($path);
            if (is_string($src) && $src !== '') {
                $m = array();
                if (preg_match('/const\s+evaluateData\s*=\s*(\{[\s\S]*?\});/m', $src, $m)) {
                    $obj = rtrim($m[1], ';');
                    $json = preg_replace('/([,{]\s*)([A-Za-z_][A-Za-z0-9_]*)\s*:/', '$1"$2":', $obj);
                    $json = preg_replace('/,\s*]/', ']', $json);
                    $json = preg_replace('/,\s*}/', '}', $json);
                    $data = json_decode($json, true);
                    if (is_array($data)) {
                        $title = isset($data['evaluateTitle']) ? (string)$data['evaluateTitle'] : $title;
                        $text = isset($data['evaluateText']) ? (string)$data['evaluateText'] : $text;
                        $desc = isset($data['evaluateDescription']) ? (string)$data['evaluateDescription'] : $desc;
                        $items = isset($data['items']) && is_array($data['items']) ? $data['items'] : $items;
                    }
                }
            }
        }
    }
    $prepared = array();
    foreach ($items as $it) {
        $avatar_id = 0;
        if (isset($it['avatar']) && function_exists('buildpro_import_image_id')) {
            $avatar_id = buildpro_import_image_id($it['avatar']);
        }
        $prepared[] = array(
            'name' => isset($it['name']) ? (string)$it['name'] : '',
            'position' => isset($it['position']) ? (string)$it['position'] : '',
            'description' => isset($it['description']) ? (string)$it['description'] : '',
            'avatar_id' => (int)$avatar_id,
        );
    }
    update_post_meta($home_id, 'buildpro_evaluate_title', $title);
    update_post_meta($home_id, 'buildpro_evaluate_text', $text);
    update_post_meta($home_id, 'buildpro_evaluate_desc', $desc);
    update_post_meta($home_id, 'buildpro_evaluate_items', $prepared);
    update_post_meta($home_id, 'buildpro_evaluate_enabled', 1);
    set_theme_mod('buildpro_evaluate_title', $title);
    set_theme_mod('buildpro_evaluate_text', $text);
    set_theme_mod('buildpro_evaluate_desc', $desc);
    set_theme_mod('buildpro_evaluate_items', $prepared);
    set_theme_mod('buildpro_evaluate_enabled', 1);
    set_theme_mod('buildpro_evaluate_data', array(
        'title' => $title,
        'text' => $text,
        'desc' => $desc,
        'items' => $prepared,
    ));
}
