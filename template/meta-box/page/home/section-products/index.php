<?php if (!defined('ABSPATH')) {
    exit;
} ?>
<div class="buildpro-materials-block" style="margin-bottom:10px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:12px">
    <h4><?php echo esc_html__('Products Section Status', 'buildpro'); ?></h4>
    <input type="hidden" id="materials_enabled" name="materials_enabled" value="<?php echo isset($materials_enabled) ? (int)$materials_enabled : 1; ?>">
    <div style="display:flex;gap:8px">
        <button type="button" class="button button-secondary" id="materials_disable_btn"><?php echo esc_html__('Remove Section', 'buildpro'); ?></button>
        <button type="button" class="button button-primary" id="materials_enable_btn"><?php echo esc_html__('Add Section', 'buildpro'); ?></button>
        <span id="materials_enabled_state" style="align-self:center;color:#374151"></span>
    </div>
</div>
<div class="buildpro-materials-block" id="buildpro-materials-meta-box">
    <p class="buildpro-materials-field">
        <label><?php echo esc_html__('Section Title', 'buildpro'); ?></label>
        <input type="text" name="materials_title" class="regular-text" value="<?php echo esc_attr($materials_title); ?>"
            placeholder="<?php echo esc_attr__('MATERIALS', 'buildpro'); ?>">
    </p>
    <p class="buildpro-materials-field">
        <label><?php echo esc_html__('Section Description', 'buildpro'); ?></label>
        <textarea name="materials_description" rows="4" class="large-text"
            placeholder="<?php echo esc_attr__('Short description', 'buildpro'); ?>"><?php echo esc_textarea($materials_description); ?></textarea>
    </p>
    <p class="buildpro-materials-field">
        <label><?php echo esc_html__('View All Button Text', 'buildpro'); ?></label>
        <input type="text" name="materials_view_all_text" class="regular-text"
            value="<?php echo esc_attr(isset($materials_view_all_text) ? $materials_view_all_text : ''); ?>"
            placeholder="<?php echo esc_attr__('View All Products', 'buildpro'); ?>">
    </p>
</div>