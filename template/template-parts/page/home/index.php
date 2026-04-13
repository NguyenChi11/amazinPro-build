<?php
if (function_exists('buildpro_render_home_sections')) {
    buildpro_render_home_sections();
    return;
}

get_template_part('template/template-parts/page/home/section-banner/index');
get_template_part('template/template-parts/page/home/section-data/index');
get_template_part('template/template-parts/page/home/section-products/index');
get_template_part('template/template-parts/page/home/section-services/index');
get_template_part('template/template-parts/page/home/section-evaluate/index');
get_template_part('template/template-parts/page/home/section-projects/index');
get_template_part('template/template-parts/page/home/section-post/index');
