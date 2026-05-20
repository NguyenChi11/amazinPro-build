<?php
$post_desc_short = isset($post_desc_short) ? $post_desc_short : '';
?>
<div class="buildpro-post-block">
    <p class="buildpro-post-field">
        <label><?php esc_html_e('Description Short', 'buildpro'); ?></label>
        <textarea name="buildpro_post_description_short" rows="5"
            class="large-text"><?php echo esc_textarea($post_desc_short); ?></textarea>
    </p>
</div>
