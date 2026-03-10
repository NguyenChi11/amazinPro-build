<?php
$pid = 0;
if (is_singular('page')) {
    $pid = get_the_ID();
}
if ($pid <= 0) {
    $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => 1));
    if (!empty($pages)) {
        $pid = (int) $pages[0]->ID;
    }
}
if ($pid <= 0) {
    $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-us-page.php', 'number' => 1));
    if (!empty($pages)) {
        $pid = (int) $pages[0]->ID;
    }
}
$use_mod = is_customize_preview();
$enabled = $use_mod ? get_theme_mod('buildpro_about_core_values_enabled', 1) : get_post_meta($pid, 'buildpro_about_core_values_enabled', true);
$enabled = $enabled === '' ? 1 : (int) $enabled;
$title = $use_mod ? get_theme_mod('buildpro_about_core_values_title', '') : get_post_meta($pid, 'buildpro_about_core_values_title', true);
$desc  = $use_mod ? get_theme_mod('buildpro_about_core_values_description', '') : get_post_meta($pid, 'buildpro_about_core_values_description', true);
$items = $use_mod ? get_theme_mod('buildpro_about_core_values_items', array()) : get_post_meta($pid, 'buildpro_about_core_values_items', true);
$items = is_array($items) ? $items : array();
if ($enabled && !empty($items)) :
?>
    <section class="about-core-values" <?php echo empty($items) ? ' data-auto="1"' : ''; ?>>
        <?php if (is_customize_preview()) : ?>
            <div class="core-values__hover-outline" aria-hidden="true"></div>
        <?php endif; ?>
        <div class="about-core-values__inner">
            <div class="about-core-values__header">
                <?php if ($title !== '') : ?>
                    <h2 class="about-core-values__title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>
                <?php if ($desc !== '') : ?>
                    <p class="about-core-values__description"><?php echo esc_html($desc); ?></p>
                <?php endif; ?>
            </div>
            <div class="about-core-values__grid">
                <?php if (!empty($items)) : ?>
                    <?php foreach ($items as $it) : ?>
                        <?php
                        $icon_id = isset($it['icon_id']) ? (int)$it['icon_id'] : 0;
                        $icon_url = $icon_id ? wp_get_attachment_image_url($icon_id, 'thumbnail') : (isset($it['icon_url']) ? (string)$it['icon_url'] : '');
                        $icon = isset($it['icon']) ? (string)$it['icon'] : '';
                        $it_title = isset($it['title']) ? (string)$it['title'] : '';
                        $it_desc = isset($it['description']) ? (string)$it['description'] : '';
                        $it_url = isset($it['url']) ? (string)$it['url'] : '#';
                        ?>
                        <div class="about-core-values__card">
                            <div class="about-core-values__icon">
                                <?php
                                if (!empty($icon_url)) {
                                    echo '<img src="' . esc_url($icon_url) . '" alt="" style="max-width:2.75rem;height:auto;border-radius:0.625rem;">';
                                } elseif ($icon !== '' && function_exists('buildpro_svg_icon')) {
                                    echo buildpro_svg_icon($icon, 'solid');
                                }
                                ?>
                            </div>
                            <?php if ($it_title !== '') : ?>
                                <h3 class="about-core-values__card-title"><?php echo esc_html($it_title); ?></h3>
                            <?php endif; ?>
                            <?php if ($it_desc !== '') : ?>
                                <p class="about-core-values__card-desc"><?php echo esc_html($it_desc); ?></p>
                            <?php endif; ?>
                            <a class="about-core-values__card-link" href="<?php echo esc_url($it_url); ?>">
                                <span>View Details</span>
                                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>