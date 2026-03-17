<div class="buildpro-post-block">
    <p class="buildpro-post-field">
        <label><?php esc_html_e('Banner Image', 'buildpro'); ?></label>
        <input type="hidden" id="buildpro_post_banner_id" name="buildpro_post_banner_id"
            value="<?php echo esc_attr($banner_id); ?>">
        <button type="button" class="button"
            id="buildpro_post_select_banner"><?php esc_html_e('Select photo', 'buildpro'); ?></button>
        <button type="button" class="button"
            id="buildpro_post_remove_banner"><?php esc_html_e('Remove photo', 'buildpro'); ?></button>
    </p>
    <div class="image-banner-preview" id="buildpro_post_banner_preview">
        <?php if ($thumb) : ?>
            <img src="<?php echo esc_url($thumb); ?>">
        <?php else : ?>
            <span style="color:#888"><?php esc_html_e('No banner selected', 'buildpro'); ?></span>
        <?php endif; ?>
    </div>
</div>