<?php

if (!function_exists('buildpro_home_get_inline_i18n_data')) {
    function buildpro_home_get_inline_i18n_data()
    {
        return array(
            'itemFormat' => __('Item %d', 'buildpro'),
            'addItem' => __('Add Item', 'buildpro'),
            'removeItem' => __('Remove item', 'buildpro'),
            'remove' => __('Remove', 'buildpro'),

            'image' => __('Image', 'buildpro'),
            'content' => __('Content', 'buildpro'),
            'link' => __('Link', 'buildpro'),
            'icon' => __('Icon', 'buildpro'),
            'avatar' => __('Avatar', 'buildpro'),

            'selectImage' => __('Select image', 'buildpro'),
            'useImage' => __('Use Image', 'buildpro'),
            'selectPhoto' => __('Select photo', 'buildpro'),
            'removePhoto' => __('Remove photo', 'buildpro'),
            'noImageSelected' => __('No image selected', 'buildpro'),
            'noPhotoSelectedYet' => __('No photo selected yet', 'buildpro'),

            'selectIcon' => __('Select icon', 'buildpro'),
            'removeIcon' => __('Remove icon', 'buildpro'),
            'chooseIcon' => __('Choose icon', 'buildpro'),
            'use' => __('Use', 'buildpro'),
            'noIconSelected' => __('No icon selected', 'buildpro'),

            'chooseLink' => __('Choose Link', 'buildpro'),
            'sameTab' => __('Same Tab', 'buildpro'),
            'openInNewTab' => __('Open in new tab', 'buildpro'),
            'openInNewTabBlank' => __('Open in new tab (_blank)', 'buildpro'),
            'default' => __('Default', 'buildpro'),
            'close' => __('Close', 'buildpro'),
            'searchPagesPosts' => __('Search pages/posts', 'buildpro'),
            'enterKeyword' => __('Enter keyword...', 'buildpro'),
            'noResultsFound' => __('No results found.', 'buildpro'),

            'number' => __('Number', 'buildpro'),
            'text' => __('Text', 'buildpro'),
            'title' => __('Title', 'buildpro'),
            'description' => __('Description', 'buildpro'),
            'type' => __('Type', 'buildpro'),
            'name' => __('Name', 'buildpro'),
            'position' => __('Position', 'buildpro'),
            'linkUrl' => __('Link URL', 'buildpro'),
            'linkTitle' => __('Link Title', 'buildpro'),
            'linkTarget' => __('Link Target', 'buildpro'),
            'buttonText' => __('Button text', 'buildpro'),
            'viewDetails' => __('View Details', 'buildpro'),

            'apply' => __('Apply', 'buildpro'),
            'changesPreviewedInstantlyPublishToSave' => __('Changes are previewed instantly. Click Publish to save.', 'buildpro'),
            'useThisPhoto' => __('Use this photo', 'buildpro'),
        );
    }
}

if (!function_exists('buildpro_home_add_inline_i18n')) {
    function buildpro_home_add_inline_i18n($handle)
    {
        if (!is_string($handle) || $handle === '') {
            return;
        }

        $data = buildpro_home_get_inline_i18n_data();
        $js = 'window.buildproHomeI18n = ' . wp_json_encode($data) . ';';
        wp_add_inline_script($handle, $js, 'before');
    }
}

require get_template_directory() . '/inc/customizer/home-page/section-banner/index.php';
require get_template_directory() . '/inc/customizer/home-page/section-option/index.php';
require get_template_directory() . '/inc/customizer/home-page/section-data/index.php';
require get_template_directory() . '/inc/customizer/home-page/section-products/index.php';
require get_template_directory() . '/inc/customizer/home-page/section-service/index.php';
require get_template_directory() . '/inc/customizer/home-page/section-evaluate/index.php';
require get_template_directory() . '/inc/customizer/home-page/section-projects/index.php';
require get_template_directory() . '/inc/customizer/home-page/section-post/index.php';
