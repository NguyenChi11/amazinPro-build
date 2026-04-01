<?php if (!is_array($items)) {
    $items = array();
} ?>
<input type="hidden" id="buildpro-option-data" <?php $this->link(); ?>
    value="<?php echo esc_attr(wp_json_encode($items)); ?>">
<div id="buildpro-option-wrapper">
    <?php
    $index = 0;
    foreach ($items as $item) {
        $icon_id = isset($item['icon_id']) ? (int)$item['icon_id'] : 0;
        $text = isset($item['text']) ? sanitize_text_field($item['text']) : '';
        $desc = isset($item['description']) ? sanitize_textarea_field($item['description']) : '';
        $thumb = $icon_id ? wp_get_attachment_image_url($icon_id, 'thumbnail') : '';
    ?>
    <div class="buildpro-option-row" data-index="<?php echo esc_attr($index); ?>">
        <div class="buildpro-option-header">
            <span
                class="buildpro-option-label"><?php echo $text ? esc_html($text) : sprintf(esc_html__('Item %d', 'buildpro'), $index + 1); ?></span>
            <span class="buildpro-option-arrow">&#9660;</span>
        </div>
        <div class="buildpro-option-body" style="display:none">
            <div class="buildpro-option-tabs">
                <button type="button" class="buildpro-option-tab active"
                    data-tab="icon"><?php echo esc_html__('Icon', 'buildpro'); ?></button>
                <button type="button" class="buildpro-option-tab"
                    data-tab="content"><?php echo esc_html__('Content', 'buildpro'); ?></button>
            </div>
            <div class="buildpro-option-grid">
                <div class="buildpro-option-block tab-content" data-tab="icon" style="display:block">
                    <h4><?php echo esc_html__('Icon', 'buildpro'); ?></h4>
                    <div class="buildpro-option-field">
                        <input type="hidden" class="option-icon-id" data-field="icon_id"
                            value="<?php echo esc_attr($icon_id); ?>">
                        <button type="button"
                            class="button select-option-icon"><?php echo esc_html__('Select icon', 'buildpro'); ?></button>
                        <button type="button"
                            class="button remove-option-icon"><?php echo esc_html__('Remove icon', 'buildpro'); ?></button>
                    </div>
                    <div class="option-icon-preview"
                        style="margin-top:8px;min-height:84px;display:flex;align-items:center;justify-content:center;background:#fff;border:1px dashed #ddd;border-radius:6px">
                        <?php echo $thumb ? '<img src="' . esc_url($thumb) . '" style="max-height:80px;">' : '<span style="color:#888">' . esc_html__('No icon selected', 'buildpro') . '</span>'; ?>
                    </div>
                </div>
                <div class="buildpro-option-block tab-content" data-tab="content" style="display:none">
                    <h4><?php echo esc_html__('Content', 'buildpro'); ?></h4>
                    <p class="buildpro-option-field">
                        <label><?php echo esc_html__('Title', 'buildpro'); ?></label>
                        <input type="text" class="regular-text" data-field="text"
                            value="<?php echo esc_attr($text); ?>">
                    </p>
                    <p class="buildpro-option-field">
                        <label><?php echo esc_html__('Description', 'buildpro'); ?></label>
                        <textarea rows="4" class="large-text"
                            data-field="description"><?php echo esc_textarea($desc); ?></textarea>
                    </p>
                </div>
            </div>
            <div class="buildpro-option-actions">
                <button type="button"
                    class="button remove-option-row"><?php echo esc_html__('Remove item', 'buildpro'); ?></button>
            </div>
        </div><!-- /.buildpro-option-body -->
    </div>
    <?php
        $index++;
    }
    ?>
</div>
<button type="button" class="button button-primary"
    id="buildpro-option-add"><?php echo esc_html__('Add Item', 'buildpro'); ?></button>