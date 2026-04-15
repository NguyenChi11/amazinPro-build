<?php
$enabled = apply_filters('buildpro_home_contact_enabled', true);
if (!$enabled) {
    return;
}

$title = apply_filters('buildpro_home_contact_title', __('Get Expert Advice for Your Dream Home', 'buildpro'));
$description = apply_filters('buildpro_home_contact_description', __('Leave your email and our construction experts will contact you with personalized solutions.', 'buildpro'));
$placeholder = apply_filters('buildpro_home_contact_placeholder', __('Enter your email', 'buildpro'));
$image_url = apply_filters('buildpro_home_contact_image_url', get_theme_file_uri('/assets/images/image_contact.jpg'));
$home_form_id = function_exists('buildpro_cf7_get_home_form_id') ? (int) buildpro_cf7_get_home_form_id() : 0;
$use_cf7_home_form = $home_form_id > 0 && class_exists('WPCF7_ContactForm');
?>

<section class="section-contact" data-aos="fade-up">
    <?php if (is_customize_preview()): ?>
    <div class="section-contact__hover-outline"></div>

    <script>
    (function() {
        var btn = document.querySelector('.section-contact__customize-button');
        if (btn && window.parent && window.parent.wp && window.parent.wp.customize) {
            btn.addEventListener('click', function() {
                window.parent.wp.customize.section('buildpro_contact_section').focus();
            });
        }
    })();
    </script>
    <?php endif; ?>
    <div class="section-contact__inner">
        <div class="section-contact__media">
            <img src="<?php echo esc_url($image_url); ?>"
                alt="<?php echo esc_attr__('Architecture consultation', 'buildpro'); ?>">
        </div>
        <div class="section-contact__content">
            <h2 class="section-contact__title"><?php echo esc_html($title); ?></h2>
            <p class="section-contact__description"><?php echo esc_html($description); ?></p>

            <?php if ($use_cf7_home_form) : ?>
            <div class="section-contact__cf7">
                <?php echo do_shortcode('[contact-form-7 id="' . (int) $home_form_id . '"]'); ?>
            </div>
            <?php else : ?>
            <form class="section-contact__form" action="" method="post"
                data-invalid-message="<?php echo esc_attr__('Please enter a valid email address.', 'buildpro'); ?>"
                data-success-message="<?php echo esc_attr__('Thank you. We will contact you shortly.', 'buildpro'); ?>"
                novalidate>
                <label class="screen-reader-text"
                    for="section-contact-email"><?php esc_html_e('Email address', 'buildpro'); ?></label>
                <input id="section-contact-email" class="section-contact__input" type="email" name="contact_email"
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
</section>