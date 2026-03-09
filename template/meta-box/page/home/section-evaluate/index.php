<?php if (!defined('ABSPATH')) {
    exit;
} ?>
<div class="buildpro-evaluate-block"
    style="margin-bottom:10px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:12px">
    <h4>Evaluate Status</h4>
    <input type="hidden" id="buildpro_evaluate_enabled" name="buildpro_evaluate_enabled" value="1">
    <div style="display:flex;gap:8px">
        <button type="button" class="button button-secondary" id="buildpro_evaluate_disable_btn">Disable
            Evaluate</button>
        <button type="button" class="button button-primary" id="buildpro_evaluate_enable_btn">Enable Evaluate</button>
        <span id="buildpro_evaluate_enabled_state" style="align-self:center;color:#374151"></span>
    </div>
</div>
<div id="buildpro_evaluate_meta" class="buildpro-evaluate-block">
    <p class="buildpro-evaluate-field"><label>Title</label><input type="text" name="buildpro_evaluate_title"
            class="regular-text" value="<?php echo esc_attr($title); ?>" placeholder="Title"></p>
    <p class="buildpro-evaluate-field"><label>Text</label><input type="text" name="buildpro_evaluate_text"
            class="regular-text" value="<?php echo esc_attr($text); ?>" placeholder="Text"></p>
    <p class="buildpro-evaluate-field"><label>Description</label><textarea name="buildpro_evaluate_desc" rows="4"
            class="large-text" placeholder="Description"><?php echo esc_textarea($desc); ?></textarea></p>
</div>
<div id="buildpro_evaluate_items_wrap" class="buildpro-evaluate-block">
    <?php
    $index = 0;
    foreach ($items as $item) {
        $name = isset($item['name']) ? sanitize_text_field($item['name']) : '';
        $position = isset($item['position']) ? sanitize_text_field($item['position']) : '';
        $description = isset($item['description']) ? sanitize_textarea_field($item['description']) : '';
        $avatar_id = isset($item['avatar_id']) ? (int)$item['avatar_id'] : 0;
        $thumb = $avatar_id ? wp_get_attachment_image_url($avatar_id, 'thumbnail') : '';
    ?>
        <div class="buildpro-evaluate-row" data-index="<?php echo esc_attr($index); ?>">
            <div class="buildpro-evaluate-grid">
                <div class="buildpro-evaluate-col">
                    <p class="buildpro-evaluate-field">
                        <label>Avatar</label>
                        <input type="hidden" class="evaluate-avatar-id"
                            name="buildpro_evaluate_items[<?php echo esc_attr($index); ?>][avatar_id]"
                            value="<?php echo esc_attr($avatar_id); ?>">
                        <button type="button" class="button evaluate-select-avatar">Select Avatar</button>
                        <button type="button" class="button evaluate-remove-avatar">Remove Avatar</button>
                    </p>
                    <div class="evaluate-avatar-preview">
                        <?php echo $thumb ? '<img src="' . esc_url($thumb) . '" style="max-height:112px">' : '<span style="color:#888">No photo selected yet</span>'; ?>
                    </div>
                </div>
                <div class="buildpro-evaluate-col">
                    <p class="buildpro-evaluate-field"><label>Name</label><input type="text"
                            name="buildpro_evaluate_items[<?php echo esc_attr($index); ?>][name]" class="regular-text"
                            value="<?php echo esc_attr($name); ?>" placeholder="Name"></p>
                    <p class="buildpro-evaluate-field"><label>Position</label><input type="text"
                            name="buildpro_evaluate_items[<?php echo esc_attr($index); ?>][position]" class="regular-text"
                            value="<?php echo esc_attr($position); ?>" placeholder="Position"></p>
                    <p class="buildpro-evaluate-field"><label>Description</label><textarea
                            name="buildpro_evaluate_items[<?php echo esc_attr($index); ?>][description]" rows="4"
                            class="large-text"
                            placeholder="Description"><?php echo esc_textarea($description); ?></textarea></p>
                </div>
            </div>
            <div class="buildpro-evaluate-actions"><button type="button" class="button evaluate-remove-row">Xóa</button>
            </div>
        </div>
    <?php
        $index++;
    }
    ?>
    <button type="button" class="button button-primary" id="buildpro_evaluate_add_row">Add a row</button>
</div>