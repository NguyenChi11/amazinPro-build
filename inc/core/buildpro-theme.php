<?php
function buildpro_ensure_page_with_template($title, $slug, $template)
{
    $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => $template, 'number' => 1));
    if (!empty($pages)) {
        $p = $pages[0];
        if ($p->post_status !== 'publish') {
            wp_update_post(array('ID' => $p->ID, 'post_status' => 'publish'));
        }
        return (int)$p->ID;
    }
    $existing = get_page_by_path($slug, OBJECT, 'page');
    if ($existing) {
        $pid = (int)$existing->ID;
        update_post_meta($pid, '_wp_page_template', $template);
        if ($existing->post_status !== 'publish') {
            wp_update_post(array('ID' => $pid, 'post_status' => 'publish'));
        }
        return $pid;
    }
    $pid = wp_insert_post(array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => $title,
        'post_name' => $slug,
    ));
    if ($pid && !is_wp_error($pid)) {
        update_post_meta($pid, '_wp_page_template', $template);
        return (int)$pid;
    }
    return 0;
}

function buildpro_create_default_pages()
{
    $home_id = buildpro_ensure_page_with_template('Home', 'home', 'home-page.php');
    if ($home_id > 0) {
        if (get_option('show_on_front') !== 'page') {
            update_option('show_on_front', 'page');
        }
        update_option('page_on_front', $home_id);
    }
    buildpro_ensure_page_with_template('About Us', 'about-us', 'about-us-page.php');
    buildpro_ensure_page_with_template('Blogs', 'blogs', 'blogs-page.php');
    buildpro_ensure_page_with_template('Products', 'products', 'products-page.php');
    buildpro_ensure_page_with_template('Projects', 'projects', 'projects-page.php');
    // Migrate existing pages using old slug 'about-page.php' -> 'about-us-page.php'
    $old_pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => -1));
    foreach ($old_pages as $op) {
        update_post_meta($op->ID, '_wp_page_template', 'about-us-page.php');
    }
}
