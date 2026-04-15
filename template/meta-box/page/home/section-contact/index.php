<?php if (!defined('ABSPATH')) {
    exit;
} ?>

<div id="buildpro-contact-meta-box">
    <div class="buildpro-contact-block"
        style="margin-bottom:10px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:12px">
        <h4><?php echo esc_html__('Contact Section Status', 'buildpro'); ?></h4>
        <input type="hidden" id="buildpro_contact_enabled" name="buildpro_contact_enabled"
            value="<?php echo isset($enabled) ? (int) $enabled : 1; ?>">
        <div style="display:flex;gap:8px">
            <button type="button" class="button button-secondary"
                id="buildpro_contact_disable_btn"><?php echo esc_html__('Hide Section', 'buildpro'); ?></button>
            <button type="button" class="button button-primary"
                id="buildpro_contact_enable_btn"><?php echo esc_html__('Show Section', 'buildpro'); ?></button>
            <span id="buildpro_contact_enabled_state" style="align-self:center;color:#374151"></span>
        </div>
    </div>

    <div class="buildpro-contact-block buildpro-contact-fields">
        <p class="buildpro-contact-field">
            <label><?php echo esc_html__('Title', 'buildpro'); ?></label>
            <input type="text" name="buildpro_contact_title" class="regular-text"
                value="<?php echo esc_attr($contact_title); ?>">
        </p>

        <p class="buildpro-contact-field">
            <label><?php echo esc_html__('Description', 'buildpro'); ?></label>
            <textarea name="buildpro_contact_description" rows="4"
                class="large-text"><?php echo esc_textarea($contact_description); ?></textarea>
        </p>

        <p class="buildpro-contact-field">
            <label><?php echo esc_html__('Input Placeholder', 'buildpro'); ?></label>
            <input type="text" name="buildpro_contact_placeholder" class="regular-text"
                value="<?php echo esc_attr($contact_placeholder); ?>">
        </p>

        <div class="buildpro-contact-field buildpro-contact-field--image">
            <label><?php echo esc_html__('Section Image', 'buildpro'); ?></label>
            <div class="buildpro-contact-image-preview" id="buildpro_contact_image_preview">
                <?php if (!empty($contact_image_url)) : ?>
                    <img src="<?php echo esc_url($contact_image_url); ?>" alt="" />
                <?php else : ?>
                    <span><?php echo esc_html__('No image selected', 'buildpro'); ?></span>
                <?php endif; ?>
            </div>
            <input type="hidden" id="buildpro_contact_image_id" name="buildpro_contact_image_id"
                value="<?php echo esc_attr($contact_image_id); ?>">
            <p class="buildpro-contact-image-actions">
                <button type="button" class="button"
                    id="buildpro_contact_upload_btn"><?php echo esc_html__('Choose Image', 'buildpro'); ?></button>
                <button type="button" class="button"
                    id="buildpro_contact_remove_btn"><?php echo esc_html__('Remove', 'buildpro'); ?></button>
            </p>
        </div>
    </div>
</div>