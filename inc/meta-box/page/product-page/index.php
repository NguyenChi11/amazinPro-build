<?php
require get_template_directory() . '/inc/meta-box/page/product-page/section-title/index.php';

if (!function_exists('buildpro_product_page_group_meta_box_add')) {
    function buildpro_product_page_group_meta_box_add($post_type, $post)
    {
        if ($post_type !== 'page') {
            return;
        }
        $template = get_page_template_slug($post->ID);
        if ($template !== 'products-page.php' && $template !== 'product-page.php') {
            return;
        }
        add_meta_box('buildpro_product_group', esc_html__('Products Page', 'buildpro'), 'buildpro_product_page_group_meta_box_render', 'page', 'normal', 'high');
    }
}
add_action('add_meta_boxes', 'buildpro_product_page_group_meta_box_add', 10, 2);

if (!function_exists('buildpro_product_page_group_meta_box_render')) {
    function buildpro_product_page_group_meta_box_render($post)
    {
        $template = get_page_template_slug($post->ID);
        if ($template !== 'products-page.php' && $template !== 'product-page.php') {
            return;
        }
        require get_template_directory() . '/template/meta-box/page/products/section-tabs/index.php';
        if (function_exists('render_products_page_tabs')) {
            render_products_page_tabs();
        }
    }
}
