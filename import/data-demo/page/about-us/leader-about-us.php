<?php
function buildpro_import_about_us_leader_demo($target_id = 0)
{
    $about_id = (int)$target_id;
    if ($about_id <= 0) {
        $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => 1));
        if (!empty($pages)) {
            $about_id = (int)$pages[0]->ID;
        }
        if ($about_id <= 0) {
            $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-us-page.php', 'number' => 1));
            if (!empty($pages)) {
                $about_id = (int)$pages[0]->ID;
            }
        }
        if ($about_id <= 0) {
            $about = get_page_by_path('about-us');
            if ($about && $about->post_type === 'page') {
                $about_id = (int)$about->ID;
            }
        }
        if ($about_id <= 0) {
            $about = get_page_by_path('about');
            if ($about && $about->post_type === 'page') {
                $about_id = (int)$about->ID;
            }
        }
    }
    if ($about_id <= 0) {
        return;
    }
    $existing = get_post_meta($about_id, 'buildpro_about_leader_items', true);
    if (is_array($existing) && !empty($existing)) {
        return;
    }
    if (!function_exists('buildpro_import_parse_js')) {
        return;
    }
    $data = buildpro_import_parse_js('/assets/data/about-us-page/leader-data.js', 'leaderData');
    if (!is_array($data) || empty($data)) {
        return;
    }
    $title = isset($data['title']) ? (string)$data['title'] : '';
    $text = isset($data['description']) ? (string)$data['description'] : '';
    $executives = isset($data['core_executive']) ? (string)$data['core_executive'] : '';
    $workforce = isset($data['total_workforce']) ? (string)$data['total_workforce'] : '';
    $items = array();
    if (isset($data['items']) && is_array($data['items'])) {
        foreach ($data['items'] as $it) {
            $img = isset($it['image']) ? $it['image'] : '';
            $iid = function_exists('buildpro_import_image_id') ? buildpro_import_image_id($img) : 0;
            $items[] = array(
                'icon_id' => $iid,
                'icon_url' => '',
                'name' => isset($it['name']) ? (string)$it['name'] : '',
                'position' => isset($it['position']) ? (string)$it['position'] : '',
                'description' => isset($it['description']) ? (string)$it['description'] : '',
                'url' => isset($it['url']) ? (string)$it['url'] : '',
            );
        }
    }
    update_post_meta($about_id, 'buildpro_about_leader_enabled', 1);
    update_post_meta($about_id, 'buildpro_about_leader_title', $title);
    update_post_meta($about_id, 'buildpro_about_leader_text', $text);
    update_post_meta($about_id, 'buildpro_about_leader_executives', $executives);
    update_post_meta($about_id, 'buildpro_about_leader_workforce', $workforce);
    update_post_meta($about_id, 'buildpro_about_leader_items', $items);
    set_theme_mod('buildpro_about_leader_enabled', 1);
    set_theme_mod('buildpro_about_leader_title', $title);
    set_theme_mod('buildpro_about_leader_text', $text);
    set_theme_mod('buildpro_about_leader_executives', $executives);
    set_theme_mod('buildpro_about_leader_workforce', $workforce);
    set_theme_mod('buildpro_about_leader_items', $items);
}
