<div class="buildpro-block">
    <h3><?php echo esc_html__('Products Title', 'buildpro'); ?></h3>
    <p class="buildpro-row">
        <label><?php echo esc_html__('Title', 'buildpro'); ?></label>
        <input type="text" class="regular-text" name="products_title" value="<?php echo esc_attr($title); ?>">
    </p>
    <p class="buildpro-row">
        <label><?php echo esc_html__('Description', 'buildpro'); ?></label>
        <textarea class="large-text" rows="4" name="products_description"><?php echo esc_textarea($desc); ?></textarea>
    </p>
</div>