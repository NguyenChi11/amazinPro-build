<?php
require get_template_directory() . '/inc/meta-box/page/about-us-page/section-banner/index.php';
require get_template_directory() . '/inc/meta-box/page/about-us-page/section-core-values/index.php';
require get_template_directory() . '/inc/meta-box/page/about-us-page/section-leader/index.php';
require get_template_directory() . '/inc/meta-box/page/about-us-page/section-policy/index.php';
require get_template_directory() . '/inc/meta-box/page/about-us-page/section-contact/index.php';

if (!function_exists('buildpro_about_us_admin_print_i18n')) {
    function buildpro_about_us_admin_print_i18n()
    {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;

        $data = array(
            'chooseImage' => __('Choose Image', 'buildpro'),
            'useImage' => __('Use Image', 'buildpro'),
            'remove' => __('Remove', 'buildpro'),
            'title' => __('Title', 'buildpro'),
            'text' => __('Text', 'buildpro'),
            'description' => __('Description', 'buildpro'),
            'value' => __('Value', 'buildpro'),
            'label' => __('Label', 'buildpro'),
            'addFact' => __('Add Fact', 'buildpro'),
            'addItem' => __('Add Item', 'buildpro'),
            'addCertification' => __('Add Certification', 'buildpro'),
            'iconImage' => __('Icon Image', 'buildpro'),
            'image' => __('Image', 'buildpro'),
            'noImage' => __('No image', 'buildpro'),
            'url' => __('URL', 'buildpro'),
            'name' => __('Name', 'buildpro'),
            'position' => __('Position', 'buildpro'),
        );

        echo '<script>window.buildproAboutUsAdminI18n = window.buildproAboutUsAdminI18n || ' . wp_json_encode($data) . ';</script>';
    }
}

function buildpro_about_group_meta_box_add($post_type, $post)
{
    if ($post_type !== 'page') {
        return;
    }
    $template = get_page_template_slug($post->ID);
    if ($template !== 'about-us-page.php') {
        return;
    }
    add_meta_box('buildpro_about_group', esc_html__('About Us', 'buildpro'), 'buildpro_about_group_meta_box_render', 'page', 'normal', 'high');
}
add_action('add_meta_boxes', 'buildpro_about_group_meta_box_add', 10, 2);

function buildpro_about_group_meta_box_render($post)
{
    $template = get_page_template_slug($post->ID);
    if ($template !== 'about-us-page.php') {
        return;
    }

    buildpro_about_us_admin_print_i18n();

    // Render tabs navigation
    require get_template_directory() . '/template/meta-box/page/about-us/section-tabs/index.php';
    render_about_us_page_tabs();
}
