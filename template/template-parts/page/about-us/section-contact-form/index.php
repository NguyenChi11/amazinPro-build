<?php
$title = __('Get Expert Advice for Your Dream Home', 'buildpro');
$description = __('Leave your email and our construction experts will contact you with personalized solutions.', 'buildpro');
$placeholder = __('Enter your email', 'buildpro');
$home_form_id = function_exists('buildpro_cf7_get_home_form_id') ? (int) buildpro_cf7_get_home_form_id() : 0;
$form_id = $home_form_id > 0 ? $home_form_id : (int) get_option('buildpro_cf7_demo_form_id', 0);
$use_cf7_home_form = $form_id > 0 && class_exists('WPCF7_ContactForm');
$pid = get_queried_object_id();
$use_mod = is_customize_preview();
if (!$pid || get_post_type($pid) !== 'page') {
    $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'about-page.php', 'number' => 1));
    if (!empty($pages)) {
        $pid = (int)$pages[0]->ID;
    }
}
$address = $use_mod ? get_theme_mod('buildpro_about_contact_address', '') : get_post_meta($pid, 'buildpro_about_contact_address', true);
if (!$use_mod) {
    if ($address === '') $address = get_theme_mod('buildpro_about_contact_address', '');
}
$map_image_id = $use_mod ? (int) get_theme_mod('buildpro_about_contact_form_map_image_id', 0) : (int) get_post_meta($pid, 'buildpro_about_contact_form_map_image_id', true);
if (!$use_mod && $map_image_id <= 0) {
    $map_image_id = (int) get_theme_mod('buildpro_about_contact_form_map_image_id', 0);
}
$map_url = $map_image_id ? wp_get_attachment_image_url($map_image_id, 'full') : get_theme_file_uri('/assets/images/map.jpg');
?>
<section class="about-contact-form" data-aos="fade-up">
    <div class="about-contact-form__inner" id="about-contact-form-inner">
        <div class="about-contact-form__content">
            <h2 class="section-contact__title"><?php echo esc_html($title); ?></h2>
            <p class="section-contact__description"><?php echo esc_html($description); ?></p>

            <?php if ($use_cf7_home_form) : ?>
                <div class="section-contact__cf7">
                    <?php echo do_shortcode('[contact-form-7 id="' . (int) $form_id . '"]'); ?>
                </div>
            <?php else : ?>
                <form class="section-contact__form" action="" method="post"
                    data-invalid-message="<?php echo esc_attr__('Please enter a valid email address.', 'buildpro'); ?>"
                    data-success-message="<?php echo esc_attr__('Thank you. We will contact you shortly.', 'buildpro'); ?>"
                    novalidate>
                    <label class="screen-reader-text"
                        for="about-contact-email"><?php esc_html_e('Email address', 'buildpro'); ?></label>
                    <input id="about-contact-email" class="section-contact__input" type="email" name="contact_email"
                        placeholder="<?php echo esc_attr($placeholder); ?>" autocomplete="email" required>
                    <button class="section-contact__submit" type="submit"
                        aria-label="<?php echo esc_attr__('Send email', 'buildpro'); ?>">
                        <span class="screen-reader-text"><?php esc_html_e('Send email', 'buildpro'); ?></span>
                    </button>
                </form>

                <p class="section-contact__feedback" aria-live="polite"></p>
            <?php endif; ?>
        </div>
    </div>
    <div class="about-contact-form__aside">
        <div class="about-contact-form__map" style="background-image:url('<?php echo esc_url($map_url); ?>')">
            <?php if (!empty($address)) : ?>
                <div class="about-contact-form__map-note">
                    <div class="about-contact-form__map-note-icon" aria-hidden="true">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div class="about-contact-form__map-note-text"><?php echo esc_html($address); ?></div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>