<?php
$home_url = home_url('/');
$contact_url = home_url('/contact/');
?>

<section class="error-404-page">
    <div class="error-404-container">
        <h1 class="error-404-title">Error 404?</h1>
        <h2 class="error-404-subtitle">Page Not Found</h2>
        <p class="error-404-description">
            The page you are looking for might have been moved, removed, or is temporarily unavailable. We're building
            something better!
        </p>
        <div class="error-404-buttons">
            <a href="<?php echo esc_url($home_url); ?>" class="btn btn-primary">Back to Home</a>
            <a href="<?php echo esc_url($contact_url); ?>" class="btn btn-secondary">Contact Support</a>
        </div>
    </div>
</section>