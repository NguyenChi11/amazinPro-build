<?php
require get_template_directory() . '/inc/meta-box/page/about-us-page/section-banner/index.php';
require get_template_directory() . '/inc/meta-box/page/about-us-page/section-core-values/index.php';
require get_template_directory() . '/inc/meta-box/page/about-us-page/section-leader/index.php';
require get_template_directory() . '/inc/meta-box/page/about-us-page/section-policy/index.php';
require get_template_directory() . '/inc/meta-box/page/about-us-page/section-contact/index.php';

function buildpro_about_group_meta_box_add($post_type, $post)
{
    if ($post_type !== 'page') {
        return;
    }
    $template = get_page_template_slug($post->ID);
    if ($template !== 'about-us-page.php') {
        return;
    }
    add_meta_box('buildpro_about_group', 'About Us', 'buildpro_about_group_meta_box_render', 'page', 'normal', 'high');
}
add_action('add_meta_boxes', 'buildpro_about_group_meta_box_add', 10, 2);

function buildpro_about_group_meta_box_render($post)
{
    $template = get_page_template_slug($post->ID);
    if ($template !== 'about-us-page.php') {
        return;
    }
    // Render tabs navigation
    require get_template_directory() . '/template/meta-box/page/about-us/section-tabs/index.php';
    render_about_us_page_tabs();
}
