<?php if (!defined('ABSPATH')) {
    exit;
} ?>
<div id="buildpro_about_banner_meta">
    <div class="buildpro-admin-tabs">
        <button type="button" class="button buildpro-about-banner-tab is-active"
            data-target="buildpro_about_banner_tab_content">Content</button>
        <button type="button" class="button buildpro-about-banner-tab"
            data-target="buildpro_about_banner_tab_facts">Facts</button>
        <button type="button" class="button buildpro-about-banner-tab"
            data-target="buildpro_about_banner_tab_media">Media</button>
    </div>
    <div id="buildpro_about_banner_tab_content">
        <p><label><input type="checkbox" name="buildpro_about_banner_enabled" value="1" <?php checked($enabled, 1); ?>>
                Enable</label></p>
        <p><label>Text<br><input type="text" class="widefat" name="buildpro_about_banner_text"
                    value="<?php echo esc_attr($text); ?>"></label></p>
        <p><label>Title<br><input type="text" class="widefat" name="buildpro_about_banner_title"
                    value="<?php echo esc_attr($title); ?>"></label></p>
        <p><label>Description</label></p>
        <?php
        ob_start();
        wp_editor($desc, 'buildpro_about_banner_description_editor', array(
            'textarea_name' => 'buildpro_about_banner_description',
            'textarea_rows' => 6,
            'media_buttons' => false,
            'teeny' => true,
            'quicktags' => false,
        ));
        echo ob_get_clean();
        ?>
    </div>
    <div id="buildpro_about_banner_tab_facts" style="display:none">
        <div id="buildpro_about_banner_facts_wrap">
            <?php if (!empty($facts)) {
                foreach ($facts as $i => $f) {
                    $fl = isset($f['label']) ? $f['label'] : '';
                    $fv = isset($f['value']) ? $f['value'] : ''; ?>
                    <div class="about-fact">
                        <p><label>Label<br><input type="text" class="widefat"
                                    name="buildpro_about_banner_facts[<?php echo $i; ?>][label]"
                                    value="<?php echo esc_attr($fl); ?>"></label></p>
                        <p><label>Value<br><input type="text" class="widefat"
                                    name="buildpro_about_banner_facts[<?php echo $i; ?>][value]"
                                    value="<?php echo esc_attr($fv); ?>"></label></p>
                        <p><button type="button" class="button remove-fact">Remove</button></p>
                    </div>
            <?php }
            } ?>
        </div>
        <p><button type="button" class="button" id="buildpro_add_fact">Add Fact</button></p>
    </div>
    <div id="buildpro_about_banner_tab_media" style="display:none">
        <input type="hidden" id="buildpro_about_banner_image_id" name="buildpro_about_banner_image_id"
            value="<?php echo (int) $image_id; ?>">
        <div id="buildpro_about_banner_image_preview">
            <?php echo ($thumb ? '<img src="' . esc_url($thumb) . '" style="max-width:150px;height:auto;">' : ''); ?>
        </div>
        <button type="button" class="button" id="buildpro_about_banner_image_select">Select Image</button>
        <button type="button" class="button" id="buildpro_about_banner_image_remove">Remove</button>
    </div>
</div>