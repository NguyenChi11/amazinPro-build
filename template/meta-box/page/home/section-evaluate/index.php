<?php if (!defined('ABSPATH')) {
    exit;
} ?>
<div class="buildpro-evaluate-block"
    style="margin-bottom:10px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:12px">
    <h4><?php echo esc_html__('Evaluate Status', 'buildpro'); ?></h4>
    <input type="hidden" id="buildpro_evaluate_enabled" name="buildpro_evaluate_enabled" value="1">
    <div style="display:flex;gap:8px">
        <button type="button" class="button button-secondary" id="buildpro_evaluate_disable_btn"><?php echo esc_html__('Disable Section', 'buildpro'); ?></button>
        <button type="button" class="button button-primary" id="buildpro_evaluate_enable_btn"><?php echo esc_html__('Enable Section', 'buildpro'); ?></button>
        <span id="buildpro_evaluate_enabled_state" style="align-self:center;color:#374151"></span>
    </div>
</div>
<div id="buildpro_evaluate_meta" class="buildpro-evaluate-block">
    <p class="buildpro-evaluate-field"><label><?php echo esc_html__('Title', 'buildpro'); ?></label><input type="text" name="buildpro_evaluate_title"
            class="regular-text" value="<?php echo esc_attr($title); ?>" placeholder="<?php echo esc_attr__('Title', 'buildpro'); ?>"></p>
    <p class="buildpro-evaluate-field"><label><?php echo esc_html__('Text', 'buildpro'); ?></label><input type="text" name="buildpro_evaluate_text"
            class="regular-text" value="<?php echo esc_attr($text); ?>" placeholder="<?php echo esc_attr__('Text', 'buildpro'); ?>"></p>
    <p class="buildpro-evaluate-field"><label><?php echo esc_html__('Description', 'buildpro'); ?></label><textarea name="buildpro_evaluate_desc" rows="4"
            class="large-text" placeholder="<?php echo esc_attr__('Description', 'buildpro'); ?>"><?php echo esc_textarea($desc); ?></textarea></p>
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
                        <label><?php echo esc_html__('Avatar', 'buildpro'); ?></label>
                        <input type="hidden" class="evaluate-avatar-id"
                            name="buildpro_evaluate_items[<?php echo esc_attr($index); ?>][avatar_id]"
                            value="<?php echo esc_attr($avatar_id); ?>">
                        <button type="button" class="button evaluate-select-avatar"><?php echo esc_html__('Select photo', 'buildpro'); ?></button>
                        <button type="button" class="button evaluate-remove-avatar"><?php echo esc_html__('Remove', 'buildpro'); ?></button>
                    </p>
                    <div class="evaluate-avatar-preview">
                        <?php echo $thumb ? '<img src="' . esc_url($thumb) . '" style="max-height:112px">' : '<span style="color:#888">' . esc_html__('No photo selected yet', 'buildpro') . '</span>'; ?>
                    </div>
                </div>
                <div class="buildpro-evaluate-col">
                    <p class="buildpro-evaluate-field"><label><?php echo esc_html__('Name', 'buildpro'); ?></label><input type="text"
                            name="buildpro_evaluate_items[<?php echo esc_attr($index); ?>][name]" class="regular-text"
                            value="<?php echo esc_attr($name); ?>" placeholder="<?php echo esc_attr__('Name', 'buildpro'); ?>"></p>
                    <p class="buildpro-evaluate-field"><label><?php echo esc_html__('Position', 'buildpro'); ?></label><input type="text"
                            name="buildpro_evaluate_items[<?php echo esc_attr($index); ?>][position]" class="regular-text"
                            value="<?php echo esc_attr($position); ?>" placeholder="<?php echo esc_attr__('Position', 'buildpro'); ?>"></p>
                    <p class="buildpro-evaluate-field"><label><?php echo esc_html__('Description', 'buildpro'); ?></label><textarea
                            name="buildpro_evaluate_items[<?php echo esc_attr($index); ?>][description]" rows="4"
                            class="large-text"
                            placeholder="<?php echo esc_attr__('Description', 'buildpro'); ?>"><?php echo esc_textarea($description); ?></textarea></p>
                </div>
            </div>
            <div class="buildpro-evaluate-actions"><button type="button" class="button evaluate-remove-row"><?php echo esc_html__('Remove', 'buildpro'); ?></button>
            </div>
        </div>
    <?php
        $index++;
    }
    ?>
    <button type="button" class="button button-primary" id="buildpro_evaluate_add_row"><?php echo esc_html__('Add row', 'buildpro'); ?></button>
</div>