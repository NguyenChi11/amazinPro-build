<?php if (!defined('ABSPATH')) {
    exit;
} ?>
<div class="buildpro-post-section-block">
    <h4><?php echo esc_html__('Post Section Status', 'buildpro'); ?></h4>
    <input type="hidden" id="buildpro_post_enabled" name="buildpro_post_enabled" value="1">
    <div style="display:flex;gap:8px">
        <button type="button" class="button button-secondary" id="buildpro_post_disable_btn"><?php echo esc_html__('Disable Post', 'buildpro'); ?></button>
        <button type="button" class="button button-primary" id="buildpro_post_enable_btn"><?php echo esc_html__('Enable Post', 'buildpro'); ?></button>
        <span id="buildpro_post_enabled_state" style="align-self:center;color:#374151"></span>
    </div>
</div>
<div class="buildpro-post-section-block">
    <p class="buildpro-post-section-field">
        <label><?php echo esc_html__('Title', 'buildpro'); ?></label>
        <input type="text" name="title_post" class="regular-text" value="<?php echo esc_attr($title); ?>"
            placeholder="<?php echo esc_attr__('LATEST POSTS', 'buildpro'); ?>">
    </p>
    <p class="buildpro-post-section-field">
        <label><?php echo esc_html__('Description', 'buildpro'); ?></label>
        <textarea name="description_post" rows="4" class="large-text"
            placeholder="<?php echo esc_attr__('Description', 'buildpro'); ?>"><?php echo esc_textarea($desc); ?></textarea>
    </p>
</div>