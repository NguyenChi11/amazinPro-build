<?php
function buildpro_import_terms_of_service_demo($target_id = 0)
{
    $terms_id = (int) $target_id;
    if ($terms_id <= 0) {
        $pages = get_pages(array(
            'meta_key' => '_wp_page_template',
            'meta_value' => 'terms-of-service-page.php',
            'number' => 1,
        ));
        if (!empty($pages)) {
            $terms_id = (int) $pages[0]->ID;
        }
        if ($terms_id <= 0) {
            $terms = get_page_by_path('terms-of-service');
            if ($terms && $terms->post_type === 'page') {
                $terms_id = (int) $terms->ID;
            }
        }
    }

    if ($terms_id <= 0) {
        return;
    }

    $existing_content = (string) get_post_field('post_content', $terms_id);
    if (trim($existing_content) !== '') {
        return;
    }

    $path = get_theme_file_path('/assets/data/terms-of-services.js');
    if (!file_exists($path)) {
        return;
    }

    $src = file_get_contents($path);
    if (!is_string($src) || $src === '') {
        return;
    }

    $m = array();
    if (!preg_match('/const\s+termsOfServiceWysiwygHtml\s*=\s*`([\s\S]*?)`;/m', $src, $m)) {
        return;
    }

    $html = trim($m[1]);
    if ($html === '') {
        return;
    }

    wp_update_post(array(
        'ID' => $terms_id,
        'post_content' => wp_kses_post($html),
    ));
}
