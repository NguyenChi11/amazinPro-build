<div class="buildpro-post-block">
    <p class="buildpro-post-field">
        <label>Title</label>
        <input type="text" name="buildpro_post_quote_title" class="regular-text" value="<?php echo esc_attr($quote_title); ?>">
    </p>
    <p class="buildpro-post-field">
        <label>Description</label>
        <textarea name="buildpro_post_quote_description" rows="4" class="large-text"><?php echo esc_textarea($quote_desc); ?></textarea>
    </p>
    <p class="buildpro-post-field">
        <label>Image Description</label>
        <textarea name="buildpro_post_quote_desc_image_desc" rows="3" class="large-text"><?php echo esc_textarea($quote_desc_img_desc); ?></textarea>
    </p>
    <div class="buildpro-post-field">
        <label>Gallery</label>
        <div class="quote-gallery" id="buildpro_post_quote_gallery">
            <?php foreach ($quote_gallery as $img_id) : ?>
                <?php $u = wp_get_attachment_image_url((int)$img_id, 'thumbnail'); ?>
                <?php if ($u) : ?>
                    <div class="quote-thumb" data-id="<?php echo esc_attr($img_id); ?>">
                        <img src="<?php echo esc_url($u); ?>">
                        <button type="button" class="remove">x</button>
                        <input type="hidden" name="buildpro_post_quote_gallery[]" value="<?php echo esc_attr($img_id); ?>">
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <button type="button" class="button" id="buildpro_post_add_gallery">Add image</button>
    </div>
    <div class="buildpro-post-field">
        <label>Quote Repeater</label>
        <div id="buildpro_post_quote_kv">
            <?php $i = 0;
            foreach ($quote_kv as $row) : ?>
                <?php
                $k = isset($row['key']) ? sanitize_text_field($row['key']) : '';
                $v = isset($row['value']) ? sanitize_text_field($row['value']) : '';
                ?>
                <div class="kv-row" data-index="<?php echo esc_attr($i); ?>">
                    <input type="text" name="buildpro_post_quote_kv[<?php echo esc_attr($i); ?>][key]" value="<?php echo esc_attr($k); ?>" placeholder="Key" class="regular-text">
                    <input type="text" name="buildpro_post_quote_kv[<?php echo esc_attr($i); ?>][value]" value="<?php echo esc_attr($v); ?>" placeholder="Value" class="regular-text">
                    <button type="button" class="button remove-kv">Remove</button>
                </div>
            <?php $i++;
            endforeach; ?>
        </div>
        <button type="button" class="button button-primary" id="buildpro_post_add_kv">Add row</button>
    </div>
</div>