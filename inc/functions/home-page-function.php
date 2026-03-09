<?php
function buildpro_find_page_by_templates_or_slugs($templates, $slugs)
{
    foreach ($templates as $tpl) {
        $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => $tpl, 'number' => 1));
        if (!empty($pages)) {
            return (int)$pages[0]->ID;
        }
    }
    foreach ($slugs as $slug) {
        $p = get_page_by_path($slug, OBJECT, 'page');
        if ($p) {
            return (int)$p->ID;
        }
    }
    return 0;
}
function buildpro_ensure_ordered_primary_menu()
{
    $location = 'menu-1';
    $menu_name = 'Primary Menu';
    $menu = wp_get_nav_menu_object($menu_name);
    if (!$menu) {
        $menu_id = wp_create_nav_menu($menu_name);
    } else {
        $menu_id = (int) $menu->term_id;
    }
    $locs = get_nav_menu_locations();
    if (!isset($locs[$location]) || (int) $locs[$location] !== $menu_id) {
        $locs[$location] = $menu_id;
        set_theme_mod('nav_menu_locations', $locs);
    }
    $targets = array(
        array('templates' => array('home-page.php'), 'slugs' => array('home', 'trang-chu', 'homepage')),
        array('templates' => array('project-page.php', 'projects-page.php'), 'slugs' => array('projects', 'project', 'du-an')),
        array('templates' => array('product-page.php', 'products-page.php'), 'slugs' => array('products', 'product', 'san-pham')),
        array('templates' => array('blogs-page.php', 'blog-page.php'), 'slugs' => array('blogs', 'blog', 'tin-tuc')),
        array('templates' => array('about-page.php', 'about-us-page.php'), 'slugs' => array('about', 'about-us', 'gioi-thieu')),
    );
    $existing = wp_get_nav_menu_items($menu_id);
    $existing_by_object = array();
    if (is_array($existing)) {
        foreach ($existing as $it) {
            if (isset($it->type) && $it->type === 'post_type_archive' && isset($it->object) && $it->object === 'project' && !empty($it->ID)) {
                wp_delete_post((int)$it->ID, true);
                continue;
            }
            if (isset($it->object) && $it->object === 'page' && !empty($it->object_id)) {
                $existing_by_object[(int) $it->object_id] = true;
            }
        }
    }
    $position = 1;
    foreach ($targets as $t) {
        $pid = buildpro_find_page_by_templates_or_slugs($t['templates'], $t['slugs']);
        if ($pid > 0 && !isset($existing_by_object[$pid])) {
            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-object-id' => $pid,
                'menu-item-object' => 'page',
                'menu-item-type' => 'post_type',
                'menu-item-status' => 'publish',
                'menu-item-position' => $position,
            ));
        }
        $position++;
    }
}
add_action('after_setup_theme', function () {
    buildpro_ensure_ordered_primary_menu();
}, 100);
