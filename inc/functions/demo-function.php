<?php
function buildpro_import_parse_js($rel_file, $const_name)
{
    $path = get_theme_file_path($rel_file);
    if (!file_exists($path)) {
        return array();
    }
    $s = file_get_contents($path);
    if (!is_string($s) || $s === '') {
        return array();
    }
    $re = '/const\s+' . preg_quote($const_name, '/') . '\s*=\s*(\{[\s\S]*?\});/m';
    if (!preg_match($re, $s, $m)) {
        return array();
    }
    $obj = $m[1];
    $obj = rtrim($obj, ';');
    $json = preg_replace('/([,{]\s*)([A-Za-z_][A-Za-z0-9_]*)\s*:/', '$1"$2":', $obj);
    $json = preg_replace('/,\s*]/', ']', $json);
    $json = preg_replace('/,\s*}/', '}', $json);
    $data = json_decode($json, true);
    return is_array($data) ? $data : array();
}

function buildpro_import_create_wc_product($item)
{
    if (!(class_exists('WooCommerce') || function_exists('wc_get_product'))) {
        return 0;
    }
    $title = isset($item['title']) ? $item['title'] : '';
    $slug = isset($item['link']) ? buildpro_import_slug_from_link($item['link']) : sanitize_title($title);
    if ($slug) {
        $exists = get_page_by_path($slug, OBJECT, 'product');
        if ($exists) {
            return (int)$exists->ID;
        }
    }
    $post_id = wp_insert_post(array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'post_title' => $title,
        'post_name' => $slug,
        'post_content' => isset($item['description']) ? $item['description'] : '',
        'post_excerpt' => isset($item['shortDescription']) ? $item['shortDescription'] : '',
    ));
    $img = isset($item['image']) ? buildpro_import_image_id($item['image']) : 0;
    if ($img) {
        set_post_thumbnail($post_id, $img);
    }
    $gids = array();
    if (isset($item['gallery']) && is_array($item['gallery'])) {
        foreach ($item['gallery'] as $g) {
            $id = buildpro_import_image_id($g);
            if ($id) {
                $gids[] = $id;
            }
        }
    }
    if (!empty($gids)) {
        update_post_meta($post_id, '_product_image_gallery', implode(',', array_map('intval', $gids)));
    }
    $reg = isset($item['regularPrice']) ? (string)$item['regularPrice'] : '';
    $sale = isset($item['salePrice']) ? (string)$item['salePrice'] : '';
    if ($sale !== '') {
        update_post_meta($post_id, '_sale_price', $sale);
        update_post_meta($post_id, '_price', $sale);
    }
    if ($reg !== '') {
        update_post_meta($post_id, '_regular_price', $reg);
        if ($sale === '') {
            update_post_meta($post_id, '_price', $reg);
        }
    }
    wp_set_object_terms($post_id, 'simple', 'product_type', false);
    wp_set_object_terms($post_id, 'visible', 'product_visibility', false);
    $cat = isset($item['category']) ? $item['category'] : '';
    if ($cat !== '') {
        $term = term_exists($cat, 'product_cat');
        if (!$term || is_wp_error($term)) {
            $term = wp_insert_term($cat, 'product_cat');
        }
        if (is_array($term) && isset($term['term_id'])) {
            wp_set_object_terms($post_id, (int)$term['term_id'], 'product_cat', false);
        } elseif (is_numeric($term)) {
            wp_set_object_terms($post_id, (int)$term, 'product_cat', false);
        }
    }
    $attrs = isset($item['attributes']) && is_array($item['attributes']) ? $item['attributes'] : array();
    $meta_attrs = array();
    $pos = 0;
    foreach ($attrs as $name => $value) {
        $key = sanitize_title($name);
        $meta_attrs[$key] = array(
            'name' => $name,
            'value' => is_array($value) ? implode(' | ', $value) : (string)$value,
            'position' => $pos,
            'is_visible' => 1,
            'is_variation' => 0,
            'is_taxonomy' => 0,
        );
        $pos++;
    }
    if (!empty($meta_attrs)) {
        update_post_meta($post_id, '_product_attributes', $meta_attrs);
    }
    $typical = isset($item['typicalRange']) ? $item['typicalRange'] : '';
    if ($typical !== '') {
        update_post_meta($post_id, 'typical_range', $typical);
    }
    return (int)$post_id;
}

function buildpro_import_slug_from_link($link)
{
    $p = parse_url($link);
    $path = isset($p['path']) ? $p['path'] : '';
    $slug = basename($path);
    return sanitize_title($slug);
}

function buildpro_import_image_id($url)
{
    if (!is_string($url) || $url === '') {
        return 0;
    }
    $src = buildpro_import_resolve_theme_path($url);
    $exist = buildpro_import_find_attachment_by_source($src);
    if ($exist) {
        return $exist;
    }
    return buildpro_import_copy_to_uploads($src);
}

function buildpro_import_find_attachment_by_source($src)
{
    $q = new WP_Query(array(
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'posts_per_page' => 1,
        'meta_query' => array(
            array('key' => 'buildpro_source_file', 'value' => $src, 'compare' => '='),
        ),
        'fields' => 'ids',
        'no_found_rows' => true,
    ));
    if ($q->have_posts()) {
        $ids = $q->posts;
        return isset($ids[0]) ? (int)$ids[0] : 0;
    }
    return 0;
}

function buildpro_import_copy_to_uploads($src_path)
{
    if (!file_exists($src_path)) {
        return 0;
    }
    $uploads = wp_upload_dir();
    $base = trailingslashit($uploads['basedir']) . 'buildpro-imports';
    if (!is_dir($base)) {
        wp_mkdir_p($base);
    }
    $name = basename($src_path);
    $dest = trailingslashit($base) . $name;
    $i = 1;
    while (file_exists($dest)) {
        $pi = pathinfo($name);
        $alt = $pi['filename'] . '-' . $i . (isset($pi['extension']) ? '.' . $pi['extension'] : '');
        $dest = trailingslashit($base) . $alt;
        $i++;
    }
    if (!copy($src_path, $dest)) {
        return 0;
    }
    $ft = wp_check_filetype($dest, null);
    $att = array(
        'post_mime_type' => $ft['type'],
        'post_title' => sanitize_file_name(basename($dest)),
        'post_content' => '',
        'post_status' => 'inherit',
    );
    $attach_id = wp_insert_attachment($att, $dest);
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $meta = wp_generate_attachment_metadata($attach_id, $dest);
    wp_update_attachment_metadata($attach_id, $meta);
    update_post_meta($attach_id, 'buildpro_source_file', $src_path);
    return (int)$attach_id;
}

function buildpro_import_resolve_theme_path($url)
{
    $rel = preg_replace('#^/wp-content/themes/buildpro#', '', $url);
    $rel = '/' . ltrim($rel, '/');
    $path = get_theme_file_path($rel);
    return $path;
}

function buildpro_maybe_import_default_content()
{
    if ((defined('REST_REQUEST') && REST_REQUEST) || (defined('DOING_AJAX') && DOING_AJAX)) {
        return;
    }
    if (get_option('buildpro_default_content_imported') === '1') {
        $wc_active = class_exists('WooCommerce') || function_exists('wc_get_product');
        if (!$wc_active || get_option('buildpro_wc_default_content_imported') === '1') {
            // Run any newly added section imports that may have been missed in previous runs
            $about_leader_demo_file = get_theme_file_path('/import/data-demo/page/about-us/leader-about-us.php');
            if (file_exists($about_leader_demo_file)) {
                require_once $about_leader_demo_file;
                if (function_exists('buildpro_import_about_us_leader_demo')) {
                    buildpro_import_about_us_leader_demo();
                }
            }
            return;
        }
    }
    $footer_demo_file = get_theme_file_path('/import/data-demo/footer-demo.php');
    if (file_exists($footer_demo_file)) {
        require_once $footer_demo_file;
        if (function_exists('buildpro_import_footer_demo')) {
            buildpro_import_footer_demo();
        }
    }
    $banner_demo_file = get_theme_file_path('/import/data-demo/page/home/banner-home.php');
    if (file_exists($banner_demo_file)) {
        require_once $banner_demo_file;
        if (function_exists('buildpro_import_banner_demo')) {
            buildpro_import_banner_demo();
        }
    }
    $option_demo_file = get_theme_file_path('/import/data-demo/page/home/option-home.php');
    if (file_exists($option_demo_file)) {
        require_once $option_demo_file;
        if (function_exists('buildpro_import_option_demo')) {
            buildpro_import_option_demo();
        }
    }
    $data_demo_file = get_theme_file_path('/import/data-demo/page/home/data-home.php');
    if (file_exists($data_demo_file)) {
        require_once $data_demo_file;
        if (function_exists('buildpro_import_data_demo')) {
            buildpro_import_data_demo();
        }
    }
    $products_demo_file = get_theme_file_path('/import/data-demo/page/home/products-home.php');
    if (file_exists($products_demo_file)) {
        require_once $products_demo_file;
        if (function_exists('buildpro_import_product_demo')) {
            buildpro_import_product_demo();
        }
    }

    $service_demo_file = get_theme_file_path('/import/data-demo/page/home/service-home.php');
    if (file_exists($service_demo_file)) {
        require_once $service_demo_file;
        if (function_exists('buildpro_import_service_demo')) {
            buildpro_import_service_demo();
        }
    }
    $evaluate_demo_file = get_theme_file_path('/import/data-demo/page/home/evaluate-home.php');
    if (file_exists($evaluate_demo_file)) {
        require_once $evaluate_demo_file;
        if (function_exists('buildpro_import_evaluate_demo')) {
            buildpro_import_evaluate_demo();
        }
    }
    $project_demo_file = get_theme_file_path('/import/data-demo/page/home/project-home.php');
    if (file_exists($project_demo_file)) {
        require_once $project_demo_file;
        if (function_exists('buildpro_import_project_demo')) {
            buildpro_import_project_demo();
        }
    }
    $post_demo_file = get_theme_file_path('/import/data-demo/page/home/post-home.php');
    if (file_exists($post_demo_file)) {
        require_once $post_demo_file;
        if (function_exists('buildpro_import_post_demo')) {
            buildpro_import_post_demo();
        }
    }
    $projects_title_demo_file = get_theme_file_path('/import/data-demo/page/projects/title-project.php');
    if (file_exists($projects_title_demo_file)) {
        require_once $projects_title_demo_file;
        if (function_exists('buildpro_import_projects_title_demo')) {
            buildpro_import_projects_title_demo();
        }
    }
    $about_banner_demo_file = get_theme_file_path('/import/data-demo/page/about-us/banner-about-us.php');
    if (file_exists($about_banner_demo_file)) {
        require_once $about_banner_demo_file;
        if (function_exists('buildpro_import_about_us_banner_demo')) {
            buildpro_import_about_us_banner_demo();
        }
    }
    $about_core_values_demo_file = get_theme_file_path('/import/data-demo/page/about-us/core-value-about-us.php');
    if (file_exists($about_core_values_demo_file)) {
        require_once $about_core_values_demo_file;
        if (function_exists('buildpro_import_about_us_core_values_demo')) {
            buildpro_import_about_us_core_values_demo();
        }
    }
    $about_leader_demo_file = get_theme_file_path('/import/data-demo/page/about-us/leader-about-us.php');
    if (file_exists($about_leader_demo_file)) {
        require_once $about_leader_demo_file;
        if (function_exists('buildpro_import_about_us_leader_demo')) {
            buildpro_import_about_us_leader_demo();
        }
    }
    $wc_active = class_exists('WooCommerce') || function_exists('wc_get_product');
    if ($wc_active) {
        update_option('buildpro_wc_default_content_imported', '1');
    } else {
        update_option('buildpro_wc_do_import', '1');
    }
    update_option('buildpro_do_import', '0');
    update_option('buildpro_default_content_imported', '1');
}

add_action('init', 'buildpro_maybe_import_default_content');

// When WooCommerce is activated after first import, reset WC flag so products get imported
add_action('activated_plugin', function ($plugin) {
    if (strpos($plugin, 'woocommerce') !== false) {
        delete_option('buildpro_wc_default_content_imported');
    }
});

function buildpro_import_create_post($item)
{
    $title = isset($item['title']) ? $item['title'] : '';
    $slug = isset($item['link']) ? buildpro_import_slug_from_link($item['link']) : sanitize_title($title);
    if ($slug) {
        $exists = get_page_by_path($slug, OBJECT, 'post');
        if ($exists) {
            return (int)$exists->ID;
        }
    }
    $date = isset($item['date']) ? $item['date'] : '';
    $postarr = array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'post_title' => $title,
        'post_name' => $slug,
        'post_content' => isset($item['description']) ? $item['description'] : '',
    );
    if ($date) {
        $postarr['post_date'] = $date;
        $postarr['post_date_gmt'] = get_gmt_from_date($date);
    }
    $post_id = wp_insert_post($postarr);
    $img = isset($item['image']) ? buildpro_import_image_id($item['image']) : 0;
    if ($img) {
        set_post_thumbnail($post_id, $img);
    }
    $banner = 0;
    if (isset($item['banner']) && is_array($item['banner']) && !empty($item['banner'])) {
        $banner = buildpro_import_image_id($item['banner'][0]);
    }
    update_post_meta($post_id, 'buildpro_post_banner_id', $banner);
    update_post_meta($post_id, 'buildpro_post_description', isset($item['description']) ? $item['description'] : '');
    $gids = array();
    if (isset($item['gallery']) && is_array($item['gallery'])) {
        foreach ($item['gallery'] as $g) {
            $id = buildpro_import_image_id($g);
            if ($id) {
                $gids[] = $id;
            }
        }
    }
    update_post_meta($post_id, 'buildpro_post_quote_gallery', $gids);
    return (int)$post_id;
}

function buildpro_import_create_project($item)
{
    $title = isset($item['title']) ? $item['title'] : '';
    $slug = sanitize_title($title);
    if ($slug) {
        $exists = get_page_by_path($slug, OBJECT, 'project');
        if ($exists) {
            return (int)$exists->ID;
        }
    }
    $post_id = wp_insert_post(array(
        'post_type' => 'project',
        'post_status' => 'publish',
        'post_title' => $title,
        'post_name' => $slug,
        'post_content' => isset($item['about']) ? $item['about'] : '',
    ));
    $img = isset($item['image']) ? buildpro_import_image_id($item['image']) : 0;
    if ($img) {
        set_post_thumbnail($post_id, $img);
    }
    $banner = 0;
    if (isset($item['gallery']) && is_array($item['gallery']) && !empty($item['gallery'])) {
        $banner = buildpro_import_image_id($item['gallery'][0]);
    }
    update_post_meta($post_id, 'project_banner_id', $banner);
    update_post_meta($post_id, 'location_project', isset($item['location']) ? $item['location'] : '');
    update_post_meta($post_id, 'about_project', isset($item['about']) ? $item['about'] : '');
    update_post_meta($post_id, 'price_project', isset($item['price']) ? $item['price'] : '');
    update_post_meta($post_id, 'date_time_project', isset($item['dateTime']) ? $item['dateTime'] : '');
    $gids = array();
    if (isset($item['gallery']) && is_array($item['gallery'])) {
        foreach ($item['gallery'] as $g) {
            $id = buildpro_import_image_id($g);
            if ($id) {
                $gids[] = $id;
            }
        }
    }
    update_post_meta($post_id, 'project_gallery_ids', $gids);
    $standards = array();
    if (isset($item['standards']) && is_array($item['standards'])) {
        foreach ($item['standards'] as $st) {
            $iid = isset($st['image']) ? buildpro_import_image_id($st['image']) : 0;
            $standards[] = array(
                'image_id' => $iid,
                'title' => isset($st['title']) ? $st['title'] : '',
                'description' => isset($st['description']) ? $st['description'] : '',
            );
        }
    }
    update_post_meta($post_id, 'project_standards', $standards);
    return (int)$post_id;
}

// Admin action: reset WC import flag so products re-import on next load
add_action('admin_init', function () {
    if (
        isset($_GET['buildpro_reset_wc_import']) &&
        current_user_can('manage_options') &&
        wp_verify_nonce($_GET['_wpnonce'], 'buildpro_reset_wc_import')
    ) {
        delete_option('buildpro_wc_default_content_imported');
        delete_option('buildpro_default_content_imported');
        wp_safe_redirect(admin_url('index.php?buildpro_wc_reset=1'));
        exit;
    }
});

add_action('admin_notices', function () {
    $wc_active = class_exists('WooCommerce') || function_exists('wc_get_product');
    if (!$wc_active || !current_user_can('manage_options')) {
        return;
    }
    if (get_option('buildpro_wc_default_content_imported') !== '1') {
        $nonce = wp_create_nonce('buildpro_reset_wc_import');
        $url = admin_url('index.php?buildpro_reset_wc_import=1&_wpnonce=' . $nonce);
        echo '<div class="notice notice-warning"><p>';
        echo '<strong>BuildPro:</strong> WooCommerce products chưa được import. ';
        echo '<a href="' . esc_url($url) . '" class="button button-primary">Import ngay</a>';
        echo '</p></div>';
    }
    if (isset($_GET['buildpro_wc_reset'])) {
        echo '<div class="notice notice-success is-dismissible"><p>BuildPro: Đã reset — products sẽ được import khi tải trang tiếp theo.</p></div>';
    }
});
