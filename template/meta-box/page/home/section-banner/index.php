<?php if (!defined('ABSPATH')) {
    exit;
} ?>
<div class="buildpro-banner-block"
    style="margin-bottom:10px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:12px">
    <h4>Home Banner Status</h4>
    <input type="hidden" id="buildpro_banner_enabled" name="buildpro_banner_enabled" value="1">
    <div style="display:flex;gap:8px">
        <button type="button" class="button button-secondary" id="buildpro_banner_disable_btn">Disable Banner</button>
        <button type="button" class="button button-primary" id="buildpro_banner_enable_btn">Enable Banner</button>
        <span id="buildpro_banner_enabled_state" style="align-self:center;color:#374151"></span>
    </div>
</div>
<template id="buildpro-banner-row-template">
    <div class="buildpro-banner-row" data-index="__INDEX__">
        <div class="buildpro-banner-grid">
            <div class="buildpro-banner-block">
                <h4>Banner Image</h4>
                <div class="buildpro-banner-field">
                    <input type="hidden" class="banner-image-id" name="buildpro_banner_items[__INDEX__][image_id]"
                        value="">
                    <button type="button" class="button select-banner-image">Select Image</button>
                    <button type="button" class="button remove-banner-image">Remove Image</button>
                </div>
                <div class="banner-image-preview"
                    style="margin-top:8px;min-height:84px;display:flex;align-items:center;justify-content:center;background:#fff;border:1px dashed #ddd;border-radius:6px">
                    <span style="color:#888">No image selected</span>
                </div>
            </div>
            <div class="buildpro-banner-block">
                <h4>Banner Content</h4>
                <p class="buildpro-banner-field"><label>Type</label><input type="text"
                        name="buildpro_banner_items[__INDEX__][type]" class="regular-text" value=""></p>
                <p class="buildpro-banner-field"><label>Text</label><input type="text"
                        name="buildpro_banner_items[__INDEX__][text]" class="regular-text" value=""></p>
                <p class="buildpro-banner-field"><label>Description</label><textarea
                        name="buildpro_banner_items[__INDEX__][description]" rows="4" class="large-text"></textarea></p>
                <h4>Liên kết</h4>
                <p class="buildpro-banner-field"><label>Link URL</label><input type="url"
                        name="buildpro_banner_items[__INDEX__][link_url]" class="regular-text" value=""
                        placeholder="https://..."> <button type="button" class="button choose-link">Choose Link</button>
                </p>
                <p class="buildpro-banner-field"><label>Link Title</label><input type="text"
                        name="buildpro_banner_items[__INDEX__][link_title]" class="regular-text" value=""
                        placeholder="Text nút"></p>
                <p class="buildpro-banner-field"><label>Link Target</label><select
                        name="buildpro_banner_items[__INDEX__][link_target]">
                        <option value="">Default</option>
                        <option value="_blank">Open in new tab</option>
                    </select></p>
            </div>
        </div>
        <div class="buildpro-banner-actions"><button type="button" class="button remove-banner-row">Remove Item</button>
        </div>
    </div>
</template>
<div id="buildpro-banner-wrapper"></div>
<button type="button" class="button button-primary" id="buildpro-banner-add">Add Item</button>