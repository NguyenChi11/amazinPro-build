<?php
function buildpro_admin_maybe_import_header()
{
    if (!is_admin()) {
        return;
    }
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->base !== 'post') {
        return;
    }
    $post_id = isset($_GET['post']) ? absint($_GET['post']) : 0;
    if ($post_id <= 0) {
        return;
    }
    if (get_post_type($post_id) !== 'page') {
        return;
    }
    $front_id = (int) get_option('page_on_front');
    $tpl = get_page_template_slug($post_id);
    if ($post_id !== $front_id && !empty($tpl) && $tpl !== 'home-page.php') {
        return;
    }
    $logo = (int) get_theme_mod('header_logo', 0);
    $title = (string) get_theme_mod('buildpro_header_title', '');
    $desc = (string) get_theme_mod('buildpro_header_description', '');
    if ($title === '') {
        $title = (string) get_theme_mod('header_text', '');
    }
    if ($desc === '') {
        $desc = (string) get_theme_mod('header_description', '');
    }
    if ($logo || $title !== '' || $desc !== '') {
        return;
    }
    $header_demo_file = get_theme_file_path('/inc/import/data-demo/header-demo.php');
    if (file_exists($header_demo_file)) {
        require_once $header_demo_file;
        if (function_exists('buildpro_import_header_demo')) {
            buildpro_import_header_demo();
        }
    }
}
add_action('current_screen', 'buildpro_admin_maybe_import_header');
