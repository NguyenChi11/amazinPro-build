<?php if (!defined('ABSPATH')) {
    exit;
} ?>
<template id="buildpro-option-row-template">
    <div class="buildpro-option-row" data-index="__INDEX__">
        <div class="buildpro-option-grid">
            <div class="buildpro-option-block">
                <h4><?php echo esc_html__('Icon', 'buildpro'); ?></h4>
                <div class="buildpro-option-field">
                    <input type="hidden" class="option-icon-id" name="buildpro_option_items[__INDEX__][icon_id]"
                        value="">
                    <button type="button" class="button select-option-icon"><?php echo esc_html__('Select photo', 'buildpro'); ?></button>
                    <button type="button" class="button remove-option-icon"><?php echo esc_html__('Remove', 'buildpro'); ?></button>
                </div>
                <div class="option-icon-preview"
                    style="margin-top:8px;min-height:84px;display:flex;align-items:center;justify-content:center;background:#fff;border:1px dashed #ddd;border-radius:6px">
                    <span style="color:#888"><?php echo esc_html__('No icon selected', 'buildpro'); ?></span>
                </div>
            </div>
            <div class="buildpro-option-block">
                <h4><?php echo esc_html__('Content', 'buildpro'); ?></h4>
                <p class="buildpro-option-field"><label><?php echo esc_html__('Text', 'buildpro'); ?></label><input type="text"
                        name="buildpro_option_items[__INDEX__][text]" class="regular-text" value=""></p>
                <p class="buildpro-option-field"><label><?php echo esc_html__('Description', 'buildpro'); ?></label><textarea
                        name="buildpro_option_items[__INDEX__][description]" rows="4" class="large-text"></textarea></p>
            </div>
        </div>
        <div class="buildpro-option-actions"><button type="button" class="button remove-option-row"><?php echo esc_html__('Remove', 'buildpro'); ?></button>
        </div>
    </div>
</template>
<div class="buildpro-option-block"
    style="margin-bottom:10px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:12px">
    <h4><?php echo esc_html__('Section Option Status', 'buildpro'); ?></h4>
    <input type="hidden" id="buildpro_option_enabled" name="buildpro_option_enabled"
        value="<?php echo isset($enabled) ? (int)$enabled : 1; ?>">
    <div style="display:flex;gap:8px">
        <button type="button" class="button button-secondary" id="buildpro_option_disable_btn"><?php echo esc_html__('Disable Section', 'buildpro'); ?></button>
        <button type="button" class="button button-primary" id="buildpro_option_enable_btn"><?php echo esc_html__('Enable Section', 'buildpro'); ?></button>
        <span id="buildpro_option_enabled_state" style="align-self:center;color:#374151"></span>
    </div>
</div>
<div id="buildpro-option-wrapper"></div>
<button type="button" class="button button-primary" id="buildpro-option-add"><?php echo esc_html__('Add row', 'buildpro'); ?></button>