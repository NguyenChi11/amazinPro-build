<?php

/**
 * Customizer control templates for footer dynamic controls.
 * Required variables:
 *   $buildpro_control_type — 'footer-list-pages' | 'footer-contact-links'
 *   $items       — array of items
 *   $label       — control label
 *   $description — control description
 *   $link_attr   — output of $this->get_link()
 */

if (isset($buildpro_control_type) && $buildpro_control_type === 'footer-list-pages') :
?>
    <span class="customize-control-title"><?= esc_html($label) ?></span>
    <?php if (!empty($description)) : ?>
        <p class="description"><?= esc_html($description) ?></p>
    <?php endif; ?>
    <input type="hidden" class="footer-list-pages-json" <?= $link_attr ?> value="<?= esc_attr(wp_json_encode($items)) ?>">
    <div id="customizer-footer-list-pages-wrapper">
        <?php $index = 0;
        foreach ($items as $lp) :
            $lp_url    = isset($lp['url'])    ? esc_url($lp['url'])                      : '';
            $lp_title  = isset($lp['title'])  ? sanitize_text_field($lp['title'])         : '';
            $lp_target = isset($lp['target']) ? sanitize_text_field($lp['target'])        : '';
        ?>
            <div class="buildpro-block" data-index="<?= esc_attr($index) ?>">
                <p class="buildpro-field">
                    <label>Link URL</label>
                    <input type="url" class="regular-text" data-field="url" value="<?= esc_attr($lp_url) ?>" placeholder="https://...">
                    <button type="button" class="button choose-link">Choose link</button>
                </p>
                <p class="buildpro-field">
                    <label>Link Title</label>
                    <input type="text" class="regular-text" data-field="title" value="<?= esc_attr($lp_title) ?>">
                </p>
                <p class="buildpro-field">
                    <label>Link Target</label>
                    <select data-field="target">
                        <option value="" <?= selected($lp_target, '', false) ?>>Same Tab</option>
                        <option value="_blank" <?= selected($lp_target, '_blank', false) ?>>Open New Tab</option>
                    </select>
                </p>
                <div class="buildpro-actions">
                    <button type="button" class="button remove-row">Remove Item</button>
                </div>
            </div>
        <?php $index++;
        endforeach; ?>
    </div>
    <button type="button" class="button button-primary" id="customizer-footer-list-pages-add">Add item</button>

<?php elseif (isset($buildpro_control_type) && $buildpro_control_type === 'footer-contact-links') : ?>
    <span class="customize-control-title"><?= esc_html($label) ?></span>
    <?php if (!empty($description)) : ?>
        <p class="description"><?= esc_html($description) ?></p>
    <?php endif; ?>
    <input type="hidden" class="footer-contact-links-json" <?= $link_attr ?> value="<?= esc_attr(wp_json_encode($items)) ?>">
    <div id="customizer-footer-contact-links-wrapper">
        <?php $index = 0;
        foreach ($items as $cl) :
            $cl_icon_id = isset($cl['icon_id']) ? absint($cl['icon_id'])                  : 0;
            $cl_thumb   = $cl_icon_id ? wp_get_attachment_image_url($cl_icon_id, 'thumbnail') : '';
            $cl_url     = isset($cl['url'])    ? esc_url($cl['url'])                      : '';
            $cl_title   = isset($cl['title'])  ? sanitize_text_field($cl['title'])         : '';
            $cl_target  = isset($cl['target']) ? sanitize_text_field($cl['target'])        : '';
        ?>
            <div class="buildpro-block" data-index="<?= esc_attr($index) ?>">
                <p class="buildpro-field">
                    <label>Icon</label>
                    <input type="hidden" class="regular-text" data-field="icon_id" value="<?= esc_attr($cl_icon_id) ?>">
                    <button type="button" class="button select-contact-icon">Selected photo</button>
                    <button type="button" class="button remove-contact-icon">Remove photo</button>
                </p>
                <div class="image-preview contact-icon-preview">
                    <?= $cl_thumb ? '<img src="' . esc_url($cl_thumb) . '" style="max-height:80px;">' : '<span style="color:#888">No Image Selected</span>' ?>
                </div>
                <p class="buildpro-field">
                    <label>Link URL</label>
                    <input type="url" class="regular-text" data-field="url" value="<?= esc_attr($cl_url) ?>" placeholder="https://...">
                    <button type="button" class="button choose-link">Choose link</button>
                </p>
                <p class="buildpro-field">
                    <label>Link Title</label>
                    <input type="text" class="regular-text" data-field="title" value="<?= esc_attr($cl_title) ?>">
                </p>
                <p class="buildpro-field">
                    <label>Link Target</label>
                    <select data-field="target">
                        <option value="" <?= selected($cl_target, '', false) ?>>Same Tab</option>
                        <option value="_blank" <?= selected($cl_target, '_blank', false) ?>>Open New Tab</option>
                    </select>
                </p>
                <div class="buildpro-actions">
                    <button type="button" class="button remove-row">Remove Item</button>
                </div>
            </div>
        <?php $index++;
        endforeach; ?>
    </div>
    <button type="button" class="button button-primary" id="customizer-footer-contact-links-add">Add item</button>

<?php endif; ?>