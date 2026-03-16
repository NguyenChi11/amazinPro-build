<?php
/*
Template Name: 404 Page
*/

get_header();
?>

<main id="primary" class="site-main">
    <?php
    // Load 404 styles
    wp_enqueue_style('404-styles', get_template_directory_uri() . '/template/template-parts/page/404/style.css', array(), '1.0.0');

    // Load 404 template
    get_template_part('template/template-parts/page/404/index');
    ?>
</main>

<?php
get_footer();
?>