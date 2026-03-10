<?php if (!defined('ABSPATH')) {
    exit;
} ?>

<div id="buildpro_about_leader_meta">
    <div class="buildpro-admin-tabs">
        <button type="button" class="button buildpro-about-leader-tabs is-active"
            data-tab="buildpro_about_leader_tab_content">Content</button>
        <button type="button" class="button buildpro-about-leader-tabs"
            data-tab="buildpro_about_leader_tab_items">Item</button>
    </div>
    <div id="buildpro_about_leader_tab_content">
        <p><label><input type="checkbox" name="buildpro_about_leader_enabled" value="1"
                    <?php checked($enabled, 1); ?>>Enable Leader</label></p>
        <p><label>Title<br><input type="text" class="widefat" name="buildpro_about_leader_title"
                    value="<?php echo esc_attr($title); ?>"></label></p>
        <p><label>Text<br><input type="text" class="widefat" name="buildpro_about_leader_text"
                    value="<?php echo esc_attr($text); ?>"></label></p>
        <p><label>Core Executives<br><input type="text" class="widefat" name="buildpro_about_leader_executives"
                    value="<?php echo esc_attr($executives); ?>"></label></p>
        <p><label>Total Workforce<br><input type="text" class="widefat" name="buildpro_about_leader_workforce"
                    value="<?php echo esc_attr($workforce); ?>"></label></p>
    </div>
    <div id="buildpro_about_leader_tab_items" style="display: none;">
        <div id="buildpro_about_leader_items_wraps">
            <?php if (!empty($items)) {
                foreach ($items as $i => $it) {
                    $icon_id = isset($it['icon_id']) ? (int) $it['icon_id'] : 0;
                    $icon_url = $icon_id ? wp_get_attachment_image_url($icon_id, 'thumbnail') : (isset($it['icon_url']) ? (string) $it['icon_url'] : '');
                    $it_name = isset($it['name']) ? (string) $it['name'] : '';
                    $it_position = isset($it['position']) ? (string) $it['position'] : '';
                    $it_description = isset($it['description']) ? (string) $it['description'] : '';
                    $it_url = isset($it['url']) ? (string) $it['url'] : ''; ?>

            <div class="leader-item">
                <p><label>Icon Image</label></p>
                <input type="hidden" id="buildpro_about_leader_image_id_<?php echo $i; ?>"
                    name="buildpro_about_leader_items[<?php echo $i; ?>][icon_id]"
                    value="<?php echo (int) $icon_id; ?>">
                <div id="buildpro_about_leader_image_preview_<?php echo $i; ?>">
                    <?php echo ($icon_url ? '<img src="' . esc_url($icon_url) . '" style="max-width:150px;height:auto;">' : ''); ?>
                </div>
                <button type="button" class="button buildpro_about_leader_image_select"
                    data-idx="<?php echo $i; ?>">Select Image</button>
                <button type="button" class="button buildpro_about_leader_image_remove"
                    data-idx="<?php echo $i; ?>">Remove</button>
                <p><label>Name<br><input type="text" class="widefat"
                            name="buildpro_about_leader_items[<?php echo $i; ?>][name]"
                            value="<?php echo esc_attr($it_name); ?>"></label></p>
                <p><label>Position<br><input type="text" class="widefat"
                            name="buildpro_about_leader_items[<?php echo $i; ?>][position]"
                            value="<?php echo esc_attr($it_position); ?>"></label></p>
                <p><label>Description<br><input type="text" class="widefat"
                            name="buildpro_about_leader_items[<?php echo $i; ?>][description]"
                            value="<?php echo esc_attr($it_description); ?>"></label></p>
                <p><label>URL<br><input type="text" class="widefat"
                            name="buildpro_about_leader_items[<?php echo $i; ?>][url]"
                            value="<?php echo esc_attr($it_url); ?>"></label></p>
                <p><button type="button" class="button remove-leader">Remove</button></p>
            </div>
            <?php }
            } ?>
        </div>
        <p><button type="button" class="button" id="buildpro_about_leader_add_item">Add Item</button></p>
    </div>
</div>