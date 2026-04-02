<?php

/**
 * Customizer control templates for footer dynamic controls.
 * Required variables:
 *   $buildpro_control_type — 'footer-contact-links' | 'footer-single-link'
 *   $items       — array of items (for footer-contact-links)
 *   $item        — array (for footer-single-link)
 *   $label       — control label
 *   $description — control description
 *   $link_attr   — output of $this->get_link()
 */

if (isset($buildpro_control_type) && $buildpro_control_type === 'footer-contact-links') : ?>
<span class="customize-control-title"><?= esc_html($label) ?></span>
<?php if (!empty($description)) : ?>
<p class="description"><?= esc_html($description) ?></p>
<?php endif; ?>
<input type="hidden" id="buildpro-footer-contact-links-data" class="footer-contact-links-json" <?= $link_attr ?>
    value="<?= esc_attr(wp_json_encode($items)) ?>">
<div id="customizer-footer-contact-links-wrapper">
    <?php $index = 0;
        foreach ($items as $cl) :
            $cl_icon_id = isset($cl['icon_id']) ? absint($cl['icon_id'])                  : 0;
            $cl_thumb   = $cl_icon_id ? wp_get_attachment_image_url($cl_icon_id, 'thumbnail') : '';
            $cl_url     = isset($cl['url'])    ? esc_url($cl['url'])                      : '';
            $cl_title   = isset($cl['title'])  ? sanitize_text_field($cl['title'])         : '';
            $cl_target  = isset($cl['target']) ? sanitize_text_field($cl['target'])        : '';
        ?>
    <div class="buildpro-block buildpro-footer-row" data-index="<?= esc_attr($index) ?>">
        <div class="buildpro-footer-row-header" role="button" tabindex="0" aria-expanded="false">
            <span class="buildpro-footer-row-label">
                <?php echo $cl_title ? esc_html($cl_title) : sprintf(esc_html__('Item %d', 'buildpro'), $index + 1); ?>
            </span>
            <span class="buildpro-footer-row-arrow">&#9660;</span>
        </div>
        <div class="buildpro-footer-row-body" style="display:none">
            <p class="buildpro-field">
                <label><?php echo esc_html__('Icon', 'buildpro'); ?></label>
                <input type="hidden" class="regular-text" data-field="icon_id" value="<?= esc_attr($cl_icon_id) ?>">
                <button type="button"
                    class="button select-contact-icon"><?php echo esc_html__('Select photo', 'buildpro'); ?></button>
                <button type="button"
                    class="button remove-contact-icon"><?php echo esc_html__('Remove photo', 'buildpro'); ?></button>
            </p>
            <div class="image-preview contact-icon-preview">
                <?= $cl_thumb ? '<img src="' . esc_url($cl_thumb) . '" style="max-height:80px;">' : '<span style="color:#888">' . esc_html__('No image selected', 'buildpro') . '</span>' ?>
            </div>
            <p class="buildpro-field">
                <label><?php echo esc_html__('URL', 'buildpro'); ?></label>
                <input type="url" class="regular-text" data-field="url" value="<?= esc_attr($cl_url) ?>"
                    placeholder="https://...">
                <button type="button"
                    class="button choose-link"><?php echo esc_html__('Choose Link', 'buildpro'); ?></button>
            </p>
            <p class="buildpro-field">
                <label><?php echo esc_html__('Button Label', 'buildpro'); ?></label>
                <input type="text" class="regular-text" data-field="title" value="<?= esc_attr($cl_title) ?>">
            </p>
            <p class="buildpro-field">
                <label><?php echo esc_html__('Link Target', 'buildpro'); ?></label>
            <div class="checkbox-label">
                <input type="checkbox" data-field="target" value="_blank" <?php checked($cl_target, '_blank'); ?>>
                <?php echo esc_html__('Open in new tab', 'buildpro'); ?>
            </div>
            </p>
            <div class="buildpro-actions">
                <button type="button" class="button remove-row"><?php echo esc_html__('Remove', 'buildpro'); ?></button>
            </div>
        </div>
    </div>
    <?php $index++;
        endforeach; ?>
</div>
<button type="button" class="button button-primary"
    id="customizer-footer-contact-links-add"><?php echo esc_html__('Add Item', 'buildpro'); ?></button>

<template id="buildpro-footer-contact-links-template">
    <div class="buildpro-block buildpro-footer-row" data-index="">
        <div class="buildpro-footer-row-header" role="button" tabindex="0" aria-expanded="true">
            <span class="buildpro-footer-row-label"><?php echo esc_html__('Item', 'buildpro'); ?></span>
            <span class="buildpro-footer-row-arrow">&#9660;</span>
        </div>
        <div class="buildpro-footer-row-body" style="display:block">
            <p class="buildpro-field">
                <label><?php echo esc_html__('Icon', 'buildpro'); ?></label>
                <input type="hidden" class="regular-text" data-field="icon_id" value="0">
                <button type="button"
                    class="button select-contact-icon"><?php echo esc_html__('Select photo', 'buildpro'); ?></button>
                <button type="button"
                    class="button remove-contact-icon"><?php echo esc_html__('Remove photo', 'buildpro'); ?></button>
            </p>
            <div class="image-preview contact-icon-preview">
                <span style="color:#888"><?php echo esc_html__('No image selected', 'buildpro'); ?></span>
            </div>
            <p class="buildpro-field">
                <label><?php echo esc_html__('URL', 'buildpro'); ?></label>
                <input type="url" class="regular-text" data-field="url" value="" placeholder="https://...">
                <button type="button"
                    class="button choose-link"><?php echo esc_html__('Choose Link', 'buildpro'); ?></button>
            </p>
            <p class="buildpro-field">
                <label><?php echo esc_html__('Button Label', 'buildpro'); ?></label>
                <input type="text" class="regular-text" data-field="title" value="">
            </p>
            <p class="buildpro-field">
                <label><?php echo esc_html__('Link Target', 'buildpro'); ?></label>
            <div class="checkbox-label">
                <input type="checkbox" data-field="target" value="_blank">
                <?php echo esc_html__('Open in new tab', 'buildpro'); ?>
            </div>
            </p>
            <div class="buildpro-actions">
                <button type="button" class="button remove-row"><?php echo esc_html__('Remove', 'buildpro'); ?></button>
            </div>
        </div>
    </div>
</template>

<?php elseif (isset($buildpro_control_type) && $buildpro_control_type === 'footer-single-link') : ?>
<?php
    $item = isset($item) && is_array($item) ? $item : array();
    $url = isset($item['url']) ? esc_url($item['url']) : '';
    $title = isset($item['title']) ? sanitize_text_field($item['title']) : '';
    $target = isset($item['target']) ? sanitize_text_field($item['target']) : '';
    $control_id = isset($buildpro_single_link_id) ? sanitize_key($buildpro_single_link_id) : 'buildpro_single_link';
    ?>
<span class="customize-control-title"><?= esc_html($label) ?></span>
<?php if (!empty($description)) : ?>
<p class="description"><?= esc_html($description) ?></p>
<?php endif; ?>
<div class="buildpro-single-link-wrapper" id="customizer-<?= esc_attr($control_id) ?>-wrapper">
    <input type="hidden" class="footer-single-link-json" <?= $link_attr ?>
        value="<?= esc_attr(wp_json_encode($item)) ?>">
    <p class="buildpro-field">
        <label><?php echo esc_html__('URL', 'buildpro'); ?></label>
        <input type="url" class="regular-text" data-field="url" value="<?= esc_attr($url) ?>" placeholder="https://...">
        <button type="button" class="button choose-link"><?php echo esc_html__('Choose Link', 'buildpro'); ?></button>
    </p>
    <p class="buildpro-field">
        <label><?php echo esc_html__('Button Label', 'buildpro'); ?></label>
        <input type="text" class="regular-text" data-field="title" value="<?= esc_attr($title) ?>">
    </p>
    <p class="buildpro-field">
        <label><?php echo esc_html__('Link Target', 'buildpro'); ?></label>
    <div class="checkbox-label">
        <input type="checkbox" data-field="target" value="_blank" <?php checked($target, '_blank'); ?>>
        <?php echo esc_html__('Open in new tab', 'buildpro'); ?>
    </div>
    </p>
</div>

<?php endif; ?>