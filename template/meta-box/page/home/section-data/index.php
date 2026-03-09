<?php if (!defined('ABSPATH')) {
    exit;
} ?>

<div class="buildpro-data-block"
    style="margin-bottom:10px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:12px">
    <h4>Section Data Status</h4>
    <input type="hidden" id="buildpro_data_enabled" name="buildpro_data_enabled"
        value="<?php echo isset($enabled) ? (int)$enabled : 1; ?>">
    <div style="display:flex;gap:8px">
        <button type="button" class="button button-secondary" id="buildpro_data_disable_btn">Remove Section</button>
        <button type="button" class="button button-primary" id="buildpro_data_enable_btn">Add Section</button>
        <span id="buildpro_data_enabled_state" style="align-self:center;color:#374151"></span>
    </div>
</div>

<template id="buildpro-data-row-template">
    <div class="buildpro-data-row" data-index="__INDEX__">
        <div class="buildpro-data-grid">
            <div class="buildpro-data-block">
                <h4>Number</h4>
                <p class="buildpro-data-field"><label>Number</label><input type="text"
                        name="buildpro_data_items[__INDEX__][number]" class="regular-text" value=""></p>
            </div>
            <div class="buildpro-data-block">
                <h4>Text</h4>
                <p class="buildpro-data-field"><label>Text</label><input type="text"
                        name="buildpro_data_items[__INDEX__][text]" class="regular-text" value=""></p>
            </div>
        </div>
        <div class="buildpro-data-actions"><button type="button" class="button remove-data-row">Remove Item</button>
        </div>
    </div>
</template>
<div id="buildpro-data-wrapper"></div>
<button type="button" class="button button-primary" id="buildpro-data-add">Add Item</button>