<?php
$section_data_items = [];
$page_id = get_queried_object_id();
$enabled = get_post_meta($page_id, 'buildpro_data_enabled', true);
$enabled = $enabled === '' ? 1 : (int)$enabled;
if (is_customize_preview()) {
    $enabled_mod = get_theme_mod('buildpro_data_enabled', 1);
    $enabled = (int)$enabled_mod;
}
$rows = get_post_meta($page_id, 'buildpro_data_items', true);
if (is_customize_preview()) {
    $mods = get_theme_mod('buildpro_data_items', array());
    if (is_array($mods) && !empty($mods)) {
        $rows = $mods;
    }
}
if ($rows && is_array($rows)) {
    foreach ($rows as $row) {
        $number = isset($row['number']) ? $row['number'] : '';
        $text = isset($row['text']) ? $row['text'] : '';
        $section_data_items[] = [
            'number' => $number,
            'text'   => $text,
        ];
    }
}
?>
<?php $style = $enabled !== 1 ? ' style="display:none"' : ''; ?>
<section class="section-data" <?php echo $style; ?>>
    <?php if (is_customize_preview()): ?>
    <div class="section-data__hover-outline"></div>


    <script>
    (function() {
        var btn = document.querySelector('.section-data__customize-button');
        if (btn && window.parent && window.parent.wp && window.parent.wp.customize) {
            btn.addEventListener('click', function() {
                window.parent.wp.customize.section('buildpro_data_section').focus();
            });
        }
    })();
    </script>
    <?php endif; ?>
    <div class="section-data-container">
        <?php foreach ($section_data_items as $item): ?>
        <div class="section-data__item">
            <h3 class="section-data__item-number"><?php echo $item['number']; ?></h3>
            <p class="section-data__item-text"><?php echo $item['text']; ?></p>
        </div>
        <?php endforeach; ?>
    </div>
    <?php if (empty($section_data_items)): ?>
    <script src="<?php echo esc_url(get_theme_file_uri('/assets/data/data-items.js')); ?>"></script>
    <?php endif; ?>
</section>
