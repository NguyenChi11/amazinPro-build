<div class="buildpro-about-policy-repeater"
    data-type="<?php echo strpos($this->id, 'certifications') !== false ? 'certs' : 'items'; ?>">
    <!-- <p class="description">
        <?php
        if (strpos($this->id, 'certifications') !== false) {
            echo esc_html__('Manage Certifications (image/url/title/desc).', 'buildpro');
        } else {
            echo esc_html__('Manage Right Items (icon/title/desc).', 'buildpro');
        }
        ?>
    </p> -->
    <div class="buildpro-about-policy-list"></div>
    <p><button type="button" class="button button-secondary buildpro-about-policy-add"><?php echo esc_html__('Add Item', 'buildpro'); ?></button></p>
    <input type="hidden" class="buildpro-about-policy-input"
        value="<?php echo esc_attr(wp_json_encode(is_array($items) ? $items : array())); ?>" <?php $this->link(); ?> />
</div>