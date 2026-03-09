<?php
function buildpro_import_project_demo($target_id = 0)
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
    $existing = new WP_Query(array(
        'post_type' => 'project',
        'posts_per_page' => 1,
        'post_status' => 'publish',
        'no_found_rows' => true,
        'fields' => 'ids',
    ));
    if ($existing->have_posts()) {
        wp_reset_postdata();
        return;
    }
    wp_reset_postdata();
    $title = '';
    $desc = '';
    if (function_exists('buildpro_import_parse_js')) {
        $data = buildpro_import_parse_js('/assets/data/project-data.js', 'projectsData');
        if (is_array($data)) {
            $title = isset($data['projectTitle']) ? (string)$data['projectTitle'] : '';
            $desc = isset($data['projectDescription']) ? (string)$data['projectDescription'] : '';
            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $it) {
                    if (function_exists('buildpro_import_create_project')) {
                        buildpro_import_create_project($it);
                    }
                }
            }
        }
    }
    update_post_meta($home_id, 'projects_title', $title);
    update_post_meta($home_id, 'projects_description', $desc);
    update_post_meta($home_id, 'buildpro_portfolio_enabled', 1);
    set_theme_mod('projects_title', $title);
    set_theme_mod('projects_description', $desc);
    set_theme_mod('buildpro_portfolio_enabled', 1);
}
