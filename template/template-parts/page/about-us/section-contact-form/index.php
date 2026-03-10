<?php
$form_id = (int) get_option('buildpro_cf7_demo_form_id', 0);
$title = 'Send an Inquiry';
$pid = get_queried_object_id();
$use_mod = is_customize_preview();
if (!$pid || get_post_type($pid) !== 'page') {
    $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => 1));
    if (!empty($pages)) {
        $pid = (int)$pages[0]->ID;
    }
}
$address = $use_mod ? get_theme_mod('buildpro_about_contact_address', '') : get_post_meta($pid, 'buildpro_about_contact_address', true);
$phone = $use_mod ? get_theme_mod('buildpro_about_contact_phone', '') : get_post_meta($pid, 'buildpro_about_contact_phone', true);
$email = $use_mod ? get_theme_mod('buildpro_about_contact_email', '') : get_post_meta($pid, 'buildpro_about_contact_email', true);
if (!$use_mod) {
    if ($address === '') $address = get_theme_mod('buildpro_about_contact_address', '');
    if ($phone === '') $phone = get_theme_mod('buildpro_about_contact_phone', '');
    if ($email === '') $email = get_theme_mod('buildpro_about_contact_email', '');
}
$map_image_id = $use_mod ? (int) get_theme_mod('buildpro_about_contact_form_map_image_id', 0) : (int) get_post_meta($pid, 'buildpro_about_contact_form_map_image_id', true);
if (!$use_mod && $map_image_id <= 0) {
    $map_image_id = (int) get_theme_mod('buildpro_about_contact_form_map_image_id', 0);
}
$map_url = $map_image_id ? wp_get_attachment_image_url($map_image_id, 'full') : get_theme_file_uri('/assets/images/map.jpg');
?>
<section class="about-contact-form">
    <div class="about-contact-form__inner">
        <h2 class="about-contact-form__title"><?php echo esc_html($title); ?></h2>
        <div class="about-contact-form__content">
            <?php
            if ($form_id > 0 && class_exists('WPCF7_ContactForm')) {
                echo do_shortcode('[contact-form-7 id="' . $form_id . '" title="BuildPro Contact Form"]');
            } else {
                echo '<p>Contact Form chưa sẵn sàng.</p>';
            }
            ?>
        </div>
    </div>
    <div class="about-contact-form__aside">
        <div class="about-contact-form__map" style="background-image:url('<?php echo esc_url($map_url); ?>')">
            <?php if (!empty($address)) : ?>
                <div class="about-contact-form__map-note">
                    <div class="about-contact-form__map-note-text"><?php echo esc_html($address); ?></div>
                </div>
            <?php endif; ?>
        </div>
        <div class="about-contact-form__connect">
            <h3 class="about-contact-form__connect-title">Connect With Us</h3>
            <div class="about-contact-form__connect-list">
                <?php if (!empty($email)) : ?>
                    <a class="connect-item" href="mailto:<?php echo esc_attr($email); ?>" aria-label="Email">
                        <i class="fa-solid fa-globe"></i>
                    </a>
                <?php endif; ?>
                <?php if (!empty($phone)) : ?>
                    <a class="connect-item" href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $phone)); ?>"
                        aria-label="Phone">
                        <i class="fa-solid fa-phone"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>