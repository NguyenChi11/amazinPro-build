<?php
$items = is_array($items) ? $items : array();
?>
<input type="hidden" id="buildpro-banner-data" <?php $this->link(); ?>
    value="<?php echo esc_attr(wp_json_encode($items)); ?>">
<div id="buildpro-banner-wrapper">
    <?php
    $index = 0;
    foreach ($items as $item) {
        $image_id   = isset($item['image_id']) ? (int) $item['image_id'] : 0;
        $type       = isset($item['type']) ? sanitize_text_field($item['type']) : '';
        $text       = isset($item['text']) ? sanitize_text_field($item['text']) : '';
        $desc       = isset($item['description']) ? sanitize_textarea_field($item['description']) : '';
        $link_url   = isset($item['link_url']) ? esc_url_raw($item['link_url']) : '';
        $link_title = isset($item['link_title']) ? sanitize_text_field($item['link_title']) : '';
        $link_target = isset($item['link_target']) ? sanitize_text_field($item['link_target']) : '';
        $thumb = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';
    ?>
        <div class="buildpro-banner-row" data-index="<?php echo esc_attr($index); ?>">
            <div class="buildpro-banner-header">
                <span
                    class="buildpro-banner-label"><?php echo $text ? esc_html($text) : sprintf(esc_html__('Item %d', 'buildpro'), $index + 1); ?></span>
                <span class="buildpro-banner-arrow">&#9660;</span>
            </div>
            <div class="buildpro-banner-body" style="display:none">
                <div class="buildpro-banner-grid">
                    <div class="buildpro-banner-block">
                        <h4><?php echo esc_html__('Image', 'buildpro'); ?></h4>
                        <div class="buildpro-banner-field">
                            <input type="hidden" class="banner-image-id" data-field="image_id"
                                value="<?php echo esc_attr($image_id); ?>">
                            <button type="button"
                                class="button select-banner-image"><?php echo esc_html__('Select photo', 'buildpro'); ?></button>
                            <button type="button"
                                class="button remove-banner-image"><?php echo esc_html__('Remove photo', 'buildpro'); ?></button>
                        </div>
                        <div class="banner-image-preview">
                            <?php echo $thumb ? '<img src="' . esc_url($thumb) . '" style="max-height:80px;">' : '<span style="color:#888">' . esc_html__('No image selected', 'buildpro') . '</span>'; ?>
                        </div>
                    </div>
                    <div class="buildpro-banner-block">
                        <h4><?php echo esc_html__('Content', 'buildpro'); ?></h4>
                        <p class="buildpro-banner-field"><label><?php echo esc_html__('Type', 'buildpro'); ?></label><input
                                type="text" class="regular-text" data-field="type" value="<?php echo esc_attr($type); ?>">
                        </p>
                        <p class="buildpro-banner-field"><label><?php echo esc_html__('Text', 'buildpro'); ?></label><input
                                type="text" class="regular-text" data-field="text" value="<?php echo esc_attr($text); ?>">
                        </p>
                        <p class="buildpro-banner-field">
                            <label><?php echo esc_html__('Description', 'buildpro'); ?></label><textarea rows="4"
                                class="large-text" data-field="description"><?php echo esc_textarea($desc); ?></textarea>
                        </p>
                        <h4><?php echo esc_html__('Link', 'buildpro'); ?></h4>
                        <p class="buildpro-banner-field">
                            <label><?php echo esc_html__('Button Link', 'buildpro'); ?></label>
                            <input type="url" class="regular-text" data-field="link_url"
                                value="<?php echo esc_attr($link_url); ?>" placeholder="https://...">
                            <button type="button"
                                class="button choose-link"><?php echo esc_html__('Choose Link', 'buildpro'); ?></button>
                        </p>
                        <p class="buildpro-banner-field">
                            <label><?php echo esc_html__('Button Label', 'buildpro'); ?></label>
                            <input type="text" class="regular-text" data-field="link_title"
                                value="<?php echo esc_attr($link_title); ?>"
                                placeholder="<?php echo esc_attr__('Button text', 'buildpro'); ?>">
                        </p>
                        <p class="buildpro-banner-field">
                            <label><?php echo esc_html__('Link Target', 'buildpro'); ?></label>
                        <div class="checkbox-label">
                            <input type="checkbox" data-field="link_target" value="_blank"
                                <?php checked($link_target, '_blank'); ?>>
                            <?php echo esc_html__('Open in new tab', 'buildpro'); ?>
                        </div>
                        </p>
                        <div class="buildpro-link-panel">
                            <div class="buildpro-banner-block">
                                <h4><?php echo esc_html__('Choose Link', 'buildpro'); ?></h4>
                                <p class="buildpro-banner-field">
                                    <label><?php echo esc_html__('Search pages/posts', 'buildpro'); ?></label><input
                                        type="text" class="regular-text buildpro-link-search"
                                        placeholder="<?php echo esc_attr__('Enter keyword...', 'buildpro'); ?>">
                                </p>
                                <div class="buildpro-link-results">
                                </div>
                                <p class="buildpro-banner-field"><label><input type="checkbox" class="buildpro-link-target">
                                        <?php echo esc_html__('Open in new tab (_blank)', 'buildpro'); ?></label></p>
                                <div class="buildpro-banner-actions"><button type="button"
                                        class="button buildpro-link-close"><?php echo esc_html__('Close', 'buildpro'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="buildpro-banner-actions"><button type="button"
                        class="button remove-banner-row"><?php echo esc_html__('Remove item', 'buildpro'); ?></button>
                </div>
            </div><!-- /.buildpro-banner-body -->
        </div>
    <?php
        $index++;
    }
    ?>
</div><button type="button" class="button button-primary"
    id="buildpro-banner-add"><?php echo esc_html__('Add Item', 'buildpro'); ?></button>

<template id="buildpro-banner-template">
    <div class="buildpro-banner-row" data-index="">
        <div class="buildpro-banner-header">
            <span class="buildpro-banner-label">Item</span>
            <span class="buildpro-banner-arrow">&#9660;</span>
        </div>
        <div class="buildpro-banner-body" style="display:block">
            <div class="buildpro-banner-grid">
                <div class="buildpro-banner-block">
                    <h4><?php echo esc_html__('Image', 'buildpro'); ?></h4>
                    <div class="buildpro-banner-field">
                        <input type="hidden" class="banner-image-id" data-field="image_id" value="">
                        <button type="button"
                            class="button select-banner-image"><?php echo esc_html__('Select photo', 'buildpro'); ?></button>
                        <button type="button"
                            class="button remove-banner-image"><?php echo esc_html__('Remove photo', 'buildpro'); ?></button>
                    </div>
                    <div class="banner-image-preview"
                        style="margin-top:8px;min-height:84px;display:flex;align-items:center;justify-content:center;background:#fff;border:1px dashed #ddd;border-radius:6px">
                        <span style="color:#888"><?php echo esc_html__('No image selected', 'buildpro'); ?></span>
                    </div>
                </div>
                <div class="buildpro-banner-block">
                    <h4><?php echo esc_html__('Content', 'buildpro'); ?></h4>
                    <p class="buildpro-banner-field"><label><?php echo esc_html__('Type', 'buildpro'); ?></label><input
                            type="text" class="regular-text" data-field="type" value=""></p>
                    <p class="buildpro-banner-field"><label><?php echo esc_html__('Text', 'buildpro'); ?></label><input
                            type="text" class="regular-text" data-field="text" value=""></p>
                    <p class="buildpro-banner-field">
                        <label><?php echo esc_html__('Description', 'buildpro'); ?></label><textarea rows="4"
                            class="large-text" data-field="description"></textarea>
                    </p>
                    <h4><?php echo esc_html__('Link', 'buildpro'); ?></h4>
                    <p class="buildpro-banner-field">
                        <label><?php echo esc_html__('Button Link', 'buildpro'); ?></label><input type="url"
                            class="regular-text" data-field="link_url" value="" placeholder="https://..."> <button
                            type="button"
                            class="button choose-link"><?php echo esc_html__('Choose Link', 'buildpro'); ?></button>
                    </p>
                    <p class="buildpro-banner-field">
                        <label><?php echo esc_html__('Button Label', 'buildpro'); ?></label><input type="text"
                            class="regular-text" data-field="link_title" value=""
                            placeholder="<?php echo esc_attr__('Button text', 'buildpro'); ?>">
                    </p>
                    <p class="buildpro-banner-field">
                        <label><?php echo esc_html__('Link Target', 'buildpro'); ?></label>
                    <div class="checkbox-label">
                        <input type="checkbox" data-field="link_target" value="_blank">
                        <?php echo esc_html__('Open in new tab', 'buildpro'); ?>
                    </div>
                    </p>
                    <div class="buildpro-link-panel" style="display:none;margin-top:8px">
                        <div class="buildpro-banner-block" style="background:#fff">
                            <h4><?php echo esc_html__('Choose Link', 'buildpro'); ?></h4>
                            <p class="buildpro-banner-field">
                                <label><?php echo esc_html__('Search pages/posts', 'buildpro'); ?></label><input
                                    type="text" class="regular-text buildpro-link-search"
                                    placeholder="<?php echo esc_attr__('Enter keyword...', 'buildpro'); ?>">
                            </p>
                            <div class="buildpro-link-results"
                                style="max-height:190px;overflow:auto;border:1px solid #eee;border-radius:6px;background:#fafafa;padding:6px">
                            </div>
                            <p class="buildpro-banner-field"><label><input type="checkbox" class="buildpro-link-target">
                                    <?php echo esc_html__('Open in new tab (_blank)', 'buildpro'); ?></label></p>
                            <div class="buildpro-banner-actions"><button type="button"
                                    class="button buildpro-link-close"><?php echo esc_html__('Close', 'buildpro'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="buildpro-banner-actions"><button type="button"
                    class="button remove-banner-row"><?php echo esc_html__('Remove item', 'buildpro'); ?></button></div>
        </div>
    </div>
</template>