<?php
function buildpro_import_about_us_core_values_demo($target_id = 0)
{
    $about_id = (int)$target_id;
    if ($about_id <= 0) {
        $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => 1));
        if (!empty($pages)) {
            $about_id = (int) $pages[0]->ID;
        }
        if ($about_id <= 0) {
            $about = get_page_by_path('about-us');
            if ($about && $about->post_type === 'page') {
                $about_id = (int) $about->ID;
            }
        }
        if ($about_id <= 0) {
            $about = get_page_by_path('about');
            if ($about && $about->post_type === 'page') {
                $about_id = (int) $about->ID;
            }
        }
    }
    if ($about_id <= 0) {
        return;
    }
    $existing = get_post_meta($about_id, 'buildpro_about_core_values_items', true);
    if (is_array($existing) && !empty($existing)) {
        return;
    }
    if (!function_exists('buildpro_import_parse_js')) {
        return;
    }
    $data = buildpro_import_parse_js('/assets/data/about-us-page/core-values-data.js', 'aboutUsCoreValuesData');
    if (!is_array($data) || empty($data)) {
        return;
    }
    $title = isset($data['title']) ? (string) $data['title'] : '';
    $desc = isset($data['description']) ? (string) $data['description'] : '';
    $items = array();
    if (isset($data['items']) && is_array($data['items'])) {
        foreach ($data['items'] as $it) {
            $items[] = array(
                'icon_id' => 0,
                'icon_url' => '',
                'icon' => isset($it['icon']) ? (string) $it['icon'] : '',
                'title' => isset($it['title']) ? (string) $it['title'] : '',
                'description' => isset($it['description']) ? (string) $it['description'] : '',
                'url' => isset($it['url']) ? (string) $it['url'] : '',
            );
        }
    }
    update_post_meta($about_id, 'buildpro_about_core_values_enabled', 1);
    update_post_meta($about_id, 'buildpro_about_core_values_title', $title);
    update_post_meta($about_id, 'buildpro_about_core_values_description', $desc);
    update_post_meta($about_id, 'buildpro_about_core_values_items', $items);
    set_theme_mod('buildpro_about_core_values_enabled', 1);
    set_theme_mod('buildpro_about_core_values_title', $title);
    set_theme_mod('buildpro_about_core_values_description', $desc);
    set_theme_mod('buildpro_about_core_values_items', $items);
}