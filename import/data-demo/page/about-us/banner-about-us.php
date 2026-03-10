<?php
function buildpro_import_about_us_banner_demo()
{
    $about_id = 0;
    $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => 1));
    if (!empty($pages)) {
        $about_id = (int) $pages[0]->ID;
    }
    if ($about_id <= 0) {
        $about = get_page_by_path('about');
        if ($about && $about->post_type === 'page') {
            $about_id = (int) $about->ID;
        }
    }
    if ($about_id <= 0) {
        return;
    }
    $existing = get_post_meta($about_id, 'buildpro_about_us_banner', true);
    if (is_array($existing) && !empty($existing)) {
        return;
    }
    if (!function_exists('buildpro_import_parse_js')) {
        return;
    }
    $data = buildpro_import_parse_js('/assets/data/about-us-page/banner-data.js', 'aboutUsBannerData');
    if (!is_array($data) || empty($data)) {
        return;
    }
    $left = isset($data['left']) && is_array($data['left']) ? $data['left'] : array();
    $right = isset($data['right']) && is_array($data['right']) ? $data['right'] : array();
    $img_url = isset($right['image']) ? $right['image'] : '';
    $image_id = function_exists('buildpro_import_image_id') ? buildpro_import_image_id($img_url) : 0;
    $items = array();
    if (isset($left['items']) && is_array($left['items'])) {
        foreach ($left['items'] as $it) {
            $items[] = array(
                'text' => isset($it['text']) ? (string)$it['text'] : '',
                'description' => isset($it['description']) ? (string)$it['description'] : '',
            );
        }
    }
    $prepared = array(
        'left' => array(
            'label' => isset($left['label']) ? (string)$left['label'] : '',
            'title' => isset($left['title']) ? (string)$left['title'] : '',
            'description' => isset($left['description']) ? (string)$left['description'] : '',
            'items' => $items,
        ),
        'right' => array(
            'image_id' => $image_id,
        ),
    );
    update_post_meta($about_id, 'buildpro_about_us_banner', $prepared);
    update_post_meta($about_id, 'buildpro_about_us_banner_enabled', 1);
    set_theme_mod('buildpro_about_us_banner', $prepared);
    set_theme_mod('buildpro_about_us_banner_enabled', 1);
}
