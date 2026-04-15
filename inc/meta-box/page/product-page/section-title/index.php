<?php
function buildpro_products_title_add_meta_box($post_type, $post)
{
    if ($post_type !== 'page') {
        return;
    }
    $template = get_page_template_slug($post->ID);
    if ($template !== 'products-page.php' && $template !== 'product-page.php') {
        return;
    }
    add_meta_box(
        'buildpro_products_title_meta',
        esc_html__('Products Title', 'buildpro'),
        'buildpro_products_title_render_meta_box',
        'page',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'buildpro_products_title_add_meta_box', 10, 2);

function buildpro_products_title_render_meta_box($post)
{
    wp_nonce_field('buildpro_products_title_meta_save', 'buildpro_products_title_meta_nonce');
    $title = get_post_meta($post->ID, 'products_title', true);
    $desc = get_post_meta($post->ID, 'products_description', true);
    wp_enqueue_style('buildpro-products-title-admin', get_theme_file_uri('template/meta-box/page/products/section-title/style.css'), array(), null);
    wp_enqueue_script('buildpro-products-title-admin', get_theme_file_uri('template/meta-box/page/products/section-title/script.js'), array(), null, true);
    wp_add_inline_script('buildpro-products-title-admin', 'window.buildproProductsTitleState=' . wp_json_encode(array()) . ';', 'before');
    include get_theme_file_path('template/meta-box/page/products/section-title/index.php');
}

function buildpro_products_title_save_meta($post_id)
{
    if (!isset($_POST['buildpro_products_title_meta_nonce']) || !wp_verify_nonce($_POST['buildpro_products_title_meta_nonce'], 'buildpro_products_title_meta_save')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $template = get_page_template_slug($post_id);
    if ($template !== 'products-page.php' && $template !== 'product-page.php') {
        return;
    }

    $title = isset($_POST['products_title']) ? sanitize_text_field($_POST['products_title']) : '';
    $desc = isset($_POST['products_description']) ? sanitize_textarea_field($_POST['products_description']) : '';
    update_post_meta($post_id, 'products_title', $title);
    update_post_meta($post_id, 'products_description', $desc);
    set_theme_mod('products_title', $title);
    set_theme_mod('products_description', $desc);
}
add_action('save_post_page', 'buildpro_products_title_save_meta');
