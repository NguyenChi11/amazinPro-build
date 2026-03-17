<?php
require get_template_directory() . '/inc/meta-box/page/home-page/section-banner/index.php';
require get_template_directory() . '/inc/meta-box/page/home-page/section-option/index.php';
require get_template_directory() . '/inc/meta-box/page/home-page/section-data/index.php';
require get_template_directory() . '/inc/meta-box/page/home-page/section-products/index.php';
require get_template_directory() . '/inc/meta-box/page/home-page/section-service/index.php';
require get_template_directory() . '/inc/meta-box/page/home-page/section-evaluate/index.php';
require get_template_directory() . '/inc/meta-box/page/home-page/section-projects/index.php';
require get_template_directory() . '/inc/meta-box/page/home-page/section-post/index.php';

function buildpro_home_group_meta_box_add($post_type, $post)
{
    if ($post_type !== 'page') {
        return;
    }
    $template = get_page_template_slug($post->ID);
    $front_id = (int) get_option('page_on_front');
    if ($template !== 'home-page.php' && (int)$post->ID !== $front_id) {
        return;
    }
    add_meta_box('buildpro_home_group', esc_html__('Home Page', 'buildpro'), 'buildpro_home_group_meta_box_render', 'page', 'normal', 'high');
}
add_action('add_meta_boxes', 'buildpro_home_group_meta_box_add', 10, 2);

function buildpro_home_group_meta_box_render($post)
{
    $template = get_page_template_slug($post->ID);
    $front_id = (int) get_option('page_on_front');
    if ($template !== 'home-page.php' && (int)$post->ID !== $front_id) {
        return;
    }

    // Shared i18n for Home Page admin templates/scripts.
    echo '<script>window.buildproHomeAdminI18n=' . wp_json_encode(array(
        'displaying' => __('Displaying', 'buildpro'),
        'hidden' => __('Hidden', 'buildpro'),
        'enabled' => __('Enabled', 'buildpro'),
        'disabled' => __('Disabled', 'buildpro'),
        'noImageSelected' => __('No image selected', 'buildpro'),
        'noIconSelected' => __('No icon selected', 'buildpro'),
        'noPhotoSelectedYet' => __('No photo selected yet', 'buildpro'),
        'chooseImage' => __('Choose Image', 'buildpro'),
        'useImage' => __('Use Image', 'buildpro'),
        'selectOptionIcon' => __('Select Option Icon', 'buildpro'),
        'useIcon' => __('Use Icon', 'buildpro'),
        'selectAPhoto' => __('Select a photo', 'buildpro'),
        'useThisPhoto' => __('Use this photo', 'buildpro'),
        'avatar' => __('Avatar', 'buildpro'),
        'selectPhoto' => __('Select photo', 'buildpro'),
        'removePhoto' => __('Remove photo', 'buildpro'),
        'name' => __('Name', 'buildpro'),
        'position' => __('Position', 'buildpro'),
        'description' => __('Description', 'buildpro'),
        'remove' => __('Remove', 'buildpro'),
        'addRow' => __('Add row', 'buildpro'),
    )) . ';</script>';

    // Render tabs navigation
    require get_template_directory() . '/template/meta-box/page/home/section-tabs/index.php';
    render_home_page_tabs();
}
