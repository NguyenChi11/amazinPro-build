<?php if (!defined('ABSPATH')) {
    exit;
} ?>
<div class="buildpro-portfolio-block"
    style="margin-bottom:10px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:12px">
    <h4><?php echo esc_html__('Projects Section Status', 'buildpro'); ?></h4>
    <input type="hidden" id="buildpro_portfolio_enabled" name="buildpro_portfolio_enabled" value="1">
    <div style="display:flex;gap:8px">
        <button type="button" class="button button-secondary" id="buildpro_portfolio_disable_btn"><?php echo esc_html__('Disable Section', 'buildpro'); ?></button>
        <button type="button" class="button button-primary" id="buildpro_portfolio_enable_btn"><?php echo esc_html__('Enable Section', 'buildpro'); ?></button>
        <span id="buildpro_portfolio_enabled_state" style="align-self:center;color:#374151"></span>
    </div>
</div>
<div id="buildpro_portfolio_meta" class="buildpro-portfolio-block">
    <p class="buildpro-portfolio-field"><label><?php echo esc_html__('Section Title', 'buildpro'); ?></label><input type="text" name="projects_title"
            class="regular-text" value="<?php echo esc_attr($title); ?>" placeholder="<?php echo esc_attr__('PROJECTS', 'buildpro'); ?>"></p>
    <p class="buildpro-portfolio-field"><label><?php echo esc_html__('Description', 'buildpro'); ?></label><textarea name="projects_description" rows="4"
            class="large-text" placeholder="<?php echo esc_attr__('Short description', 'buildpro'); ?>"><?php echo esc_textarea($desc); ?></textarea></p>
    <p class="buildpro-portfolio-field"><label><?php echo esc_html__('View All Button Text', 'buildpro'); ?></label><input type="text" name="projects_view_all_text"
            class="regular-text" value="<?php echo esc_attr(isset($view_all_text) ? $view_all_text : ''); ?>" placeholder="<?php echo esc_attr__('View All Projects', 'buildpro'); ?>"></p>
</div>