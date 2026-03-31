<?php
function buildpro_import_privacy_policy_demo($target_id = 0)
{
    $privacy_id = (int) $target_id;
    if ($privacy_id <= 0) {
        $pages = get_pages(array(
            'meta_key' => '_wp_page_template',
            'meta_value' => 'privacy-policy-page.php',
            'number' => 1,
        ));
        if (!empty($pages)) {
            $privacy_id = (int) $pages[0]->ID;
        }
        if ($privacy_id <= 0) {
            $privacy = get_page_by_path('privacy-policy');
            if ($privacy && $privacy->post_type === 'page') {
                $privacy_id = (int) $privacy->ID;
            }
        }
    }

    if ($privacy_id <= 0) {
        return;
    }

    $existing_content = (string) get_post_field('post_content', $privacy_id);
    if (trim($existing_content) !== '') {
        return;
    }

    $path = get_theme_file_path('/assets/data/privacy-policy-page.js');
    if (!file_exists($path)) {
        return;
    }

    $src = file_get_contents($path);
    if (!is_string($src) || $src === '') {
        return;
    }

    $m = array();
    if (!preg_match('/const\s+privacyPolicyWysiwygHtml\s*=\s*`([\s\S]*?)`;/m', $src, $m)) {
        return;
    }

    $html = trim($m[1]);
    if ($html === '') {
        return;
    }

    wp_update_post(array(
        'ID' => $privacy_id,
        'post_content' => wp_kses_post($html),
    ));
}
