<?php
$product_id = isset($_GET['project-id']) ? (int) $_GET['project-id'] : (isset($_GET['product_id']) ? (int) $_GET['product_id'] : 0);
$product_title = $product_id ? get_the_title($product_id) : '';
$product_image = $product_id ? get_the_post_thumbnail_url($product_id, 'large') : '';

$title = __('Finalize Your Project', 'buildpro');
$description = __('Please provide your contact information below to finalize your project booking. Our team will contact you shortly.', 'buildpro');

// Load styles
wp_enqueue_style('checkout-contact-style', get_template_directory_uri() . '/template/template-parts/page/checkout-contact/style.css', array(), '1.0.0');
?>

<div class="checkout-contact-container">
    <?php get_template_part('template/template-parts/breadcrums/index'); ?>

    <section class="checkout-contact" data-aos="fade-up">
        <div class="checkout-contact__wrapper">
            <div class="checkout-contact__form-side">
                <header class="checkout-contact__header">
                    <h1 class="checkout-contact__title"><?php echo esc_html($title); ?></h1>
                    <p class="checkout-contact__description"><?php echo esc_html($description); ?></p>
                </header>

                <?php if (function_exists('buildpro_cf7_is_active') && buildpro_cf7_is_active()) :
                    $checkout_form_id = buildpro_cf7_get_checkout_form_id();
                    if ($checkout_form_id > 0) :
                        echo do_shortcode('[contact-form-7 id="' . $checkout_form_id . '" title="' . buildpro_cf7_checkout_form_title() . '"]');
                    endif;
                else : ?>
                <form id="checkout-contact-form" class="checkout-contact__form" action="" method="POST">
                    <input type="hidden" name="project_id" value="<?php echo esc_attr($product_id); ?>">
                    <input type="hidden" name="project_title" value="<?php echo esc_attr($product_title); ?>">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="full_name"><?php _e('Full Name', 'buildpro'); ?> *</label>
                            <input type="text" id="full_name" name="full_name" required
                                placeholder="<?php _e('Enter your full name', 'buildpro'); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email"><?php _e('Email Address', 'buildpro'); ?> *</label>
                            <input type="email" id="email" name="email" required
                                placeholder="<?php _e('Enter your email', 'buildpro'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="phone"><?php _e('Phone Number', 'buildpro'); ?> *</label>
                            <input type="tel" id="phone" name="phone" required
                                placeholder="<?php _e('Enter your phone number', 'buildpro'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="message"><?php _e('Additional Notes (Optional)', 'buildpro'); ?></label>
                        <textarea id="message" name="message" rows="4"
                            placeholder="<?php _e('Anything else we should know?', 'buildpro'); ?>"></textarea>
                    </div>

                    <div class="checkout-contact__footer">
                        <button type="submit" class="checkout-contact__submit">
                            <span><?php _e('Submit Booking', 'buildpro'); ?></span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
                <?php endif; ?>
            </div>

            <div class="checkout-contact__summary-side">
                <?php if ($product_id): ?>
                <div class="project-summary-card">
                    <h3 class="project-summary-card__title"><?php _e('Project Summary', 'buildpro'); ?></h3>
                    <?php if ($product_image): ?>
                    <div class="project-summary-card__image-wrap">
                        <img src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr($product_title); ?>">
                    </div>
                    <?php endif; ?>
                    <div class="project-summary-card__content">
                        <h4 class="project-summary-card__name"><?php echo esc_html($product_title); ?></h4>
                        <?php 
                            $price = get_post_meta($product_id, '_price', true);
                            if ($price): ?>
                        <p class="project-summary-card__price">
                            <?php echo function_exists('wc_price') ? wc_price($price) : $price; ?>
                        </p>
                        <?php endif; ?>

                        <div class="project-summary-card__meta">
                            <?php 
                                $bedrooms = get_post_meta($product_id, 'buildpro_product_bedrooms', true);
                                $bathrooms = get_post_meta($product_id, 'buildpro_product_bathrooms', true);
                                ?>
                            <?php if ($bedrooms): ?>
                            <span><i class="fa-solid fa-bed"></i> <?php echo esc_html($bedrooms); ?></span>
                            <?php endif; ?>
                            <?php if ($bathrooms): ?>
                            <span><i class="fa-solid fa-bath"></i> <?php echo esc_html($bathrooms); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="checkout-contact__promo">
                    <div class="checkout-contact__promo-image">
                        <img src="<?php echo esc_url(get_theme_file_uri('/assets/images/image_contact.jpg')); ?>"
                            alt="">
                    </div>
                    <div class="checkout-contact__promo-text">
                        <h3><?php _e('Why Choose Us?', 'buildpro'); ?></h3>
                        <p><?php _e('We provide world-class construction and architecture services tailored to your vision.', 'buildpro'); ?>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>