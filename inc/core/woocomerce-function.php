<?php
add_filter('woocommerce_create_pages', function ($pages) {
    return array();
}, 99);
add_filter('woocommerce_enable_setup_wizard', '__return_false');