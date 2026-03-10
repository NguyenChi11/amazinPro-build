<div class="buildpro-about-leader-repeater">
    <p class="description">Manage Leader items (image, name, position, description, url).</p>
    <div class="buildpro-about-leader-list"></div>
    <p><button type="button" class="button button-secondary buildpro-about-leader-add">Add Item</button></p>
    <input type="hidden" class="buildpro-about-leader-input"
        value="<?php echo esc_attr(wp_json_encode(is_array($items) ? $items : array())); ?>" <?php $this->link(); ?> />
</div>