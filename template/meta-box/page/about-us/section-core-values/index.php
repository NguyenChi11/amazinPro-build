<?php if (!defined('ABSPATH')) {
    exit;
} ?>
<div id="buildpro_about_core_values_meta" class="buildpro-post-block">
    <div class="buildpro-admin-tabs">
        <button type="button" class="button buildpro-about-core-values-tab is-active"
            data-target="buildpro_about_core_values_tab_content"><?php echo esc_html__('Content', 'buildpro'); ?></button>
        <button type="button" class="button buildpro-about-core-values-tab"
            data-target="buildpro_about_core_values_tab_items"><?php echo esc_html__('Items', 'buildpro'); ?></button>
    </div>
    <div id="buildpro_about_core_values_tab_content">
        <p><label><input type="checkbox" name="buildpro_about_core_values_enabled" value="1"
                    <?php checked($enabled, 1); ?>> <?php echo esc_html__('Enabled', 'buildpro'); ?></label></p>
        <p><label><?php echo esc_html__('Title', 'buildpro'); ?><br><input type="text" class="widefat" name="buildpro_about_core_values_title"
                    value="<?php echo esc_attr($title); ?>"></label></p>
        <p><label><?php echo esc_html__('Description', 'buildpro'); ?></label></p>
        <?php
        ob_start();
        wp_editor($desc, 'buildpro_about_core_values_description_editor', array(
            'textarea_name' => 'buildpro_about_core_values_description',
            'textarea_rows' => 5,
            'media_buttons' => false,
            'teeny' => true,
            'quicktags' => false,
        ));
        echo ob_get_clean();
        ?>
    </div>
    <div id="buildpro_about_core_values_tab_items" style="display:none">
        <div id="buildpro_about_core_values_items_wrap">
            <?php if (!empty($items)) {
                foreach ($items as $i => $it) {
                    $icon_id = isset($it['icon_id']) ? (int)$it['icon_id'] : 0;
                    $icon_url = $icon_id ? wp_get_attachment_image_url($icon_id, 'thumbnail') : (isset($it['icon_url']) ? (string)$it['icon_url'] : '');
                    $it_title = isset($it['title']) ? (string)$it['title'] : '';
                    $it_desc = isset($it['description']) ? (string)$it['description'] : '';
                    $it_url = isset($it['url']) ? (string)$it['url'] : '';
                    $it_link_title = isset($it['link_title']) ? (string)$it['link_title'] : '';
            ?>
                    <div class="core-value-item">
                        <p><label><?php echo esc_html__('Icon Image', 'buildpro'); ?></label></p>
                        <div class="cv-icon-preview" id="cv_icon_preview_<?php echo $i; ?>">
                            <?php echo $icon_url ? '<img src="' . esc_url($icon_url) . '" style="max-width:60px;height:auto;border-radius:6px;border:1px solid #e5e7eb;">' : '<div class="cv-icon-empty">' . esc_html__('No image', 'buildpro') . '</div>'; ?>
                        </div>
                        <input type="hidden" id="cv_icon_id_<?php echo $i; ?>"
                            name="buildpro_about_core_values_items[<?php echo $i; ?>][icon_id]"
                            value="<?php echo (int)$icon_id; ?>">
                        <input type="hidden" id="cv_icon_url_<?php echo $i; ?>"
                            name="buildpro_about_core_values_items[<?php echo $i; ?>][icon_url]"
                            value="<?php echo esc_attr($icon_url); ?>">
                        <p>
                            <button type="button" class="button cv-select-image" data-idx="<?php echo $i; ?>"><?php echo esc_html__('Choose Image', 'buildpro'); ?></button>
                            <button type="button" class="button cv-remove-image" data-idx="<?php echo $i; ?>"><?php echo esc_html__('Remove', 'buildpro'); ?></button>
                        </p>
                        <p><label><?php echo esc_html__('Title', 'buildpro'); ?><br><input type="text" class="widefat"
                                    name="buildpro_about_core_values_items[<?php echo $i; ?>][title]"
                                    value="<?php echo esc_attr($it_title); ?>"></label></p>
                        <p><label><?php echo esc_html__('Description', 'buildpro'); ?><br><textarea class="widefat" rows="3"
                                    name="buildpro_about_core_values_items[<?php echo $i; ?>][description]"><?php echo esc_textarea($it_desc); ?></textarea></label>
                        </p>
                        <p><label><?php echo esc_html__('URL', 'buildpro'); ?><br><input type="text" class="widefat"
                                    name="buildpro_about_core_values_items[<?php echo $i; ?>][url]"
                                    value="<?php echo esc_attr($it_url); ?>"></label></p>
                        <p><label><?php echo esc_html__('Link Title', 'buildpro'); ?><br><input type="text" class="widefat"
                                    name="buildpro_about_core_values_items[<?php echo $i; ?>][link_title]"
                                    value="<?php echo esc_attr($it_link_title); ?>"></label></p>
                        <p><button type="button" class="button button-secondary cv-choose-link" data-idx="<?php echo $i; ?>"><?php echo esc_html__('Choose Link', 'buildpro'); ?></button></p>
                        <p><button type="button" class="button remove-core-value"><?php echo esc_html__('Remove', 'buildpro'); ?></button></p>
                    </div>
            <?php }
            } ?>
        </div>
        <p><button type="button" class="button" id="buildpro_add_core_value_item"><?php echo esc_html__('Add Item', 'buildpro'); ?></button></p>
    </div>
</div>