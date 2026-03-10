<div class="buildpro-about-core-values-repeater">
    <p class="description">Manage Core Values items (icon, title, description, url).</p>
    <div class="buildpro-about-core-values-list"></div>
    <p><button type="button" class="button button-secondary buildpro-about-core-values-add">Add Item</button></p>
    <input type="hidden" class="buildpro-about-core-values-input"
        value="<?php echo esc_attr(wp_json_encode(is_array($items) ? $items : array())); ?>" />
</div>