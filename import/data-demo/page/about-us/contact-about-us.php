<?php
function buildpro_import_about_us_contact_demo($target_id = 0)
{
    $about_id = (int)$target_id;
    if ($about_id <= 0) {
        $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => 1));
        if (count($pages) > 0) {
            $about_id = $pages[0]->ID;
        }
        if ($about_id <= 0) {
            $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-us-page.php', 'number' => 1));
            if (count($pages) > 0) {
                $about_id = $pages[0]->ID;
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
    $existing = get_post_meta($about_id, 'buildpro_about_contact_items', true);
    if (is_array($existing) && !empty($existing)) {
        return;
    }
    if (!function_exists('buildpro_import_parse_js')) {
        return;
    }
    $data = buildpro_import_parse_js('/assets/data/about-us-page/contact-data.js', 'contactData');
    if (!is_array($data) || empty($data)) {
        return;
    }
    $title = isset($data['title']) ? (string)$data['title'] : '';
    $text = isset($data['desc']) ? (string)$data['desc'] : '';
    $address = isset($data['address']) ? (string)$data['address'] : '';
    $phone = isset($data['phone']) ? (string)$data['phone'] : '';
    $email = isset($data['email']) ? (string)$data['email'] : '';

    update_post_meta($about_id, 'buildpro_about_contact_enabled', 1);
    update_post_meta($about_id, 'buildpro_about_contact_title', $title);
    update_post_meta($about_id, 'buildpro_about_contact_text', $text);
    update_post_meta($about_id, 'buildpro_about_contact_address', $address);
    update_post_meta($about_id, 'buildpro_about_contact_phone', $phone);
    update_post_meta($about_id, 'buildpro_about_contact_email', $email);
    set_theme_mod('buildpro_about_contact_enabled', 1);
    set_theme_mod('buildpro_about_contact_title', $title);
    set_theme_mod('buildpro_about_contact_text', $text);
    set_theme_mod('buildpro_about_contact_address', $address);
    set_theme_mod('buildpro_about_contact_phone', $phone);
    set_theme_mod('buildpro_about_contact_email', $email);
}
