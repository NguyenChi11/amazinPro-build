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

$section_option_items = array();
$option_enabled = get_post_meta($page_id, 'buildpro_option_enabled', true);
$option_enabled = $option_enabled === '' ? 1 : (int) $option_enabled;
$option_rows = get_post_meta($page_id, 'buildpro_option_items', true);
if (is_customize_preview()) {
    $option_mods = get_theme_mod('buildpro_option_items', array());
    if (is_array($option_mods) && !empty($option_mods)) {
        $option_rows = $option_mods;
    }
    $option_enabled_mod = get_theme_mod('buildpro_option_enabled', 1);
    $option_enabled = (int) $option_enabled_mod;
}
if ($option_rows && is_array($option_rows)) {
    foreach ($option_rows as $row) {
        $icon_id = isset($row['icon_id']) ? (int) $row['icon_id'] : 0;
        $icon_url = isset($row['icon_url']) ? $row['icon_url'] : '';
        $text = isset($row['text']) ? $row['text'] : '';
        $description = isset($row['description']) ? $row['description'] : '';
        $section_option_items[] = array(
            'icon_id'     => $icon_id,
            'icon_url'    => $icon_url,
            'text'        => $text,
            'description' => $description,
        );
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

$banner_bg = get_theme_file_uri('/assets/images/banner.png');
$image_mask = get_theme_file_uri('/assets/images/image_bg.png');
?>

<section class="section-banner" data-aos="fade-up"
    data-i18n-view-about-us="<?php echo esc_attr__('View About Us', 'buildpro'); ?>"
    data-i18n-right-arrow="<?php echo esc_attr__('Right arrow', 'buildpro'); ?>"
    data-arrow-icon-src="<?php echo esc_url(get_theme_file_uri('/assets/images/icon/Arrow_Right.png')); ?>"
    style="--buildpro-banner-bg:url('<?php echo esc_url($banner_bg); ?>');--buildpro-image-mask:url('<?php echo esc_url($image_mask); ?>');">
    <div class="section-banner_container">
        <?php if (is_customize_preview()): ?>
            <div class="section-banner__hover-outline"></div>
            <div class="section-banner__customize-shortcut">
                <button class="section-banner__customize-button" data-target-section="buildpro_banner_section">
                    <?php esc_html_e('Edit Banner', 'buildpro'); ?>
                </button>
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
                    $btn_title =  !empty($section_banner_house['link_title']) ? $section_banner_house['link_title'] : __('View About Us', 'buildpro');
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
                                alt="<?php echo esc_attr__('Right arrow', 'buildpro'); ?>">
                        </a>
                    <?php else: ?>
                        <button class="section-banner__item-button" disabled>
                            <?php echo esc_html($btn_title); ?>
                            <img class="section-banner__item-button-icon"
                                src="<?php echo esc_url(get_theme_file_uri('/assets/images/icon/Arrow_Right.png')); ?>"
                                alt="<?php echo esc_attr__('Right arrow', 'buildpro'); ?>">
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


    <?php if ($option_enabled === 1 && !empty($section_option_items)): ?>
        <div class="section-banner__options" data-aos="fade-up"
            data-i18n-icon="<?php echo esc_attr__('icon', 'buildpro'); ?>">
            <div class="swiper section-banner__options-swiper mySwiper">
                <div class="swiper-wrapper section-banner__options-swiper-wrapper">
                    <?php foreach ($section_option_items as $section_option_item): ?>
                        <div class="swiper-slide section-banner__options-swiper-item">
                            <div class="section-banner__options-item">
                                <div class="section-banner__options-item-icon">
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
                                        <img src="<?php echo esc_url($icon_src); ?>" class="section-banner__options-item-icon-image"
                                            alt="<?php echo esc_attr__('icon', 'buildpro'); ?>">
                                    <?php endif; ?>
                                </div>
                                <h3 class="section-banner__options-item-text">
                                    <?php echo esc_html($section_option_item['text']); ?></h3>
                                <p class="section-banner__options-item-description">
                                    <?php echo esc_html($section_option_item['description']); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination section-banner__options-pagination"></div>
            </div>
        </div>
    <?php elseif (is_customize_preview()): ?>
        <div class="section-banner__options" data-no-fallback="1" style="display:none"></div>
    <?php endif; ?>
</section>