<?php if (!defined('ABSPATH')) {
    exit;
} ?>
<div id="buildpro_about_contact_meta">
    <div class="buildpro-admin-tabs">
        <button type="button" class="button buildpro-about-contact-tabs is-active"
            data-tab="buildpro_about_contact_tab_content">Content</button>
        <button type="button" class="button buildpro-about-contact-tabs"
            data-tab="buildpro_about_contact_tab_contact">Contact</button>
    </div>
    <div id="buildpro_about_contact_tab_content">
        <p><label><input type="checkbox" name="buildpro_about_contact_enabled" value="1"
                    <?php checked($enabled, 1); ?>>Enable Contact</label></p>
        <p><label>Title<br><input type="text" class="widefat" name="buildpro_about_contact_title"
                    value="<?php echo esc_attr($title); ?>"></label></p>
        <p><label>Text<br><input type="text" class="widefat" name="buildpro_about_contact_text"
                    value="<?php echo esc_attr($text); ?>"></label></p>
        <div class="buildpro-field buildpro-field--image">
            <?php
            $map_image_id = (int) get_post_meta($post->ID, 'buildpro_about_contact_form_map_image_id', true);
            $map_thumb = $map_image_id ? wp_get_attachment_image_url($map_image_id, 'thumbnail') : '';
            ?>
            <label>Map Image</label>
            <div class="buildpro-image-wrap" style="margin:8px 0;">
                <img src="<?php echo esc_url($map_thumb ?: get_template_directory_uri() . '/assets/images/map.jpg'); ?>" alt="" style="max-width:120px;border:1px solid #ddd;border-radius:4px;">
            </div>
            <input type="hidden" name="buildpro_about_contact_form_map_image_id" value="<?php echo esc_attr($map_image_id); ?>">
            <p>
                <button type="button" class="button buildpro-map-upload">Choose Image</button>
                <button type="button" class="button buildpro-map-remove">Remove</button>
            </p>
        </div>
    </div>
    <div id="buildpro_about_contact_tab_contact" style="display: none;">
        <p><label>Address<br><input type="text" class="widefat" name="buildpro_about_contact_address"
                    value="<?php echo esc_attr($address); ?>"></label></p>
        <p><label>Phone<br><input type="text" class="widefat" name="buildpro_about_contact_phone"
                    value="<?php echo esc_attr($phone); ?>"></label></p>
        <p><label>Email<br><input type="text" class="widefat" name="buildpro_about_contact_email"
                    value="<?php echo esc_attr($email); ?>"></label></p>
    </div>
</div>
