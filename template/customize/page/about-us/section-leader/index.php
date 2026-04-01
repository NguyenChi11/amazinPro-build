<div class="buildpro-about-leader-repeater">
    <div class="buildpro-about-leader-list"></div>
    <p><button type="button"
            class="button button-secondary buildpro-about-leader-add"><?php echo esc_html__('Add Item', 'buildpro'); ?></button>
    </p>
    <input type="hidden" class="buildpro-about-leader-input"
        value="<?php echo esc_attr(wp_json_encode(is_array($items) ? $items : array())); ?>" <?php $this->link(); ?> />
</div>