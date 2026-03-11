<?php
get_header();

if (have_posts()) {
    the_post();
    get_template_part('template/template-parts/single/single-project/index');
}

get_footer();
