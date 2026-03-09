<?php if (!defined('ABSPATH')) {
    exit;
} ?>
<div class="buildpro-portfolio-block"
    style="margin-bottom:10px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:12px">
    <h4>Portfolio Status</h4>
    <input type="hidden" id="buildpro_portfolio_enabled" name="buildpro_portfolio_enabled" value="1">
    <div style="display:flex;gap:8px">
        <button type="button" class="button button-secondary" id="buildpro_portfolio_disable_btn">Disable
            Portfolio</button>
        <button type="button" class="button button-primary" id="buildpro_portfolio_enable_btn">Enable Portfolio</button>
        <span id="buildpro_portfolio_enabled_state" style="align-self:center;color:#374151"></span>
    </div>
</div>
<div id="buildpro_portfolio_meta" class="buildpro-portfolio-block">
    <p class="buildpro-portfolio-field"><label>Portfolio Title</label><input type="text" name="projects_title"
            class="regular-text" value="<?php echo esc_attr($title); ?>" placeholder="PROJECTS"></p>
    <p class="buildpro-portfolio-field"><label>Description</label><textarea name="projects_description" rows="4"
            class="large-text" placeholder="Short Description"><?php echo esc_textarea($desc); ?></textarea></p>
</div>