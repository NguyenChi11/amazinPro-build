<?php

if (!function_exists('buildpro_about_us_add_inline_i18n')) {
    function buildpro_about_us_add_inline_i18n($handle)
    {
        $data = array(
            'itemLabel' => __('Item %d', 'buildpro'),
            'label' => __('Label', 'buildpro'),
            'value' => __('Value', 'buildpro'),
            'remove' => __('Remove', 'buildpro'),
            'addItem' => __('Add Item', 'buildpro'),
            'addFact' => __('Add Fact', 'buildpro'),
            'chooseImage' => __('Choose Image', 'buildpro'),
            'useImage' => __('Use image', 'buildpro'),
            'noImageSelected' => __('No image selected', 'buildpro'),
            'image' => __('Image', 'buildpro'),
            'iconImage' => __('Icon Image', 'buildpro'),
            'name' => __('Name', 'buildpro'),
            'position' => __('Position', 'buildpro'),
            'description' => __('Description', 'buildpro'),
            'url' => __('Button Link', 'buildpro'),
            'title' => __('Title', 'buildpro'),
            'loading' => __('Loading...', 'buildpro'),
            'limitNote' => __('Only up to %d items will be saved; extra items will not be saved.', 'buildpro'),
            'noItemsHelp' => __('No items. Click "%s" to add.', 'buildpro'),
        );

        wp_add_inline_script(
            $handle,
            'window.buildproAboutUsI18n=' . wp_json_encode($data) . ';',
            'before'
        );
    }
}

require get_template_directory() . '/inc/customizer/about-us-page/section-banner/index.php';
require get_template_directory() . '/inc/customizer/about-us-page/section-leader/index.php';
require get_template_directory() . '/inc/customizer/about-us-page/section-policy/index.php';
require get_template_directory() . '/inc/customizer/about-us-page/section-contact/index.php';
