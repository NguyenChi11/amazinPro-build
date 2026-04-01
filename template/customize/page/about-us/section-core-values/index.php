<div class="buildpro-about-core-values-repeater">
    <div class="buildpro-about-core-values-list"></div>
    <p><button type="button"
            class="button button-secondary buildpro-about-core-values-add"><?php echo esc_html__('Add Item', 'buildpro'); ?></button>
    </p>
    <input type="hidden" class="buildpro-about-core-values-input"
        value="<?php echo esc_attr(wp_json_encode(is_array($items) ? $items : array())); ?>" />
</div>