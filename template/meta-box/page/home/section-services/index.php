<?php if (!defined('ABSPATH')) {
    exit;
} ?>
<div class="buildpro-services-block"
    style="margin-bottom:10px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:12px">
    <h4>Section Status</h4>
    <input type="hidden" id="buildpro_service_enabled" name="buildpro_service_enabled"
        value="<?php echo isset($service_enabled) ? (int)$service_enabled : 1; ?>">
    <div style="display:flex;gap:8px">
        <button type="button" class="button button-secondary" id="buildpro_service_disable_btn">Remove Section</button>
        <button type="button" class="button button-primary" id="buildpro_service_enable_btn">Add Section</button>
        <span id="buildpro_service_enabled_state" style="align-self:center;color:#374151"></span>
    </div>
</div>
<div class="buildpro-services-block">
    <p class="buildpro-services-field">
        <label>Section Title</label>
        <input type="text" name="buildpro_service_title" class="regular-text"
            value="<?php echo esc_attr($service_title); ?>" placeholder="CORE SERVICES">
    </p>
    <p class="buildpro-services-field">
        <label>Section Description</label>
        <textarea name="buildpro_service_desc" rows="4" class="large-text"
            placeholder="Comprehensive construction solutions"><?php echo esc_textarea($service_desc); ?></textarea>
    </p>
</div>
<div id="buildpro-service-wrapper">
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
        <div class="buildpro-service-row" data-index="<?php echo esc_attr($index); ?>">
            <div class="buildpro-service-grid">
                <div class="buildpro-service-block">
                    <h4>Icon</h4>
                    <div class="buildpro-service-field">
                        <input type="hidden" class="service-icon-id"
                            name="buildpro_service_items[<?php echo esc_attr($index); ?>][icon_id]"
                            value="<?php echo esc_attr($icon_id); ?>">
                        <button type="button" class="button select-service-icon">Select Icon</button>
                        <button type="button" class="button remove-service-icon">Remove Icon</button>
                    </div>
                    <div class="service-icon-preview">
                        <?php echo $thumb ? '<img src="' . esc_url($thumb) . '" style="max-height:80px;">' : '<span style="color:#888">No Icon Selected</span>'; ?>
                    </div>
                </div>
                <div class="buildpro-service-block">
                    <h4>Content</h4>
                    <p class="buildpro-service-field">
                        <label>Title</label>
                        <input type="text" name="buildpro_service_items[<?php echo esc_attr($index); ?>][title]"
                            class="regular-text" value="<?php echo esc_attr($title); ?>">
                    </p>
                    <p class="buildpro-service-field">
                        <label>Description</label>
                        <textarea name="buildpro_service_items[<?php echo esc_attr($index); ?>][description]" rows="4"
                            class="large-text"><?php echo esc_textarea($desc); ?></textarea>
                    </p>
                    <h4>Link</h4>
                    <p class="buildpro-service-field">
                        <label>Link URL</label>
                        <input type="url" name="buildpro_service_items[<?php echo esc_attr($index); ?>][link_url]"
                            class="regular-text" value="<?php echo esc_attr($link_url); ?>" placeholder="https://...">
                        <button type="button" class="button choose-link">Choose Link</button>
                    </p>
                    <p class="buildpro-service-field">
                        <label>Link Title</label>
                        <input type="text" name="buildpro_service_items[<?php echo esc_attr($index); ?>][link_title]"
                            class="regular-text" value="<?php echo esc_attr($link_title); ?>" placeholder="View Details">
                    </p>
                    <p class="buildpro-service-field">
                        <label>Link Target</label>
                        <select name="buildpro_service_items[<?php echo esc_attr($index); ?>][link_target]">
                            <option value="" <?php selected($link_target, '', true); ?>>Same Tab</option>
                            <option value="_blank" <?php selected($link_target, '_blank', true); ?>>Open in New Tab</option>
                        </select>
                    </p>
                </div>
            </div>
            <div class="buildpro-service-actions">
                <button type="button" class="button remove-service-row">Remove Item</button>
            </div>
        </div>
    <?php
        $index++;
    }
    ?>
</div>
<button type="button" class="button button-primary" id="buildpro-service-add">Add Item</button>
<template id="buildpro-service-row-template">
    <div class="buildpro-service-row">
        <div class="buildpro-service-grid">
            <div class="buildpro-service-block">
                <h4>Icon</h4>
                <div class="buildpro-service-field">
                    <input type="hidden" class="service-icon-id" data-name="icon_id" value="">
                    <button type="button" class="button select-service-icon">Select Icon</button>
                    <button type="button" class="button remove-service-icon">Remove Icon</button>
                </div>
                <div class="service-icon-preview"><span style="color:#888">No Icon Selected</span></div>
            </div>
            <div class="buildpro-service-block">
                <h4>Nội dung</h4>
                <p class="buildpro-service-field"><label>Title</label><input type="text" class="regular-text"
                        data-name="title" value=""></p>
                <p class="buildpro-service-field"><label>Description</label><textarea rows="4" class="large-text"
                        data-name="description"></textarea></p>
                <h4>Liên kết</h4>
                <p class="buildpro-service-field"><label>Link URL</label><input type="url" class="regular-text"
                        data-name="link_url" value="" placeholder="https://..."> <button type="button"
                        class="button choose-link">Choose Link</button></p>
                <p class="buildpro-service-field"><label>Link Title</label><input type="text" class="regular-text"
                        data-name="link_title" value=""></p>
                <p class="buildpro-service-field"><label>Link Target</label><select data-name="link_target">
                        <option value="">Same Tab</option>
                        <option value="_blank">Open in New Tab</option>
                    </select></p>
            </div>
        </div>
        <div class="buildpro-service-actions"><button type="button" class="button remove-service-row">Remove Item</button>
        </div>
    </div>
</template>