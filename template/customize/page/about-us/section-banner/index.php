<div class="buildpro-about-facts-repeater">
    <p class="description">
        <?php echo esc_html__('Manage About Us facts (label & value). Use the buttons to add or remove items.', 'buildpro'); ?>
    </p>
    <p class="description">
        <?php echo esc_html__('You can add up to 4 facts" bên trên nút "Add Fact" ', 'buildpro'); ?>
    </p>
    <div class="buildpro-about-facts-list"></div>
    <p><button type="button"
            class="button button-secondary buildpro-about-facts-add"><?php echo esc_html__('Add Fact', 'buildpro'); ?></button>
    </p>
    <input type="hidden" class="buildpro-about-facts-input"
        value="<?php echo esc_attr(wp_json_encode(is_array($items) ? $items : array())); ?>" />
</div>