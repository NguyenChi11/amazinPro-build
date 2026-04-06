<?php
function buildpro_import_post_demo($target_id = 0)
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
    if (function_exists('buildpro_import_parse_js')) {
        $data = buildpro_import_parse_js('/assets/data/post-data.js', 'postsData');
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $it) {
                if (function_exists('buildpro_import_create_post')) {
                    buildpro_import_create_post($it);
                }
            }
        }
        $title = isset($data['postsTitle']) ? (string)$data['postsTitle'] : '';
        $desc = isset($data['postsDescription']) ? (string)$data['postsDescription'] : '';
        $view_all_text = isset($data['postsViewAllText']) ? (string)$data['postsViewAllText'] : '';
        if ($title !== '') {
            update_post_meta($home_id, 'title_post', $title);
            set_theme_mod('title_post', $title);
        }
        if ($desc !== '') {
            update_post_meta($home_id, 'description_post', $desc);
            set_theme_mod('description_post', $desc);
        }
        if ($view_all_text !== '') {
            update_post_meta($home_id, 'buildpro_post_view_all_text', $view_all_text);
            set_theme_mod('buildpro_post_view_all_text', $view_all_text);
        }

        // Also populate the Customizer bundle used by the preview template.
        // Template reads: get_theme_mod('buildpro_post_data') with keys: title, desc, view_all_text.
        if ($title !== '' || $desc !== '' || $view_all_text !== '') {
            $bundle = array(
                'title' => $title,
                'desc' => $desc,
                'view_all_text' => $view_all_text,
            );
            set_theme_mod('buildpro_post_data', $bundle);
        }
    }
    update_post_meta($home_id, 'buildpro_post_enabled', 1);
    set_theme_mod('buildpro_post_enabled', 1);
}
