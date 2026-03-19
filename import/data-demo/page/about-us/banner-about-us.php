<?php
function buildpro_import_about_us_banner_demo()
{
    $about_id = 0;
    $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-us-page.php', 'number' => 1));
    if (!empty($pages)) {
        $about_id = (int) $pages[0]->ID;
    }
    if ($about_id <= 0) {
        $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => 1));
        if (!empty($pages)) {
            $about_id = (int) $pages[0]->ID;
        }
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

    // Don't override if the banner is already configured.
    $existing_title = get_post_meta($about_id, 'buildpro_about_banner_title', true);
    $existing_img = (int) get_post_meta($about_id, 'buildpro_about_banner_image_id', true);
    $existing_facts = get_post_meta($about_id, 'buildpro_about_banner_facts', true);
    if ($existing_title !== '' || $existing_img > 0 || (is_array($existing_facts) && !empty($existing_facts))) {
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

    $img_url = isset($right['image']) ? (string) $right['image'] : '';
    $image_id = function_exists('buildpro_import_image_id') ? buildpro_import_image_id($img_url) : 0;

    $facts = array();
    if (isset($left['items']) && is_array($left['items'])) {
        foreach ($left['items'] as $it) {
            $lbl = isset($it['text']) ? trim((string) $it['text']) : '';
            $val = isset($it['description']) ? trim((string) $it['description']) : '';
            if ($lbl === '' && $val === '') {
                continue;
            }
            $facts[] = array(
                'label' => $lbl,
                'value' => $val,
            );
        }
    }

    $text = isset($left['label']) ? (string) $left['label'] : '';
    $title = isset($left['title']) ? (string) $left['title'] : '';
    $desc = isset($left['description']) ? (string) $left['description'] : '';

    update_post_meta($about_id, 'buildpro_about_banner_enabled', 1);
    update_post_meta($about_id, 'buildpro_about_banner_text', $text);
    update_post_meta($about_id, 'buildpro_about_banner_title', $title);
    update_post_meta($about_id, 'buildpro_about_banner_description', $desc);
    update_post_meta($about_id, 'buildpro_about_banner_facts', $facts);
    if ($image_id) {
        update_post_meta($about_id, 'buildpro_about_banner_image_id', (int) $image_id);
    }

    set_theme_mod('buildpro_about_banner_enabled', 1);
    set_theme_mod('buildpro_about_banner_text', $text);
    set_theme_mod('buildpro_about_banner_title', $title);
    set_theme_mod('buildpro_about_banner_description', $desc);
    set_theme_mod('buildpro_about_banner_facts', $facts);
    if ($image_id) {
        set_theme_mod('buildpro_about_banner_image_id', (int) $image_id);
    }
}
