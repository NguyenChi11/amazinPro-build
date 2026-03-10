<div class="buildpro-post-block">
    <p class="buildpro-post-field">
        <label>Banner Image</label>
        <input type="hidden" id="buildpro_post_banner_id" name="buildpro_post_banner_id"
            value="<?php echo esc_attr($banner_id); ?>">
        <button type="button" class="button" id="buildpro_post_select_banner">Select photo</button>
        <button type="button" class="button" id="buildpro_post_remove_banner">Remove photo</button>
    </p>
    <div class="image-banner-preview" id="buildpro_post_banner_preview">
        <?php if ($thumb) : ?>
            <img src="<?php echo esc_url($thumb); ?>">
        <?php else : ?>
            <span style="color:#888">No banner selected</span>
        <?php endif; ?>
    </div>
</div>