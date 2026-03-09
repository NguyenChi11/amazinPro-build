<?php
$section_option_items = [];
$page_id = get_queried_object_id();
$enabled = get_post_meta($page_id, 'buildpro_option_enabled', true);
$enabled = $enabled === '' ? 1 : (int)$enabled;
$rows = get_post_meta($page_id, 'buildpro_option_items', true);
if (is_customize_preview()) {
    $mods = get_theme_mod('buildpro_option_items', array());
    if (is_array($mods) && !empty($mods)) {
        $rows = $mods;
    }
    $enabled_mod = get_theme_mod('buildpro_option_enabled', 1);
    $enabled = (int)$enabled_mod;
}
if ($rows && is_array($rows)) {
    foreach ($rows as $row) {
        $icon_id = isset($row['icon_id']) ? (int)$row['icon_id'] : 0;
        $icon_url = isset($row['icon_url']) ? $row['icon_url'] : '';
        $text = isset($row['text']) ? $row['text'] : '';
        $description = isset($row['description']) ? $row['description'] : '';
        $section_option_items[] = [
            'icon_id'     => $icon_id,
            'icon_url'    => $icon_url,
            'text'        => $text,
            'description' => $description,
        ];
    }
}
$assets_base = get_theme_file_uri('/assets/images/icon/');
$min_count = 6;
$count = count($section_option_items);
if ($count > 0 && $count < $min_count) {
    $duplicated = $section_option_items;
    while (count($duplicated) < $min_count) {
        foreach ($section_option_items as $item) {
            $duplicated[] = $item;
            if (count($duplicated) >= $min_count) {
                break;
            }
        }
    }
    $section_option_items = $duplicated;
}
?>
<?php
$no_items = empty($section_option_items);
$style = $enabled !== 1 ? ' style="display:none"' : '';
?>
<section class="section-option"
    <?php echo $style; ?><?php echo $no_items ? ' data-no-fallback="1" style="display:none"' : ''; ?>>
    <?php if (is_customize_preview()): ?>
    <div class="section-option__hover-outline"></div>


    <script>
    (function() {
        var btn = document.querySelector('.section-option__customize-button');
        if (btn && window.parent && window.parent.wp && window.parent.wp.customize) {
            btn.addEventListener('click', function() {
                window.parent.wp.customize.section('buildpro_option_section').focus();
            });
        }
    })();
    </script>
    <?php endif; ?>
    <div class="swiper section-option__swiper">
        <div class="swiper-wrapper section-option__swiper-wrapper">
            <?php foreach ($section_option_items as $section_option_item): ?>
            <div class="swiper-slide section-option__swiper-item">
                <div class="section-option__item">
                    <div class="section-option__item-icon">
                        <?php
                            $icon_src = '';
                            if (!empty($section_option_item['icon_id'])) {
                                $icon_src = wp_get_attachment_image_url($section_option_item['icon_id'], 'full');
                            }
                            if (!$icon_src && !empty($section_option_item['icon_url'])) {
                                $icon_src = $section_option_item['icon_url'];
                            }
                            ?>
                        <?php if ($icon_src): ?>
                        <img src="<?php echo esc_url($icon_src); ?>" class="section-option__item-icon-image" alt="Icon">
                        <?php endif; ?>
                    </div>
                    <h3 class="section-option__item-text"><?php echo $section_option_item['text']; ?></h3>
                    <p class="section-option__item-description"><?php echo $section_option_item['description']; ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
