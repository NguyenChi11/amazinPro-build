<?php

/**
 * Breadcrumb Template Part
 *
 * Displays breadcrumb navigation based on current page context
 */

// Only show breadcrumb if not on homepage
if (!is_front_page()) : ?>
    <?php
    // Include breadcrumb function if not already loaded
    if (!function_exists('display_breadcrumb')) {
        $breadcrumb_function_path = get_template_directory() . '/inc/functions/breadcrums-function.php';
        if (file_exists($breadcrumb_function_path)) {
            require_once $breadcrumb_function_path;
        }
    }

    // Display breadcrumb if function exists
    if (function_exists('display_breadcrumb')) :
    ?>
        <div class="breadcrumb-container">
            <?php
            display_breadcrumb(' > ', 'custom-breadcrumb-wrapper');

            // Add structured data for SEO
            if (function_exists('get_breadcrumb_json_ld')) {
                $json_ld = get_breadcrumb_json_ld();
                if (!empty($json_ld)) {
                    echo '<script type="application/ld+json">' . $json_ld . '</script>';
                }
            }
            ?>
        </div>
    <?php endif; ?>
<?php endif; ?>