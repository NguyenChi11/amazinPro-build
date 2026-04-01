<?php if (!defined('ABSPATH')) {
    exit;
} ?>

<div id="buildpro_about_policy_meta">
    <div class="buildpro-admin-tabs">
        <button type="button" class="button buildpro-about-policy-tabs is-active"
            data-tab="buildpro_about_policy_tabs_certification"><?php echo esc_html__('Certification', 'buildpro'); ?></button>
        <button type="button" class="button buildpro-about-policy-tabs"
            data-tab="buildpro_about_policy_tab_items"><?php echo esc_html__('Warranty', 'buildpro'); ?></button>
    </div>
    <div id="buildpro_about_policy_tabs_certification">
        <p><label><input type="checkbox" name="buildpro_about_policy_enabled" value="1"
                    <?php checked($enabled, 1); ?>><?php echo esc_html__('Enabled', 'buildpro'); ?></label></p>
        <p><label><?php echo esc_html__('Left Title', 'buildpro'); ?><br><input type="text" class="widefat" name="buildpro_about_policy_title_left"
                    value="<?php echo esc_attr($title_left); ?>"></label></p>
        <p><label><?php echo esc_html__('Business Registration', 'buildpro'); ?><br><input type="text" class="widefat"
                    name="buildpro_about_policy_business_registration"
                    value="<?php echo esc_attr($business_registration); ?>"></label></p>
        <p><label><?php echo esc_html__('General Contractor', 'buildpro'); ?><br><input type="text" class="widefat"
                    name="buildpro_about_policy_general_contractor"
                    value="<?php echo esc_attr($general_contractor); ?>"></label></p>
        <p><label><?php echo esc_html__('DUNS Number', 'buildpro'); ?><br><input type="text" class="widefat" name="buildpro_about_policy_duns_number"
                    value="<?php echo esc_attr($duns_number); ?>"></label></p>
        <h3 class="title"><?php echo esc_html__('Certifications', 'buildpro'); ?></h3>
        <div id="buildpro_about_policy_certs_wrap">
            <?php if (!empty($certifications)) {
                foreach ($certifications as $i => $c) {
                    $image_id = isset($c['image_id']) ? (int)$c['image_id'] : 0;
                    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : (isset($c['image_url']) ? (string)$c['image_url'] : '');
                    $c_url = isset($c['url']) ? (string)$c['url'] : '';
                    $c_title = isset($c['title']) ? (string)$c['title'] : '';
                    $c_desc = isset($c['desc']) ? (string)$c['desc'] : ''; ?>
                    <div class="policy-cert-item">
                        <p><label><?php echo esc_html__('Image', 'buildpro'); ?></label></p>
                        <div class="policy-cert-preview" id="policy_cert_preview_<?php echo $i; ?>">
                            <?php echo $image_url ? '<img src="' . esc_url($image_url) . '" style="max-width:120px;height:auto;border:1px solid #e5e7eb;border-radius:6px;">' : '<div class="policy-cert-empty">' . esc_html__('No image', 'buildpro') . '</div>'; ?>
                        </div>
                        <input type="hidden" id="policy_cert_image_id_<?php echo $i; ?>"
                            name="buildpro_about_policy_certifications[<?php echo $i; ?>][image_id]"
                            value="<?php echo (int)$image_id; ?>">
                        <input type="hidden" id="policy_cert_image_url_<?php echo $i; ?>"
                            name="buildpro_about_policy_certifications[<?php echo $i; ?>][image_url]"
                            value="<?php echo esc_attr($image_url); ?>">
                        <p>
                            <button type="button" class="button policy-cert-select" data-idx="<?php echo $i; ?>"><?php echo esc_html__('Choose Image', 'buildpro'); ?></button>
                            <button type="button" class="button policy-cert-remove" data-idx="<?php echo $i; ?>"><?php echo esc_html__('Remove', 'buildpro'); ?></button>
                        </p>
                        <p><label><?php echo esc_html__('URL', 'buildpro'); ?><br><input type="text" class="widefat"
                                    name="buildpro_about_policy_certifications[<?php echo $i; ?>][url]"
                                    value="<?php echo esc_attr($c_url); ?>"></label></p>
                        <p><label><?php echo esc_html__('Title', 'buildpro'); ?><br><input type="text" class="widefat"
                                    name="buildpro_about_policy_certifications[<?php echo $i; ?>][title]"
                                    value="<?php echo esc_attr($c_title); ?>"></label></p>
                        <p><label><?php echo esc_html__('Description', 'buildpro'); ?><br><textarea class="widefat" rows="3"
                                    name="buildpro_about_policy_certifications[<?php echo $i; ?>][desc]"><?php echo esc_textarea($c_desc); ?></textarea></label>
                        </p>
                        <p><button type="button" class="button remove-policy-cert"><?php echo esc_html__('Remove', 'buildpro'); ?></button></p>
                    </div>
            <?php }
            } ?>
        </div>
        <p><button type="button" class="button" id="buildpro_about_policy_add_cert"><?php echo esc_html__('Add Certification', 'buildpro'); ?></button></p>
    </div>
    <div id="buildpro_about_policy_tab_items" style="display:none">
        <p><label><?php echo esc_html__('Right Title', 'buildpro'); ?><br><input type="text" class="widefat" name="buildpro_about_policy_title_right"
                    value="<?php echo esc_attr($title_right); ?>"></label></p>
        <p><label><?php echo esc_html__('Description', 'buildpro'); ?><br><textarea class="widefat" rows="4"
                    name="buildpro_about_policy_warranty_desc"><?php echo esc_textarea($warranty_desc); ?></textarea></label>
        </p>
        <div id="buildpro_about_policy_items_wrap">
            <?php if (!empty($items)) {
                foreach ($items as $i => $it) {
                    $icon_id = isset($it['icon_id']) ? (int)$it['icon_id'] : 0;
                    $icon_url = $icon_id ? wp_get_attachment_image_url($icon_id, 'thumbnail') : (isset($it['icon_url']) ? (string)$it['icon_url'] : '');
                    $it_title = isset($it['title']) ? (string)$it['title'] : '';
                    $it_desc = isset($it['desc']) ? (string)$it['desc'] : ''; ?>
                    <div class="policy-item">
                        <p><label><?php echo esc_html__('Icon Image', 'buildpro'); ?></label></p>
                        <div class="policy-icon-preview" id="policy_icon_preview_<?php echo $i; ?>">
                            <?php echo $icon_url ? '<img src="' . esc_url($icon_url) . '" style="max-width:60px;height:auto;border-radius:6px;border:1px solid #e5e7eb;">' : '<div class="policy-icon-empty">' . esc_html__('No image', 'buildpro') . '</div>'; ?>
                        </div>
                        <input type="hidden" id="policy_icon_id_<?php echo $i; ?>"
                            name="buildpro_about_policy_items[<?php echo $i; ?>][icon_id]" value="<?php echo (int)$icon_id; ?>">
                        <input type="hidden" id="policy_icon_url_<?php echo $i; ?>"
                            name="buildpro_about_policy_items[<?php echo $i; ?>][icon_url]"
                            value="<?php echo esc_attr($icon_url); ?>">
                        <p>
                            <button type="button" class="button policy-select-image" data-idx="<?php echo $i; ?>"><?php echo esc_html__('Choose Image', 'buildpro'); ?></button>
                            <button type="button" class="button policy-remove-image"
                                data-idx="<?php echo $i; ?>"><?php echo esc_html__('Remove', 'buildpro'); ?></button>
                        </p>
                        <p><label><?php echo esc_html__('Title', 'buildpro'); ?><br><input type="text" class="widefat"
                                    name="buildpro_about_policy_items[<?php echo $i; ?>][title]"
                                    value="<?php echo esc_attr($it_title); ?>"></label></p>
                        <p><label><?php echo esc_html__('Description', 'buildpro'); ?><br><textarea class="widefat" rows="3"
                                    name="buildpro_about_policy_items[<?php echo $i; ?>][desc]"><?php echo esc_textarea($it_desc); ?></textarea></label>
                        </p>
                        <p><button type="button" class="button remove-policy-item"><?php echo esc_html__('Remove', 'buildpro'); ?></button></p>
                    </div>
            <?php }
            } ?>
        </div>
        <p><button type="button" class="button" id="buildpro_about_policy_add_item"><?php echo esc_html__('Add Item', 'buildpro'); ?></button></p>
    </div>
</div>