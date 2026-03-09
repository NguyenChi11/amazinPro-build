<?php if (!is_array($items)) {
    $items = array();
} ?>
<input type="hidden" id="buildpro-data-data" <?php $this->link(); ?>
    value="<?php echo esc_attr(wp_json_encode($items)); ?>">
<div id="buildpro-data-wrapper">
    <?php
    $index = 0;
    foreach ($items as $item) {
        $number = isset($item['number']) ? sanitize_text_field($item['number']) : '';
        $text = isset($item['text']) ? sanitize_text_field($item['text']) : '';
    ?>
    <div class="buildpro-data-row" data-index="<?php echo esc_attr($index); ?>">
        <div class="buildpro-data-header">
            <span class="buildpro-data-label"><?php echo $text ? esc_html($text) : 'Item ' . ($index + 1); ?></span>
            <span class="buildpro-data-arrow">&#9660;</span>
        </div>
        <div class="buildpro-data-body" style="display:none">
            <div class="buildpro-data-grid">
                <div class="buildpro-data-block">
                    <h4>Number</h4>
                    <p class="buildpro-data-field">
                        <label>Number</label>
                        <input type="text" class="regular-text" data-field="number"
                            value="<?php echo esc_attr($number); ?>">
                    </p>
                </div>
                <div class="buildpro-data-block">
                    <h4>Text</h4>
                    <p class="buildpro-data-field">
                        <label>Text</label>
                        <input type="text" class="regular-text" data-field="text"
                            value="<?php echo esc_attr($text); ?>">
                    </p>
                </div>
            </div>
            <div class="buildpro-data-actions">
                <button type="button" class="button remove-data-row">Delete item</button>
            </div>
        </div><!-- /.buildpro-data-body -->
    </div>
    <?php
        $index++;
    }
    ?>
</div>
<button type="button" class="button button-primary" id="buildpro-data-add">Add item</button>