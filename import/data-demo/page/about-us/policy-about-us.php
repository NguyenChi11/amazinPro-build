<?php
function buildpro_import_about_us_policy_demo($target_id = 0)
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
    $exists_items = get_post_meta($about_id, 'buildpro_about_policy_items', true);
    $exists_certs = get_post_meta($about_id, 'buildpro_about_policy_certifications', true);
    if ((is_array($exists_items) && !empty($exists_items)) || (is_array($exists_certs) && !empty($exists_certs))) {
        return;
    }
    if (!function_exists('buildpro_import_parse_js')) {
        return;
    }
    $data = buildpro_import_parse_js('/assets/data/about-us-page/policy-data.js', 'policyData');
    if (!is_array($data) || empty($data)) {
        return;
    }
    $title_left = isset($data['title_left']) ? (string)$data['title_left'] : '';
    $business_registration = isset($data['business_registration']) ? (string)$data['business_registration'] : '';
    $general_contractor = isset($data['general_contractor']) ? (string)$data['general_contractor'] : '';
    $duns_number = isset($data['duns_number']) ? (string)$data['duns_number'] : '';
    $title_right = isset($data['title_right']) ? (string)$data['title_right'] : '';
    $warranty_desc = isset($data['warranty_desc']) ? (string)$data['warranty_desc'] : '';
    $items = array();
    if (isset($data['items']) && is_array($data['items'])) {
        foreach ($data['items'] as $it) {
            $img = isset($it['image']) ? $it['image'] : '';
            $iid = function_exists('buildpro_import_image_id') ? buildpro_import_image_id($img) : 0;
            $items[] = array(
                'icon_id' => $iid,
                'icon_url' => '',
                'title' => isset($it['title']) ? (string)$it['title'] : '',
                'desc' => isset($it['desc']) ? (string)$it['desc'] : '',
            );
        }
    }
    $certs = array();
    if (isset($data['items_certification']) && is_array($data['items_certification'])) {
        foreach ($data['items_certification'] as $c) {
            $img = isset($c['certification_image']) ? $c['certification_image'] : '';
            $iid = function_exists('buildpro_import_image_id') ? buildpro_import_image_id($img) : 0;
            $certs[] = array(
                'image_id' => $iid,
                'image_url' => '',
                'url' => isset($c['certification_url']) ? (string)$c['certification_url'] : '',
                'title' => isset($c['certification_title']) ? (string)$c['certification_title'] : '',
                'desc' => isset($c['certification_desc']) ? (string)$c['certification_desc'] : '',
            );
        }
    }
    update_post_meta($about_id, 'buildpro_about_policy_enabled', 1);
    update_post_meta($about_id, 'buildpro_about_policy_title_left', $title_left);
    update_post_meta($about_id, 'buildpro_about_policy_business_registration', $business_registration);
    update_post_meta($about_id, 'buildpro_about_policy_general_contractor', $general_contractor);
    update_post_meta($about_id, 'buildpro_about_policy_duns_number', $duns_number);
    update_post_meta($about_id, 'buildpro_about_policy_title_right', $title_right);
    update_post_meta($about_id, 'buildpro_about_policy_warranty_desc', $warranty_desc);
    update_post_meta($about_id, 'buildpro_about_policy_items', $items);
    update_post_meta($about_id, 'buildpro_about_policy_certifications', $certs);
    set_theme_mod('buildpro_about_policy_enabled', 1);
    set_theme_mod('buildpro_about_policy_title_left', $title_left);
    set_theme_mod('buildpro_about_policy_business_registration', $business_registration);
    set_theme_mod('buildpro_about_policy_general_contractor', $general_contractor);
    set_theme_mod('buildpro_about_policy_duns_number', $duns_number);
    set_theme_mod('buildpro_about_policy_title_right', $title_right);
    set_theme_mod('buildpro_about_policy_warranty_desc', $warranty_desc);
    set_theme_mod('buildpro_about_policy_items', $items);
    set_theme_mod('buildpro_about_policy_certifications', $certs);
}
