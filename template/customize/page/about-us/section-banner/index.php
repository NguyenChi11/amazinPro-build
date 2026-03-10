<div class="buildpro-about-facts-repeater">
    <p class="description">Manage About Us facts (label & value). Use the buttons to add or remove items.</p>
    <div class="buildpro-about-facts-list"></div>
    <p><button type="button" class="button button-secondary buildpro-about-facts-add">Add Fact</button></p>
    <input type="hidden" class="buildpro-about-facts-input"
        value="<?php echo esc_attr(wp_json_encode(is_array($items) ? $items : array())); ?>" />
</div>