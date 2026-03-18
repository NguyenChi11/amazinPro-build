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
$enabled = $use_mod ? get_theme_mod('buildpro_about_banner_enabled', 1) : get_post_meta($pid, 'buildpro_about_banner_enabled', true);
$enabled = $enabled === '' ? 1 : (int) $enabled;
$text = $use_mod ? get_theme_mod('buildpro_about_banner_text', '') : get_post_meta($pid, 'buildpro_about_banner_text', true);
$title = $use_mod ? get_theme_mod('buildpro_about_banner_title', '') : get_post_meta($pid, 'buildpro_about_banner_title', true);
$desc = $use_mod ? get_theme_mod('buildpro_about_banner_description', '') : get_post_meta($pid, 'buildpro_about_banner_description', true);
$facts = $use_mod ? get_theme_mod('buildpro_about_banner_facts', array()) : get_post_meta($pid, 'buildpro_about_banner_facts', true);
$facts = is_array($facts) ? $facts : array();
$image_id = $use_mod ? absint(get_theme_mod('buildpro_about_banner_image_id', 0)) : (int) get_post_meta($pid, 'buildpro_about_banner_image_id', true);
$img_url = $image_id ? wp_get_attachment_image_url($image_id, 'full') : '';
if ($enabled) :
?>
    <section class="about-us__section-banner" data-aos="fade-up">
        <?php if (is_customize_preview()) : ?>
            <div class="about-banner__hover-outline" aria-hidden="true"></div>
        <?php endif; ?>
        <div class="about-us__section-banner__left">
            <?php if (!empty($text)) : ?>
                <p class="about-us__section-banner__left__text"><?php echo esc_html($text); ?></p>
            <?php endif; ?>
            <?php if (!empty($title)) : ?>
                <h1 class="about-us__section-banner__left__title"><?php echo esc_html($title); ?></h1>
            <?php endif; ?>
            <?php if (!empty($desc)) : ?>
                <div class="about-us__section-banner__left__description"><?php echo wp_kses_post(wpautop($desc)); ?></div>
            <?php endif; ?>
            <?php
            $facts_clean = array();
            foreach ($facts as $f) {
                $lbl = isset($f['label']) ? trim($f['label']) : '';
                $val = isset($f['value']) ? trim($f['value']) : '';
                if ($lbl !== '' || $val !== '') {
                    $facts_clean[] = array('label' => $lbl, 'value' => $val);
                }
            }
            if (!empty($facts_clean)) :
            ?>
                <div class="about-us__section-banner__left__wrapper">
                    <?php foreach ($facts_clean as $f) : ?>
                        <div class="about-us__section-banner__left__wrapper__item">
                            <?php if (!empty($f['label'])) : ?>
                                <p class="about-us__section-banner__left__wrapper__item__text"><?php echo esc_html($f['label']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($f['value'])) : ?>
                                <p class="about-us__section-banner__left__wrapper__item__description">
                                    <?php echo esc_html($f['value']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($img_url)) : ?>
            <div class="about-us__section-banner__right">
                <img class="about-us__section-banner__right__image" src="<?php echo esc_url($img_url); ?>" alt="">
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>