<?php
if (!is_array($items ?? null)) {
    $items = array();
}
$title = isset($data['title']) ? sanitize_text_field($data['title']) : '';
$text  = isset($data['text']) ? sanitize_text_field($data['text']) : '';
$desc  = isset($data['desc']) ? sanitize_textarea_field($data['desc']) : '';
$rows  = isset($data['items']) && is_array($data['items']) ? $data['items'] : $items;
?>
<input type="hidden" id="buildpro-evaluate-data" <?php $this->link(); ?>
    value="<?php echo esc_attr(wp_json_encode(array('title' => $title, 'text' => $text, 'desc' => $desc, 'items' => $rows))); ?>">
<div id="buildpro-evaluate-wrapper">
    <div class="buildpro-evaluate-block">
        <h4>Evaluate Title</h4>
        <p class="buildpro-evaluate-field">
            <label>Title</label>
            <input type="text" class="regular-text" data-field="title" value="<?php echo esc_attr($title); ?>">
        </p>
        <p class="buildpro-evaluate-field">
            <label>Text</label>
            <input type="text" class="regular-text" data-field="text" value="<?php echo esc_attr($text); ?>">
        </p>
        <p class="buildpro-evaluate-field">
            <label>Description</label>
            <textarea rows="4" class="large-text" data-field="desc"><?php echo esc_textarea($desc); ?></textarea>
        </p>
    </div>
    <div class="buildpro-evaluate-block" id="buildpro-evaluate-items">
        <?php
        $index = 0;
        foreach ($rows as $item) {
            $name = isset($item['name']) ? sanitize_text_field($item['name']) : '';
            $position = isset($item['position']) ? sanitize_text_field($item['position']) : '';
            $description = isset($item['description']) ? sanitize_textarea_field($item['description']) : '';
            $avatar_id = isset($item['avatar_id']) ? (int)$item['avatar_id'] : 0;
            $thumb = $avatar_id ? wp_get_attachment_image_url($avatar_id, 'thumbnail') : '';
        ?>
            <div class="buildpro-evaluate-row" data-index="<?php echo esc_attr($index); ?>">
                <div class="buildpro-evaluate-row-header">
                    <span class="buildpro-evaluate-row-label"><?php echo $name ? esc_html($name) : 'Item ' . ($index + 1); ?></span>
                    <span class="buildpro-evaluate-row-arrow">&#9660;</span>
                </div>
                <div class="buildpro-evaluate-row-body" style="display:none">
                    <div class="buildpro-evaluate-grid">
                        <div class="buildpro-evaluate-col">
                            <p class="buildpro-evaluate-field">
                                <label>Avatar</label>
                                <input type="hidden" class="evaluate-avatar-id" value="<?php echo esc_attr($avatar_id); ?>">
                                <button type="button" class="button evaluate-select-avatar">Chọn ảnh</button>
                                <button type="button" class="button evaluate-remove-avatar">Xóa ảnh</button>
                            </p>
                            <div class="evaluate-avatar-preview">
                                <?php echo $thumb ? '<img src="' . esc_url($thumb) . '" style="max-height:112px">' : '<span style="color:#888">No photo selected yet</span>'; ?>
                            </div>
                        </div>
                        <div class="buildpro-evaluate-col">
                            <p class="buildpro-evaluate-field"><label>Name</label><input type="text" class="regular-text"
                                    data-item="name" value="<?php echo esc_attr($name); ?>" placeholder="Name"></p>
                            <p class="buildpro-evaluate-field"><label>Position</label><input type="text" class="regular-text"
                                    data-item="position" value="<?php echo esc_attr($position); ?>" placeholder="Position"></p>
                            <p class="buildpro-evaluate-field"><label>Description</label><textarea rows="4" class="large-text"
                                    data-item="description"
                                    placeholder="Description"><?php echo esc_textarea($description); ?></textarea></p>
                        </div>
                    </div>
                    <div class="buildpro-evaluate-actions"><button type="button" class="button evaluate-remove-row">Xóa</button>
                    </div>
                </div><!-- /.buildpro-evaluate-row-body -->
            </div>
        <?php
            $index++;
        }
        ?>
        <button type="button" class="button" id="buildpro-evaluate-add">Add a row</button>
    </div>
    <div class="buildpro-evaluate-actions"><button type="button" class="button button-primary"
            id="buildpro-evaluate-apply">Apply</button></div>
    <p class="description">Changes are previewed instantly. Click Publish to save.</p>
</div>