<?php if (!defined('ABSPATH')) {
    exit;
} ?>
<div class="buildpro-banner-block"
    style="margin-bottom:10px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:12px">
    <h4><?php echo esc_html__('Home Banner Status', 'buildpro'); ?></h4>
    <input type="hidden" id="buildpro_banner_enabled" name="buildpro_banner_enabled" value="1">
    <div style="display:flex;gap:8px">
        <button type="button" class="button button-secondary"
            id="buildpro_banner_disable_btn"><?php echo esc_html__('Disable Banner', 'buildpro'); ?></button>
        <button type="button" class="button button-primary"
            id="buildpro_banner_enable_btn"><?php echo esc_html__('Enable Banner', 'buildpro'); ?></button>
        <span id="buildpro_banner_enabled_state" style="align-self:center;color:#374151"></span>
    </div>
</div>
<template id="buildpro-banner-row-template">
    <div class="buildpro-banner-row" data-index="__INDEX__">
        <div class="buildpro-banner-grid">
            <div class="buildpro-banner-block">
                <h4><?php echo esc_html__('Banner Image', 'buildpro'); ?></h4>
                <div class="buildpro-banner-field">
                    <input type="hidden" class="banner-image-id" name="buildpro_banner_items[__INDEX__][image_id]"
                        value="">
                    <button type="button"
                        class="button select-banner-image"><?php echo esc_html__('Select photo', 'buildpro'); ?></button>
                    <button type="button"
                        class="button remove-banner-image"><?php echo esc_html__('Remove photo', 'buildpro'); ?></button>
                </div>
                <div class="banner-image-preview"
                    style="margin-top:8px;min-height:84px;display:flex;align-items:center;justify-content:center;background:#fff;border:1px dashed #ddd;border-radius:6px">
                    <span style="color:#888"><?php echo esc_html__('No image selected', 'buildpro'); ?></span>
                </div>
            </div>
            <div class="buildpro-banner-block">
                <h4><?php echo esc_html__('Banner Content', 'buildpro'); ?></h4>
                <p class="buildpro-banner-field"><label><?php echo esc_html__('Type', 'buildpro'); ?></label><input
                        type="text" name="buildpro_banner_items[__INDEX__][type]" class="regular-text" value=""></p>
                <p class="buildpro-banner-field"><label><?php echo esc_html__('Text', 'buildpro'); ?></label><input
                        type="text" name="buildpro_banner_items[__INDEX__][text]" class="regular-text" value=""></p>
                <p class="buildpro-banner-field">
                    <label><?php echo esc_html__('Description', 'buildpro'); ?></label><textarea
                        name="buildpro_banner_items[__INDEX__][description]" rows="4" class="large-text"></textarea>
                </p>
                <h4><?php echo esc_html__('Link', 'buildpro'); ?></h4>
                <p class="buildpro-banner-field">
                    <label><?php echo esc_html__('Button URL', 'buildpro'); ?></label><input type="url"
                        name="buildpro_banner_items[__INDEX__][link_url]" class="regular-text" value=""
                        placeholder="https://..."> <button type="button"
                        class="button choose-link"><?php echo esc_html__('Choose Link', 'buildpro'); ?></button>
                </p>
                <p class="buildpro-banner-field">
                    <label><?php echo esc_html__('Button Label', 'buildpro'); ?></label><input type="text"
                        name="buildpro_banner_items[__INDEX__][link_title]" class="regular-text" value=""
                        placeholder="<?php echo esc_attr__('Button text', 'buildpro'); ?>">
                </p>
                <p class="buildpro-banner-field"><label><?php echo esc_html__('Link Target', 'buildpro'); ?></label>
                <div class="checkbox-label">
                    <input type="hidden" name="buildpro_banner_items[__INDEX__][link_target]" value="">
                    <input type="checkbox" name="buildpro_banner_items[__INDEX__][link_target]" value="_blank">
                    <?php echo esc_html__('Open in new tab', 'buildpro'); ?>
                </div>
                </p>
            </div>
        </div>
        <div class="buildpro-banner-actions"><button type="button"
                class="button remove-banner-row"><?php echo esc_html__('Remove', 'buildpro'); ?></button>
        </div>
    </div>
</template>
<div id="buildpro-banner-wrapper"></div>
<button type="button" class="button button-primary"
    id="buildpro-banner-add"><?php echo esc_html__('Add row', 'buildpro'); ?></button>