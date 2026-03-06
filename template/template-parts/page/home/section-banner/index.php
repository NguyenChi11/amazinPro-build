<?php
$arrow_right = 60;

$section_banner_houses = [];
$page_id = get_queried_object_id();
$enabled = get_post_meta($page_id, 'buildpro_banner_enabled', true);
$enabled = $enabled === '' ? 1 : (int)$enabled;
if (is_customize_preview()) {
    $enabled_mod = get_theme_mod('buildpro_banner_enabled', 1);
    $enabled = (int)$enabled_mod;
}
if ($enabled !== 1) {
    return;
}
$rows = get_post_meta($page_id, 'buildpro_banner_items', true);
if (is_customize_preview()) {
    $mods = get_theme_mod('buildpro_banner_items', array());
    if (is_array($mods) && !empty($mods)) {
        $rows = $mods;
    }
}
if ($rows && is_array($rows)) {
    foreach ($rows as $row) {
        $image_id = isset($row['image_id']) ? (int)$row['image_id'] : 0;
        $type = isset($row['type']) ? $row['type'] : '';
        $text = isset($row['text']) ? $row['text'] : '';
        $description = isset($row['description']) ? $row['description'] : '';
        $link_url = isset($row['link_url']) ? $row['link_url'] : '';
        $link_title = isset($row['link_title']) ? $row['link_title'] : '';
        $link_target = isset($row['link_target']) ? $row['link_target'] : '';
        $section_banner_houses[] = [
            'image_id'    => $image_id,
            'type'        => $type,
            'text'        => $text,
            'description' => $description,
            'link_url'    => $link_url,
            'link_title'  => $link_title,
            'link_target' => $link_target,
        ];
    }
}
if (empty($section_banner_houses)) {
    if (is_customize_preview()) {
?>
<section class="section-banner" data-no-fallback="1" style="display:none"></section>
<?php
    }
    return;
}
?>

<section class="section-banner">
    <div class="section-banner_container">
        <?php if (is_customize_preview()): ?>
        <div class="section-banner__hover-outline"></div>
        <div class="section-banner__customize-shortcut">
            <button class="section-banner__customize-button" data-target-section="buildpro_banner_section">Edit
                Banner</button>
        </div>
        <script>
        (function() {
            var btn = document.querySelector('.section-banner__customize-button');
            if (btn && window.parent && window.parent.wp && window.parent.wp.customize) {
                btn.addEventListener('click', function() {
                    window.parent.wp.customize.section('buildpro_banner_section').focus();
                });
            }
        })();
        </script>
        <?php endif; ?>
        <div class=" container-banner-left">
            <?php foreach ($section_banner_houses as $index => $section_banner_house): ?>
            <div class="section-banner__item <?php echo $index === 0 ? 'active' : ''; ?> ">
                <div class="section-banner__item-content">
                    <h3 class="section-banner__item-type"><?php echo esc_html($section_banner_house['type']); ?></h3>
                    <h2 class="section-banner__item-text"><?php echo esc_html($section_banner_house['text']); ?></h2>
                    <p class="section-banner__item-description">
                        <?php echo esc_html($section_banner_house['description']); ?></p>
                </div>
                <?php
                    $btn_url = $section_banner_house['link_url'];
                    $btn_title =  !empty($section_banner_house['link_title']) ? $section_banner_house['link_title'] : 'View About Us';
                    $btn_target = $section_banner_house['link_target'];
                    $target_attr = $btn_target ? ' target="' . esc_attr($btn_target) . '"' : '';
                    $rel_attr = ($btn_target === '_blank') ? ' rel="noopener"' : '';
                    ?>
                <?php if ($btn_url): ?>
                <a class="section-banner__item-button" href="<?php echo esc_url($btn_url); ?>"
                    <?php echo $target_attr . $rel_attr; ?>>
                    <?php echo esc_html($btn_title); ?>
                    <img class="section-banner__item-button-icon"
                        src="<?php echo esc_url(get_theme_file_uri('/assets/images/icon/Arrow_Right.png')); ?>"
                        alt="Arrow Right">
                </a>
                <?php else: ?>
                <button class="section-banner__item-button" disabled>
                    <?php echo esc_html($btn_title); ?>
                    <img class="section-banner__item-button-icon"
                        src="<?php echo esc_url(get_theme_file_uri('/assets/images/icon/Arrow_Right.png')); ?>"
                        alt="Arrow Right">
                </button>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="container-banner-right">
            <div class="section-banner__image-stack">
                <?php foreach ($section_banner_houses as $index => $section_banner_house): ?>
                <?php
                    $img_url = $section_banner_house['image_id'] ? wp_get_attachment_image_url($section_banner_house['image_id'], 'full') : (isset($section_banner_house['image_url']) ? $section_banner_house['image_url'] : null);
                    if ($img_url) :
                    ?>
                <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($section_banner_house['type']); ?>"
                    class="section-banner__image<?php echo $index === 0 ? ' active' : ''; ?>" />
                <?php else : ?>
                <div class="section-banner__image placeholder<?php echo $index === 0 ? ' active' : ''; ?>">
                    <!-- No image available -->
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="section-banner__pagination">
        <div class="section-banner__pagination-container">
            <?php foreach ($section_banner_houses as $i => $house): ?>
            <button
                class="section-banner__page <?php echo $i === 0 ? 'pos-center active' : ($i === 1 ? 'pos-right' : 'pos-left'); ?>"
                disabled data-index="<?php echo esc_attr($i); ?>" aria-label="<?php echo esc_attr($house['type']); ?>">
                <span class="section-banner__page-dot"></span>
            </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>