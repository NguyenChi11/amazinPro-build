<?php
$page_id = get_queried_object_id();
$service_enabled = get_post_meta($page_id, 'buildpro_service_enabled', true);
$service_enabled = $service_enabled === '' ? 1 : (int)$service_enabled;
$service_title = get_post_meta($page_id, 'buildpro_service_title', true);
$service_desc = get_post_meta($page_id, 'buildpro_service_desc', true);
if (is_customize_preview()) {
    $mod_title = get_theme_mod('buildpro_service_title', '');
    if ($mod_title !== '') {
        $service_title = $mod_title;
    }
    $mod_desc = get_theme_mod('buildpro_service_desc', '');
    if ($mod_desc !== '') {
        $service_desc = $mod_desc;
    }
    $mod_enabled = get_theme_mod('buildpro_service_enabled', 1);
    $service_enabled = (int)$mod_enabled;
}
$service_items = [];
$rows = get_post_meta($page_id, 'buildpro_service_items', true);
if (is_customize_preview()) {
    $mods = get_theme_mod('buildpro_service_items', array());
    if (is_array($mods) && !empty($mods)) {
        $rows = $mods;
    }
}
if ($rows && is_array($rows)) {
    foreach ($rows as $row) {
        $icon_id = isset($row['icon_id']) ? (int)$row['icon_id'] : 0;
        $title = isset($row['title']) ? $row['title'] : '';
        $description = isset($row['description']) ? $row['description'] : '';
        $link_url = isset($row['link_url']) ? $row['link_url'] : '';
        $link_title = isset($row['link_title']) ? $row['link_title'] : '';
        $link_target = isset($row['link_target']) ? $row['link_target'] : '';
        $service_items[] = [
            'icon_id' => $icon_id,
            'title' => $title,
            'description' => $description,
            'link_url' => $link_url,
            'link_title' => $link_title,
            'link_target' => $link_target,
        ];
    }
}
$icon_right = 212;
?>
<?php $style = ($service_enabled !== 1 || empty($service_items)) ? ' style="display:none"' : ''; ?>
<section class="section-services" <?php echo $style; ?>>
    <?php if (is_customize_preview()): ?>
        <div class="section-services__hover-outline"></div>
    <?php endif; ?>
    <div class="section-services__header">
        <?php if ($service_title !== ''): ?>
            <h2 class="section-services__title">
                <?php echo esc_html($service_title); ?>
            </h2>
        <?php endif; ?>
        <?php if ($service_desc !== ''): ?>
            <p class="section-services__description">
                <?php echo esc_html($service_desc); ?>
            </p>
        <?php endif; ?>
    </div>
    <div class="section-services__container">
        <?php foreach ($service_items as $item): ?>
            <div class="section-services__item">
                <div class="section-services__item-icon">
                    <?php
                    $icon_url = $item['icon_id'] ? wp_get_attachment_image_url($item['icon_id'], 'full') : '';
                    ?>
                    <?php if ($icon_url): ?>
                        <img src="<?php echo esc_url($icon_url); ?>" alt="<?php echo esc_attr($item['title']); ?>"
                            class="section-services__item-icon-image">
                    <?php endif; ?>
                </div>
                <h3 class="section-services__item-title"><?php echo esc_html($item['title']); ?></h3>
                <p class="section-services__item-description"><?php echo esc_html($item['description']); ?></p>
                <?php if (!empty($item['link_url'])): ?>
                    <?php
                    $target_attr = !empty($item['link_target']) ? ' target="' . esc_attr($item['link_target']) . '"' : '';
                    $rel_attr = (!empty($item['link_target']) && $item['link_target'] === '_blank') ? ' rel="noopener"' : '';
                    ?>
                    <a class="section-services__item-link" href="<?php echo esc_url($item['link_url']); ?>"
                        <?php echo $target_attr . $rel_attr; ?>>
                        <?php echo esc_html('View Details'); ?>
                        <img src="<?php echo esc_url(get_theme_file_uri('/assets/images/icon/Arrow_Right_blue.png')); ?>"
                            alt="right arrow" class="section-services__item-link-icon">
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>