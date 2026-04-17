<?php
function buildpro_import_decode_js_string_literal($literal)
{
    if (!is_string($literal) || strlen($literal) < 2) {
        return '';
    }

    $quote = $literal[0];
    if (($quote !== '"' && $quote !== "'") || substr($literal, -1) !== $quote) {
        return '';
    }

    $inner = substr($literal, 1, -1);
    return stripcslashes($inner);
}

function buildpro_import_expand_join_expressions($object_source)
{
    if (!is_string($object_source) || $object_source === '') {
        return $object_source;
    }

    $pattern = '/\[((?:\s*"(?:\\\\.|[^"\\\\])*"\s*,?)*)\s*\]\s*\.join\(\s*("(?:\\\\.|[^"\\\\])*"|\'(?:\\\\.|[^\'\\\\])*\')\s*\)/s';

    return preg_replace_callback($pattern, function ($matches) {
        $array_json = '[' . $matches[1] . ']';
        $array_json = preg_replace('/,\s*]/', ']', $array_json);
        $parts = json_decode($array_json, true);
        if (!is_array($parts)) {
            return $matches[0];
        }

        $separator = buildpro_import_decode_js_string_literal($matches[2]);
        $parts = array_map(function ($part) {
            return is_scalar($part) ? (string) $part : '';
        }, $parts);

        return json_encode(implode($separator, $parts), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }, $object_source);
}

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
    $obj = buildpro_import_expand_join_expressions($obj);
    $json = preg_replace('/([,{]\s*)([A-Za-z_][A-Za-z0-9_]*)\s*:/', '$1"$2":', $obj);
    $json = preg_replace('/,\s*]/', ']', $json);
    $json = preg_replace('/,\s*}/', '}', $json);
    $data = json_decode($json, true);
    return is_array($data) ? $data : array();
}

function buildpro_import_get_wc_products_data()
{
    $primary = buildpro_import_parse_js('/assets/data/product-data.js', 'ProductsData');
    if (isset($primary['items']) && is_array($primary['items']) && !empty($primary['items'])) {
        return $primary;
    }

    $fallback = buildpro_import_parse_js('/assets/data/woocommerce-product-data.js', 'woocommerceProductData');
    return is_array($fallback) ? $fallback : array();
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
    $price = isset($item['price']) ? (string)$item['price'] : '';
    $reg = isset($item['regularPrice']) ? (string)$item['regularPrice'] : $price;
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

    $key_info = (isset($item['keyInformation']) && is_array($item['keyInformation'])) ? $item['keyInformation'] : array();

    $overview = isset($item['overview']) ? sanitize_textarea_field((string) $item['overview']) : '';
    if ($overview !== '') {
        update_post_meta($post_id, 'buildpro_product_overview', $overview);
    }

    $area = isset($key_info['area']) ? (string) $key_info['area'] : (isset($item['area']) ? (string) $item['area'] : '');
    if ($area !== '') {
        update_post_meta($post_id, 'buildpro_product_area', sanitize_text_field($area));
    }

    $location = isset($item['location']) ? (string) $item['location'] : '';
    if ($location !== '') {
        update_post_meta($post_id, 'buildpro_product_location', sanitize_text_field($location));
    }

    $bedrooms = isset($key_info['bedroom']) ? (string) $key_info['bedroom'] : (isset($item['bedroom']) ? (string) $item['bedroom'] : '');
    if ($bedrooms !== '') {
        update_post_meta($post_id, 'buildpro_product_bedrooms', sanitize_text_field($bedrooms));
    }

    $bathrooms = isset($key_info['bathroom']) ? (string) $key_info['bathroom'] : (isset($item['bathroom']) ? (string) $item['bathroom'] : '');
    if ($bathrooms !== '') {
        update_post_meta($post_id, 'buildpro_product_bathrooms', sanitize_text_field($bathrooms));
    }

    $meta_key_map = array(
        'lotSize' => 'buildpro_product_lot_size',
        'garage' => 'buildpro_product_garage',
        'yearBuilt' => 'buildpro_product_year_built',
        'floors' => 'buildpro_product_floors',
    );
    foreach ($meta_key_map as $src_key => $meta_key) {
        if (!isset($key_info[$src_key])) {
            continue;
        }
        $value = sanitize_text_field((string) $key_info[$src_key]);
        if ($value !== '') {
            update_post_meta($post_id, $meta_key, $value);
        }
    }

    $list_map = array(
        'features' => 'buildpro_product_features',
        'interiorFeatures' => 'buildpro_product_interior_features',
    );
    foreach ($list_map as $src_key => $meta_key) {
        if (!isset($item[$src_key])) {
            continue;
        }

        $rows = array();
        $src_value = $item[$src_key];
        if (is_array($src_value)) {
            foreach ($src_value as $row) {
                $row = sanitize_text_field((string) $row);
                if ($row !== '') {
                    $rows[] = $row;
                }
            }
        } else {
            $single = sanitize_textarea_field((string) $src_value);
            if ($single !== '') {
                $rows[] = $single;
            }
        }

        if (!empty($rows)) {
            update_post_meta($post_id, $meta_key, implode("\n", $rows));
        }
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
    if (!is_string($url) || $url === '') {
        return get_theme_file_path('/');
    }

    $path_part = $url;
    if (preg_match('#^https?://#i', $url)) {
        $parsed = parse_url($url);
        if (is_array($parsed) && isset($parsed['path']) && is_string($parsed['path']) && $parsed['path'] !== '') {
            $path_part = $parsed['path'];
        }
    }

    $theme_dir = function_exists('get_template') ? (string) get_template() : '';
    $rel = $path_part;
    if ($theme_dir !== '') {
        $pattern_current = '#^/wp-content/themes/' . preg_quote($theme_dir, '#') . '#';
        $rel = preg_replace($pattern_current, '', $rel);
    }
    if ($rel === $path_part) {
        // Back-compat with old demo data that used /wp-content/themes/buildpro/...
        $rel = preg_replace('#^/wp-content/themes/buildpro#', '', $rel);
    }

    $rel = '/' . ltrim($rel, '/');
    return get_theme_file_path($rel);
}

function buildpro_maybe_import_header_demo_once()
{
    if (get_option('buildpro_header_demo_imported') === '1') {
        return;
    }

    $header_demo_file = get_theme_file_path('/import/data-demo/header-demo.php');
    if (file_exists($header_demo_file)) {
        require_once $header_demo_file;
        if (function_exists('buildpro_import_header_demo')) {
            buildpro_import_header_demo();
        }
    }

    update_option('buildpro_header_demo_imported', '1');
}

function buildpro_maybe_import_about_us_contact_demo_once()
{
    if (get_option('buildpro_about_contact_demo_imported') === '1') {
        return;
    }

    $about_contact_demo_file = get_theme_file_path('/import/data-demo/page/about-us/contact-about-us.php');
    if (file_exists($about_contact_demo_file)) {
        require_once $about_contact_demo_file;
        if (function_exists('buildpro_import_about_us_contact_demo')) {
            buildpro_import_about_us_contact_demo();
            update_option('buildpro_about_contact_demo_imported', '1');
            return;
        }
    }
}

function buildpro_maybe_import_home_contact_form_once()
{
    if (get_option('buildpro_home_contact_form_imported') === '1') {
        return;
    }

    if (!function_exists('buildpro_cf7_maybe_create_form')) {
        return;
    }

    buildpro_cf7_maybe_create_form();

    if (function_exists('buildpro_cf7_get_home_form_id') && (int) buildpro_cf7_get_home_form_id() > 0) {
        update_option('buildpro_home_contact_form_imported', '1');
    }
}

function buildpro_maybe_import_privacy_policy_demo_once()
{
    if (get_option('buildpro_privacy_policy_demo_imported') === '1') {
        return;
    }

    $privacy_demo_file = get_theme_file_path('/import/data-demo/page/privacy-policy/privacy-policy.php');
    if (file_exists($privacy_demo_file)) {
        require_once $privacy_demo_file;
        if (function_exists('buildpro_import_privacy_policy_demo')) {
            buildpro_import_privacy_policy_demo();
            update_option('buildpro_privacy_policy_demo_imported', '1');
            return;
        }
    }
}

function buildpro_maybe_import_terms_of_service_demo_once()
{
    if (get_option('buildpro_terms_of_service_demo_imported') === '1') {
        return;
    }

    $terms_demo_file = get_theme_file_path('/import/data-demo/page/term-of-service/term-of-service.php');
    if (file_exists($terms_demo_file)) {
        require_once $terms_demo_file;
        if (function_exists('buildpro_import_terms_of_service_demo')) {
            buildpro_import_terms_of_service_demo();
            update_option('buildpro_terms_of_service_demo_imported', '1');
            return;
        }
    }
}

function buildpro_has_published_content($post_type)
{
    $q = new WP_Query(array(
        'post_type' => $post_type,
        'posts_per_page' => 1,
        'post_status' => 'publish',
        'no_found_rows' => true,
        'fields' => 'ids',
    ));
    $has_posts = $q->have_posts();
    wp_reset_postdata();
    return (bool) $has_posts;
}

function buildpro_maybe_import_post_demo_once()
{
    $should_import_posts = !buildpro_has_published_content('post');

    if (!$should_import_posts && function_exists('buildpro_import_parse_js')) {
        $post_data = buildpro_import_parse_js('/assets/data/post-data.js', 'postsData');
        $expected_posts = (isset($post_data['items']) && is_array($post_data['items'])) ? count($post_data['items']) : 0;
        if ($expected_posts > 0) {
            $post_counts = wp_count_posts('post');
            $published_posts = (is_object($post_counts) && isset($post_counts->publish)) ? (int) $post_counts->publish : 0;
            $should_import_posts = ($published_posts < $expected_posts);
        }
    }

    if (!$should_import_posts) {
        update_option('buildpro_post_demo_imported', '1');
        return;
    }

    $post_demo_file = get_theme_file_path('/import/data-demo/page/home/post-home.php');
    if (file_exists($post_demo_file)) {
        require_once $post_demo_file;
        if (function_exists('buildpro_import_post_demo')) {
            buildpro_import_post_demo();
        }
    }

    $post_counts_after = wp_count_posts('post');
    $published_after = (is_object($post_counts_after) && isset($post_counts_after->publish)) ? (int) $post_counts_after->publish : 0;
    if ($published_after > 0) {
        update_option('buildpro_post_demo_imported', '1');
    }
}

function buildpro_backfill_demo_post_types_if_missing()
{
    buildpro_maybe_import_post_demo_once();

    $project_demo_file = get_theme_file_path('/import/data-demo/page/home/project-home.php');
    if (post_type_exists('project') && file_exists($project_demo_file)) {
        $should_import_projects = !buildpro_has_published_content('project');

        if (!$should_import_projects && function_exists('buildpro_import_parse_js')) {
            $project_data = buildpro_import_parse_js('/assets/data/project-data.js', 'projectsData');
            $expected_projects = (isset($project_data['items']) && is_array($project_data['items'])) ? count($project_data['items']) : 0;
            if ($expected_projects > 0) {
                $project_counts = wp_count_posts('project');
                $published_projects = (is_object($project_counts) && isset($project_counts->publish)) ? (int) $project_counts->publish : 0;
                $should_import_projects = ($published_projects < $expected_projects);
            }
        }

        if ($should_import_projects) {
            require_once $project_demo_file;
            if (function_exists('buildpro_import_project_demo')) {
                buildpro_import_project_demo();
            }
        }
    }

    $wc_active = class_exists('WooCommerce') || function_exists('wc_get_product');
    $products_demo_file = get_theme_file_path('/import/data-demo/page/home/products-home.php');
    if ($wc_active && file_exists($products_demo_file)) {
        $should_import_products = !buildpro_has_published_content('product');

        if (!$should_import_products && function_exists('buildpro_import_get_wc_products_data')) {
            $data = buildpro_import_get_wc_products_data();
            $expected = (isset($data['items']) && is_array($data['items'])) ? count($data['items']) : 0;
            if ($expected > 0) {
                $counts = wp_count_posts('product');
                $published = (is_object($counts) && isset($counts->publish)) ? (int) $counts->publish : 0;
                $should_import_products = ($published < $expected);
            }
        }

        if ($should_import_products) {
            require_once $products_demo_file;
            if (function_exists('buildpro_import_product_demo')) {
                buildpro_import_product_demo();
            }
        }
    }

    if ($wc_active) {
        update_option('buildpro_wc_default_content_imported', '1');
        update_option('buildpro_wc_do_import', '0');
    }
}

function buildpro_maybe_import_default_content()
{
    if ((defined('REST_REQUEST') && REST_REQUEST) || (defined('DOING_AJAX') && DOING_AJAX)) {
        return;
    }

    if (function_exists('buildpro_create_pages_from_templates_once')) {
        buildpro_create_pages_from_templates_once();
    }

    // Header demo (logo/title/description) should be set on first import.
    if (function_exists('buildpro_maybe_import_header_demo_once')) {
        buildpro_maybe_import_header_demo_once();
    }

    if (function_exists('buildpro_maybe_import_home_contact_form_once')) {
        buildpro_maybe_import_home_contact_form_once();
    }

    if (function_exists('buildpro_maybe_import_privacy_policy_demo_once')) {
        buildpro_maybe_import_privacy_policy_demo_once();
    }

    if (function_exists('buildpro_maybe_import_terms_of_service_demo_once')) {
        buildpro_maybe_import_terms_of_service_demo_once();
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

            if (function_exists('buildpro_maybe_import_about_us_contact_demo_once')) {
                buildpro_maybe_import_about_us_contact_demo_once();
            }

            $products_title_demo_file = get_theme_file_path('/import/data-demo/page/products/title-product.php');
            if (file_exists($products_title_demo_file)) {
                require_once $products_title_demo_file;
                if (function_exists('buildpro_import_products_title_demo')) {
                    buildpro_import_products_title_demo();
                }
            }

            buildpro_backfill_demo_post_types_if_missing();
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
    $products_demo_file = get_theme_file_path('/import/data-demo/page/home/products-home.php');
    if (file_exists($products_demo_file)) {
        require_once $products_demo_file;
        if (function_exists('buildpro_import_product_demo')) {
            buildpro_import_product_demo();
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
    update_option('buildpro_post_demo_imported', '1');
    $projects_title_demo_file = get_theme_file_path('/import/data-demo/page/projects/title-project.php');
    if (file_exists($projects_title_demo_file)) {
        require_once $projects_title_demo_file;
        if (function_exists('buildpro_import_projects_title_demo')) {
            buildpro_import_projects_title_demo();
        }
    }

    $products_title_demo_file = get_theme_file_path('/import/data-demo/page/products/title-product.php');
    if (file_exists($products_title_demo_file)) {
        require_once $products_title_demo_file;
        if (function_exists('buildpro_import_products_title_demo')) {
            buildpro_import_products_title_demo();
        }
    }

    $about_banner_demo_file = get_theme_file_path('/import/data-demo/page/about-us/banner-about-us.php');
    if (file_exists($about_banner_demo_file)) {
        require_once $about_banner_demo_file;
        if (function_exists('buildpro_import_about_us_banner_demo')) {
            buildpro_import_about_us_banner_demo();
        }
    }

    $about_policy_demo_file = get_theme_file_path('/import/data-demo/page/about-us/policy-about-us.php');
    if (file_exists($about_policy_demo_file)) {
        require_once $about_policy_demo_file;
        if (function_exists('buildpro_import_about_us_policy_demo')) {
            buildpro_import_about_us_policy_demo();
        }
    }

    if (function_exists('buildpro_maybe_import_about_us_contact_demo_once')) {
        buildpro_maybe_import_about_us_contact_demo_once();
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
        update_option('buildpro_wc_do_import', '0');
    } else {
        update_option('buildpro_wc_do_import', '1');
    }
    update_option('buildpro_do_import', '0');
    update_option('buildpro_default_content_imported', '1');

    buildpro_backfill_demo_post_types_if_missing();
}

add_action('init', 'buildpro_maybe_import_default_content', 50);

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
    if (isset($item['banner'])) {
        if (is_array($item['banner']) && !empty($item['banner'])) {
            $banner = buildpro_import_image_id($item['banner'][0]);
        } elseif (is_string($item['banner']) && $item['banner'] !== '') {
            $banner = buildpro_import_image_id($item['banner']);
        }
    }
    if (!$banner && isset($item['gallery']) && is_array($item['gallery']) && !empty($item['gallery'])) {
        $banner = buildpro_import_image_id($item['gallery'][0]);
    }
    update_post_meta($post_id, 'buildpro_post_banner_id', $banner);
    update_post_meta($post_id, 'buildpro_post_description', isset($item['description']) ? $item['description'] : '');
    update_post_meta($post_id, 'buildpro_post_paragraph', isset($item['paragraph']) ? $item['paragraph'] : '');
    update_post_meta($post_id, 'buildpro_post_quote_title', isset($item['quoteTitle']) ? $item['quoteTitle'] : '');
    update_post_meta($post_id, 'buildpro_post_quote_description', isset($item['quoteDescription']) ? $item['quoteDescription'] : '');
    update_post_meta($post_id, 'buildpro_post_quote_desc_image_desc', isset($item['quoteImageDescription']) ? $item['quoteImageDescription'] : '');

    $quote_kv = array();
    if (isset($item['quoteKeyValues']) && is_array($item['quoteKeyValues'])) {
        foreach ($item['quoteKeyValues'] as $row) {
            $k = isset($row['key']) ? (string) $row['key'] : '';
            $v = isset($row['value']) ? (string) $row['value'] : '';
            if ($k !== '' || $v !== '') {
                $quote_kv[] = array('key' => $k, 'value' => $v);
            }
        }
    }
    update_post_meta($post_id, 'buildpro_post_quote_kv', $quote_kv);

    $gids = array();
    $quote_gallery = array();
    if (isset($item['quoteGallery']) && is_array($item['quoteGallery'])) {
        $quote_gallery = $item['quoteGallery'];
    } elseif (isset($item['gallery']) && is_array($item['gallery'])) {
        $quote_gallery = $item['gallery'];
    }
    if (!empty($quote_gallery)) {
        foreach ($quote_gallery as $g) {
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
    if (isset($item['banner'])) {
        if (is_array($item['banner']) && !empty($item['banner'])) {
            $banner = buildpro_import_image_id($item['banner'][0]);
        } elseif (is_string($item['banner']) && $item['banner'] !== '') {
            $banner = buildpro_import_image_id($item['banner']);
        }
    }
    if (!$banner && isset($item['gallery']) && is_array($item['gallery']) && !empty($item['gallery'])) {
        $banner = buildpro_import_image_id($item['gallery'][0]);
    }
    $about_img = (isset($item['aboutImage']) && is_string($item['aboutImage']) && $item['aboutImage'] !== '')
        ? buildpro_import_image_id($item['aboutImage'])
        : 0;
    update_post_meta($post_id, 'project_banner_id', $banner);
    update_post_meta($post_id, 'location_project', isset($item['location']) ? $item['location'] : '');
    update_post_meta($post_id, 'about_project', isset($item['about']) ? $item['about'] : '');
    update_post_meta($post_id, 'about_image_project', $about_img);
    update_post_meta($post_id, 'project_overview_project', isset($item['projectOverview']) ? $item['projectOverview'] : '');
    update_post_meta($post_id, 'the_vision_project', isset($item['vision']) ? $item['vision'] : '');
    update_post_meta($post_id, 'architectural_design_project', isset($item['architecturalDesign']) ? $item['architecturalDesign'] : '');
    update_post_meta($post_id, 'price_project', isset($item['price']) ? $item['price'] : '');
    update_post_meta($post_id, 'date_time_project', isset($item['dateTime']) ? $item['dateTime'] : '');
    update_post_meta($post_id, 'information_project', isset($item['information']) ? $item['information'] : '');
    update_post_meta($post_id, 'total_area_project', isset($item['totalArea']) ? $item['totalArea'] : '');
    update_post_meta($post_id, 'completion_project', isset($item['completion']) ? $item['completion'] : '');
    update_post_meta($post_id, 'architectural_style_project', isset($item['architecturalStyle']) ? $item['architecturalStyle'] : '');

    $project_key_info = array();
    if (isset($item['keyInfomation']) && is_array($item['keyInfomation'])) {
        foreach ($item['keyInfomation'] as $row) {
            $k = isset($row['key']) ? (string) $row['key'] : '';
            $v = isset($row['value']) ? (string) $row['value'] : '';
            if ($k !== '' || $v !== '') {
                $project_key_info[] = array('key' => $k, 'value' => $v);
            }
        }
    }
    update_post_meta($post_id, 'project_key_infomation', $project_key_info);

    $project_highlights = array();
    if (isset($item['highlights']) && is_array($item['highlights'])) {
        foreach ($item['highlights'] as $highlight_item) {
            $highlight_item = trim((string) $highlight_item);
            if ($highlight_item !== '') {
                $project_highlights[] = $highlight_item;
            }
        }
    }
    update_post_meta($post_id, 'project_highlight_options', $project_highlights);

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
