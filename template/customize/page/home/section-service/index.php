<?php if (!defined('ABSPATH')) {
    exit;
} ?>
<?php if (!is_array($items)) {
    $items = array();
} ?>
<input type="hidden" id="buildpro-services-data" <?php $this->link(); ?>
    value="<?php echo esc_attr(wp_json_encode($items)); ?>">
<div id="buildpro-services-wrapper">
    <?php
    $index = 0;
    foreach ($items as $item) {
        $icon_id = isset($item['icon_id']) ? (int)$item['icon_id'] : 0;
        $title = isset($item['title']) ? sanitize_text_field($item['title']) : '';
        $desc = isset($item['description']) ? sanitize_textarea_field($item['description']) : '';
        $link_url = isset($item['link_url']) ? esc_url_raw($item['link_url']) : '';
        $link_title = isset($item['link_title']) ? sanitize_text_field($item['link_title']) : '';
        $link_target = isset($item['link_target']) ? sanitize_text_field($item['link_target']) : '';
        $thumb = $icon_id ? wp_get_attachment_image_url($icon_id, 'thumbnail') : '';
    ?>
        <div class="buildpro-services-row" data-index="<?php echo esc_attr($index); ?>">
            <div class="buildpro-services-header">
                <span class="buildpro-services-label"><?php echo $title ? esc_html($title) : sprintf(esc_html__('Item %d', 'buildpro'), $index + 1); ?></span>
                <span class="buildpro-services-arrow">&#9660;</span>
            </div>
            <div class="buildpro-services-body" style="display:none">
                <div class="buildpro-services-grid">
                    <div class="buildpro-services-block">
                        <h4><?php echo esc_html__('Icon', 'buildpro'); ?></h4>
                        <div class="buildpro-services-field">
                            <input type="hidden" class="services-icon-id" data-field="icon_id"
                                value="<?php echo esc_attr($icon_id); ?>">
                            <button type="button" class="button select-services-icon"><?php echo esc_html__('Select icon', 'buildpro'); ?></button>
                            <button type="button" class="button remove-services-icon"><?php echo esc_html__('Remove icon', 'buildpro'); ?></button>
                        </div>
                        <div class="services-icon-preview">
                            <?php echo $thumb ? '<img src="' . esc_url($thumb) . '" style="max-height:80px;">' : '<span style="color:#888">' . esc_html__('No icon selected', 'buildpro') . '</span>'; ?>
                        </div>
                    </div>
                    <div class="buildpro-services-block">
                        <h4><?php echo esc_html__('Content', 'buildpro'); ?></h4>
                        <p class="buildpro-services-field">
                            <label><?php echo esc_html__('Title', 'buildpro'); ?></label>
                            <input type="text" class="regular-text" data-field="title" value="<?php echo esc_attr($title); ?>">
                        </p>
                        <p class="buildpro-services-field">
                            <label><?php echo esc_html__('Description', 'buildpro'); ?></label>
                            <textarea rows="4" class="large-text"
                                data-field="description"><?php echo esc_textarea($desc); ?></textarea>
                        </p>
                        <h4><?php echo esc_html__('Link', 'buildpro'); ?></h4>
                        <p class="buildpro-services-field">
                            <label><?php echo esc_html__('Button Link', 'buildpro'); ?></label>
                            <input type="url" class="regular-text" data-field="link_url"
                                value="<?php echo esc_attr($link_url); ?>" placeholder="https://...">
                            <button type="button" class="button choose-link"><?php echo esc_html__('Choose Link', 'buildpro'); ?></button>
                        </p>
                        <p class="buildpro-services-field">
                            <label><?php echo esc_html__('Button Label', 'buildpro'); ?></label>
                            <input type="text" class="regular-text" data-field="link_title"
                                value="<?php echo esc_attr($link_title); ?>" placeholder="<?php echo esc_attr__('View Details', 'buildpro'); ?>">
                        </p>
                        <p class="buildpro-services-field">
                            <label><?php echo esc_html__('Link Target', 'buildpro'); ?></label>
                        <div class="checkbox-label">
                            <input type="checkbox" data-field="link_target" value="_blank" <?php checked($link_target, '_blank'); ?>>
                            <?php echo esc_html__('Open in new tab', 'buildpro'); ?>
                        </div>
                        </p>
                    </div>
                </div>
                <div class="buildpro-services-actions">
                    <button type="button" class="button remove-services-row"><?php echo esc_html__('Remove item', 'buildpro'); ?></button>
                </div>
            </div><!-- /.buildpro-services-body -->
        </div>
    <?php
        $index++;
    }
    ?>
    <span id="buildpro_service_enabled_state" style="align-self:center;color:#374151"></span>
    <button type="button" class="button button-primary" id="buildpro-services-add"><?php echo esc_html__('Add Item', 'buildpro'); ?></button>
</div>