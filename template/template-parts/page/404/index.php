<?php
$home_url = home_url('/');
$contact_url = home_url('/contact/');
?>

<section class="error-404-page">
    <div class="error-404-container">
        <h1 class="error-404-title"><?php esc_html_e('Error 404?', 'buildpro'); ?></h1>
        <h2 class="error-404-subtitle"><?php esc_html_e('Page Not Found', 'buildpro'); ?></h2>
        <p class="error-404-description">
            <?php esc_html_e("The page you are looking for might have been moved, removed, or is temporarily unavailable. We're building something better!", 'buildpro'); ?>
        </p>
        <div class="error-404-buttons">
            <a href="<?php echo esc_url($home_url); ?>" class="btn btn-primary"><?php esc_html_e('Back to Home', 'buildpro'); ?></a>
            <a href="<?php echo esc_url($contact_url); ?>" class="btn btn-secondary"><?php esc_html_e('Contact Support', 'buildpro'); ?></a>
        </div>
    </div>
</section>