<?php
$pid = get_queried_object_id();
$use_mod = is_customize_preview();
if (!$pid || get_post_type($pid) !== 'page') {
    $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => 1));
    if (!empty($pages)) {
        $pid = (int)$pages[0]->ID;
    }
}
if (!$pid || get_post_type($pid) !== 'page') {
    $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-us-page.php', 'number' => 1));
    if (!empty($pages)) {
        $pid = (int)$pages[0]->ID;
    }
}
$enabled = $use_mod ? get_theme_mod('buildpro_about_contact_enabled', 1) : get_post_meta($pid, 'buildpro_about_contact_enabled', true);
$enabled = ($enabled === '' ? 1 : (int)$enabled);
$title = $use_mod ? get_theme_mod('buildpro_about_contact_title', '') : get_post_meta($pid, 'buildpro_about_contact_title', true);
$text = $use_mod ? get_theme_mod('buildpro_about_contact_text', '') : get_post_meta($pid, 'buildpro_about_contact_text', true);
$address = $use_mod ? get_theme_mod('buildpro_about_contact_address', '') : get_post_meta($pid, 'buildpro_about_contact_address', true);
$phone = $use_mod ? get_theme_mod('buildpro_about_contact_phone', '') : get_post_meta($pid, 'buildpro_about_contact_phone', true);
$email = $use_mod ? get_theme_mod('buildpro_about_contact_email', '') : get_post_meta($pid, 'buildpro_about_contact_email', true);
if (!$use_mod) {
    if ($title === '') $title = get_theme_mod('buildpro_about_contact_title', '');
    if ($text === '') $text = get_theme_mod('buildpro_about_contact_text', '');
    if ($address === '') $address = get_theme_mod('buildpro_about_contact_address', '');
    if ($phone === '') $phone = get_theme_mod('buildpro_about_contact_phone', '');
    if ($email === '') $email = get_theme_mod('buildpro_about_contact_email', '');
}
if ($enabled) :
?>
    <section class="about-contact" data-aos="fade-up">
        <?php if (is_customize_preview()) : ?>
            <div class="contact__hover-outline" aria-hidden="true"></div>
        <?php endif; ?>
        <div class="about-contact__inner">
            <?php if ($title !== '') : ?>
                <h2 class="about-contact__title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>
            <?php if ($text !== '') : ?>
                <p class="about-contact__desc"><?php echo esc_html($text); ?></p>
            <?php endif; ?>
            <div class="about-contact__grid">
                <?php if ($address !== '') : ?>
                    <div class="about-contact__card">
                        <div class="about-contact__icon">
                            <img src="<?php echo esc_url(get_theme_file_uri('/assets/images/icon/icon_building.png')); ?>"
                                alt="">
                        </div>
                        <div class="about-contact__content">
                            <div class="about-contact__label"><?php esc_html_e('Office Address', 'buildpro'); ?></div>
                            <div class="about-contact__value"><?php echo esc_html($address); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($phone !== '') : ?>
                    <div class="about-contact__card">
                        <div class="about-contact__icon">
                            <img src="<?php echo esc_url(get_theme_file_uri('/assets/images/icon/icon_phone_ft.png')); ?>"
                                alt="">
                        </div>
                        <div class="about-contact__content">
                            <div class="about-contact__label"><?php esc_html_e('Phone Number', 'buildpro'); ?></div>
                            <div class="about-contact__value"><?php echo esc_html($phone); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($email !== '') : ?>
                    <div class="about-contact__card">
                        <div class="about-contact__icon">
                            <img src="<?php echo esc_url(get_theme_file_uri('/assets/images/icon/icon_email_ft.png')); ?>"
                                alt="">
                        </div>
                        <div class="about-contact__content">
                            <div class="about-contact__label"><?php esc_html_e('Official Email', 'buildpro'); ?></div>
                            <div class="about-contact__value"><?php echo esc_html($email); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>